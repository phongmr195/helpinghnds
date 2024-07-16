@extends('adminlte::page')
@section('title', 'Forbidden')
@section('content_header')
<h1>403 Forbidden</h1>
@stop
@section('content')
<section class="content">
    <div class="error-page">
        <h2 class="headline text-warning"> 403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> You do n't have permission access to this page!</h3>
            <p>
                We could not find the page you were looking for.
                Meanwhile, you may <a href="{{route('admin.dashboard')}}">Go to dashboard</a>.
            </p>
        </div>
        <!-- /.error-content -->
    </div>
    <!-- /.error-page -->
</section>
@stop
@section('css')
@stop
@section('js')
@stop