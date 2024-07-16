@extends('adminlte::page')
@section('title', 'Assist portal - Payment')
@section('content_header')
<h1>Payments</h1>
{{ Breadcrumbs::render('payment') }}
<hr class="custom_hr"/>
@stop
@section('content')
<!-- Row Filter -->
<div class="row mgb-15">
    <div class="col-sm-12">
        <form action="" class="js-fr-filter" method="GET">
            @csrf
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputNumberId">Country</label>
                        <select class="form-control custom-select js_select2" name="nation_code" data-placeholder="All">
                            @foreach ($countries as $country)
                                <option value="{{$country->alt}}" {{$country->is_default ? 'selected' : ''}}>{{$country->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputNumberId">Worker</label>
                        <select class="form-control custom-select js_select_worker_name" name="worker_id">
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputPhone">Cash type</label>
                        <select class="form-control custom-select" name="type">
                            <option value="" selected>All</option>
                            <option value="cash_in">Cash in</option>
                            <option value="cash_out">Cash out</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Card type</label>
                        <select class="form-control custom-select" name="card_type">
                            <option value="" selected>All</option>
                            <option value="DC">Local card (ATM)</option>
                            <option value="IC">International card (Visa, Master,...)</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Payment status</label>
                        <select class="form-control custom-select" name="status">
                            <option value="" selected>All</option>
                            <option value="1">Done</option>
                            <option value="3">Refund</option>
                            <option value="0">Processing</option>
                            <option value="2">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Transaction ID | Order ID</label>
                        <input type="text" class="form-control" name="transactionId_orderId" placeholder="Transaction ID | Order ID">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputGender">From date</label>
                        <input type="text" class="form-control js_single_date" autocomplete="off" name="from_date" value="{{getStartDate()}}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="inputStatus">To date</label>
                        <input type="text" class="form-control js_single_date" autocomplete="off" name="to_date" value="{{getEndDate()}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @include('admin.components.filters.filter-submit')
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END Row Filter -->
<div class="row">
    <div class="col-sm-6">
        <div class="total-earninged popup bg-warning text-center">
            <p class="text-center">
                CASH IN <br>
                <strong class="total-number js_show_total_cash_in"></strong>
            </p>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="total-earninged popup bg-warning text-center">
            <p class="text-center">
                CASH OUT <br>
                <strong class="total-number js_show_total_cash_out"></strong>
            </p>
        </div>
    </div>
</div>
<div class="row"> 
    <div class="col-sm-12 table-responsive">
        <table id="table_data_payment" class="table table-bordered table-hover dataTable dtr-inline table-users table-sm" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Transaction ID</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Cash type</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Card type</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Status</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">With order ID</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Amount (đ)</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">With worker</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Created date</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@section('css')
@stop
@section('js')
<script>
    // Handle get total cash in total cash out
    getTotalCashInCashOut();

    // Load single daterangepicker
    var formatDateMDY = 'MM-DD-YYYY';
    $('input.js_single_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false,
        autoApply: true,
        locale: {
            format: formatDateMDY
        },
    });

    $('input.js_single_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format(formatDateMDY));
    });

    // Load select2
    $('.js_select2').select2({
        allowClear: true,
    }).on('change', function() {
        $('#value')
            .removeClass('select2-offscreen')
            .select2({
                allowClear: true,
            });
    }).trigger('change');

    $('.js_select_worker_name').select2({
        placeholder:' Full name | ID',
        allowClear: true,
        ajax: {
            url: '{{route('admin.users.ajax.list_worker_name')}}',
            dataType: 'json',
            delay: 200,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    // Handle load datatables
    var url = '{{route('admin.payment.ajax.list_payments')}}'; 
    createDatatable(url);
    $('.js_filter_datatable').on('click', function(e){
        e.preventDefault();
        $('#table_data_payment').DataTable().destroy();
        createDatatable(url);
        getTotalCashInCashOut();
    });

    function createDatatable(url){
        $('#table_data_payment').DataTable({
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
                {"data": "transaction_id", 'className': 'all'},
                {"data": "cash_type", 'className': 'all'},
                {"data": "card_type", 'className': 'all'},
                {"data": "status", 'className': 'all'},
                {"data": "order_id", 'className': 'all'},
                {"data": "amount", 'className': 'all'},
                {"data": "worker", 'className': 'all'},
                {"data": "created_at", 'className': 'all'},
                {"data": "action", 'className': 'all'},
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

    function getTotalCashInCashOut(){
        var formData = new FormData($('.js-fr-filter')[0]);
        $.ajax({
            url : '{{route('admin.payment.ajax.total_cashIn_cashOut')}}',
            method : 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                if(res.data){
                    $('.js_show_total_cash_in').text(res.data.totalCashIn);
                    $('.js_show_total_cash_out').text(res.data.totalCashOut);
                }
            },
            error: function(err){
                console.log(err);  
            }
        });
    }
</script>
@stop