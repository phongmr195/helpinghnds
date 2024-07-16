@extends('adminlte::page')
@section('title', 'Assist portal - Account')
@section('content_header')
<h1>Account</h1>
{{ Breadcrumbs::render('account') }}
<hr class="custom_hr"/>
@stop
@section('content')
<!-- Row Filter -->
<div class="row mgb-15">
    <div class="col-sm-12">
        <form action="{{route('admin.users.filter_list_account')}}" class="js-fr-filter" method="GET">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputGender">Fullname</label>
                        @include('admin.components.filters.filter-name')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputGender">Phone</label>
                        @include('admin.components.filters.filter-phone')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputStatus">Department</label>
                        <select class="form-control custom-select" name="role_id">
                            <option value="" {{(isset(request()->role_id) && request()->role_id == '') ? 'selected' : ''}}>All</option>
                            @foreach ($roles as $role)
                                <option value="{{$role->id}}" {{(isset(request()->role_id) && request()->role_id == $role->id) ? 'selected' : ''}}>{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputStatus">Status</label>
                        @include('admin.components.filters.filter-account-status')
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="inputCeatedAt">Created at</label>
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
<div class="row list-button-action">
    <div class="col-sm-12">
        <div class="form-group">
            <button class="btn btn-secondary btn-warning js_show_modal_permission" tabindex="0" aria-controls="datatable_data" type="button"><span><i class="fas fa-users-cog"></i> Permission role</span></button>
        </div>
    </div>
</div>
<!-- END Row Filter -->
<!--Show message flash -->
@include('admin.modals.role-permission')
@include('admin.modals.create-role')
@include('admin.modals.create-user-account')
@include('admin.modals.grid-reset-pass')
@include('admin.modals.update-profile-account')
<!--END Show message flash -->
<div class="row"> 
    <div class="col-sm-12 table-responsive">
        <table id="table_data_user" class="table table-bordered table-hover dataTable dtr-inline table-sm table-users js_reload_datatable table-sm" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Department</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Fullname</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Phone</th>
                    <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Gender</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Email</th>
                    <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Status</th>
                    <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Created at</th>
                    <th class="sorting text-center" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending"></th>
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
    var url = '{{route('admin.users.ajax.list_account')}}';
    createDatatable(url);
    $('.js_filter_datatable').on('click', function(e){
        e.preventDefault();
        $('#table_data_user').DataTable().destroy();
        createDatatable(url);
    });

    function createDatatable(url){
        $('#table_data_user').DataTable({
            dom: 'Blfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 row', '25 row', '50 row', 'Display all' ]
            ],
            buttons: [
                {
                    className: 'btn-warning js_show_modal_create_account',
                    text: '<i class="fas fa-user-plus"></i> Add new'
                },
                {
                    className: 'btn-success js_click_reload_datatable',
                    text: '<i class="fas fa-sync-alt"></i> Refresh'
                },
            ],
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
                {"data": "user_role", 'className': 'all'},
                {"data": "name", 'className': 'all'},
                {"data": "phone", 'className': 'all'},
                {"data": "gender", 'className': 'text-center'},
                {"data": "email", 'className': 'all'},
                {"data": "status", 'className': 'text-center'},
                {"data": "created_at", 'className': 'text-center'},
                {"data": "action", 'className': 'text-center'},
            ],
            "searching": false,
            "processing": true,
            "serverSide": true,
            "language": table_lang,
            "order" : [[0, "desc"]],
            "scrollX" : '{{checkIsMobile()}}',
            "searching" : false,
            drawCallback: function(){
                const observer = lozad(); // lazy loads elements with default selector as ".lozad"
                observer.observe();
            }
        });  
    }
</script>
@stop