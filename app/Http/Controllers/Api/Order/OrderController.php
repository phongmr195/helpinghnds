<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderStatusRequest;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Http\Requests\Api\StatusCodeOrderRequest;
use App\Http\Requests\Api\CheckStatusOrderRequest;
use App\Http\Requests\Api\Order\OrderFinishRequest;
use App\Http\Requests\Api\Order\PauseOrResumOrderRequest;
use App\Jobs\NewJob;
use App\Models\BalanceLog;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\PaymentLog;
use App\Models\Service;
use App\Models\UserTokenPayment;
use App\Services\Api\DvService;
use App\Services\Api\OrderService;
use App\Services\Api\UserService;
use App\Services\Api\FcmService;
use App\Services\Admin\PaymentService;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{

    use ApiResponser;

    protected $orderService;
    protected $dvService;
    protected $userService;
    protected $fcmService;
    protected $paymentService;

    public function __construct(OrderService $orderService, DvService $dvService, UserService $userService, FcmService $fcmService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->dvService = $dvService;
        $this->userService = $userService;
        $this->fcmService = $fcmService;
        $this->paymentService = $paymentService;
    }

    /**
     * Get status order
     */
    public function getStatusOrder(Request $request, Order $order)
    {
        $orderStatus = $this->orderService->getStatusOrder($order);
        return $this->success($orderStatus);
    }

    /**
     * Create order
     */
    public function createOrder(CreateOrderRequest $request)
    {
        $data = $request->all();
        $data['phone'] = $request->user()->phone;
        $data['service_name'] = $data['service_name'];
        $data['service_child_name'] = $data['service_child_name'];
        $checkPayToken = $this->paymentService->verifyPayToken($data['token_payment'], $request->user()->id);

        if (!$checkPayToken) {
            return $this->error('Paytoken has expired!', 400);
        }

        $order = $this->orderService->createOrder($request->user(), $data);
        if ($order) {
            return $this->findWorkerWhenHasOrder($order, $request);
        }
        return $this->success(getOrderDataObject($order['id']));
    }

    /**
     * Confirm order
     */
    public function confirmOrder(Request $request, Order $order)
    {
        $confirmOrder = $this->orderService->confirmOrder($order);
        if ($confirmOrder) {
            return $this->success($confirmOrder);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * Start or cancel order
     */
    public function startOrCancelOrder(StatusCodeOrderRequest $request, Order $order)
    {
        $user = $request->user();
        $startOrCancelOrder = $this->orderService->startOrCancelOrder($request->status_code, $order, $request->cancel_reason);
        if ($startOrCancelOrder) {
            if ($request->status_code == Order::CANCEL) { // status cancel
                $customer = $this->userService->getUserByID($order->user_id);
                // $this->orderService->updateOrderStatus($order, OrderStatus::CANCEL);

                // Handle case worker has not accept job but the customer canceled the job
                if (is_null($order->worker_id) || empty($order->worker_id)) {
                    $worker = $this->userService->getWorkerNearest($order->detail->latitude, $order->detail->longtitude, $this->orderService->getWorkerIdsCancelOrder($order->id), $order->nation_code);
                    if ($worker) {
                        //push data
                        $notiDataForWorker = [
                            'order' => getOrderDetailFullData($order->id),
                            'user' => getUserDataObject($order->user_id),
                            'worker' => getUserDataObject($worker->id),
                            'title' => 'Work cancelled',
                            'body' => '',
                            'image' => asset('assets/images/icon-app-for-client.jpg'),
                            'sound' => '',
                            'fcm' => [
                                'type' => 'FCM-ORDER-CANCELED'
                            ]
                        ];

                        $this->fcmService->sendNotification($worker->device_token, $notiDataForWorker, $worker->device_platform);
                    }
                } else {
                    $worker = $this->userService->getUserByID($order->worker_id);
                    $worker->update(['is_working' => User::WORKING_OFF]);

                    //push data
                    $notiDataForWorker = [
                        'order' => getOrderDetailFullData($order->id),
                        'user' => getUserDataObject($order->user_id),
                        'worker' => getUserDataObject($worker->id),
                        'title' => 'Work cancelled',
                        'body' => '',
                        'image' => asset('assets/images/icon-app-for-client.jpg'),
                        'sound' => '',
                        'fcm' => [
                            'type' => 'FCM-ORDER-CANCELED'
                        ]
                    ];

                    //neu la worker cancel thi push cho user
                    if ($user->id == $order->worker_id) {
                        $this->fcmService->sendNotification($customer->device_token, $notiDataForWorker, $customer->device_platform);
                    } else { //la user thi push worker
                        $this->fcmService->sendNotification($worker->device_token, $notiDataForWorker, $worker->device_platform);
                    }
                }
            }

            return $this->success(null, 'Work cancelled');
        }

        return $this->badRequest(400, 'Bad request', 'Invalid status code');
    }

    /**
     * Pause order
     */
    public function pauseOrder(StatusCodeOrderRequest $request, Order $order)
    {
        $pauseOrder = $this->orderService->pauseOrder($request->status_code, $order);
        if ($pauseOrder) {
            return $this->success(null);
        }

        return $this->badRequest(400, 'Bad request', 'Invalid status code');
    }

    /**
     * Check status order
     */
    public function checkStatusOrder(CheckStatusOrderRequest $request)
    {
        $order = $this->orderService->getOrderWithUserID($request->user()->id, $request->order_id);
        if ($order) {
            //la dang cho xac nhan cua worker
            if (in_array($order->order_status, array(
                OrderStatus::WAITING_WORKER_ACCEPT,
                OrderStatus::WORKER_ACCEPTED,
                OrderStatus::WORKER_GOING
            ))) {

                //qua 30s thuc hien cancel worker
                $updatedAt = Carbon::parse($order->updated_at)->addSeconds(30)->timestamp;

                //Da co worker va dang cho tra loi
                if (!empty($order->worker_id) && $order->worker_id > 0) {

                    //cho tra loi hoac da tra loi nhung van goi lai thi tra ve luon
                    if (
                        $updatedAt > Carbon::now()->timestamp
                        || in_array($order->order_status, array(
                            OrderStatus::WORKER_ACCEPTED,
                            OrderStatus::WORKER_GOING
                        ))
                    ) {
                        $worker = $this->userService->getUserByID($order->worker_id);
                        $dataReponse = [
                            'order' => getOrderDetailFullData($order->id),
                            'user' => getUserDataObject($request->user()->id),
                            'worker' => getUserDataObject($worker->id),
                            'timeout' => 30,
                            'message' => 'Waiting Assistant accept work',
                        ];
                        return $this->success($dataReponse);
                    }

                    //het thoi gian cap nhat log cancel va tim worker khac
                    $this->orderService->createOrderLog($order->worker_id, $order->id, 'cancel');
                    $this->orderService->updateOrder($order, array('status' => OrderStatus::PENDING));
                }
                return $this->findWorkerWhenHasOrder($order, $request);
            }
            ///khac trang thai cho tra ve kg tim thay nua
            return $this->success(array(), 'Ok', 201);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    public function checkHasOrder(Request $request)
    {
        $user = $request->user();
        $order = $this->orderService->checkHasOrder($user->id, $user->user_type);
        if ($order) {
            $result = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($order->worker_id)
            ];

            return $this->success($result);
        }

        return $this->success(null, 'No work found!');
    }

    public function workerAcceptWork(CheckStatusOrderRequest $request)
    {
        $data = $request->all();
        $worker = $request->user();
        $order = $this->orderService->getOrderByID($data['order_id']);
        if ($order) {

            $accept = $this->orderService->workerAcceptWork($worker, $data);

            //lay thong tin khach hang cua don hang
            $customer = $this->userService->getUserByID($order->user_id);

            //ghi log action by worker
            $this->orderService->createOrderLog($worker->id, $order->id, ($accept ? 'accept' : 'cancel'));

            //xu ly action respone
            if ($accept) {

                //cap nhat trang thai la đang chay toi
                $order->update([
                    'order_status' => OrderStatus::WORKER_GOING
                ]);

                $order->detail()->update([
                    'status_id' => OrderStatus::WORKER_GOING
                ]);

                //FCM
                // Handle push notification for user client when has woker accpet
                $deviceTokenCustomer = $customer->device_token;
                //push noti data for user
                $notiData = [
                    'order' => getOrderDetailFullData($order->id),
                    'user' => getUserDataObject($order->user_id),
                    'worker' => getUserDataObject($worker->id),
                    'title' => 'Assistant accepted work', //declined: Assistant declined work
                    'body' => '',
                    'image' => URL::to(config('constant.iconApp')),
                    'sound' => '',
                    'fcm' => [
                        'type' => 'FCM-TO-USER-JOB-ACCEPTED' // 'FCM-TO-USER-JOB-DECLINED'
                    ]
                ];
                $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);

                $dataReponse = [
                    'order' => getOrderDetailFullData($order->id),
                    'user' => getUserDataObject($order->user_id),
                    'worker' => getUserDataObject($worker->id),
                    'message' => 'Assistant is going to you',
                ];

                return $this->success($dataReponse);
            }

            //neu worker cancel thi huy job va tim worker khac
            if (in_array($order->order_status, array(OrderStatus::PENDING, OrderStatus::WAITING_WORKER_ACCEPT))) {
                $order->update([
                    'worker_id' => 0,
                ]);

                return $this->findWorkerWhenHasOrder($order, $request);
            }

            //neu da huy job thi return luon
            return $this->success(array(), 'Ok', 201);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    public function findWorkerWhenHasOrder($order, $request)
    {
        $dataUpdateOrder = [
            'worker_id' => null,
            'status' => OrderStatus::FAILED
        ];

        $dataReponse = [
            'order' => getOrderDataObject($order->id),
            'message' => "Can't find the Assistant."
        ];

        //tim cac worker dang cho chap nhan job moi
        $waitingWorkers = $this->orderService->getWorkerInWaitingOnJobs($order->nation_code);

        //tim cac worker da bo qua job hien tai
        $canceledWorkers = $this->orderService->getWorkerIdsCancelOrder($order->id);
        
        //ket qua phai bo qua cac worker
        $ignoreWorkers = array_merge($waitingWorkers, $canceledWorkers);

        //tim work gan vi tri user theo khu vuc cua USER
        $worker = $this->userService->getWorkerNearest($order->detail->latitude, $order->detail->longtitude, $ignoreWorkers,$order->nation_code);
        
        // Handle when has worker
        if ($worker) {
            // Handle push notification
            $deviceTokenWorker = $worker->device_token;
            $notiData = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($worker->id),
                'title' => 'New Assignment',
                'body' => '',
                'image' => URL::to(config('constant.iconApp')),
                'fcm' => [
                    'type' => 'FCM-TO-WORKER-NEW-JOB',
                ]
            ];

            $dataUpdateOrder = [
                'worker_id' => $worker->id,
                'status' => OrderStatus::WAITING_WORKER_ACCEPT,
            ];

            $this->fcmService->sendNotification($deviceTokenWorker, $notiData, $worker->device_platform);

            $dataReponse = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($request->user()->id),
                'worker' => getUserDataObject($worker->id),
                'timeout' => 30,
                'message' => 'Waiting Assistant accept work'
            ];

            $this->orderService->updateOrder($order, $dataUpdateOrder);

            //tim lai job nay xem da dc worker accept chua
            NewJob::dispatch('checkWorkerJob', array(
                'order_id' => $order->id,
                'worker_id' => $worker->id
            ))->delay(now()->addSeconds(30));

            return $this->success($dataReponse);
        }

        //cap nhat failed status
        $this->orderService->updateOrder($order, $dataUpdateOrder);

        return $this->success($dataReponse, 'Ok', 201);
    }

    public function workerArrive(OrderStatusRequest $request)
    {
        $worker = $request->user();
        $order = $this->orderService->getOrderByID($request->order_id);
        if ($order) {

            $customer = $this->userService->getUserByID($order->user_id);

            $this->orderService->updateOrderStatus($order, OrderStatus::WORKER_ARRIVE);
            // Handle push notification
            $deviceTokenCustomer = $customer->device_token;
            //push data
            $notiData = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($worker->id),
                'title' => 'Assistant has arrived',
                'body' => '',
                'image' => URL::to(config('constant.iconApp')),
                'sound' => '',
                'fcm' => [
                    'type' => 'FCM-TO-USER-WORKER-ARRIVED'
                ]
            ];
            $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);
            return $this->success($order, 'Worker arrived');
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * Order start work
     */
    public function orderStartWork(OrderStatusRequest $request)
    {
        $order = $this->orderService->orderStartWork($request->order_id);
        $worker = $this->userService->getUserByID($order->worker_id);
        $customer = $request->user();

        if ($customer->user_type == User::IS_USER && $order) {
            // Handle push notification
            $deviceTokenWorker = $worker->device_token;
            $deviceTokenCustomer = $customer->device_token;
            //push data
            $notiDataForWorker = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($worker->id),
                'title' => 'Worked starts',
                'body' => '',
                'image' => URL::to(config('constant.iconApp')),
                'sound' => '',
                'fcm' => [
                    'type' => 'FCM-WORKING'
                ]
            ];

            $notiDataForCustomer = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($worker->id),
                'title' => 'Worked starts',
                'body' => '',
                'image' => URL::to(config('constant.iconApp')),
                'sound' => '',
                'fcm' => [
                    'type' => 'FCM-WORKING'
                ]
            ];

            $this->fcmService->sendNotification($deviceTokenWorker, $notiDataForWorker, $worker->device_platform);
            $this->fcmService->sendNotification($deviceTokenCustomer, $notiDataForCustomer, $customer->device_platform);

            return $this->success($order, 'Working start');
        }

        return $this->badRequest(400, 'Bad request', 'User forbidden or order not found!');
    }

    /**
     * Worker pause or resum work
     */
    public function workerPauseOrResumWork(PauseOrResumOrderRequest $request)
    {
        $type = $request->type;
        $order = $this->orderService->getOrderByID($request->order_id);
        $customer = $this->userService->getUserByID($order->user_id);
        $worker = $request->user();
        if (
            $order
            && $worker->id == $order->worker_id
            && in_array($order->order_status, array(OrderStatus::WORKING, OrderStatus::PAUSE))
        ) {

            //history worker requested
            $order->update([
                'is_requested' => $type
            ]);

            $typeTitle = '';
            switch ($type) {
                case 'pause':
                    $typeTitle = 'paused';
                    $order->update([
                        'updated_at' => time(),
                        'order_status' =>  8 //working
                    ]);
                    break;
                case 'resum':
                    $typeTitle = 'resumed';
                    $order->update([
                        'updated_at' => time(),
                        'order_status' =>  5 //working
                    ]);
                    break;

                default:
                    # code...
                    break;
            }

            //update working_time
            $working_time = json_decode($order->detail->working_time);
            if ($working_time) {
                array_push($working_time, round(microtime(true) * 1000));
                $order->detail()->update([
                    "working_time" => $working_time
                ]);
            }

            ///lay lai full order khi da update
            $fullOrderData = getOrderDetailFullData($order->id);

            ///////////////
            // Handle push notification
            $deviceTokenCustomer = $customer->device_token;

            //push data
            $notiData = [
                'order' => $fullOrderData,
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($order->worker_id),
                'title' => 'Assistant ' . $typeTitle . ' work',
                'body' => '',
                'image' => asset('assets/images/icon-app-for-worker.jpg'),
                'sound' => '',
                'fcm' => [
                    'type' => $type == "pause" ? 'FCM-REQUEST-PAUSE' : 'FCM-REQUEST-RESUM'
                ]
            ];
            $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);

            return $this->success($fullOrderData, 'Worker ' . $type . ' work');
        }

        return $this->badRequest(400, 'Bad request', 'Order not found.');
    }

    /**
     * User pause or resum work
     */
    public function userPauseOrResumWork(PauseOrResumOrderRequest $request)
    {
        $type = $request->type;
        $order = $this->orderService->getOrderByID($request->order_id);
        $worker = $this->userService->getUserByID($order->worker_id);
        $customer = $request->user();
        if ($order && $order->user_id == $customer->id) {

            if ($order->work_time == 0) {
                $start = Carbon::parse($order->detail->begin_at);
            } else {
                $start = $order->updated_at;
            }

            $now = getCurrentTime();
            $totalDuration = $start->diffInMinutes($now);
            if ($type == 'pause') {
                $order->update([
                    'work_time' => (int) $order->work_time + (int) $totalDuration,
                    'order_status' => OrderStatus::PAUSE //paused
                ]);
            }

            if ($type == 'resum') {
                $order->update([
                    'updated_at' => $now,
                    'order_status' => OrderStatus::WORKING //working
                ]);
            }

            //update working_time
            $working_time = json_decode($order->detail->working_time);
            if ($working_time) {
                array_push($working_time, round(microtime(true) * 1000));
                $order->detail()->update([
                    "working_time" => $working_time
                ]);
            }

            // Handle push notification
            $deviceTokenWorker = $worker->device_token;
            //push data
            $notiData = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($order->worker_id),
                'title' => 'User ' . $type . ' work',
                'body' => '',
                'image' => asset('assets/images/icon-app-for-worker.jpg'),
                'sound' => '',
                'fcm' => [
                    'type' => $type == 'pause' ? 'FCM-WORK-PAUSED' : 'FCM-WORK-RESUMED'
                ]
            ];
            $this->fcmService->sendNotification($deviceTokenWorker, $notiData, $worker->device_platform);

            return $this->success($notiData['order'], 'User has ' . $type . ' work');
        }

        return $this->badRequest(400, 'Bad request', 'Order not found.');
    }

    /**
     * Hande finish order and payment
     */
    public function orderFinishWork(OrderFinishRequest $request)
    {
        $orderID = $request->order_id;
        $order = $this->orderService->getOrderByID($orderID);
        $userLogged = $request->user();

        if (
            !empty($order)
            && in_array($order->order_status, [OrderStatus::DONE, OrderStatus::WORKING, OrderStatus::PAUSE])
            && in_array($userLogged->id, [$order->user_id, $order->worker_id])
        ) {
            // Order done payment deny
            if (!is_null($order->transaction_id) && $order->payment_status) {
                return $this->success($order, 'This order has been successfully paid.');
            }

            //Init var
            if ($order->work_time == 0) {
                $start = Carbon::parse($order->detail->begin_at);
            } else {
                $start = $order->updated_at;
            }

            $now = getCurrentTime();
            $totalDuration = $start->diffInMinutes($now);

            $payToken = $order->token_payment;
            $worker = $this->userService->getUserByID($order->worker_id);
            $customer = $this->userService->getUserByID($order->user_id);
            $deviceTokenWorker = $worker->device_token;
            $deviceTokenCustomer = $customer->device_token;
            $paymentType = 'paymentOrder';
            //so tien done job thuc te
            $amount = (int)$request->amount;
            //phi app,
            $feeAppPercent = (int)$worker->fee_app ?? 25;

            /**BEGIN V2 */
            //DATA NOTIFY
            $notiData = [
                'order' => getOrderDetailFullData($orderID),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($worker->id),
                'title' => 'Work completed',
                'body' => '',
                'image' => URL::to(config('constant.iconApp')),
                'sound' => '',
                'fcm' => [
                    'type' => 'FCM-ORDER-DONE'
                ]
            ];
            $cardUsed = $this->paymentService->getCardType($payToken, $order->user_id);

            //chi la done job chua di thanh toan
            if (!empty($request->setType)) {
                if ($request->setType === 'USER-SET-DONE-JOB') {
                    //done status
                    $feeAppAmount = ($amount * $feeAppPercent) / 100;
                    $order->update([
                        'order_status' => OrderStatus::DONE,
                        'work_time' => (int) $order->work_time + (int) $totalDuration,
                    ]);
                    $order->detail()->update([
                        'working_total_hour' => $request->workingTotalHour,
                        'working_total_minute' => $request->workingTotalMinute,
                        'amount' => $amount,
                        'fee_app' => $feeAppAmount,
                        'begin_end' => getCurrentTime()
                    ]);

                    //tinh truoc thu lao cho worker va nha working
                    $balance = $amount - $feeAppAmount;
                    $worker->update([
                        'is_working' => User::WORKING_OFF,
                        'balance' => DB::raw("balance + $balance")
                    ]);
                    BalanceLog::create([
                        'user_id' => $order->worker_id,
                        'order_id' => $orderID,
                        'amount' => $balance,
                        'type' => 'cash_in',
                        'description' => "Worker with order #$orderID - balance (+$balance)"
                    ]);

                    //update order done cho ca 2 phuong thuc thanh toan
                    $this->orderService->updateOrderStatus($order, OrderStatus::DONE);

                    //la worker hay user done job
                    if ($userLogged->id == $worker->id) {
                        $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);
                    } else { //la user finish
                        $this->fcmService->sendNotification($deviceTokenWorker, $notiData, $worker->device_platform);
                    }

                    return $this->success($this->orderService->getOrderByID($orderID), 'Job completed!');
                }

                //thuc hien nhan finish job danh cho ca 2 user va worker
                if ($request->setType === 'USER-SET-FINISH-JOB' && $order->order_status == OrderStatus::DONE) {
                    //cap nhat don hang neu co tip
                    if (!empty($request->amount_tip) && $request->amount_tip > 0) {
                        $paymentType = 'paymentOrderWithTip';
                        $order->detail()->update([
                            'amount_tip' => $request->amount_tip,
                            'tip' => $request->tip,
                            'tip_type' => $request->tip_type,
                        ]);
                    }

                    //neu co rating thi cap nhat rating
                    if (!empty($request->rating)) {
                        $this->userService->createRating([
                            'order_id' => $order->id,
                            'user_id' => $order->worker_id,
                            'rating' => $request->rating,
                            'note' => $request->note ?? null
                        ]);
                    }

                    //neu la user lay thong link payment va thanh toan
                    // Get link payment with stripe and card
                    $linkPaymentWithATM = $this->paymentService->getUrlPayment($this->orderService->getOrderByID($orderID), $customer, $paymentType);
                    $linkPaymentWithStripe = $this->paymentService->getUrlPaymentWithStripe([
                        'amount' => $amount,
                        'order_id' => $orderID,
                        'user_id' => $order->user_id
                    ], $paymentType);

                    $cardStripe = UserTokenPayment::where('pay_token', $payToken)
                        ->where('user_id', $order->user_id)
                        ->first();
                    if ($userLogged->id == $customer->id) {
                        if ($cardUsed === 'ATM') {                            
                            return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithATM));
                        } else {
                            // Payment with stripe
                            if($cardUsed === 'payment_with_stripe'){
                                $paymentIntentWithTripe = $this->paymentService->paymentWithStripe($cardStripe->customer_id, $cardStripe->payment_method_id, $order, $paymentType);
                                if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'succeeded'){
                                    return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                                } else {
                                    // Handle payment with stripe fail
                                    if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'requires_action'){
                                        $linkPaymentWithStripe = $paymentIntentWithTripe->next_action->redirect_to_url->url;

                                        return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                                    }

                                    return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                                }
                            }
                            // Payment with payToken vnpt
                            $dataPayment = $this->orderService->paymentWithPayToken($order, $this->getDataPaymentWithTokenVNPT($order, $amount), 'paymentOrder');
                            
                            // Push noti charges money for client if payment success
                            if ($dataPayment['status']) {
                                $order->update([
                                    'payment_status' => 1 // Payment done
                                ]);

                                return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithATM));
                            }

                            return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithATM));
                        }
                    } else {
                        // Worker end job
                        $notiDataForUser = [
                            'order' => getOrderDetailFullData($orderID, $linkPaymentWithStripe),
                            'user' => getUserDataObject($order->user_id),
                            'worker' => getUserDataObject($worker->id),
                            'title' => 'Work completed',
                            'body' => '',
                            'image' => URL::to(config('constant.iconApp')),
                            'sound' => '',
                            'fcm' => [
                                'type' => 'FCM-ORDER-DONE'
                            ]
                        ];
                        if($cardUsed === 'payment_with_stripe'){
                            $paymentIntentWithTripe = $this->paymentService->paymentWithStripe($cardStripe->customer_id, $cardStripe->payment_method_id, $order, $paymentType);
                            if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'succeeded'){
                                return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                            } else {
                                // Handle payment with stripe fail
                                if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'requires_action'){
                                    $linkPaymentWithStripe = $paymentIntentWithTripe->next_action->redirect_to_url->url;
                                    $this->fcmService->sendNotification($deviceTokenCustomer, $notiDataForUser, $customer->device_platform);

                                    return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                                }
                                $this->fcmService->sendNotification($deviceTokenCustomer, $notiDataForUser, $customer->device_platform);

                                return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                            }
                        }
                    }

                    return $this->success($this->orderService->getOrderByID($orderID), 'Work completed');
                }
            }

            /**END V2 */

            // Get info worker and user            
            $amountTip = (int)$request->amount_tip ?? 0;
            if (!empty($request->amount_tip) && $request->amount_tip > 0) {
                $paymentType = 'paymentOrderWithTip';
                $order->detail()->update([
                    'amount_tip' => $request->amount_tip,
                    'tip' => $request->tip,
                    'tip_type' => $request->tip_type,
                ]);
            }
            
            //neu co rating thi cap nhat rating
            if (!empty($request->rating)) {
                $this->userService->createRating([
                    'order_id' => $order->id,
                    'user_id' => $order->worker_id,
                    'rating' => $request->rating,
                    'note' => $request->note ?? null
                ]);
            }

            ///auto done job tuy nhien thanh toan phai chờ
            $order->update([
                'order_status' => OrderStatus::DONE,
                'work_time' => (int) $order->work_time + (int) $totalDuration,
            ]);

            $feeAppAmount = (($amount - $amountTip) * $feeAppPercent) / 100;
            $balance = $amount - $feeAppAmount;
            $order->detail()->update([
                'working_total_hour' => $request->workingTotalHour,
                'working_total_minute' => $request->workingTotalMinute,
                'amount' => $amount,
                'fee_app' => $feeAppAmount,
                'begin_end' => getCurrentTime()
            ]);

            //update order done cho ca 2 phuong thuc thanh toan
            $this->orderService->updateOrderStatus($order, OrderStatus::DONE);

            // Update balance when done job
            $worker->update([
                'is_working' => User::WORKING_OFF,
                'balance' => DB::raw("balance + $balance")
            ]);

            // // Logs balance for worker
            BalanceLog::create([
                'user_id' => $order->worker_id,
                'order_id' => $orderID,
                'amount' => $balance,
                'type' => 'cash_in',
                'description' => "Worker with order #$orderID - balance (+$balance)"
            ]);

            //viec con lai la Assist voi user

            // Payment with ATM card
            $linkPaymentWithATM = $this->paymentService->getUrlPayment($this->orderService->getOrderByID($orderID), $customer, $paymentType);

            if ($cardUsed === 'ATM') {
                ///la worker done job push ve cho use thong bao nhap otp
                if ($userLogged->id == $worker->id) {
                    $notiPaymentForCustomer = [
                        'order' => getOrderDetailFullData($orderID, $linkPaymentWithATM),
                        'user' => getUserDataObject($order->user_id),
                        'worker' => getUserDataObject($worker->id),
                        'title' => 'Work completed',
                        'body' => '',
                        'image' => URL::to(config('constant.iconApp')),
                        'sound' => '',
                        'fcm' => [
                            'type' => 'WORKER-PUSH-DONE-JOB'
                        ]
                    ];

                    $this->fcmService->sendNotification($deviceTokenCustomer, $notiPaymentForCustomer, $customer->device_platform);

                    return $this->success($order, 'Waiting for customer to enter otp to process payment!');
                } else { //la user done job push ve worker done job
                    $notiPaymentForWorker = [
                        'order' => getOrderDetailFullData($orderID, $linkPaymentWithATM),
                        'user' => getUserDataObject($order->user_id),
                        'worker' => getUserDataObject($worker->id),
                        'title' => 'Work completed',
                        'body' => '',
                        'image' => URL::to(config('constant.iconApp')),
                        'sound' => '',
                        'fcm' => [
                            'type' => 'FCM-ORDER-DONE'
                            //'type' => 'USER-PUSH-WAITING-OTP'
                        ]
                    ];
                    $this->fcmService->sendNotification($deviceTokenWorker, $notiPaymentForWorker, $worker->device_platform);

                    return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithATM), 'Waiting for customer to enter otp to process payment!', 201);
                }
            } else {
                // Handle push notification
                $notiData = [
                    'order' => getOrderDetailFullData($orderID),
                    'user' => getUserDataObject($order->user_id),
                    'worker' => getUserDataObject($worker->id),
                    'title' => 'Work completed',
                    'body' => '',
                    'image' => URL::to(config('constant.iconApp')),
                    'sound' => '',
                    'fcm' => [
                        'type' => 'FCM-ORDER-DONE'
                    ]
                ];

                if($cardUsed == 'payment_with_stripe'){
                    $card = UserTokenPayment::where('pay_token', $payToken)
                        ->where('user_id', $order->user_id)
                        ->first();

                    $paymentIntentWithTripe = $this->paymentService->paymentWithStripe($card->customer_id, $card->payment_method_id, $order, $paymentType);
                     // Handle payment with stripe fail
                     $linkPaymentWithStripe = $this->paymentService->getUrlPaymentWithStripe([
                        'amount' => $amount,
                        'order_id' => $orderID,
                        'user_id' => $order->user_id
                    ], $paymentType);

                    if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'succeeded'){
                        if ($userLogged->id == $worker->id) {
                            $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);
                        }
                    } else {
                        // Handle payment with stripe fail
                        if($paymentIntentWithTripe && $paymentIntentWithTripe->status == 'requires_action'){
                            $linkPaymentWithStripe = $paymentIntentWithTripe->next_action->redirect_to_url->url;

                            return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                        }
                    }

                    return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithStripe));
                }

                // Payment with payToken vnpt
                $dataPayment = $this->orderService->paymentWithPayToken($order, $this->getDataPaymentWithTokenVNPT($order, $amount), $paymentType);
                
                // Push noti charges money for client if payment success
                if ($dataPayment['status']) {
                    $order->update([
                        'payment_status' => 1 // Payment done
                    ]);

                    if ($userLogged->id == $worker->id) {
                        $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);
                    }
                }


                //la worker finish
                if ($userLogged->id == $worker->id) {
                    $this->fcmService->sendNotification($deviceTokenCustomer, $notiData, $customer->device_platform);
                } else { //la user finish
                    $this->fcmService->sendNotification($deviceTokenWorker, $notiData, $worker->device_platform);
                }

                //neu worker end job va thanh toan that bai thi push ve user de xac nhan thanh toan
                if ($userLogged->id == $worker->id) {
                    $notiPaymentForCustomer = [
                        'order' => getOrderDetailFullData($orderID, $linkPaymentWithATM),
                        'user' => getUserDataObject($order->user_id),
                        'worker' => getUserDataObject($worker->id),
                        'title' => 'Work completed',
                        'body' => '',
                        'image' => URL::to(config('constant.iconApp')),
                        'sound' => '',
                        'fcm' => [
                            'type' => 'WORKER-PUSH-DONE-JOB'
                        ]
                    ];
                    $this->fcmService->sendNotification($deviceTokenCustomer, $notiPaymentForCustomer, $customer->device_platform);
                }

                return $this->success($this->orderService->getOrderByID($orderID), 'Job completed!');
            }
            //thanh toan visa failed
            return $this->success(getOrderDetailFullData($orderID, $linkPaymentWithATM), 'Waiting for customer to enter otp to process payment!', 201);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * Get location worker for user
     */
    public function getOrderWorkerLocation(OrderStatusRequest $request)
    {
        $location = $this->orderService->getCurrentWorkerLocation($request->order_id);
        if ($location) {
            return $this->success($location);
        }

        return $this->success(null);
    }

    /**
     * Get detail order by user or worker
     * @param Request $request
     * @return type
     */
    public function getDetailByUserOrWorker(Request $request)
    {
        $user = $request->user();
        $order = $this->orderService->getOrderByID($request->order_id);
        if ($order && in_array($user->id, array($order->user_id, $order->worker_id))) {
            $result = array(
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => $order->worker_id ? getUserDataObject($order->worker_id) : null
            );
            return $this->success($result);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * Push to call user between worker 
     * @param Request $request
     */
    function pushIncoming(Request $request)
    {
        $user = $request->user();
        $order = $this->orderService->getOrderByID($request->order_id);
        if ($order && in_array($user->id, array($order->user_id, $order->worker_id))) {
            //push data
            $notiData = [
                'order' => getOrderDetailFullData($order->id),
                'user' => getUserDataObject($order->user_id),
                'worker' => getUserDataObject($order->worker_id),
                'title' => 'Incoming call...',
                'body' => '',
                'image' => asset('assets/images/icon-app-for-client.jpg'),
                'fcm' => [
                    'type' => 'FCM-ORDER-INCALL'
                ]
            ];

            //neu la user call worker
            $deviceToken = '';
            $devicePlatform = '';
            if ($user->id == $order->user_id) {
                $worker = User::find($order->worker_id);
                $deviceToken = $worker->device_token;
                $devicePlatform = $worker->device_platform;
            } else { //worker call user
                $customer = User::find($order->user_id);
                $deviceToken = $customer->device_token;
                $devicePlatform = $customer->device_platform;
            }
            $this->fcmService->sendNotification($deviceToken, $notiData, $devicePlatform);

            return $this->success($notiData);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found or not allowed!');
    }

    /**
     * Get list order by user or worker
     * @param Request $request
     * @return type
     */
    public function getOrdersByUserID(Request $request)
    {
        $user = $request->user();
        $langCode = $request->header('LangCode', 'en');
        $otherParams = array(
            "fromDate" => $request->fromDate,
            "toDate" => $request->toDate,
            "langCode" => $langCode
        );

        $orders = $this->orderService->getOrders($user->id, $user->user_type, $otherParams);
        if ($orders) {

            //lay danh sach services
            $services = Service::all()->toArray();
            $serColumnID = array_column($services, 'id');
            $serColumnChildName = array_column($services, 'en');

            foreach ($orders as $key => $value) {
                //neu thanh tip chưa hoan thanh thi set ve 0
                if (!$value['tip_status']) {
                    $orders[$key]['detail']['amount_tip'] = 0;
                }

                ///dich ngon ngu tieng viet
                if ($langCode == 'vi') {
                    //parent
                    $serviceParent = $services[array_search($value['detail']->service_id, $serColumnID)];
                    $orders[$key]['detail']['service_name'] = $serviceParent['vi'] . ' (' . $serviceParent['unit_vn'] . ')';

                    //child
                    if (in_array($value['detail']->service_child_name, $serColumnChildName)) {
                        $serviceChild = $services[array_search($value['detail']->service_child_name, $serColumnChildName)];
                        if (!empty($serviceChild)) {
                            $orders[$key]['detail']['service_child_name_en'] = $value['detail']->service_child_name;
                            $orders[$key]['detail']['service_child_name'] = $serviceChild['vi'];
                        }
                    }
                }

                //lay them currency ra ngoai order
                $orders[$key]['currency'] = $value['detail']->currency;
            }

            return $this->success($orders);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    function rePaymentOrder(Request $request)
    {
        $user = $request->user();
        $order = $this->orderService->getOrderByID($request->order_id);
        if (
            !empty($order)
            && $order->order_status == 6
            && $order->payment_status == 0
            && $user->id == $order->user_id
        ) {
            //neu co chon lai the thi update lai
            if (!empty($request->token_payment)) {
                $order->update([
                    'token_payment' => $request->token_payment,
                ]);
            }
            $cardUsed = $this->paymentService->getCardType($order->token_payment, $order->user_id);
            $customer = $this->userService->getUserByID($order->user_id);
            if($cardUsed == 'payment_with_stripe'){
                $linkPayment = $this->paymentService->getUrlPaymentWithStripe([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'amount' => $order->detail->amount
                ], 'paymentTip');
            } else {
                $linkPayment = $this->paymentService->getUrlPayment($order, $customer, 'paymentTip');
            }

            return $this->success(getOrderDetailFullData($order->id, $linkPayment), '', 201);
        }
        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * User tip more for helper by order_id
     * @param Request $request
     */
    public function tipOrderByUser(Request $request)
    {
        $user = $request->user();
        $order = $this->orderService->getOrderByID($request->order_id);
        if ($order) {

            // Save rating if
            if (!empty($request->rating)) {
                $this->userService->createRating([
                    'order_id' => $order->id,
                    'user_id' => $order->worker_id,
                    'rating' => $request->rating,
                    'note' => $request->note ?? null
                ]);
            }

            //!empty($request->tip_type) && !empty($request->tip) && 
            //chua tung tip lan nao
            if (!empty($request->amount_tip) && $order->tip_status == 0 && $order->order_status == 6 && $user->id == $order->user_id) {

                $amountTip =  $request->amount_tip;
                //update lai
                $order->detail()->update([
                    'amount_tip' => $amountTip,
                    'tip' => $request->tip,
                    'tip_type' => $request->tip_type
                ]);

                //neu co chon lai the thi update lai
                if (!empty($request->token_payment)) {
                    $order->update([
                        'token_payment' => $request->token_payment,
                    ]);
                }

                $customer = $this->userService->getUserByID($order->user_id);
                $cardUsed = $this->paymentService->getCardType($order->token_payment, $order->user_id);
                if($cardUsed == 'payment_with_stripe'){
                    $linkPayment = $this->paymentService->getUrlPaymentWithStripe([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'amount' => $order->detail->amount
                    ], 'paymentTip');
                } else {
                    $linkPayment = $this->paymentService->getUrlPayment($order, $customer, 'paymentTip');
                }

                return $this->success(getOrderDetailFullData($order->id, $linkPayment), 'Waiting for customer to enter otp to process payment!', 201);

                // Handle payment tip for user
                // $payToken = $order->token_payment;
                // ///user co chon the khac
                // if (!empty($request->token_payment)) {
                //     $payToken = $request->token_payment;
                // }
                // $cardUsed = $this->paymentService->getCardType($payToken, $order->user_id);
                // if ($cardUsed == 'ATM') { // Payment with ATM card
                //     return $this->success(getOrderDetailFullData($order->id, $linkPaymentWithATM), 'Waiting for customer to enter otp to process payment!', 201);
                // } else { // Payment with payToken
                //     $timeStamp = date('YmdHis');
                //     $merId = config('constant.vnpt.mer_id');
                //     $endCodeKey = config('constant.vnpt.endcode_key');
                //     $merTrxId = 'MTRXID_' . $timeStamp . '_' . rand(100, 10000);
                //     $decryptPayToken = decrypt3DES($payToken);
                //     $encryptedPayToken = encrypt3DES($decryptPayToken);
                //     $plainTxtToken = $timeStamp . $merTrxId . $merId . $amountTip . $encryptedPayToken . $endCodeKey;
                //     $merchantToken = hash('sha256', $plainTxtToken);
                //     $dataPayment = [
                //         'mer_id' => $merId,
                //         'mer_trx_id' => $merTrxId,
                //         'amount' => $amountTip,
                //         'time_stamp' => $timeStamp,
                //         'invoice_no' => 'ORDER_' . $timeStamp . '_' . $order->id,
                //         'goods_nm' => $order->detail->service_child_name,
                //         'noti_url' => route('notify.payment_result'),
                //         'merchant_token' => $merchantToken,
                //         'pay_token' => $encryptedPayToken,
                //         'user_id' => $order->user_id,
                //         'worker_id' => $order->worker_id,
                //         'order_id' => $order->id,
                //         'transaction_id' => Str::random(20),
                //         'nation_code' => $order->nation_code
                //     ];

                //     $dataPayment = $this->orderService->paymentWithPayToken($order, $dataPayment, 'payment_tip');
                //     if ($dataPayment['status']) {
                //         $order->detail()->update([
                //             'amount' => $order->detail->amount + $amountTip,
                //         ]);

                //         $order->update([
                //             'tip_status' => 1
                //         ]);
                //     }

                //     // Update last use date card
                //     $this->paymentService->updateLastUsedDateCard($payToken, $order->user_id);

                //     if ($dataPayment['status']) {
                //         return $this->success($this->orderService->getOrderByID($order->id), 'Tip order success!');
                //     }

                //     //thanh toan visa failed
                //     return $this->success(getOrderDetailFullData($order->id, $linkPaymentWithATM), 'Waiting for customer to enter otp to process payment!', 201);
                // }
            }
            return $this->success(true);
        }

        return $this->badRequest(400, 'Bad request', 'Order not found!');
    }

    /**
     * Worker push done job for user
     */
    public function workerPushDoneJob(OrderStatusRequest $request)
    {
        $order = $this->orderService->getOrderByID($request->order_id);
        $worker = $this->userService->getUserByID($order->worker_id);
        $customer = $this->userService->getUserByID($order->user_id);
        $deviceTokenCustomer = $customer->device_token;
        $linkPaymentWithATM = $this->paymentService->getUrlPayment($order, $customer);
        $notiPaymentForCustomer = [
            'order' => getOrderDetailFullData($order->id, $linkPaymentWithATM),
            'user' => getUserDataObject($order->user_id),
            'worker' => getUserDataObject($worker->id),
            'title' => 'Work completed',
            'body' => '',
            'image' => URL::to(config('constant.iconApp')),
            'sound' => '',
            'fcm' => [
                'type' => 'WORKER-PUSH-DONE-JOB'
            ]
        ];
        $this->fcmService->sendNotification($deviceTokenCustomer, $notiPaymentForCustomer, $customer->device_platform);

        return $this->success($order, 'Waiting for customer to enter otp to proceed payment!', 201);
    }

    /**
     * Handle payment with ATM
     * ///Tu VNPT do ve
     */
    public function handleNotifyPaymentWithATM(Request $request)
    {
        $results = $request->all();

        if (isset($results['invoiceNo'])) {
            $dataInvoiceNo = explode('_', $results['invoiceNo']);
            if (isset($dataInvoiceNo) && isset($dataInvoiceNo[2]) && isset($dataInvoiceNo[3])) {
                $orderId = $dataInvoiceNo[2];
                $paymentType = $dataInvoiceNo[3];
                $order = $this->orderService->getOrderByID($orderId);

                if (!is_null($order)) {

                    //kiem tra neu don hang thanh toan done
                    if ($paymentType === 'paymentOrder' && !is_null($order->transaction_id) && $order->payment_status) {
                        return 'This order has been successfully paid.';
                    }

                    //kiem tra neu thanh toan cho tip da done
                    if ($paymentType === 'paymentTip' && $order->tip_status) {
                        return 'Tip in order has been successfully paid.';
                    }

                    $transactionId = Str::random(20);
                    $dataPayment = [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'worker_id' => $order->worker_id,
                        'transaction_id' => $transactionId,
                        'type' => 'cash_in',
                        'amount' => $results['amount'],
                        'status' => (isset($results['resultCd']) && $results['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE) ? PaymentLog::PAYMENT_DONE : PaymentLog::PAYMENT_FAILED,
                        'card_type' => $results['payType'],
                        'result_cd' => $results['resultCd'],
                        'nation_code' => $order->nation_code,
                        'response_payment' => json_encode($results),
                        'request_payment' => json_encode([
                            'json' => [
                                'merId' => $results['merId'] ?? '',
                                'merTrxId' => $results['merId'] ?? '',
                                'amount' => strval($results['amount']) ?? '',
                                'currency' => 'VND',
                                'payType' => 'DC',
                                'timeStamp' => $results['timeStamp'] ?? '',
                                'invoiceNo' => $results['invoiceNo'] ?? '',
                                'goodsNm' => $results['goodsNm'] ?? '',
                                'merchantToken' => $results['merchantToken'] ?? '',
                                'payOption' => 'PAY_WITH_TOKEN',
                                'payToken' => $results['payToken'] ?? '',
                                'orderId' => strval($results['userId']) ?? '',
                                'fee' => ""
                            ]
                        ])
                    ];

                    // Save payment logs
                    $this->paymentService->savePaymentLogs($order, $dataPayment);
                    $this->paymentService->updateLastUsedDateCard($order->token_payment, $order->user_id);

                    if (isset($results['resultCd']) && $results['resultCd'] == OrderStatus::PAYMENT_SUCCESS_CODE) {

                        $worker = $this->userService->getUserByID($order->worker_id);

                        // Update payment order
                        if ($paymentType == 'paymentOrder') {
                            $order->update([
                                'transaction_id' => $transactionId,
                                'payment_status' => 1 // Payment done
                            ]);

                            //neu co tip trong don hang thi update tip done
                            if (
                                $order->tip_status == 0
                                && !empty($order->detail->amount_tip)
                                && $order->detail->amount + $order->detail->amount_tip == $results['amount']
                            ) {
                                $this->handleTipDone($order, $worker, $order->detail->amount_tip);
                            }
                            return;
                        }

                        // Update amount order with payment tip
                        if ($paymentType == 'paymentTip') {
                            return $this->handleTipDone($order, $worker, $results['amount']);
                        }

                        // Push noti payment done for worker
                        // $worker = $this->userService->getUserByID($order->worker_id);
                        // $deviceTokenWorker = $worker->device_token;
                        // $notiPaymentForWorker = [
                        //     'order' => getOrderDetailFullData($order->id),
                        //     'user' => getUserDataObject($order->user_id),
                        //     'worker' => getUserDataObject($worker->id),
                        //     'title' => 'Work completed',
                        //     'body' => '',
                        //     'image' => URL::to(config('constant.iconApp')),
                        //     'sound' => '',
                        //     'fcm' => [
                        //         'type' => 'FCM-USER-DONE-OTP'
                        //     ]
                        // ];
                        // $this->fcmService->sendNotification($deviceTokenWorker, $notiPaymentForWorker, $worker->device_platform);
                    }
                }
            }
        }
    }

    private function handleTipDone($order, $worker, $amountTip)
    {
        if (!empty($order) && $order->detail->amount_tip == $amountTip) {
            $order->update([
                'tip_status' => 1
            ]);

            $order->detail()->update([
                'amount' => $order->detail->amount + $order->detail->amount_tip,
            ]);

            //cong tien tip vao blance worker
            $worker->update([
                'balance' => DB::raw("balance + $amountTip")
            ]);

            // // Logs balance for worker
            BalanceLog::create([
                'user_id' => $order->worker_id,
                'order_id' => $order->id,
                'amount' => $amountTip,
                'type' => 'cash_in',
                'description' => "Tip order #" . $order->id . " - balance (+$amountTip)"
            ]);
            return true;
        }

        return false;
    }

    /**
     * Get list order non paid
     */
    public function getOrdersNonpaid(Request $request)
    {
        $data = $this->orderService->getListOrderNonpaid($request->user());

        return $this->success($data);
    }

    /**
     * Get data payment with token for vnpt
     *
     * @param mixed $order
     * @param int $amount
     * 
     * @return array
     */
    public function getDataPaymentWithTokenVNPT($order, int $amount)
    {
        $timeStamp = date('YmdHis');
        $merId = config('constant.vnpt.mer_id');
        $endCodeKey = config('constant.vnpt.endcode_key');
        $merTrxId = 'MTRXID_' . $timeStamp . '_' . rand(100, 10000);
        $decryptPayToken = decrypt3DES($order->token_payment);
        $encryptedPayToken = encrypt3DES($decryptPayToken);
        $plainTxtToken = $timeStamp . $merTrxId . $merId . $amount . $encryptedPayToken . $endCodeKey;
        $merchantToken = hash('sha256', $plainTxtToken);
        
        return [
            'mer_id' => $merId,
            'mer_trx_id' => $merTrxId,
            'amount' => $amount,
            'time_stamp' => $timeStamp,
            'invoice_no' => 'ORDER_' . $timeStamp . '_' . $order->id,
            'goods_nm' => $order->detail->service_child_name,
            'noti_url' => route('notify.payment_result'),
            'merchant_token' => $merchantToken,
            'pay_token' => $encryptedPayToken,
            'user_id' => $order->user_id,
            'worker_id' => $order->worker_id,
            'order_id' => $order->id,
            'transaction_id' => Str::random(20),
            'nation_code' => $order->nation_code
        ];
    }
}