@extends('adminlte::page')
@section('title', 'Settings')
@section('content_header')
<h1>
    Role management
    <a class="btn btn-info btn-sm" href="{{route('admin.settings.view_add')}}">
        <i class="fas fa-plus"></i> Create new role
    </a>
</h1>
{{ Breadcrumbs::render('settings') }}
<hr class="custom_hr">
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        @if (session('status_success'))
            <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>
                    {!! session()->get('status_success') !!}
                </strong>
            </div>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <table id="table-settings-role" class="table table-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($roles))
                    @foreach ($roles as $role)
                        <tr class="odd">
                            <td class="dtr-control sorting_1" tabindex="0">{{$role->id}}</td>
                            <td>{{$role->name}}</td>
                            <td>
                                <div class="btn-group btn-action">
                                    <a data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        {{-- <a class=" dropdown-item btn btn-info btn-sm" href="{{route('admin.settings.detail-role', ['id' => $role->id])}}">
                                            <i class="fas fa-eye"></i> View
                                        </a> --}}
                                        <a class="dropdown-item btn btn-info btn-sm" href="{{route('admin.settings.view-edit-role', ['id' => $role->id])}}">
                                            <i class="fas fa-edit"></i> View
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm" data-id="{{$role->id}}" data-name="{{$role->name}}" data-url="">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
{{-- <hr class="custom_hr">
<h3>
    Page management
    <a class="btn btn-info btn-sm" href="{{route('admin.settings.view_add')}}">
        <i class="fas fa-plus"></i> Create new page
    </a>
</h3> --}}
{{-- <div class="row">
    <div class="col-sm-12">
        <table id="table-settings-page" class="table table-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Slug</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Order</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Route name</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($pages))
                    @foreach ($pages as $page)
                        <tr class="odd">
                            <td class="dtr-control sorting_1" tabindex="0">{{$page->id}}</td>
                            <td>{{$page->name}}</td>
                            <td>{{$page->slug}}</td>
                            <td>{{$page->order}}</td>
                            <td>{{$page->route_name}}</td>
                            <td>
                                <div class="btn-group btn-action">
                                    <a data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                        <a class="dropdown-item btn btn-info btn-sm" href="">
                                            <i class="fas fa-edit"></i> View
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm" data-id="{{$page->id}}" data-name="{{$page->name}}" data-url="">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div> --}}
@stop
@section('css')
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
@stop
@section('js')
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://adminlte.io/themes/v3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table-settings-role').DataTable();
    } );
</script>
@stop