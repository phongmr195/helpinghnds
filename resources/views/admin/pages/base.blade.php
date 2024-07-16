@extends('adminlte::page')
@section('title', 'Assist portal - Customers')
@section('content_header')
<h1>{{$page->name}}</h1>
{{ Breadcrumbs::render('customer') }}
<hr class="custom_hr"/>
@stop
@section('content')
<!-- Row Filter -->
@include('admin.blocks.filter')
<!-- END Row Filter -->

<!--Show message flash -->
@include('admin.partials.success-flash')
<!--END Show message flash -->

<!--Datatable-->
@include('admin.blocks.datatable', ['datatable' => $datatable])
<!--END Datatable-->
@stop
@section('css')
@stop
@section('js')
<script>
    // Daterangepicker set start date, end date
    var formatDate = 'YYYY-MM-DD';
    $('.js_select_date_created').daterangepicker({
        // autoUpdateInput: false,
        startDate: '{{getStartDate()}}',
        endDate: '{{getEndDate()}}',
        locale: {
            format: formatDate
        },
    });
</script>
@stop