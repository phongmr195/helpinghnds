<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Services\BaseService;
use App\Services\Admin\PaymentService;
use App\Exceptions\GeneralException;
use App\Models\OrderLog;
use App\Models\OrderStatus;
use App\Models\PaymentLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService extends BaseService
{

    //La cac trang thai trong luc lam viec
    private $isInStatusWorking = array(
        OrderStatus::WORKING,
        OrderStatus::WORKER_ACCEPTED,
        OrderStatus::WORKER_ARRIVE,
        OrderStatus::WORKER_GOING,
        OrderStatus::PAUSE,
        OrderStatus::WAITING_PAYMENT_OTP
    );

    protected $paymentService;

    public function __construct(Order $order, PaymentService $paymentService)
    {
        $this->model = $order;
        $this->paymentService = $paymentService;
    }

    /**
     * Get status order
     * @param Order $order
     *
     * @return boolean $order
     */
    public function getStatusOrder(Order $order)
    {
        return [
            'order_status' => $order->order_status
        ];
    }

    /**
     * Create new order
     * @param array $data
     *
     * @return array
     */
    public function createOrder(User $user, array $data = [])
    {
        DB::beginTransaction();
        try {

            $order = $this->model->create([
                'user_id' => $user->id,
                'address' => $data['address'],
                'address_title' => isset($data['address_title']) ? $data['address_title'] : null,
                'token_payment' => $data['token_payment'],
                'order_status' => OrderStatus::PENDING,
                'nation_code' => $user->nation_code ?? 'vn'
            ]);

            // Save order detail
            if ($order) {

                //don vi tien hien tai user dang xai
                $currency = 'VND';
                if (!empty($user->nation_code) && $user->nation_code != 'vn') {
                    $currency = 'USD';
                }

                $order->detail()->create([
                    'service_id' => $data['service_id'],
                    'service_name' => $data['service_name'],
                    'service_child_name' => $data['service_child_name'],
                    'price' => $data['price'],
                    'currency' => $currency,
                    'status_id' => OrderStatus::PENDING,
                    'phone' => $data['phone'],
                    'note_description' => isset($data['note']) ? $data['note'] : null,
                    'longtitude' => $data['longtitude'],
                    'latitude' => $data['latitude']
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this order. Please try again.'));
        }

        DB::commit();

        return $order;
    }

    /**
     * Confirm order status
     * @param Order $order
     *
     * @return bool
     */
    public function confirmOrder(Order $order)
    {
        try {
            if ($order) {
                return $this->updateOrderStatus($order, OrderStatus::WORKER_GOING);
            }
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Start or Cancel order
     *
     * @param Order $order
     * @param $statuCode
     *
     * @return bool
     */
    public function startOrCancelOrder(int $statusCode, Order $order, $cancel_reason = '')
    {

        //yeu cau start job
        if (
            $statusCode == Order::BEGIN_AT
            && $this->updateOrderStatus($order, OrderStatus::WORKING)
        ) {
            $order->detail()->update([
                'begin_at' => getCurrentTime(),
                'status_id' => $statusCode
            ]);
            return true;
        }

        //yeu cau huy job tu worker or user
        if (
            $statusCode == Order::CANCEL
            && $this->updateOrderStatus($order, OrderStatus::CANCEL)
        ) {
            $order->detail()->update([
                'cancel_at' => getCurrentTime(),
                'cancel_reason' => $cancel_reason,
                'status_id' => $statusCode
            ]);

            return true;
        }

        return false;
    }

    /**
     * Pause order
     * @param Order $order
     * @param $statuCode
     *
     * @return bool
     */
    public function pauseOrder(int $statusCode, Order $order)
    {
        if ($statusCode == Order::BEGIN_PAUSE) {
            $order->detail()->update([
                'begin_pause' => getCurrentTime(),
                'status_id' => $statusCode
            ]);

            return true;
        }

        return false;
    }

    /**
     * Handle payment order with payToken
     *
     * @param mixed $order
     * @param array $data
     * @param mixed $type
     *
     * @return array
     */
    public function paymentWithPayToken($order, array $data = [], $paymentType = 'paymentOrder')
    {
        try {
            $options = [
                'json' => [
                    'merId' => $data['mer_id'],
                    'merTrxId' => $data['mer_trx_id'],
                    'amount' => strval($data['amount']),
                    'currency' => 'VND',
                    'payType' => 'IC',
                    'timeStamp' => $data['time_stamp'],
                    'invoiceNo' => $data['invoice_no'],
                    'goodsNm' => $data['goods_nm'],
                    'notiUrl' => $data['noti_url'],
                    'merchantToken' => $data['merchant_token'],
                    'payOption' => 'PAY_WITH_TOKEN_API',
                    'payToken' => $data['pay_token'],
                    'userId' => strval($data['user_id']),
                    'fee' => ""
                ]
            ];
            $responsePayment = callGuzzleHttp('POST', config('constant.vnpt.pay_with_token_url'), $options);
            if (!empty($paymentType) && in_array($paymentType, ['paymentOrder', 'paymentOrderWithTip'])) {
                $order->update([
                    'transaction_id' => $data['transaction_id'],
                ]);
            }

            // Update last use date card
            $this->paymentService->updateLastUsedDateCard($order->token_payment, $order->user_id);

            // Save logs payment
            PaymentLog::create([
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'nation_code' => $data['nation_code'],
                'card_type' => 'IC',
                'type' => 'cash_in',
                'status' => isset($responsePayment['resultCd']) && $responsePayment['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE ? PaymentLog::PAYMENT_DONE : PaymentLog::PAYMENT_FAILED,
                'user_id' => $data['user_id'],
                'worker_id' => $data['worker_id'],
                'transaction_id' => $data['transaction_id'],
                'response_payment' => json_encode($responsePayment),
                'request_payment' => json_encode($options)
            ]);

            // Upadte tip
            if(isset($responsePayment['resultCd']) && $responsePayment['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE){
                $this->paymentService->handleTipDone($order);
            }

            return [
                'status' => isset($responsePayment['resultCd']) && $responsePayment['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE ? true : false,
                'data' => $responsePayment
            ];
        } catch (Exception $e) {
            throw new GeneralException(__('There was a problem create this transaction. Please try again.', $e->getCode()));
        }

        return [
            'status' => false,
            'data' => null
        ];
    }

    /**
     * Get order
     * @param int $user_id
     * @param int $order_id
     *
     * @return Order
     */
    public function getOrderWithUserID(int $userId, int $orderId)
    {
        return $this->model->where('id', $orderId)
            ->whereHas('detail', function ($q) {
                $q->whereIn('status_id', array(
                    OrderStatus::PENDING,
                    OrderStatus::WAITING_WORKER_ACCEPT,
                    OrderStatus::WORKER_ACCEPTED,
                    OrderStatus::FAILED,
                    OrderStatus::WORKER_GOING
                ));
            })
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Update order
     *
     * @param array $data
     * @param mixed $order
     *
     * @return bool
     */
    public function updateOrder($order, array $data)
    {
        $order->update([
            'worker_id' => $data['worker_id'] ?? null,
        ]);
        $this->updateOrderStatus($order, $data['status']);

        $order->detail()->update([
            'status_id' => $data['status']
        ]);
    }

    /**
     * Get order by user or worker
     *
     * @param int $userID
     * @param string $userType
     *
     * @return object
     */
    public function checkHasOrder(int $userID, string $userType)
    {
        $query = $this->model->with('detail', 'customer', 'worker')
            ->whereHas('customer')
            ->whereIn('order_status', $this->isInStatusWorking);

        switch ($userType) {
            case User::IS_WORKER:
                $query->where('worker_id', $userID)->whereHas('worker');
                break;
            case User::IS_USER:
                $query->where('user_id', $userID);
                break;
            default:
                return false;
        }

        return $query->first();
    }

    /**
     * Get order by user or worker
     *
     * @param int $userID
     * @param string $userType
     */
    public function getOrders(int $userID, string $userType, $otherParams = [])
    {

        $orderStatus = array_merge($this->isInStatusWorking, [OrderStatus::DONE]);
        $query = $this->model->with('detail', 'customer', 'worker')
            ->whereHas('customer')
            ->whereIn('order_status', $orderStatus)
            ->orderBy('created_at', 'DESC');

        switch ($userType) {
            case User::IS_WORKER:
                $query->where('worker_id', $userID)->whereHas('worker');

                // IS worker filter date
                ///format: m/d/Y
                // format m-d-Y -->reformat replace ---m/d/Y
                $day = date('w');

                //from date
                $fromDate = date('Y-m-d', strtotime('-' . $day . ' days'));
                if (!empty($otherParams['fromDate'])) {
                    $fromDate = date('Y-m-d', strtotime(str_replace('-', '/', $otherParams['fromDate'])));
                }

                //to date
                $toDate = date('Y-m-d', strtotime('+' . (6 - $day) . ' days'));
                if (!empty($otherParams['toDate'])) {
                    $toDate = date('Y-m-d', strtotime(str_replace('-', '/', $otherParams['toDate'])));
                }

                $query->whereRaw("DATE(created_at) BETWEEN DATE('$fromDate') AND DATE('$toDate')");

                break;
            case User::IS_USER:
                $query->where('user_id', $userID);
                break;
            default:
                return false;
        }

        return $query->take(50)->get();
    }

    /**
     * Worker accept work
     *
     * 
     * @param User $worker
     * @param array $data
     *
     * @return bool
     */
    public function workerAcceptWork($worker, array $data = [])
    {
        if (
            !empty($data['order_id'])
            && !empty($data['status'])
        ) {
            $order = $this->model->find($data['order_id']);
            if (
                $order
                && in_array($order->order_status, [OrderStatus::PENDING, OrderStatus::WAITING_WORKER_ACCEPT])
                && $this->updateOrderStatus($order, OrderStatus::WORKER_ACCEPTED)
            ) {
                $order->update([
                    'worker_id' => $worker->id,
                ]);

                $worker->update([
                    'is_working' => User::WORKING_ON
                ]);

                return true;
            }
        }

        return false;
    }

    /**
     * Get order by id
     * 
     * @param int $orderID
     * @return Order
     *
     * @return Order Object
     */
    public function getOrderByID($orderID)
    {
        return $this->model::query()->with('detail')
            ->where('id', $orderID)
            ->first();
    }

    /**
     * Create order log
     * @param int $workerID
     * @param int $orderID
     * @param string $type
     * @return OrderLog
     */
    public function createOrderLog(int $workerID, int $orderId, string $type)
    {
        return OrderLog::create([
            'worker_id' => $workerID,
            'order_id' => $orderId,
            'type' => $type,
            'by_user' => $workerID
        ]);
    }

    /**
     * Start work
     * @param int $orderID
     *
     * @return object|bool
     */
    public function orderStartWork($orderID)
    {
        $order = $this->getOrderByID($orderID);
        if ($order) {
            $this->updateOrderStatus($order, OrderStatus::WORKING);

            $order->detail()->update([
                'begin_at' => getCurrentTime(),
                'working_time' => [round(microtime(true) * 1000)] //thoi gian lam viec luu theo milisecons
            ]);

            return $order;
        }

        return false;
    }

    /**
     * Get location worker for user
     * 
     * @param int $orderID
     *
     * @return array
     */
    public function getCurrentWorkerLocation($orderID)
    {
        $order = $this->model->where('id', $orderID)
            ->with('worker')
            ->whereHas('worker')
            ->whereIn('order_status', [OrderStatus::WORKER_ACCEPTED, OrderStatus::WORKER_ARRIVE, OrderStatus::WORKER_GOING, OrderStatus::WORKING])
            ->first();
        if ($order) {
            return [
                'longtitude' => $order->worker->longtitude,
                'latitude' => $order->worker->latitude
            ];
        }

        return false;
    }

    /**
     * Update order status
     *
     * @param mixed $order
     * @param $status
     *
     * @return mixed
     */
    public function updateOrderStatus($order, $status, $orderUPdateMoreData = array())
    {
        //cap nhat failed hoac cancel
        if (($status == OrderStatus::FAILED || $status == OrderStatus::CANCEL)
            && in_array((int)$order->order_status, array(
                OrderStatus::PENDING,
                OrderStatus::WAITING_WORKER_ACCEPT,
                OrderStatus::WORKER_ACCEPTED,
                OrderStatus::WORKER_GOING,
                OrderStatus::WORKER_ARRIVE
            ))
        ) {
            $order->update([
                'order_status' => $status
            ]);
            return true;
        }

        //cho phep update mien sau trang thai phai lon hon hien tai va kg bang chinh no
        if ((int)$status > (int)$order->order_status) {
            $order->update([
                'order_status' => $status
            ]);

            $order->detail()->update([
                'status_id' => $status
            ]);

            return true;
        }

        return false;

        //ver2 gan buoc chac che cac trang thai dc phep cap nhat
        // $flagIsUpdate = false;
        // //cap nhat trang thai chap nhan cua worker
        // if (
        //     $status == OrderStatus::WORKER_ACCEPTED
        //     && in_array((int)$order->order_status, array(
        //         OrderStatus::PENDING,
        //         OrderStatus::WAITING_WORKER_ACCEPT
        //     ))
        // ) {
        //     $flagIsUpdate = true;
        // }

        // //cap nhat trang thai toi noi lam viec khi don hang phai going
        // if (
        //     $status == OrderStatus::WORKER_ARRIVE
        //     && in_array((int)$order->order_status, [OrderStatus::WORKER_ACCEPTED])
        // ) {
        //     $flagIsUpdate = true;
        // }


        // //cap nhat trang thai working
        // if (
        //     $status == OrderStatus::WORKING
        //     && in_array((int)$order->order_status, array(
        //         OrderStatus::WORKER_ARRIVE,
        //         OrderStatus::PAUSE
        //     ))
        // ) {
        //     $flagIsUpdate = true;
        // }

        // //cap nhat trang thai DONE
        // if (
        //     $status == OrderStatus::DONE
        //     && in_array((int)$order->order_status, array(
        //         OrderStatus::WORKING,
        //         OrderStatus::PAUSE,
        //         OrderStatus::WAITING_PAYMENT_OTP
        //     ))
        // ) {
        //     $flagIsUpdate = true;
        // }

        // //cap nhat trang thai pause thi phai dang working
        // if (
        //     $status == OrderStatus::PAUSE
        //     && in_array((int)$order->order_status, [OrderStatus::WORKING])
        // ) {
        //     $flagIsUpdate = true;
        // }

        // //cap nhat trang thai cho thanh toan
        // if (
        //     $status == OrderStatus::WAITING_PAYMENT_OTP
        //     && in_array((int)$order->order_status, [OrderStatus::DONE])
        // ) {
        //     $flagIsUpdate = true;
        // }

        // if ($flagIsUpdate) {
        //     $order->update([
        //         'order_status' => $status
        //     ]);
        //     return true;
        // }

        // return false;
    }


    /**
     * Get list orders non paid
     *
     * @param mixed $user
     *
     * @return object
     */
    function getListOrderNonpaid($user)
    {
        $order = $this->model::query()
            ->with('detail')
            ->where('user_id', $user->id)
            ->where('order_status', OrderStatus::DONE)
            ->where('payment_status', 0)
            ->latest('id')
            ->first();
        if ($order) {
            $cardUsed = $this->paymentService->getCardType($order->token_payment, $order->user_id);
            if($cardUsed == 'payment_with_stripe'){
                $linkPayment = $this->paymentService->getUrlPaymentWithStripe([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'amount' => $order->detail->amount
                ]);
            } else {
                $linkPayment = $this->paymentService->getUrlPayment($order, $user);
            }

            return [
                'id' => $order->id,
                'link_payment' => $linkPayment,
                'payment_status' => 0,
                'order_status' => OrderStatus::DONE,
                'user_id' => $user->id,
                'worker_id' => $order->worker_id
            ];
        }

        return [];
    }

    /**
     * Get worker ids cancel order
     */
    function getWorkerIdsCancelOrder($orderID)
    {
        return OrderLog::where('order_id', $orderID)
            ->where('type', 'cancel')
            ->pluck('worker_id')
            ->toArray();
    }


    //tim cac worker waiting va phai dang cung khu vuc
    function getWorkerInWaitingOnJobs($nationCode = 'vn')
    {
        return $this->model::where('order_status', OrderStatus::WAITING_WORKER_ACCEPT)
            ->where('nation_code', $nationCode)
            ->pluck('worker_id')
            ->toArray();
    }
}
