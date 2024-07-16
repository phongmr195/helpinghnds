
@extends('adminlte::page')
@section('title', 'Edit Role')
@section('content_header')
<h1>
    Edit Role
    <a class="btn btn-info btn-sm" href="{{route('admin.settings')}}">
        <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
</h1>
{{ Breadcrumbs::render('settings') }}
<hr class="custom_hr">
@stop
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{route('admin.settings.edit-role')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="role_id" value="{{$role->id}}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Role</label>
                            <input type="text" name="name" class="form-control" id="" placeholder="Enter role" value="{{$role->name}}">
                        </div>
                        <div class="form-group">
                            <label for="value">Permission</label>
                            @foreach ($permissions as $item)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{$item->id}}" name="permission[]" {{in_array($item->id, $rolePermissions) ? 'checked' : ''}}>
                                    <label class="form-check-label">{{$item->name}}</label>
                                </div>
                            @endforeach
                            
                        </div>
                        <div class="form-group wrap-menu-page">
                            <label for="value">Display menu</label>
                            @foreach ($pages as $page)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{$page->id}}" name="page_ids[]" {{in_array($page->id, $rolePages) ? 'checked' : ''}}>
                                    <label class="form-check-label">{{$page->name}}</label>
                                </div>
                            @endforeach
                        </div>
                        @if (isset($alert_message))
                        <div class="alert alert-success">
                            <ul>
                                <li>{{ $alert_message }}</li>
                            </ul>
                        </div>
                        @endif
                    </div>
                   
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-6">
        </div>
        <!--/.col (right) -->
    </div>
    <!-- /.row -->
</div>
@stop
@section('css')
@stop
@section('js')
@stop
