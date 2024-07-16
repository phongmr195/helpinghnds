@extends('adminlte::page')
@section('title', 'Countries')
@section('content_header')
<h1>
    Countries List
    <a class="btn btn-info btn-sm" href="{{route('admin.countries.view_add')}}">
        <i class="fas fa-plus"></i> Add
    </a>
</h1>
@stop
@section('content')
<div class="row">
    <div class="col-sm-12">
        <table id="table-countries" class="table table-bordered table-hover dataTable dtr-inline" role="grid" aria-describedby="example2_info">
            <thead>
                <tr role="row">
                    <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Alt</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Title</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Phone Code</th>
                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($countries))
                    @foreach ($countries as $country)
                        <tr class="odd">
                            <td class="dtr-control sorting_1" tabindex="0">{{$country->id}}</td>
                            <td>{{$country->alt}}</td>
                            <td>{{$country->title}}</td>
                            <td>{{$country->phone_code}}</td>
                            <td>
                                <a class="btn btn-info btn-sm" href="{{route('admin.countries.view_detail', ['country' => $country->id])}}">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </a>
                                <a class="btn btn-danger btn-sm" href="#">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
@stop
@section('js')
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://adminlte.io/themes/v3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table-countries').DataTable();
    } );
</script>
@stop