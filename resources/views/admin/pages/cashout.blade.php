@extends('adminlte::page')
@section('title', 'Assist portal - Cashout')
@section('content_header')
<h1>Cashout</h1>
{{ Breadcrumbs::render('cashout') }}
<hr class="custom_hr"/>
@stop
@section('content')
<!-- Row Filter -->
<div class="row mgb-15">
    <div class="col-sm-12">
        <form action="" class="js-fr-filter" method="GET">
            @csrf
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputNumberId">Country</label>
                        <select class="form-control custom-select js_select2" name="nation_code" data-placeholder="All">
                            @foreach ($countries as $country)
                                <option value="{{$country->alt}}" {{$country->is_default ? 'selected' : ''}}>{{$country->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputNumberId">Worker</label>
                        <select class="form-control custom-select js_select_worker_name" name="worker_id">
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Status</label>
                        <select class="form-control custom-select" name="status">
                            <option value="" selected>All</option>
                            <option value="0">Waiting</option>
                            <option value="1">Approved</option>
                            <option value="2">Canceled</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="Enter phone">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">From date</label>
                        <input type="text" class="form-control js_single_date" autocomplete="off" name="from_date" value="" placeholder="{{getStartDate()}}">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">To date</label>
                        <input type="text" class="form-control js_single_date" autocomplete="off" name="to_date" value="" placeholder="{{getEndDate()}}">
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
    <div class="col-sm-12 table-responsive">
        <table id="table_data_cashout" class="table table-bordered table-hover dataTable dtr-inline table-users table-sm js_reload_datatable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">ID</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Worker</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Current balance</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Cashout</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Unit</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Bank info</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Status</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Created date</th>
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@include('admin.modals.cancel-cashout')
<!-- Loading transfer money for worker -->
<div class="wrap-loading-approve badge-warning js-loading-transfer-money">
    <p style="text-align: center; padding-top: 10px; font-size:14px">Processing transfer money</p>
    <div class="lds-ring-parent">
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>
<!-- END loading transfer money for worker -->
@stop
@section('css')
<style>
    #table_data_cashout td p{
        margin: 0;
    }
</style>
@stop
@section('js')
<script>
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
    var url = '{{route('admin.ajax.list_cashout')}}'; 
    createDatatable(url);
    $('.js_filter_datatable').on('click', function(e){
        e.preventDefault();
        $('#table_data_cashout').DataTable().destroy();
        createDatatable(url);
    });

    function createDatatable(url){
        $('#table_data_cashout').DataTable({
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
                {"data": "id", 'className': 'all'},
                {"data": "worker", 'className': 'all'},
                {"data": "balance", 'className': 'all'},
                {"data": "amount", 'className': 'all'},
                {"data": "unit", 'className': 'text-center'},
                {"data": "bank_info", 'className': 'all'},
                {"data": "status_icon", 'className': 'all'},
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
            },
            createdRow: function( row, data, dataIndex ) {
                if(data.status == 0){
                    $(row).addClass('row-waiting');
                }
            }
        });
    }
</script>
@stop