<?php

namespace App\Services\Admin;

use App\Services\BaseService;
use App\Models\PaymentLog;
use App\Models\TokenPaymentExpiration;
use App\Models\UserTokenPayment;
use App\Models\BalanceLog;
use App\Models\OrderStatus;
use App\Services\Api\UserService;
use App\Services\Admin\OrderService;
use Stripe\Exception\CardException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Illuminate\Support\Str;

/**
 * Class UserService.
 */
class PaymentService extends BaseService
{
    protected $userService;
    protected $orderService;
    /**
     * PaymentService constructor.
     *
     * @param  PaymentLog  $payment
     */
    public function __construct(PaymentLog $payment, UserService $userService, OrderService $orderService)
    {
        $this->model = $payment;
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    /**
     * Get list payments with filter
     *
     * @param array $params
     *
     * @return object
     */
    public function getListPaymentWithFilter(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->with(['worker' => function ($query) {
                $query->select('id', 'phone', 'name');
            }])
            ->select('id', 'order_id', 'worker_id', 'transaction_id', 'card_type', 'type', 'status', 'amount', 'created_at')
            ->latest();
    }

    /**
     * Get total cash in cash out
     *
     * @param array $params
     *
     * @return object
     */
    public function getTotalCashInCashOut(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->select(
                DB::raw('SUM(CASE WHEN type = "cash_in" AND status = "1" THEN amount ELSE 0 End) AS total_cash_in'),
                DB::raw('SUM(CASE WHEN type = "cash_out" AND status = "1" THEN amount ELSE 0 End) AS total_cash_out')
            )
            ->first();
    }

    /**
     * Get url payment with ATM
     *
     * @param mixed $order
     * @param mixed $user
     * @param mixed $amountTip
     *
     * @return string
     */
    public function getUrlPaymentWithATM($order, $user, $amountTip = 0)
    {
        $paymentType = !empty($amountTip) && $amountTip != 0 ? 'paymentTip' : 'paymentOrder';
        $orderId = $order->id;
        $amount = !empty($amountTip) && $amountTip != 0 ? $amountTip : $order->detail->amount;
        $phone = $user->phone;
        $firstName = !empty($user->first_name) ? $user->first_name : $user->name;
        $lastName = !empty($user->last_name) ? $user->last_name : $user->name;
        $email = config('constant.emailRecivePayment');
        $token = Str::random(30) . date('YmdHis');
        $paymentLink = route('admin.payment.atm') . "?userId=$user->id&firstName=$firstName&lastName=$lastName&phone=$phone&email=$email&orderId=$orderId&amount=$amount&paymentType=$paymentType&tok=$token";
        // Save token for payment link
        $this->saveTokenPaymentLink($token, $paymentLink);

        return $paymentLink;
    }

    //GET PAYMENT LINK BY VISA AND ATM
    function getUrlPayment($order, $user, $paymentType = 'paymentOrder')
    {
        $orderId = $order->id;
        $amount = $this->getPaymentAmount($order, $paymentType);
        $phone = $user->phone;
        $firstName = !empty($user->first_name) ? $user->first_name : $user->name;
        $lastName = !empty($user->last_name) ? $user->last_name : $user->name;
        $email = config('constant.emailRecivePayment');
        $token = Str::random(30) . date('YmdHis');
        $paymentLink = route('admin.payment.atm') . "?userId=$user->id&firstName=$firstName&lastName=$lastName&phone=$phone&email=$email&orderId=$orderId&amount=$amount&paymentType=$paymentType&tok=$token";
        // Save token for payment link
        $this->saveTokenPaymentLink($token, $paymentLink);

        return $paymentLink;
    }

    /**
     * Get url payment with ATM fail
     *
     * @param mixed $order
     * @param mixed $user
     * @param string $paymentType
     * @param int $amount
     *
     * @return string
     */
    public function getUrlPaymentWithATMFail($order, $user, string $paymentType = '', int $amount)
    {
        $orderId = $order->id;
        $phone = $user->phone;
        $firstName = !empty($user->first_name) ? $user->first_name : $user->name;
        $lastName = !empty($user->last_name) ? $user->last_name : $user->name;
        $email = config('constant.emailRecivePayment');
        $token = Str::random(30) . date('YmdHis');
        $paymentLink = route('admin.payment.atm') . "?userId=$user->id&firstName=$firstName&lastName=$lastName&phone=$phone&email=$email&orderId=$orderId&amount=$amount&paymentType=$paymentType&tok=$token&paymentAgain=";
        // Save token for payment link
        $this->saveTokenPaymentLink($token, $paymentLink);

        return $paymentLink;
    }

    /**
     * Get card type
     *
     * @param string $payToken
     * @param int $userId
     *
     * @return string
     */
    public function getCardType(string $payToken, int $userId)
    {
        $card = UserTokenPayment::where('pay_token', $payToken)
            ->where('user_id', $userId)
            ->first();
        
        return $card->payment_3rd == 'stripe' ? 'payment_with_stripe' : $card->value('bank_type');
    }

    /**
     * Verify payToken has time to use
     *
     * @param string $payToken
     * @param int $userId
     *
     * @return bool
     */
    public function verifyPayToken(string $payToken, int $userId)
    {
        $lastUsedDate = UserTokenPayment::where('pay_token', $payToken)->where('user_id', $userId)->value('last_used_date');        

        if (!empty($lastUsedDate) && is_string($lastUsedDate)) {
            // $date = Carbon::parse($lastUsedDate);
            // $now = Carbon::now();
            // $days = $date->diffInDays($now);

            // return $days <= 180 ? true : false;
            return true;
        }

        return false;
    }

    /**
     * Update last used date card
     *
     * @param string $payToken
     * @param int $userId
     *
     * @return mixed
     */
    public function updateLastUsedDateCard(string $payToken, int $userId)
    {
        return UserTokenPayment::where('pay_token', $payToken)
            ->where('user_id', $userId)
            ->update([
                'last_used_date' => now()->toDateTimeString()
            ]);
    }

    /**
     * Payment with stripe
     * 
     * @param string $customerId
     * @param string $paymentMethodId
     * @param mixed $order
     * 
     * @return object intent
     */
    public function paymentWithStripe(string $customerId, string $paymentMethodId, $order, $paymentType = 'paymentOrder')
    {
        $amount = $this->getPaymentAmount($order, $paymentType);
        Stripe::setApiKey(config('constant.stripe.key'));

        try {
            // Update last use date card
            $this->updateLastUsedDateCard($order->token_payment, $order->user_id);
            $dataRequestPayment = [
                // 'amount' => $amount * 100,
                'amount' => 50, // min amount for testing in production
                'currency' => 'usd',
                'customer' => $customerId,
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'off_session' => true,
            ];
            $paymentIntentResponse = PaymentIntent::create($dataRequestPayment);

            $transactionId = Str::random(20);

            if(isset($paymentIntentResponse) && $paymentIntentResponse->status == 'succeeded'){
                $order->update([
                    'transaction_id' => $transactionId,
                    'payment_status' => 1
                ]);
                $this->handleTipDone($order);
            }
            // Save payment logs
            $dataLogs = [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'worker_id' => $order->worker_id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'card_type' => 'IC',
                'status' => (isset($paymentIntentResponse) && $paymentIntentResponse->status == 'succeeded') ? PaymentLog::PAYMENT_DONE : PaymentLog::PAYMENT_FAILED,
                'response_payment' => json_encode($paymentIntentResponse),
                'request_payment' => json_encode($dataRequestPayment)
            ];
            $this->savePaymentLogs($order, $dataLogs);

            return $paymentIntentResponse;
        } catch (CardException $e) {
            // Handle payment fail
            $body = $e->getJsonBody();
            if(isset($body['error']) && $body['error']['code'] == 'authentication_required'){
                Log::info('Payment stripe require action 3DS with order #' . $order->id . ' - message : ' . $e->getMessage());
                $dataRequestPayment = [
                    // 'amount' => $amount * 100,
                    'amount' => 50, // min amount for testing in production
                    'currency' => 'usd',
                    'customer' => $customerId,
                    'payment_method' => $paymentMethodId,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                ];
                $dataRequestPaymentEncode = json_encode($dataRequestPayment);
                $paymentIntentResponse = PaymentIntent::create($dataRequestPayment);
                $stripe = new StripeClient(config('constant.stripe.key'));
                $paymentIntentConfirm = $stripe->paymentIntents->confirm(
                    $paymentIntentResponse->id,
                    ['return_url' => route('admin.payment.alert') . "?order_id=$order->id&confirm_payment=succeeded&request_payment_data=$dataRequestPaymentEncode"]
                );
                
                return $paymentIntentConfirm;
            }

            return false;
        }
    }

    /**
     * Get link payment with stripe
     * 
     * @param array $data
     * 
     * @return string uri
     */
    public function getUrlPaymentWithStripe(array $data = [], $paymentType = 'paymentOrder')
    {
        $orderId = $data['order_id'];
        $userId = $data['user_id'];
        $token = Str::random(30) . date('YmdHis');
        $paymentLink = route('admin.payment.stripe.view') . "?orderId=$orderId&userId=$userId&paymentType=$paymentType&tok=$token";
        // Save token for payment link
        $this->saveTokenPaymentLink($token, $paymentLink);

        return $paymentLink;
    }

    /**
     * Token for payment link
     * 
     * @param string $token
     * @param string $paymentLink
     * 
     * @return mixed
     */
    public function saveTokenPaymentLink($token, $paymentLink)
    {
        return TokenPaymentExpiration::create([
            'token' => $token,
            'payment_link' => $paymentLink,
            'expired_at' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    /**
     * Check token payment link
     * 
     * @param string $token
     * 
     * @return boolean
     */
    public function checkTokenPaymentLink(string $token)
    {
        return TokenPaymentExpiration::query()
            ->where('token', $token)
            ->where('expired_at', '>=', now()->toDateTimeString())
            ->exists();
    }

    /**
     * Handle save payment logs
     *
     * @param array $data
     * @param mixed $order
     *
     * @return mixed
     */
    public function savePaymentLogs($order, array $data = [])
    {
        return PaymentLog::create([
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'worker_id' => $data['worker_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'cash_in',
            'amount' => $data['amount'] ?? $order->detail->amount,
            'nation_code' => $order->nation_code,
            'card_type' => $data['card_type'],
            'status' => $data['status'],
            'response_payment' => json_encode($data['response_payment']) ?? null,
            'request_payment' => json_encode($data['request_payment']) ?? null
        ]);
    }

    /**
     * Handle tip done
     * 
     * @param mixed $order
     * 
     * @return mixed
     */
    public function handleTipDone($order)
    {
        // Handle tip done
        $orderCurrent = $this->orderService->getOrderByID($order->id);
        if ($orderCurrent && $orderCurrent->tip_status == 0 && !empty($orderCurrent->detail->amount_tip)){
            $amountTip = $orderCurrent->detail->amount_tip;
            $worker = $this->userService->getUserByID($orderCurrent->worker_id);
            $orderCurrent->update([
                'tip_status' => 1
            ]);

            $orderCurrent->detail()->update([
                'amount' => $orderCurrent->detail->amount + $amountTip,
            ]);

            //cong tien tip vao blance worker
            $worker->update([
                'balance' => DB::raw("balance + $amountTip")
            ]);

            // // Logs balance for worker
            return BalanceLog::create([
                'user_id' => $orderCurrent->worker_id,
                'order_id' => $orderCurrent->id,
                'amount' => $amountTip,
                'type' => 'cash_in',
                'description' => "Tip order #" . $orderCurrent->id . " - balance (+$amountTip)"
            ]);
        }

        return false;
    }

    /**
     * Handle payment order done with 3DS
     * 
     * @param int $orderId
     * @param string $paymentIntentId
     * @param mixed $requestPaymentData
     * 
     * @return mixed
     */
    public function handlePaymentDoneWith3DS(int $orderId, string $paymentIntentId, $requestPaymentData)
    {
        $order = $this->orderService->getOrderByID($orderId);
        if($order){
            if(is_null($order->transaction_id) && $order->order_status == OrderStatus::DONE && !$order->payment_status){
                $transactionId = Str::random(20);
                $order->update([
                    'transaction_id' => $transactionId,
                    'payment_status' => 1
                ]);
                // Save payment logs
                $paymentIntentResponse = $this->getPaymentIntentResponse($paymentIntentId);
                $dataLogs = [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'worker_id' => $order->worker_id,
                    'transaction_id' => $transactionId,
                    'amount' => $order->detail->amount,
                    'card_type' => 'IC',
                    'status' =>  PaymentLog::PAYMENT_DONE,
                    'response_payment' => json_encode($paymentIntentResponse),
                    'request_payment' => $requestPaymentData
                ];
                $this->savePaymentLogs($order, $dataLogs);
            }
    
            return $this->handleTipDone($order);
        }

        return false;
    }

    /**
     * Get payment intent response
     * 
     * @param string $paymentIntentId
     * 
     * @return object
     */
    public function getPaymentIntentResponse(string $paymentIntentId)
    {
        return  callGuzzleHttp('GET', 'https://api.stripe.com/v1/payment_intents/' . $paymentIntentId, [], [
            'Authorization' => 'Bearer ' . config('constant.stripe.key')
        ]);
    }

    /**
     * Handle get amount for payment
     * 
     * @param mixed $order
     * @param string $paymentType
     * 
     * @return int
     */
    public function getPaymentAmount($order, $paymentType)
    {
        $amount = $order->detail->amount;
        
        switch ($paymentType) {
            case 'paymentTip':
                $amount = $order->detail->amount_tip;
                break;
            case 'paymentOrderWithTip':
                $amount = $amount + $order->detail->amount_tip;
                break;
            default:
                # code...
                break;
        }

        return $amount;
    }
}