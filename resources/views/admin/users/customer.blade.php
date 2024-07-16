@extends('adminlte::page')
@section('title', 'Assist portal - Customers')
@section('content_header')
<h1>Customers</h1>
{{ Breadcrumbs::render('customer') }}
<hr class="custom_hr"/>
@stop
@section('content')
<!-- Row Filter -->
<div class="row mgb-15">
    <div class="col-sm-12">
        <form action="{{route('admin.users.filter-customer')}}" class="js-fr-filter" method="GET">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputNumberId">Fullname</label>
                        @include('admin.components.filters.filter-name')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputPhone">Phone</label>
                        @include('admin.components.filters.filter-phone')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputGender">Gender</label>
                        @include('admin.components.filters.filter-gender')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputStatus">Status</label>
                        @include('admin.components.filters.filter-status')
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
<!--Show message flash -->
@include('admin.partials.success-flash')
<!--END Show message flash -->
<div class="row"> 
    <div class="col-sm-12 table-responsive">
        <table id="table_data_user" class="table table-bordered table-hover dataTable dtr-inline table-users table-sm" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Fullname</th>
                    <th class="text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Gender</th>
                    <th class="text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Status</th>
                    <th class="text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Created At</th>
                    <th class="text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
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
    // Handle load datatables
    var url = '{{route('admin.users.ajax.list_customer')}}';
    createDatatable(url);
    $('.js_filter_datatable').on('click', function(e){
        e.preventDefault();
        $('#table_data_user').DataTable().destroy();
        createDatatable(url);
    });

    function createDatatable(url){
        $('#table_data_user').DataTable({
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
                {"data": "fullname", 'className': 'all'},
                {"data": "gender", 'className': 'text-center'},
                {"data": "status", 'className': 'text-center'},
                {"data": "created_at", 'className': 'text-center'},
                {"data": "action", 'className': 'text-center'},
            ],
            "searching": false,
            "processing": true,
            "serverSide": true,
            "language": table_lang,
            "pagingType": 'full_numbers',
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