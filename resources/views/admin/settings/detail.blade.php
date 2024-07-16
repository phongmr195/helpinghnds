
@extends('adminlte::page')
@section('title', 'Detail Role')
@section('content_header')
<h1>Detail Role</h1>
{{ Breadcrumbs::render('settings') }}
@stop
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
                <!-- /.card-header -->
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
                </div>
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
