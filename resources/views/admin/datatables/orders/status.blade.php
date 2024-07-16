<div class="user-status">
    <i class="{{getClassIconOrderStatus($order->order_status)}}"></i>
</div>
{{config('constant.order_status.' . $order->order_status)}}