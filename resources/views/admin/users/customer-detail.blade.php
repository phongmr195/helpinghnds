@extends('adminlte::page')
@section('title', 'Assist portal - Customer detail')
@section('content_header')
<h1>{{$userDetail->name}}</h1>
{{ Breadcrumbs::render('user_detail', $userDetail) }}
<hr class="custom_hr">
@stop
@section('content')
<div class="wrap-content-detail">
    <div class="row wrap-user-profile">
        <div class="col-md-5">
            <!-- Profile Image -->
            <div class="card card-widget">
                <div class="box-profile">
                    <div class="box-profile-top card-body bg-warning">
                        <div class="left-avatar">
                            <div class="avatar user-avatar-detail">
                                @if(!is_null($userDetail->profile) && isImage($userDetail->profile->avatar))
                                    <img class="img-avatar" src="{{getPathImageUpload($userDetail->profile->avatar)}}" alt="avatar">
                                @else
                                    <i class="far fa-4x fa-user-circle"></i>
                                @endif
                                <div class="user-status">
                                    <i class="{{getClassIconUserStatus($userDetail->status)}}"></i>
                                </div>
                            </div>
                        </div>
                        <div class="right-info">
                            <div class="info">
                                <h3 class="widget-user-username">{{$userDetail->name}}</h3>
                                <span>
                                    <b>{{$userDetail->phone}}</b>
                                </span>
                            </div>
                            
                            <div class="action">
                                <span class="inbox">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <span class="heart">
                                    <i class="far fa-heart"></i>
                                </span>
                                <span class="user-text-status">
                                    <span class="btn {{getClassUserStatus($userDetail->status)}}" >{{config('constant.user_status.' . $userDetail->status)}}</span>
                                    <i class="fas fa-sync-alt bg-info" data-toggle="modal" data-target="#modal-update-status-{{$userDetail->id}}"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="box-profile-center card-body">
                        <div class="other">
                            <ul>
                                <li>
                                    Rating: {{number_format($userDetail->ratings_avg_rating, 1)}} 
                                    <div class="rating_start">
                                        {!! showRatingStar($userDetail->ratings_avg_rating) !!}
                                    </div>
                                </li>
                                <li>Accpetance: {{number_format(100, 1)}}%</li>
                                <li>Cancelllation: {{number_format(0, 1)}}%</li>
                            </ul>
                        </div>
                    </div>
                    <div class="box-profile-bottom card-body">
                        <div class="head-title mgb-20">
                            <h4>
                                Profile information
                            </h4>
                            <a href="" class="bt-update-profile" data-toggle="modal" data-target="#modal-update-profile">
                                <i class="far fa-edit"></i>
                            </a>
                        </div>
                        <div class="other-info">
                            <ul>
                                <li><label for="">First name:</label> {{$userDetail->first_name}}</li>
                                <li><label for="">Gender:</label> {{$userDetail->gender}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-7">
            <!-- Payment tokens -->
            <div class="card wrap-token-information">
                <div class="card-header bg-info p-custom">
                    <div class="head-title">
                        <h4>
                            Payment tokens
                        </h4>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="tab-pane active id-information" id="id-information">
                            <div class="row">
                                <div class="table-responsive">
                                    <div class="col-sm-12">
                                        @if(isset($userDetail->tokenPayments) && count($userDetail->tokenPayments))
                                        <table id="table-settings-role" class="table table-bordered table-hover dataTable dtr-inline table-sm" role="grid" aria-describedby="example2_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">#</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Bank name</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Bank type</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Card number</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Created date</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Last used date</th>
                                                    <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($userDetail->tokenPayments as $key => $card)
                                                    <tr class="odd">
                                                        <td class="dtr-control sorting_1" tabindex="0">{{$key + 1}}</td>
                                                        <td>{{$card->bank_name}}</td>
                                                        <td>{{$card->bank_type}}</td>
                                                        <td>{{$card->card_no}}</td>
                                                        <td>{{formatDateTime($card->created_at, 'm-d-Y')}}</td>
                                                        <td>{{getLastCardUsed($card->pay_token)}}</td>
                                                        <td>
                                                            <a href="javascript:void(0)" class="dropdown-item btn btn-danger btn-sm js_remove_token_payment" data-id="{{$card->id}}" data-name="{{$card->bank_type . ' - ' . $card->card_no}}" data-url="{{route('api.delete_card')}}">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <p class="text-center">
                                                <strong>The customer has not added a payment card</strong>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- END Card for IMAGES -->
            <!-- Card for IMAGES -->
            <div class="card wrap-id-information">
                <div class="card-header bg-info p-custom">
                    <div class="head-title">
                        <h4>
                            Images
                        </h4>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="tab-pane active id-information" id="id-information">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="wrap-info-photo">
                                        <div class="img-front">
                                            @if(!is_null($userDetail->profile) && isImage($userDetail->profile->avatar))
                                                <a href="{{getPathImageUpload($userDetail->profile->avatar)}}" data-fancybox data-caption="User avatar image">
                                                    <div class="center-img text-center">
                                                        <img class="card-img-top lozad" data-src="{{getPathImageUpload($userDetail->profile->avatar)}}" />
                                                    </div>
                                                </a>      
                                                <div class="text-center">
                                                    Front
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- END Card for IMAGES -->
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Popup Update Profile-->
    @include('admin.modals.profile-info')
    <!-- END Popup Update Profile-->

    <!-- Popup update IN Information -->
    @include('admin.modals.id-information')
    <!-- END Popup update IN Information -->

    <!-- Popup Reset Password -->
    @include('admin.modals.reset-pass')
    <!-- END Popup Reset Password -->

    <!-- Popup Update Status -->
    @include('admin.modals.update-status-single', ['item' => $userDetail])
    <!-- END Popup Update Status -->
</div>
@stop
@section('css')
@stop
@section('js')
@stop