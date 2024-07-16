
@extends('adminlte::page')
@section('title', 'Add Country')
@section('content_header')
<h1>Add Country</h1>
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
                <form action="{{route('admin.countries.edit', ['country' => $country->id])}}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Alt</label>
                            <input type="text" name="alt" class="form-control" id="alt" placeholder="Enter alt" value="{{$country->alt}}">
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title" value="{{$country->title}}">
                        </div>
                        <div class="form-group">
                            <label for="phone code">Phone Code</label>
                            <input type="text" name="phone_code" class="form-control" id="phone_code" placeholder="Enter Phone Code" value="{{$country->phone_code}}">
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
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
