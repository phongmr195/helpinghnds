<?php

namespace App\Http\Controllers\Admin\Payout;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ApiResponser;
use App\Models\Card;
use App\Models\CashOutLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Services\Admin\CountryService;
use App\Services\Admin\PayoutService;
use App\Services\Admin\UserService;
use App\Http\Requests\Admin\Payout\AddcardRequest;
use App\Http\Requests\Admin\Payout\ConfirmCashoutRequest;
use Illuminate\Support\Facades\Hash;

class PayoutController extends Controller
{
    use ApiResponser;

    protected $countryService;
    protected $payoutService;
    protected $userService;

    public function __construct(CountryService $countryService, PayoutService $payoutService, UserService $userService)
    {
        $this->countryService = $countryService;
        $this->payoutService = $payoutService;
        $this->userService = $userService;
    }

    /**
     * Man hinh thong tin rut tien cua worker
     */
    public function ofWorker(Request $request)
    {
        $listCards = [];
        $firstCard = null;
        $listBank = [];
        $worker = getUserByAccessToken($request->accessToken);
        $pendingBalance = 0;

        if(!is_null($worker)){
            $pendingBalanceData = CashOutLog::where('worker_id', $worker->id)
                ->select(
                    DB::raw('SUM(CASE WHEN status = "0" THEN cash_out_amount ELSE 0 End) AS total_pending_balance'),
                )
                ->first();
            $pendingBalance = $pendingBalanceData->total_pending_balance ?? 0;
            $listCards = Card::where('user_id', $worker->id)->latest()->get();
            if(isset($listCards) && $listCards->count()){
                $firstCard = $listCards->first()->id;
            }
        }
        $response = Http::get('https://api.vietqr.io/v2/banks')->json();
        if(isset($response['data']) && count($response['data'])){
            foreach($response['data'] as $item){
                if(in_array($item['bin'], config('constant.bin_code_support'))){
                    $listBank[] = $item;
                }
            }
        }

        return view('admin.pages.payout', compact('listBank', 'worker', 'listCards', 'firstCard', 'pendingBalance'));
    }

    /**
     * Show page cashout
     */
    public function show(Request $request)
    {
        $route_refresh = 'admin.pages.cashout';
        $countries = $this->countryService->listCountry();

        return view('admin.pages.cashout', compact('route_refresh', 'countries'));
    }

    /**
     * Get list cashout
     */
    public function getListCashout(Request $request)
    {
        $data = $this->payoutService->getListCashoutWithFilter($request->all());

        return $this->createJsonDatatable($data);
    }

    /**
     * Create json datatable cashout list
     *
     * @param mixed $data
     *
     * @return json
     */
    public function createJsonDatatable($data)
    {
        return datatables()->eloquent($data)
            ->editColumn('id', function($cashout){
                return $cashout->id;
            })
            ->editColumn('balance', function($cashout){
                return number_format($cashout->current_balance, 0, ',', '.');
            })
            ->editColumn('amount', function($cashout){
                return number_format($cashout->cash_out_amount, 0, ',', '.');
            })
            ->editColumn('status_icon', function($cashout){
                return '<span class="badge '.getClassCashoutStatus($cashout->status).'">'.config("constant.cashout_status.$cashout->status").'</span>';
            })
            ->editColumn('unit', function($cashout){
                return $cashout->worker->country->currency_code ?? 'đ';
            })
            ->editColumn('worker', function($cashout){
                return '
                <div class="info">
                    <div class="name-and-phone">
                        <div class="name">
                            <a href="'.route('admin.users.worker-detail', ['user' => $cashout->worker->id]).'">
                                <span>
                                    <b>'.$cashout->worker->name.'</b>
                                </span>
                            </a>
                        </div>
                        <div class="phone">
                            <span>
                                '.$cashout->worker->phone.'
                            </span>
                        </div>
                    </div>
                </div>';
            })
            ->editColumn('bank_info', function($cashout){
                return '
                    <p><b>'.$cashout->cardInfo->code.'</b></p>
                    <p>'.$cashout->cardInfo->fullname.'</p>
                    <p>'.$cashout->cardInfo->bank_no.'</p>
                ';
            })
            ->editColumn('created_at', function($cashout){
                return formatDateTime($cashout->created_at, 'm-d-Y H:i:s');
            })
            ->editColumn('action', function($cashout){
                return getActionCashoutHtml($cashout);
            })
            ->editColumn('status', function($cashout){
                return $cashout->status;
            })
            ->rawColumns(['id', 'balance', 'status_icon', 'amount', 'action', 'worker', 'created_at', 'unit', 'bank_info', 'waiting_status'])
            ->skipTotalRecords()
            ->toJson();
    }

    /**
     * Handle approve cashout
     */
    public function hanldeApproveCashout(Request $request)
    {
        $id = $request->cashout_id;
        $cashoutData = $this->payoutService->approveCashout($id);
        $htmlBadgeWaiting = createBadgeWaitingForCashout('cashout');

        if(isset($cashoutData) && $cashoutData['success']) {
            return $this->success(['html_badge_waiting' => $htmlBadgeWaiting, 'message' => $cashoutData['message']]);
        }

        return $this->badRequest(400, 'Bad request', $cashoutData['message']);
    }

    /**
     * Handle cancel cashout
     */
    public function handleCancelCashout(Request $request)
    {
        $this->payoutService->cancelCashout($request->cashout_id, $request->reason);
        $htmlBadgeWaiting = createBadgeWaitingForCashout('cashout');

        return $this->success(['html_badge_waiting' => $htmlBadgeWaiting]);    
    }

    /**
     * Handle add card for worker
     */
    public function workerAddCard(AddcardRequest $request)
    {
        $userID = $request->user_id;
        $bankData = explode(' - ', $request->bank_name);
        $firstCard = null;
        Card::create([
            'user_id' => $userID,
            'bank_name' => $bankData[2],
            'fullname' => Str::upper(Str::slug($request->fullname, ' ')),
            'bank_no' => $request->bank_no,
            'code' => $bankData[1],
            'img_url' => $bankData[3] ?? null,
            'bin_no' => $bankData[0]
        ]);

        $listCards = Card::where('user_id', $userID)->latest()->get();
        $htmlListCard = view('admin.partials.list-card', ['listCards' => $listCards])->render();
        if(isset($listCards) && $listCards->count()){
            $firstCard = $listCards->first()->id;
        }

        $results = [
            'html_list_card' => $htmlListCard,
            'first_card' => $firstCard
        ];

        return $this->success($results);
    }

    /**
     * Get confirm data payout
     */
    public function getConfirmPayoutData(Request $request)
    {
        $cardId = $request->card_id;
        $workerID = $request->user_id;
        $payoutMoney = str_replace('.', '', $request->payout_money);
        $card = Card::where('id', $cardId)->first();

        $contentPopupPayout = view('admin.partials.content-popup-payout', ['card' => $card, 'payoutMoney' => $payoutMoney, 'workerID' => $workerID])->render();

        return $this->success(['html_content_popup' => $contentPopupPayout]);
    }

    /**
     * Handle cash out for worker
     */
    public function cashOutForWorker(ConfirmCashoutRequest $request)
    {
        $workerID = $request->user_id_info;
        $worker = $this->userService->getUserDetailByID($workerID);
        $confirmPassword = Hash::check($request->password, $worker->password);
        if(!$confirmPassword){
            return $this->error('Mật khẩu đăng nhập không đúng', 400);
        }
        $cashOutAmount = (int)$request->cash_out_amount_info;
        $pendingBalance = CashOutLog::where('worker_id', $workerID)
            ->select(
                DB::raw('SUM(CASE WHEN status = "0" THEN cash_out_amount ELSE 0 End) AS total_pending_balance'),
            )
            ->first();
        $pendingBalance = (int)$pendingBalance->total_pending_balance ?? 0;
        $balanceTemp = (int)$worker->balance - ($pendingBalance + $cashOutAmount);

        // Create log cashout for worker
        CashOutLog::create([
            'worker_id' => $workerID,
            'card_id' => $request->card_id_info,
            'cash_out_amount' => $cashOutAmount,
            'current_balance' => (int)$worker->balance - $pendingBalance,
            'transaction_id' => Str::random(20)
        ]);

        $htmlTagsMoney = view('admin.partials.list-tags-money-default', ['balanceTemp' => $balanceTemp])->render();

        $results = [
            'html_tags_money' => $htmlTagsMoney,
            'balance_temp' => number_format($balanceTemp, 0, ',', '.'),
            'pending_balance' => number_format($pendingBalance + $cashOutAmount, 0, ',', '.')
        ];

        return $this->success($results);
    }
}
