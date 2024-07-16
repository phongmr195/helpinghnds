<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\BaseService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OrderService extends BaseService
{
    /**
     * OrderService constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    /**
     * Get list orders with filter
     *
     * @param array $params
     *
     * @return collection Order
     */
    public function getListOrderWithFilter(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->select('id', 'user_id', 'worker_id', 'order_status', 'address','created_at', 'token_payment', 'payment_status', 'nation_code')
            ->whereHas('customer')
            ->whereHas('detail')
            ->with('detail')
            ->with('country')
            ->with('customer', function($q){
                $q->with('profile', function($sq){
                    $sq->select('id', 'user_id', 'avatar');
                });
            })
            ->with('worker', function($q){
                $q->with('profile', function($sq){
                    $sq->select('id', 'user_id', 'avatar');
                });
            })
            ->latest('id');
    }

    /**
     * List Order Latest
     * @return collection Order
     */
    public function listOrderLatest()
    {
        return Cache::remember(config('constant.cache.latest_order'), config('constant.cache.time'), function () {
            return $this->model->with('detail')
                ->with('customer', function($q){
                    $q->with('profile');
                })
                ->with('worker', function($q){
                    $q->with('profile');
                })
                ->whereHas('customer')
                ->whereHas('detail')
                ->latest('id')
                ->limit(10)
                ->get();
        });
}

    /**
     * Count list order today
     * @return number
     */
    public function countListOrder()
    {
        return Cache::remember(config('constant.cache.count_order'), config('constant.cache.time'), function () {
            return $this->model->selectRaw("count(case when (DATE_FORMAT(created_at, '%Y-%m-%d')) = ? then 1 end) as total_order_today", [Carbon::today()->format('Y-m-d')])
                ->selectRaw("count(*) as total_order")
                ->first();
        });
    }

    /**
     * Get total earned for worker
     *
     * @param array $orderIds
     *
     * @return mixed
     */
    public function getTotalEarned(array $orderIds = [])
    {
        $totalEarnedData = OrderDetail::query()
            ->whereIn('order_id', $orderIds)
            ->selectRaw('sum(amount) as total_amount, sum(fee_app) as total_fee_app')
            ->first();

        return formatCurrency((int)$totalEarnedData->total_amount - (int)$totalEarnedData->total_fee_app ?? 0, 'vn');
    }

    /**
     * Get order detail
     *
     * @param Order $order
     *
     * @return Order
     */
    public function detail(Order $order)
    {
        return $order->with('detail', 'country', 'paymentInfo')
            ->with(['worker' => function($query){
                $query->with('country', 'profile');
            }])
            ->with(['customer' => function($query){
                $query->with('country', 'profile');
            }])
            ->where('id', $order->id)
            ->first(); 
    }

    /**
     * Get list region from list order
     *
     * @return object
     */
    public function getListRegionFromOrder()
    {
        return $this->model::query()->select('nation_code')->with('country')->groupBy('nation_code')->get();
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
}