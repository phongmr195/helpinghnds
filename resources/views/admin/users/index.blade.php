@extends('adminlte::page')
@section('title', 'Assist portal - Users')
@section('content_header')
{{ Breadcrumbs::render('users') }}
<h1>Users List</h1>
@stop
@section('content')
<!--Show message flash -->
@include('admin.partials.success-flash')
<!--END Show message flash -->
<div class="row"> 
    <div class="col-sm-12">
        <table id="table-data" class="table table-bordered table-hover dataTable dtr-inline table-users" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Email</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Phone</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Gender</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Address</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="odd">
                        <td class="dtr-control sorting_1" tabindex="0">{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->phone}}</td>
                        <td>{{$user->gender}}</td>
                        <td>{{$user->address}}</td>
                        <td>
                            @include('admin.partials.user-action', ['item' => $user])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
@section('css')
@stop
@section('js')
<script>
</script>
@stop