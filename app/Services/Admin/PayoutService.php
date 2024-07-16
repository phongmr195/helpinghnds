<?php

namespace App\Services\Admin;

use App\Models\Card;
use App\Services\BaseService;
use App\Models\CashOutLog;
use App\Models\PaymentLog;
use App\Models\User;
use App\Models\BalanceLog;
use Illuminate\Support\Facades\DB;
use phpseclib\Crypt\RSA;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class PayoutService.
 */
class PayoutService extends BaseService
{
    /**
     * PayoutService constructor.
     *
     * @param  CashOutLog  $cashOut
     */
    public function __construct(CashOutLog $cashOut)
    {
        $this->model = $cashOut;
    }

    /**
     * Get list cashout with filter
     *
     * @param array $params
     *
     * @return object
     */
    public function getListCashoutWithFilter(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->with(['cardInfo' => function($query){
                $query->select('id', 'bank_name', 'fullname', 'bank_no', 'code', 'img_url');
            }])
            ->with(['worker' => function($query){
                $query->with(['country' => function($sq){
                    $sq->select('id', 'alt', 'currency_code');
                }])->select('id', 'phone', 'name', 'nation_code');
            }])
            ->select('id', 'card_id', 'worker_id', 'cash_out_amount', 'current_balance', 'status', 'created_at')
            ->latest();
    }

    /**
     * Approve cashout
     *
     * @param int $id
     *
     * @return boolean
     */
    public function approveCashout(int $id)
    {
        $cashOut = $this->model::query()->with('worker')->where('id', $id)->first();
        if(isset($cashOut) && in_array($cashOut->status, [CashOutLog::WAITING, CashOutLog::FAIL])){
            $cardDetail = $this->getCardDetail($cashOut->card_id);
            // if(!is_null($cardDetail)){
            //     $amount = $cashOut->cash_out_amount;
            //     $bankNo = $cardDetail->bin_no;
            //     $accNo = $cardDetail->bank_no;
            //     $accType = '0';
            //     $operation = config('constant.vnpt.operation');
            //     $requestId = 'RID_'.date('YmdHis',time()).'_'.rand(0,99999);
            //     $timeRequest = date('Y-m-d H:i:s',time());
            //     $partnerCode = config('constant.vnpt.partner_code');
            //     $referenceId = $partnerCode.date('YmdHis',time()).rand(0,99999);
            //     $memo = 'Transfer money for worker';
            //     $rsa = new RSA();
            //     $rsa->loadKey(file_get_contents(storage_path('rsa-keys/private.pem')));
            //     $plaintext = $requestId.'|'.$timeRequest.'|'.$partnerCode.'|'.$operation.'|'.$referenceId.'|'.$bankNo.'|'.$accNo.'|'.$accType.'|'.$amount.'|'.$memo;
            //     $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
            //     $signature = $rsa->sign($plaintext);
            //     $signature = base64_encode($signature);
            //     $requestData = [
            //         'form_params' => [
            //             'RequestId' => $requestId,
            //             'RequestTime' => $timeRequest,
            //             'PartnerCode' => $partnerCode,
            //             'Operation' => $operation,
            //             'BankNo' => $bankNo,
            //             'AccNo' => $accNo,
            //             'AccountName' => Str::upper($cardDetail->fullname),
            //             'AccType' => $accType,
            //             'ReferenceId' => $referenceId,
            //             'RequestAmount' => $amount,
            //             'Memo' => $memo,
            //             'Signature' => $signature
            //         ]
            //     ];

            //     $response = callGuzzleHttp('POST', config('constant.vnpt.url_transfer_money'), $requestData);

            //     try {
            //         if(isset($response) && $response['ResponseCode'] == 200){
            //             $cashOut->update([
            //                 'status' => CashOutLog::APPROVE
            //             ]);
            
            //             // Save payment logs cashout
            //             $this->savePaymentLogCashout($cashOut, CashOutLog::APPROVE, $requestData, $response);
            //             User::where('id', $cashOut->worker_id)->update([
            //                 'balance' => DB::raw("balance - $cashOut->cash_out_amount")
            //             ]);
            //             BalanceLog::create([
            //                 'user_id' => $cashOut->worker_id,
            //                 'amount' => $cashOut->cash_out_amount,
            //                 'type' => 'cash_out',
            //                 'description' => "Tranfser money for worker - balance (-$cashOut->cash_out_amount)"
            //             ]);

            //             return [
            //                 'success' => true,
            //                 'message' => 'Transfer money for worker successful'
            //             ];
            //         } else {
            //             $errorMessage = $response['ResponseMessage'];
            //             // Sxave payment logs cashout
            //             $cashOut->update([
            //                 'status' => CashOutLog::FAIL,
            //                 'reason' => $errorMessage
            //             ]);
            //             $this->savePaymentLogCashout($cashOut, CashOutLog::FAIL, $requestData, $response);

            //             return [
            //                 'success' => false,
            //                 'message' => $errorMessage
            //             ];
            //         }
            //     } catch (Exception $e) {
            //         Log::error('Transfer money for worker error - ' . $e->getMessage());

            //         return [
            //             'success' => false,
            //             'message' => $e->getMessage()
            //         ];
            //     }
            if(!is_null($cardDetail)){
                $amount = $cashOut->cash_out_amount;
                $bankNo = $cardDetail->bin_no;
                $accNo = $cardDetail->bank_no;
                $accType = '0';
                $operation = config('constant.vnpt.operation');
                $requestId = 'RID_'.date('YmdHis',time()).'_'.rand(0,99999);
                $timeRequest = date('Y-m-d H:i:s',time());
                $partnerCode = config('constant.vnpt.partner_code');
                $referenceId = $partnerCode.date('YmdHis',time()).rand(0,99999);
                $memo = 'AssistInc chuyen rut tien theo lenh ' . str_pad($id,6,'0',STR_PAD_LEFT);
                $rsa = new RSA();
                $rsa->loadKey(file_get_contents(storage_path('rsa-keys/private.pem')));
                $plaintext = $requestId.'|'.$timeRequest.'|'.$partnerCode.'|'.$operation.'|'.$referenceId.'|'.$bankNo.'|'.$accNo.'|'.$accType.'|'.$amount.'|'.$memo;
                $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
                $signature = $rsa->sign($plaintext);
                $signature = base64_encode($signature);
                $dataRequest = [
                    'RequestId' => $requestId,
                    'RequestTime' => $timeRequest,
                    'PartnerCode' => $partnerCode,
                    'Operation' => $operation,
                    'BankNo' => $bankNo,
                    'AccNo' => $accNo,
                    'AccountName' => $cardDetail->fullname,
                    'AccType' => $accType,
                    'ReferenceId' => $referenceId,
                    'RequestAmount' => $amount,
                    'Memo' => $memo,
                    'Signature' => $signature
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('constant.vnpt.url_transfer_money'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataRequest);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30); 
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT,30);

                try {
                    $response = curl_exec($ch);
                    $curlErrno = curl_errno($ch);
                    $curlError = curl_error($ch);
                    
                    if($response === false || $curlErrno > 0 || $curlError){
                        if($curlErrno > 0){
                            $errorMessage = $curlErrno['ResponseMessage'];
                            // Sxave payment logs cashout
                            $cashOut->update([
                                'status' => CashOutLog::FAIL,
                                'reason' => $errorMessage
                            ]);
                            $this->savePaymentLogCashout($cashOut, CashOutLog::FAIL);

                            return [
                                'success' => false,
                                'message' => $errorMessage
                            ];
                        }
                    } else {
                        $response = json_decode($response, true);
                        if(isset($response) && $response['ResponseCode'] == 200){
                            $cashOut->update([
                                'status' => CashOutLog::APPROVE
                            ]);
                
                            // Save payment logs cashout
                            $this->savePaymentLogCashout($cashOut, CashOutLog::APPROVE);
                            User::where('id', $cashOut->worker_id)->update([
                                'balance' => DB::raw("balance - $cashOut->cash_out_amount")
                            ]);
    
                            return [
                                'success' => true,
                                'message' => 'Transfer money for worker successful'
                            ];
                        } else {
                            $errorMessage = $response['ResponseMessage'];
                            // Sxave payment logs cashout
                            $cashOut->update([
                                'status' => CashOutLog::FAIL,
                                'reason' => $errorMessage
                            ]);
                            $this->savePaymentLogCashout($cashOut, CashOutLog::FAIL);

                            return [
                                'success' => false,
                                'message' => $errorMessage
                            ];
                        }
                    }
                    curl_close($ch);
                } catch (Exception $e) {
                    Log::error('Transfer money for worker error - ' . $e->getMessage());

                    return [
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Card info invalid'
            ];
        }

        return [
            'success' => false,
            'message' => 'Bad request'
        ];
    }

    /**
     * Cancel cashout
     *
     * @param int $id
     * @param string $reason
     *
     * @return boolean
     */
    public function cancelCashout(int $id, string $reason)
    {
        $cashOut = $this->model::query()
            ->with('worker')
            ->where('id', $id)->first();

        if(isset($cashOut) && in_array($cashOut->status, [CashOutLog::WAITING, CashOutLog::FAIL])){
            // Save payment logs cashout
            $this->savePaymentLogCashout($cashOut, CashOutLog::CANCEL);
    
            return $cashOut->update([
                'status' => CashOutLog::CANCEL,
                'reason' => $reason
            ]);
        }

        return false;
    }

    /**
     * Save payment logs cashout
     *
     * @param mixed $cashOut
     * @param int $status
     * @param mixed $requestData
     * @param mixed $response
     *
     * @return boolean
     */
    public function savePaymentLogCashout($cashOut, int $status, $requestData = null, $response = null)
    {
        return PaymentLog::updateOrInsert(
            [
                'transaction_id' => $cashOut->transaction_id
            ],
            [
                'worker_id' => $cashOut->worker_id,
                'type' => 'cash_out',
                'amount' => $cashOut->cash_out_amount,
                'nation_code' => $cashOut->worker->nation_code ?? 'vn',
                'card_type' => 'DC',
                'status' => $status,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'request_payment' => json_encode($requestData) ?? null,
                'response_payment' =>json_encode($response) ?? null
            ]
        );
    }

    /**
     * Get card detail
     *
     * @param int $cardId
     *
     * @return object card detail
     */
    public function getCardDetail(int $cardId)
    {
        return Card::query()->where('id', $cardId)->first();
    }
}
