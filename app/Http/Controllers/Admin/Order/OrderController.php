<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\Admin\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        // $orders = $this->orderService->listOrder();
        // return view('admin.orders.index', compact('orders'));
        $route_refresh = 'admin.orders.list';
        $regions = $this->orderService->getListRegionFromOrder();

        return view('admin.orders.index', compact('route_refresh', 'regions'));
    }

    /**
     * Show order detail
     */
    public function showDetailOrder(Order $order)
    {
        $orderDetail = $this->orderService->detail($order);

        return view('admin.orders.detail', compact('orderDetail'));
    }

    /**
     * Json list order
     */
    public function getListOrder(Request $request)
    {
        $dataFilter = $request->only(['id', 'order_id', 'phone', 'customer_name', 'worker_name', 'order_status', 'dates', 'nation_code', 'user_type']);
        if(!empty($request->phone)){
            $dataFilter['phone'] = $request->user_type . '_' . $request->phone;
        }

        $orders = $this->orderService->getListOrderWithFilter($dataFilter);

        return $this->createJsonDatatable($orders);
    }
    /**
     * Filter list order
     */
    public function filterOrder(Request $request)
    {
        // $orders = $this->orderService->filterOrder($request->all());
        // return view('admin.orders.index', compact('orders'));
        return view('admin.orders.index', ['route_refresh' => 'admin.orders.list']);
    }

    /**
     * Create json datatable order
     */
    public function createJsonDatatable($data)
    {
        return datatables()->eloquent($data)
            ->editColumn('order_id', function ($order) {
                return strPadLeftZero($order->id, 6);
            })
            ->editColumn('region', function($order){
                return !is_null($order->country) ? $order->country->title : 'Việt Nam';
            })
            ->editColumn('service', function ($order) {
                return $order->detail->service_name;
            })
            ->editColumn('fee_one_hour', function ($order) {
                return formatCurrency($order->detail->price, $order->nation_code);
            })
            ->editColumn('unit', function($order){
                return $order->detail->currency;
            })
            ->editColumn('payment_status', function($order){
                return $order->payment_status ? 'Paid' : 'None paid';
            })
            ->editColumn('method', function($order){
                return getMethodNamePayment($order->token_payment, $order->user_id);
            })
            ->editColumn('customer', function ($order) {
                // return view('admin.datatables.orders.fullname-customer', ['order' => $order])->render();
                if (!is_null($order->customer)) {
                    return '
                    <div class="info">
                        <div class="user-avatar">
                            ' . getAvatarHtml($order->customer) . '
                        </div>
                        <div class="name-and-phone">
                            <div class="name">
                                <a href="' . route('admin.users.customer-detail', ['user' => $order->customer->id]) . '">
                                    <span>
                                        <b>' . $order->customer->name . '</b>
                                    </span>
                                </a>
                            </div>
                            <div class="phone">
                                <span>
                                    ' . $order->customer->phone . '
                                </span>
                            </div>
                        </div>
                    </div>';
                }

                return '';
            })
            ->editColumn('worker', function ($order) {
                if (!empty($order->worker_id) && !is_null($order->worker)) {
                    return '
                    <div class="info">
                        <div class="user-avatar">
                            ' . getAvatarHtml($order->worker) . '
                        </div>
                        <div class="name-and-phone">
                            <div class="name">
                                <a href="' . route('admin.users.worker-detail', ['user' => $order->worker->id]) . '">
                                    <span>
                                        <b>' . $order->worker->name . '</b>
                                    </span>
                                </a>
                            </div>
                            <div class="phone">
                                <span>
                                    ' . $order->worker->phone . '
                                </span>
                            </div>
                        </div>
                    </div>
                ';
                }
                return '';
            })
            ->editColumn('status', function ($order) {
                return
                    '<div class="user-status">
                        <i class="' . getClassIconOrderStatus($order->order_status) . '"></i>
                    </div>
                    ' . config('constant.order_status_id.' . $order->order_status);
            })
            ->editColumn('working_time', function($order){
                return $order->detail->working_total_minute ?? 0;
            })
            ->editColumn('begin_at', function ($order) {
                if (!empty($order->detail->begin_at)) {
                    return '<b>' . formatDateTime($order->detail->begin_at, 'm-d-Y') . '</b> <br> ' . formatDateTime($order->detail->begin_at, 'H:i:s');
                }
                return '';
            })
            ->editColumn('begin_end', function ($order) {
                if (!empty($order->detail->begin_end)) {
                    return '<b>' . formatDateTime($order->detail->begin_end, 'm-d-Y') . '</b> <br> ' . formatDateTime($order->detail->begin_end, 'H:i:s');
                }
                return '';
            })
            ->editColumn('created_at', function ($order) {
                if (!empty($order->created_at)) {
                    return '<b>' . formatDateTime($order->created_at, 'm-d-Y') . '</b> <br> ' . formatDateTime($order->created_at, 'H:i:s');
                }
                return '';
            })
            ->editColumn('action', function ($order) {
                return
                    '<div class="btn-group btn-action">
                <a data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="">
                    <a class="dropdown-item btn btn-info btn-sm" href="' . route('admin.orders.detail', [$order->id]) . '">
                        <i class="fas fa-pen-square"></i> View order
                    </a>
                </div>
            </div>';
            })
            ->rawColumns(['order_id', 'customer', 'worker', 'status', 'begin_at', 'begin_end', 'created_at', 'action', 'service', 'fee_one_hour', 'unit', 'payment_status', 'method', 'region', 'working_time'])
            ->skipTotalRecords()
            ->toJson();
    }
}
