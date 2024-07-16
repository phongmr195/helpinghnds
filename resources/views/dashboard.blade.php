@extends('adminlte::page')
@section('title', 'Assist portal - Overview')
@section('content_header')
<h1>Overview</h1>
{{ Breadcrumbs::render('overview') }}
<hr class="custom_hr">
@stop
@section('content')
<div id="app">
    <div class="row">
        <div class="col-12">
            <span class="header-date-time">
                <strong>Today: {{formatDateTime(now()->toDateTimeString(), 'Y-m-d')}} 
                    <span id="js_set_time"></span>
                </strong>
            </span>
        </div>
    </div>
    <!-- Small boxes (Stat box) -->
    <!-- Block Statistical -->
    <div class="row">
        <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$orders->total_order_today}}/{{$orders->total_order}}</h3>
                    <p>New order today</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{route('admin.orders.list')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$users->total_worker_today}}/{{$users->total_worker}}</h3>
                    <p>New worker today</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{route('admin.users.list-worker')}}" class="small-box-footer">
                    More info 
                    <span class="badge badge-warning mx-1">{{$users->total_worker_online}}</span>
                    <span class="badge badge-danger mx-1">{{$users->total_worker_working}}</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-12">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$users->total_customer_today}}/{{$users->total_customer}}</h3>
                    <p>New user today</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{route('admin.users.list-customer')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <!-- END Block Statistical -->

    <!-- BLock worker pending and notify -->
    <div class="row">
        <div class="col-md-6">
            <!-- USERS LIST -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending worker</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">{{$listWorkerPending->count()}} new members</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0" style="min-height: 252px">
                    @if(isset($listWorkerPending) && $listWorkerPending->count())
                        <ul class="users-list clearfix">
                            @foreach ($listWorkerPending as $item)
                                <li class="user-avatar">
                                    @include('admin.partials.user-avatar', ['item' => $item])
                                    <a class="users-list-name" href="{{route('admin.users.worker-detail', ['user' => $item->id])}}">{{$item->name}}</a>
                                    <span class="users-list-date">{{formatDateTime($item->created_at, 'd M')}}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <!-- /.users-list -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{route('admin.users.list-worker')}}">View all worker</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!--/.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
            <!-- DIRECT CHAT -->
            <div class="card direct-chat direct-chat-warning">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Notify</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body" style="min-height: 294px">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages">
                        <!-- Message. Default to the left -->
                        <div class="direct-chat-msg">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left">No data</span>
                            </div>
                        </div>
                        <!-- /.direct-chat-msg -->
                    </div>
                    <!--/.direct-chat-messages-->
                </div>
                <!-- /.card-body -->
            </div>
            <!--/.direct-chat -->
        </div>
    </div>
    <!-- END BLock worker pending and notify -->

    <!-- Latest Order -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Latest Order</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    @if(isset($latestWork) && $latestWork->count())
                        <div class="table-responsive table-users">
                            <table class="table m-0 table-sm">
                                <thead>
                                    <tr>
                                        <th>ID Order</th>
                                        <th>Customer</th>
                                        <th>Worker</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestWork as $order)
                                        <tr>
                                            <td>
                                                <span>{{strPadLeftZero($order->id, 6)}}</span>
                                            </td>
                                            <td>
                                                @if($order->customer)
                                                    <div class="info">
                                                        <div class="user-avatar">
                                                            @if(!is_null($order->customer->profile) && !empty($order->customer->profile->avatar))
                                                                <img class="img-avatar lozad" data-src="{{getPathImageUpload($order->customer->profile->avatar)}}" alt="avatar">
                                                            @else
                                                                <i class="far fa-3x fa-user-circle"></i>
                                                            @endif
                                                        </div>
                                                        <div class="name-and-phone">
                                                            <div class="name">
                                                                <a href="{{route('admin.users.customer-detail', ['user' => $order->customer->id])}}">
                                                                    <span>
                                                                        <b>{{$order->customer->name}}</b>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                            <div class="phone">
                                                                <span>
                                                                    {{$order->customer->phone}}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->worker)
                                                    <div class="info">
                                                        <div class="user-avatar">
                                                            @if(!is_null($order->worker->profile) && !empty($order->worker->profile->avatar))
                                                                <img class="img-avatar lozad" data-src="{{getPathImageUpload($order->worker->profile->avatar)}}" alt="avatar">
                                                            @else
                                                                <i class="far fa-3x fa-user-circle"></i>
                                                            @endif
                                                        </div>
                                                        <div class="name-and-phone">
                                                            <div class="name">
                                                                <a href="{{route('admin.users.worker-detail', ['user' => $order->worker->id])}}">
                                                                    <span>
                                                                        <b>{{$order->worker->name}}</b>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                            <div class="phone">
                                                                <span>
                                                                    {{$order->worker->phone}}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>                                                
                                                <span class="badge {{getClassOrderStatus($order->order_status)}}">{{config('constant.order_status_id.' . $order->order_status)}}</span>
                                            </td>
                                            <td>
                                                <span>
                                                    {{$order->created_at}}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-action">
                                                    <a data-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" style="">
                                                        <a class="dropdown-item btn btn-info btn-sm" href="{{route('admin.orders.detail', [$order->id])}}">
                                                            <i class="fas fa-pen-square"></i> View order
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    @endif
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <a href="{{route('admin.orders.list')}}" class="btn btn-sm btn-info float-right">View all orders</a>
                </div>
                <!-- /.card-footer -->
            </div>
        </div>
    </div>
    <!-- END Latest Order -->

    <!-- Block chart -->
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card" style="min-height: 449px">
                <div class="card-header ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Order overview
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content p-0">
                        <!-- Morris chart - Sales -->
                        <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 325px;">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas id="jobOverViewChart" class="js_area_chart" style="min-height: 325px; height: 325px; max-height: 325px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card bg-gradient-primary">
                <div class="card-header border-0 ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Visitors
                    </h3>
                    <!-- card tools -->
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div id="world-map" style="height: 250px; width: 100%; position: relative; overflow: hidden; background-color: transparent;">
                    </div>
                </div>
                <!-- /.card-body-->
                <div class="card-footer bg-transparent">
                    <div class="row">
                        <div class="col-4 text-center">
                            <div id="sparkline-visitor">
                                <canvas width="160" height="100" style="width: 80px; height: 50px;"></canvas>
                            </div>
                            <div class="text-white">Visitors</div>
                        </div>
                        <!-- ./col -->
                        <div class="col-4 text-center">
                            <div id="sparkline-online">
                                <canvas width="160" height="100" style="width: 80px; height: 50px;"></canvas>
                            </div>
                            <div class="text-white">Online</div>
                        </div>
                        <!-- ./col -->
                        <div class="col-4 text-center">
                            <div id="sparkline-sale">
                                <canvas width="160" height="100" style="width: 80px; height: 50px;"></canvas>
                            </div>
                            <div class="text-white">Sales</div>
                        </div>
                        <!-- ./col -->
                    </div>
                    <!-- /.row -->
                </div>
            </div>
        </div>
    </div>
    <!-- END Block chart -->
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/jqvmap/jqvmap.min.css">
@stop
@section('js')
<script src="https://adminlte.io/themes/v3/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="https://adminlte.io/themes/v3/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<script src="https://adminlte.io/themes/v3/plugins/sparklines/sparkline.js"></script>
@stop