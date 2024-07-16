@extends('adminlte::page')
@section('title', 'Assist portal - Order detail')
@section('content_header')
<h1>{{$orderDetail->detail->service_name}}</h1>
{{ Breadcrumbs::render('order_detail', $orderDetail) }}
<hr class="custom_hr">
@stop
@section('content')
<div class="wrap-content-detail">
    <div class="row wrap-order-detail">
        <div class="col-md-5">
            <!-- Payment tokens -->
            <div class="callout callout-info order-info">
                <h5><i class="fas fa-pen-square"></i> Order information</h5>
                <div class="box-profile-center">
                    <div class="other">
                        <ul>
                            <li>ID: <span class="badge badge-warning">{{$orderDetail->id}}</span></li>
                            <li>Service name: {{$orderDetail->detail->service_name}}</li>
                            <li>Fee 1/h: {{formatCurrency($orderDetail->detail->price, $orderDetail->nation_code)}}</li>
                            <li>Status:
                                <span class="badge {{getClassOrderStatus($orderDetail->order_status)}}">{{config('constant.order_status_id.' . $orderDetail->order_status)}}</span>
                            </li>
                            <li>Start at: {{formatDateTime($orderDetail->detail->begin_at, 'm-d-Y H:i:s')}}</li>
                            <li>End at: {{formatDateTime($orderDetail->detail->begin_end, 'm-d-Y H:i:s')}}</li>
                            <li>Cancel at: <span class="badge badge-danger">{{formatDateTime($orderDetail->detail->cancel_at, 'm-d-Y H:i:s')}}</span></li>
                            <li>Cancel reason: {{$orderDetail->detail->cancel_reason}}</li>
                            <li>Duration (hour): {{round(($orderDetail->detail->working_total_minute ?? 0) / 60)}}</li>
                            <li>Amount: {{formatCurrency($orderDetail->detail->amount ?? 0, $orderDetail->nation_code)}}</li>
                            <li>Fee app: {{formatCurrency($orderDetail->detail->fee_app ?? 0, $orderDetail->nation_code)}}</li>
                            <li>Note: {{$orderDetail->detail->note_description}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END Card for IMAGES -->
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-7">
            <div class="callout callout-info customer-info">
                <h5><i class="far fa-address-book"></i> Customer</h5>
                <div class="box-profile-center">
                    <div class="other">
                        <ul>
                            <li>Fullname: <a href="{{route('admin.users.customer-detail', [$orderDetail->customer->id ?? 1])}}" class="phone-number">{{$orderDetail->customer->name}}</a></li>
                            <li>Phone: <a href="tel:{{$orderDetail->customer->phone}}" class="phone-number">{{$orderDetail->customer->phone}}</a></li>
                            <li>Address: {{$orderDetail->address}}</li>
                            <li>Bank name: {{$orderDetail->paymentInfo->bank_name ?? ''}}</li>
                            <li>Bank type: {{$orderDetail->paymentInfo->bank_type ?? ''}}</li>
                            <li>Card no: {{$orderDetail->paymentInfo->card_no ?? ''}}</li>
                            <li>Currency: {{$orderDetail->country->currency}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="callout callout-info worker-info">
                <h5><i class="far fa-address-book"></i> Worker</h5>
                <div class="box-profile-center">
                    <div class="other">
                        <ul>
                            <li>Fullname: <a href="{{route('admin.users.worker-detail', [$orderDetail->worker->id ?? 1])}}" class="phone-number">{{$orderDetail->worker->name ?? ''}}</a></li>
                            <li>Phone: <a href="tel:{{$orderDetail->worker->phone ?? ''}}" class="phone-number">{{$orderDetail->worker->phone ?? ''}}</a></li>
                            <li>Tip: {{formatCurrency($orderDetail->detail->amount_tip ?? 0, $orderDetail->nation_code)}}</li>
                            <li>Total earned : {{formatCurrency($orderDetail->detail->amount ?? 0 - $orderDetail->detail->amount_tip ?? 0, $orderDetail->nation_code)}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
</div>
@stop
@section('css')
<style>
      
</style>
@stop
@section('js')

<script type="text/javascript">
</script>

@stop