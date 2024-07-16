<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserTokenPayment;
use App\Models\AddCardLog;
use App\Models\PaymentLog;
use App\Traits\ApiResponser;
use App\Http\Requests\Api\Payment\AddCardPaymentRequest;
use App\Http\Requests\Api\Payment\PaymentWithAtmRequest;
use App\Services\Api\OrderService;
use App\Services\Admin\CountryService;
use App\Services\Admin\UserService;
use App\Services\Admin\PaymentService;
use Exception;
use App\Exceptions\GeneralException;
use App\Models\OrderStatus;
use App\Models\RefundLog;
use App\Models\BalanceLog;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use ApiResponser;
    protected $orderService;
    protected $countryService;
    protected $userService;
    protected $paymentService;

    public function __construct(OrderService $orderService, CountryService $countryService, UserService $userService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->countryService = $countryService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
    }
    /**
     * Handle store token payment card for user
     */
    public function handleCallback(Request $request)
    {
        $resultsData = $request->all();
        $title = 'Error! please try again.';
        $iconClass = '<i class="fa fa-times icon_red" aria-hidden="true"></i>';
        if (isset($resultsData['resultCd']) && $resultsData['resultCd'] != OrderStatus::PAYMENT_SUCCESS_CODE) {
            $title = !empty($resultsData['resultMsg']) ? $resultsData['resultMsg'] : 'Error! please try again.';
            $iconClass = '<i class="fa fa-times icon_red" aria-hidden="true"></i>';

            return view('payment.add-card-success', compact('title', 'iconClass'));
        }

        return view('payment.add-card-success', compact('title', 'iconClass'));
    }

    /**
     * Handle callback payment with ATM
     */
    public function handleCallbackPaymentWithATM(Request $request)
    {
        $resultsData = $request->all();
        $alert_message = 'Order not found!';
        $paymentAgain = false;
        $resultCd = '';
        $linkPaymentWithATM = '#';
        $icon = '<i class="fa fa-check icon_green" aria-hidden="true"></i>';

        if (isset($resultsData['invoiceNo']) && isset($resultsData['userId']) && isset($resultsData['resultCd'])) {
            $resultCd = $resultsData['resultCd'];
            $alert_message = 'Payment successful';
            $userId = $resultsData['userId'];
            $invoiceNoData = explode('_', $resultsData['invoiceNo']);
            if (isset($invoiceNoData) && isset($invoiceNoData[2]) &&  isset($invoiceNoData[3])) {
                $orderId = $invoiceNoData[2];
                $order = $this->orderService->getOrderByID($orderId);
                if($order){
                    // Update tip
                    if($resultCd == OrderStatus::PAYMENT_SUCCESS_CODE){
                        $this->paymentService->handleTipDone($order);
                    }
                    $user = $this->userService->getUserDetailByID($userId);
                    $paymentType = $invoiceNoData[3];
    
                    if ($resultCd != OrderStatus::PAYMENT_SUCCESS_CODE) {
                        $alert_message = $resultsData['resultMsg'];
                        $icon = '<i class="fa fa-times icon_red" aria-hidden="true"></i>';
                    }
    
                    if (isset($order) && isset($user) && $resultCd == 'PG_ER1') {
                        $linkPaymentWithATM = $this->paymentService->getUrlPaymentWithATMFail($order, $user, $paymentType, $resultsData['amount']);
                    }
                }
            }
        }

        return view('payment.alert', compact('alert_message', 'linkPaymentWithATM', 'resultCd', 'icon'));
    }

    /**
     * API get url view add card
     */
    public function getUrlViewAddCard(AddCardPaymentRequest $request)
    {
        $user = $request->user();
        $phone = $user->phone;
        $firstName = !empty($user->first_name) ? $user->first_name : $user->name;
        $lastName = !empty($user->last_name) ? $user->last_name : $user->name;
        $email = config('constant.emailRecivePayment');
        $cardType = $request->card_type;
        if(is_null($user->nation_code) || $user->nation_code == 'vn'){
            $urlAddCard = route('admin.payment.add_card') . "?userId=$user->id&firstName=$firstName&lastName=$lastName&phone=$phone&email=$email&cardType=$cardType";
        } else {
            $urlAddCard = route('admin.payment.add_card_stripe') . "?userId=$user->id";
        }


        return $this->success(['url_addcard' => $urlAddCard]);
    }

    /**
     * Add card payment for user
     */
    public function showViewAddCard(Request $request)
    {
        $orderId = date('YmdHis');
        $userId = $request->userId;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $phone = $request->phone;
        $email = $request->email;
        $cardType = $request->cardType;
        $notiUrl = route('notify.payment_addcard');
        $amount = $cardType == 'IC' ? 2000 : 10000; // Min amount 2000
        $merId = config('constant.vnpt.mer_id');
        $endCodeKey = config('constant.vnpt.endcode_key');
        $timeStamp = date('YmdHis');
        $invoiceNo = "ADD_CARD_$timeStamp";
        $merTrxId = 'MTRXID_' . $timeStamp . '_' . rand(100, 10000);
        $str = $timeStamp . $merTrxId . $merId . $amount . $endCodeKey;
        $merchantToken = hash('sha256', $str);

        // Save request add card
        $params = [
            'merTrxId' => $merTrxId,
            'invoiceNo' => $invoiceNo,
            'merchantToken' => $merchantToken,
            'amount' => $amount,
            'userId' => $userId,
            'orderId' => $orderId,
            'payType' => $cardType,
            'timeStamp' => $timeStamp,
        ];
        AddCardLog::create([
            'access_token' => $invoiceNo,
            'order_id' => $timeStamp,
            'request_params' => json_encode($params),
            'type' => 'request_addcard',
            'request_header' => json_encode($request->header()),
        ]);

        return view('payment.form-add-card', compact('notiUrl', 'amount', 'merId', 'timeStamp', 'merTrxId', 'merchantToken', 'userId', 'firstName', 'lastName', 'phone', 'email', 'cardType', 'invoiceNo'));
    }

    /**
     *  Add card with stripe
     */
    public function showViewAddCardStripe(Request $request)
    {
        return view('payment.stripe-add-card', $request);
    }

    /**
     * Payment with stripe
     */
    public function showViewPaymentWithStripe(Request $request)
    {
        $alert_message = 'Order not found!';
        $icon = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
        $orderId = $request->orderId;
        $order = $this->orderService->getOrderByID($orderId);
        $token = $request->tok;
        $paymentType = $request->paymentType;
        if(!is_null($order) && !empty($token)){
            $amount = $this->paymentService->getPaymentAmount($order, $paymentType);
            // Check token expired
            $checkTokenExpire = $this->paymentService->checkTokenPaymentLink($token);
            if(!$checkTokenExpire){
                return view('payment.alert', ['alert_message' => 'This payment link has expired, please get a new link to proceed with payment!', 'icon' => '<i class="fa fa-clock-o icon_red" aria-hidden="true"></i>']);
            }
            // Check order has been successfully paid
            if (!is_null($order->transaction_id) && $order->payment_status && $paymentType != 'paymentTip') {
                return view('payment.alert', ['alert_message' => 'This order has been successfully paid.', 'icon' => '<i class="fa fa-check icon_green" aria-hidden="true"></i>']);
            }

            $userId = $order->user_id;
            $payToken = $order->token_payment;
            $card = UserTokenPayment::where('pay_token', $payToken)->where('user_id', $order->user_id)->first();
            
            // Handle payment
            $paymentIntentWithTripe = $this->paymentService->paymentWithStripe($card->customer_id, $card->payment_method_id, $order, $paymentType);
            if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'succeeded'){
                $alert_message = 'Payment success!';
                $icon = '<i class="fa fa-check icon_green" aria-hidden="true"></i>';

                return view('payment.alert', compact('alert_message', 'icon'));
            } else {
                if($paymentIntentWithTripe->status == 'requires_action'){
                    return redirect()->to($paymentIntentWithTripe->next_action->redirect_to_url->url);
                }

                return view('payment.stripe-payment', compact('amount', 'userId', 'orderId'));
            }

            return view('payment.stripe-payment', compact('amount', 'userId', 'orderId'));
        }

        return view('payment.alert', compact('alert_message', 'icon'));
    }

    /**
     * Handle payment with stripe
     */
    public function handlePaymentWithStripe(Request $request)
    {
        $userId = $request->user_id;
        $token = $request->stripe_token;
        $orderId = $request->order_id;
        $order = $this->orderService->getOrderByID($orderId);
        $paymentType = $request->paymentType ?? 'paymentOrder';
        if($order){
            try {
                if(is_null($userId) || empty($userId)){
                    return $this->error('Invalid params request!', 400, false);
                }
    
                Stripe::setApiKey(config('constant.stripe.key'));
                
                $paymentMethod = PaymentMethod::create([
                    'type' => 'card',
                    'card' => [
                        'token' => $token,
                    ],
                ]);
    
                $customer = Customer::create([
                    'email' => 'user_' .$userId . '@gmail.com'
                ]);
    
                if($customer) {
                    $paymentMethod->attach(['customer' => $customer->id]);
                    $paymentIntentWithTripe = $this->paymentService->paymentWithStripe($customer->id, $paymentMethod->id, $order, $paymentType);
                    
                    if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'succeeded'){
                        return $this->success($order, 'Payment success!');
                    }
                }
    
                return $this->error('Card infomation invalid!', 400);
            } catch (Exception $e) {
                // Xử lý lỗi nếu có
                Log::info($e->getMessage());
                return $this->error($e->getMessage(), 400);
            }
        }

        return $this->error('Order not found!', 400);
    }

    /**
     * API get url view payment with atm
     */
    public function getUrlViewPaymentATM(PaymentWithAtmRequest $request)
    {
        $orderId = $request->order_id;
        $order = $this->orderService->getOrderByID($orderId);
        if (isset($order)) {
            $amount = $request->amount;
            $user = $this->userService->getUserDetailByID($order->user_id);
            $phone = $user->phone;
            $firstName = !empty($user->first_name) ? $user->first_name : $user->name;
            $lastName = !empty($user->last_name) ? $user->last_name : $user->name;
            $email = config('constant.emailRecivePayment');

            $urlPayment = route('admin.payment.atm') . "?userId=$user->id&firstName=$firstName&lastName=$lastName&phone=$phone&email=$email&orderId=$orderId&amount=$amount";

            return $this->success(['url_payment_atm' => $urlPayment]);
        }

        return $this->badRequest(400, 'Order not found', 'Order id invalid!');
    }

    /**
     * Payment with ATM
     */
    public function showViewPaymentWithATM(Request $request)
    {
        $orderId = $request->orderId;
        $paymentType = $request->paymentType ?? 'paymentOrder';
        $order = $this->orderService->getOrderByID($orderId);
        $paymentAgain = $request->paymentAgain ?? null;
        $token = $request->tok;
        if (!is_null($order) && !empty($token)) {
            // Check token expired
            $checkTokenExpire = $this->paymentService->checkTokenPaymentLink($token);
            if(!$checkTokenExpire){
                return view('payment.alert', ['alert_message' => 'This payment link has expired, please get a new link to proceed with payment!', 'icon' => '<i class="fa fa-clock-o icon_red" aria-hidden="true"></i>']);
            }

            //kiem tra neu don hang thanh toan done
            if ($paymentType === 'paymentOrder' && !is_null($order->transaction_id) && $order->payment_status) {
                return view('payment.alert', ['alert_message' => 'This order has been successfully paid.']);
            }

            //kiem tra neu thanh toan cho tip da done
            if ($paymentType === 'paymentTip' && $order->tip_status) {
                return view('payment.alert', ['alert_message' => 'Tip in order has been successfully paid.']);
            }

            if ($paymentType === 'paymentOrderWithTip' && $order->payment_status && $order->tip_status) {
                return view('payment.alert', ['alert_message' => 'Order has been successfully paid.']);
            }

            $goodsNmText = "Pay order #$orderId";
            if ($paymentType === 'paymentTip') {
                $goodsNmText = "Tip for order #$orderId";
            }

            if ($paymentType === 'paymentOrderWithTip') {
                $paymentType = 'paymentOrder';
                $goodsNmText = "paymentOrderWithTip for order #$orderId";
            }

            // Data for payment with ATM
            $payToken = $order->token_payment ?? '';
            $cardUsed = $this->paymentService->getCardType($payToken, $order->user_id);
            $payType = ($cardUsed == 'ATM' ? 'DC' : 'IC');
            $goodsNm = $order->detail->service_child_name ?? $goodsNmText;
            $userId = $request->userId;
            $firstName = $request->firstName;
            $lastName = $request->lastName;
            $phone = $request->phone;
            $email = $request->email;
            $notiUrl = route('notify.payment_with_atm');
            //gia tien don hang
            $amount = $order->detail->amount;
            $merId = config('constant.vnpt.mer_id');
            $endCodeKey = config('constant.vnpt.endcode_key');
            $timeStamp = date('YmdHis');
            $merTrxId = 'MTRXID_' . $timeStamp . '_' . rand(100, 10000);
            if (is_null($paymentAgain) || empty($paymentAgain)) {
                $decryptedPayToken = decrypt3DES($payToken);
                $encryptedPayToken = encrypt3DES($decryptedPayToken);
                $plainTxtToken = $timeStamp . $merTrxId . $merId . $amount . $encryptedPayToken . $endCodeKey;
                $merchantToken = hash('sha256', $plainTxtToken);
                $payOption = 'PAY_WITH_TOKEN';
            } else {
                $str = $timeStamp . $merTrxId . $merId . $amount . $endCodeKey;
                $merchantToken = hash('sha256', $str);
                $encryptedPayToken = '';
                $payOption = 'PAY_CREATE_TOKEN';
            }
            $invoiceNo = 'ORDER_' . $timeStamp . '_' . $orderId . '_' . $paymentType;

            return view('payment.form-payment-atm', compact('notiUrl', 'amount', 'orderId', 'goodsNm', 'merId', 'timeStamp', 'merTrxId', 'merchantToken', 'userId', 'firstName', 'lastName', 'phone', 'email', 'encryptedPayToken', 'invoiceNo', 'payType', 'payOption'));
        }

        return view('payment.alert', ['alert_message' => 'Order not found!']);
    }

    /**
     * Handle notify for add card logs
     */
    public function handleNotifyAddCard(Request $request)
    {
        $results = $request->all();

        if (isset($results['invoiceNo'])) {
            $check = AddCardLog::where('access_token', $results['invoiceNo'])->where('type', 'response_addcard')->exists();
            if (!$check) {
                return AddCardLog::create([
                    'access_token' => $results['invoiceNo'],
                    'order_id' => $results['timeStamp'],
                    'response' => json_encode($results),
                    'type' => 'response_addcard',
                    'amount' => $results['amount']
                ]);
            }

            return true;
        }

        return $this->error(!empty($results['resultMsg']) ? $results['resultMsg'] : 'Notify add card', 400);
    }

    /**
     * Verify merchantToken
     *
     * @param array $data
     *
     * @return bool
     */
    public function checkToken(array $data = [])
    {
        $resultCd = $data['resultCd'] ?? '';
        $timeStamp = $data['timeStamp'] ?? '';
        $merTrxId = $data['merTrxId'] ?? '';
        $trxId = $data['trxId'] ?? '';
        $amount = $data['amount'] ?? '';
        $merId = config('constant.vnpt.mer_id');
        $endCodeKey = config('constant.vnpt.endcode_key');

        if (array_key_exists("payToken", $data)) {
            $str = $resultCd . $timeStamp . $merTrxId . $trxId . $merId . $amount . $data['payToken'] . $endCodeKey;
        } else {
            $str = $resultCd . $timeStamp . $merTrxId . $trxId . $merId . $amount . $endCodeKey;
        }

        $token = hash('sha256', $str);

        $tokenResponse = $data['merchantToken'] ?? '';

        return $token != $tokenResponse ? false : true;
    }

    /**
     * Add card
     *
     * @param array $data
     *
     * @return bool
     */
    public function addCard(array $data = [])
    {
        return UserTokenPayment::create([
            'user_id' => $data['userId'],
            'pay_token' => $data['payToken'],
            'bank_name' => $data['bankName'] ?? null,
            'card_no' => $data['cardNo'] ?? null,
            'bank_type' => $data['cardType'] ?? null,
            'last_used_date' => now()->toDateTimeString(),
            'payment_3rd' => $data['payment_3rd'] ?? 'vnpt',
            'customer_id' => $data['customer_id'] ?? null,
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'card_brand' => $data['cardBrand'] ?? null
        ]);
    }

    /**
     * Hanlde notify payment success or fail
     */
    public function handleNotifyPaymentResult(Request $request)
    {
        // Coding...
        return true;
    }

    /**
     * Handle add card with stripe
     *
     * @return boolean
     */
    public function hanldeAddCardStripe(Request $request)
    {
        $userId = $request->user_id;
        $token = $request->stripe_token;
        try {
            if(is_null($userId) || empty($userId)){
                return $this->error('Invalid params request!', 400, false);
            }

            Stripe::setApiKey(config('constant.stripe.key'));
            
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $token,
                ],
            ]);

            $cardNumber = '************' .$paymentMethod->card->last4;
            $cardBrand = $paymentMethod->card->brand;

            $customer = Customer::create([
                'email' => 'user_' .$userId . '@gmail.com'
            ]);

            if($customer) {
                $paymentMethod->attach(['customer' => $customer->id]);
                $cardExist = $this->userService->checkStripeCardExist([
                    'user_id' => $userId,
                    'card_number' => $cardNumber,
                    'card_brand' => $cardBrand
                ]);
                if($cardExist) {
                    return $this->error('This card already exists!', 400);
                }
                // Add card stripe
                $this->addCard([
                    'userId' => $userId,
                    'payToken' => 'stripe_token_' . $paymentMethod->id,
                    'payment_3rd' => 'stripe',
                    'customer_id' => $customer->id,
                    'payment_method_id' => $paymentMethod->id,
                    'cardType' => '001',
                    'cardNo' => $cardNumber,
                    'cardBrand' => $cardBrand
                ]);

                return $this->success(true, 'Add card successful!');
            }

            return $this->error('Card infomation invalid!', 400);
        } catch (Exception $e) {
            // Xử lý lỗi nếu có
            Log::info($e->getMessage());
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Handle verify notify add card
     */
    public function verifyNotifyAddCard(Request $request)
    {
        $userId = $request->userId;
        $invoiceNo = $request->invoiceNo;
        $payToken = $request->payToken;
        $merId = $request->merId;
        $bankName = $request->bankName;
        $cardNo = $request->cardNo;
        $resultCd = $request->resultCd;
        $payType = $request->payType;
        $cardType = $request->cardType;
        $trxId = $request->trxId;
        $merTrxId = $request->merTrxId;
        $amount = $request->amount;
        $timeStamp = $request->timeStamp;

        $checkAddCard = AddCardLog::where('access_token', $invoiceNo)
            ->where('type', 'response_addcard')
            ->latest('id')->first();

        if (!is_null($checkAddCard)) {
            $checkAddCard->update([
                'user_id' => $userId,
                'status' => 1
            ]);

            // Handle refund for user
            try {
                $endCodeKey = config('constant.vnpt.endcode_key');
                $str = $timeStamp . $merTrxId . $trxId . $merId . $amount . $endCodeKey;
                $merchantToken = hash('sha256', $str);
                $cancelPw = urlencode(config('constant.vnpt.cancel_pw'));

                $requestUri = config('constant.vnpt.refund_url') . "?trxId=$trxId&merId=$merId&merTrxId=$merTrxId&amount=$amount&payType=$payType&cancelMsg=Refund money for user when add card payment&timeStamp=$timeStamp&merchantToken=$merchantToken&cancelPw=$cancelPw";

                $responseRefund = callGuzzleHttp('POST', $requestUri);

                // Save refund logs
                RefundLog::create([
                    'user_id' => $userId,
                    'status' => isset($responseRefund['resultCd']) && $responseRefund['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE ? 1 : 0,
                    'amount' => $amount,
                    'request' => $requestUri,
                    'response' => json_encode($responseRefund)
                ]);
            } catch (Exception $e) {
                throw new GeneralException(__('There was a problem when refund money for user. Please try again.', $e->getCode()));
            }

            $title = 'Add card successful.<br/>Thank you.';
            $iconClass = '<i class="fa fa-check icon_green" aria-hidden="true"></i>';

            // Handle add card
            $cardExist = UserTokenPayment::where('card_no', $cardNo)
                ->where('user_id', $userId)
                ->first();

            if (!is_null($cardExist)) {
                $cardExist->update([
                    'payToken' => $payToken
                ]);
            } else {
                if(!is_null($cardType) && !empty($cardType)){
                    $cardBrand = config('constant.card_type.' . $cardType);
                } else {
                    $cardBrand = 'ATM';
                }
                $this->addCard([
                    'userId' => $userId,
                    'payType' => $payType,
                    'payToken' => $payToken,
                    'bankName' => $bankName,
                    'cardNo' => $cardNo,
                    'cardType' => $cardType,
                    'cardBrand' => $cardBrand
                ]);
            }
        } else {
            if ($resultCd == OrderStatus::PAYMENT_SUCCESS_CODE) {
                $title = 'Add card successful.';
                $iconClass = '<i class="fa fa-check icon_green" aria-hidden="true"></i>';
            }
        }

        $htmlResultAddCard = view('payment.result', compact('title', 'iconClass'))->render();

        return $this->success(['html_result' => $htmlResultAddCard, 'status' => true]);
    }

    /**
     * Save payment logs
     *
     * @param array $data
     * @param mixed $order
     *
     * @return mixed
     */
    public function savePaymentLogs($order, array $data = [])
    {
        $order->update([
            'transaction_id' => $data['transaction_id']
        ]);

        // Save logs payment
        return PaymentLog::create([
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'worker_id' => $data['worker_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'cash_in',
            'amount' => $order->detail->amount,
            'nation_code' => $order->nation_code,
            'card_type' => 'DC',
            'status' => (isset($data['response_payment']['resultCd']) && $data['response_payment']['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE) ? PaymentLog::PAYMENT_DONE : PaymentLog::PAYMENT_FAILED,
            'response_payment' => json_encode($data['response_payment']),
            'request_payment' => json_encode($data['request_payment'])
        ]);
    }

    /**
     * show list payment
     */
    public function showListPayment()
    {
        $route_refresh = 'admin.payment';
        $countries = $this->countryService->listCountry();

        return view('admin.pages.payment', compact('countries', 'route_refresh'));
    }

    /**
     * Get list worker name for select2
     */
    public function getListWorkerName(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $workers = $this->userService->getListWorkerName($term);
            $morePages = true;
            if (empty($workers->nextPageUrl())) {
                $morePages = false;
            }

            $results = [
                'results' => $workers->items(),
                'pagination' => [
                    'more' => $morePages
                ]
            ];

            return response()->json($results);
        }
    }

    /**
     * Get list payment data with filter
     */
    public function getListPaymentWithFilter(Request $request)
    {
        $data = $this->paymentService->getListPaymentWithFilter($request->all());

        return $this->createJsonDatatable($data);
    }

    /**
     * Create json datatable payment
     *
     * @param mixed $data
     *
     * @return json
     */
    public function createJsonDatatable($data)
    {
        return datatables()->eloquent($data)
            ->editColumn('transaction_id', function ($payment) {
                return $payment->transaction_id;
            })
            ->editColumn('cash_type', function ($payment) {
                return $payment->type;
            })
            ->editColumn('card_type', function ($payment) {
                return $payment->card_type;
            })
            ->editColumn('status', function ($payment) {
                $class = $payment->type == 'cash_in' ? getClassPaymentStatus($payment->status) : getClassCashoutStatus($payment->status);
                $title = $payment->type == 'cash_in' ? config('constant.vnpt.status.' . $payment->status) : config('constant.cashout_status.' . $payment->status);
                
                return '<span class="badge ' . $class . '">' . $title . '</span>';
            })
            ->editColumn('order_id', function ($payment) {
                return $payment->order_id;
            })
            ->editColumn('amount', function ($payment) {
                return formatCurrency($payment->amount, $payment->nation_code);
            })
            ->editColumn('worker', function ($payment) {
                return '
                <div class="info">
                    <div class="name-and-phone">
                        <div class="name">
                            <a href="' . route('admin.users.worker-detail', ['user' => $payment->worker->id]) . '">
                                <span>
                                    <b>' . $payment->worker->name . '</b>
                                </span>
                            </a>
                        </div>
                        <div class="phone">
                            <span>
                                ' . $payment->worker->phone . '
                            </span>
                        </div>
                    </div>
                </div>';
            })
            ->editColumn('created_at', function ($payment) {
                return formatDateTime($payment->created_at, 'm-d-Y');
            })
            ->editColumn('action', function ($payment) {
                $url = !empty($payment->order_id) ? route("admin.orders.detail", [$payment->order_id]) : '#';
                return
                    '<a href="' . $url . '" class="dropdown-item btn btn-danger btn-sm" data-id="' . $payment->id . '">
                    <i class="fas fa-eye"></i>
                </a>';
            })
            ->rawColumns(['transaction_id', 'cash_type', 'card_type', 'status', 'order_id', 'amount', 'action', 'worker', 'created_at'])
            ->skipTotalRecords()
            ->toJson();
    }

    /**
     * Get total cash in cash out
     */
    public function getTotalCashInCashOut(Request $request)
    {
        $totalCashData = $this->paymentService->getTotalCashInCashOut($request->only('nation_code', 'type', 'card_type', 'status', 'transactionId_orderId', 'from_date', 'to_date', 'worker_id'));
        $results = [
            'totalCashIn' => formatCurrency($totalCashData->total_cash_in, $request->nation_code),
            'totalCashOut' => formatCurrency($totalCashData->total_cash_out, $request->nation_code),
        ];

        return $this->success($results);
    }

    /**
     * Alert payment
     */
    public function showAlert(Request $request)
    {
        return view('payment.alert');
    }

    /**
     * Handle tip done
     */
    public function handlePaymentDoneWith3DS(Request $request)
    {
        $handle = $this->paymentService->handlePaymentDoneWith3DS($request->order_id, $request->payment_intent_id, $request->request_payment_data);

        return $this->success($handle);
    }
}



