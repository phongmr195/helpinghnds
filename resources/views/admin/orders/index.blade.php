@extends('adminlte::page')
@section('title', 'Assist portal - Orders')
@section('content_header')
<h1>Orders</h1>
{{ Breadcrumbs::render('orders') }}
<hr class="custom_hr">
@stop
@section('content')
<!-- Row Filter -->
<div class="row mgb-15">
    <div class="col-sm-12">
        <form action="{{route('admin.orders.filter')}}" class="js-fr-filter" method="GET">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputIdOrder">ID Order</label>
                        <input type="text" class="form-control" name="order_id" placeholder="000001" value="{{!empty(request()->order_id) ? request()->order_id : ''}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputPhone">Phone</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <select class="custom-select custom-select-group" name="user_type">
                                    <option value="client">Customer</option>
                                    <option value="worker">Worker</option>
                                </select>
                            </div>
                            @include('admin.components.filters.filter-phone')
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputCustomer">Customer</label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Enter fullname" value="{{!empty(request()->customer_name) ? request()->customer_name : ''}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputWorker">Worker</label>
                        <input type="text" class="form-control" name="worker_name" placeholder="Enter fullname" value="{{!empty(request()->worker_name) ? request()->worker_name : ''}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputStatus">Region</label>
                        <select class="form-control custom-select input_select2" name="nation_code">
                            <option value="">All</option>
                            @if(isset($regions))
                                @foreach ($regions as $region)
                                    <option value="{{$region->nation_code}}">{{$region->country->title}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputStatus">Status</label>
                        <select id="inputStatus" class="form-control custom-select input_select2" name="order_status">
                            <option value="">All</option>
                            <option value="0" {{(isset(request()->order_status) && request()->order_status == 0) ? 'selected' : ''}}>Pending</option>
                            <option value="5" {{(isset(request()->order_status) && request()->order_status == 5) ? 'selected' : ''}}>Working</option>
                            <option value="6" {{(isset(request()->order_status) && request()->order_status == 6) ? 'selected' : ''}}>Done</option>
                            <option value="7" {{(isset(request()->order_status) && request()->order_status == 7) ? 'selected' : ''}}>Failed</option>
                            <option value="12" {{(isset(request()->order_status) && request()->order_status == 12) ? 'selected' : ''}}>Canceled</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputCeatedAt">Created At</label>
                        @include('admin.components.filters.filter-dates')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        @include('admin.components.filters.filter-submit')
                    </div>
                </div>
            </div>
        </form>
        
    </div>
</div>
<!-- END Row Filter -->
<div class="table-responsive">
    <table id="tblOrders" class="table table-bordered table-hover dataTable dtr-inline table-users table-orders table-sm" style="width: 100%;">
        <thead>
            <tr role="row">
                <th>#ID</th>
                <th>Region</th>
                <th>Service</th>
                <th>Fee / 1 hour</th>
                <th>Unit</th>
                <th>Payment status</th>
                <th>Method</th>
                <th>Customer</th>
                <th>Worker</th>
                <th class="text-center">Status</th>
                <th class="text-center">Working time(minute)</th>
                <th class="text-center">Start</th>
                <th class="text-center">End</th>
                <th class="text-center">Created date</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

@stop
@section('css')
@stop
@section('js')
<script>
    // Handle load datatables
    var url = '{{route('admin.orders.ajax.list')}}'; 

    createDatatable(url);
    
    $('.js_filter_datatable').on('click', function(e){
        e.preventDefault();
        $('#tblOrders').DataTable().destroy();
        createDatatable(url);
    });

    function createDatatable(url){
        $('#tblOrders').DataTable({
            "ajax": {
                'url': url,
                'data': function(d) {
                    var frmData = $('.js-fr-filter').serializeArray();
                    $.each(frmData, function(key, val) {
                        d[val.name] = val.value;
                    });
                }
            },
            "columns": [
                {"data": "order_id", 'className': 'all', 'name': 'id'},
                {"data": "region", 'className': 'all'},
                {"data": "service", 'className': 'all'},
                {"data": "fee_one_hour", 'className': 'all'},
                {"data": "unit", 'className': 'all'},
                {"data": "payment_status", 'className': 'all'},
                {"data": "method", 'className': 'all'},
                {"data": "customer", 'className': 'all'},
                {"data": "worker", 'className': 'all'},
                {"data": "status", 'className': 'text-center'},
                {"data": "working_time", 'className': 'text-center'},
                {"data": "begin_at", 'className': 'text-center'},
                {"data": "begin_end", 'className': 'text-center'},
                {"data": "created_at", 'className': 'text-center'},
                {"data": "action", 'className': 'text-center'},
            ],
            "searching": false,
            "processing": true,
            "serverSide": true,
            "language": table_lang,
            "pagingType": "full_numbers",
            "ordering" : false,
            "scrollX" : '{{checkIsMobile()}}',
            drawCallback: function(){
                const observer = lozad(); // lazy loads elements with default selector as ".lozad"
                observer.observe();
            }
        });
    }
</script>
@stop