@extends('adminlte::page')
@section('title', 'Page not found')
@section('content_header')
<h1>Oops! Page not found</h1>
@stop
@section('content')
<section class="content">
    <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
            <p>
                We could not find the page you were looking for.
                Meanwhile, you may <a href="{{route('admin.dashboard')}}">Go to dashboard</a> or try using the search form.
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