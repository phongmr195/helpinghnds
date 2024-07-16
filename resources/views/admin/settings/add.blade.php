
@extends('adminlte::page')
@section('title', 'Add Role')
@section('content_header')
<h1>
    Create Role
    <a class="btn btn-info btn-sm" href="{{route('admin.settings')}}">
        <i class="fas fa-long-arrow-alt-left"></i> Back
    </a>
</h1>

{{ Breadcrumbs::render('settings') }}
<hr class="custom_hr">
@stop
@section('content')
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-primary">
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('admin.settings.add-role')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="key">Role</label>
                        <input type="text" name="name" class="form-control" id="" placeholder="Enter role">
                    </div>
                    <div class="form-group">
                        <label for="value">Permission</label>
                        @foreach ($permissions as $item)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{$item->id}}" name="permission[]">
                                <label class="form-check-label">{{$item->name}}</label>
                            </div>
                        @endforeach
                        
                    </div>
                    @if (count($errors) > 0)
                    <div class="alert alert-warning">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
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
@stop
@section('css')
@stop
@section('js')
@stop
