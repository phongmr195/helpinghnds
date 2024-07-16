@extends('adminlte::page')
@section('title', 'Assist portal - Worker detail')
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
                                <li>Balance: {{formatCurrency($userDetail->balance, $userDetail->nation_code)}}</li>
                                <li>
                                    Rating: {{number_format($userDetail->ratings_avg_rating, 1)}} 
                                    <div class="rating_start">
                                        {!! showRatingStar($userDetail->ratings_avg_rating) !!}
                                    </div>
                                </li>
                                <li>Accpetance: {{number_format($workerInfo->percent_accepted != 0 ? $workerInfo->percent_accepted : 100, 1)}}%</li>
                                <li>Cancellation: {{number_format($workerInfo->percent_canceled != 0 ? $workerInfo->percent_canceled : 0, 1)}}%</li>
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
                                <li><label for="">Last name:</label> {{$userDetail->last_name}}</li>
                                <li><label for="">Gender:</label> {{$userDetail->gender}}</li>
                                <li>
                                    <label for="">Address: </label>
                                    {{$userDetail->address}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>

            <!-- Card for ID Information -->
            <div class="card wrap-id-information">
                <div class="card-header bg-info p-custom">
                    <div class="head-title">
                        <h4>
                            ID Information
                        </h4>
                        <a href="" class="bt-update-profile" data-toggle="modal" data-target="#modal-update-id-information" >
                            <i class="far fa-edit"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="tab-pane active id-information" id="id-information">
                            <ul>
                                <li>Number: <b>{{$userDetail->number_id}}</b></li>
                                <li>Type of ID: <b>{{$userDetail->type_number_id}}</b></li>
                                <li>Photo:</li>
                            </ul>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="wrap-info-photo">
                                        <div class="img-front">
                                            @if(isImage($userDetail->id_card_before))
                                            <a href="{{getPathImageUpload($userDetail->id_card_before)}}" data-fancybox data-caption="Image card before">
                                                <div class="center-img text-center">
                                                    <img class="card-img-top lozad" data-src="{{getPathImageUpload($userDetail->id_card_before)}}" />
                                                </div>
                                            </a>    
                                            @else
                                            <a href="{{asset('/assets/images/card_default.png')}}" data-fancybox data-caption="Image card before">
                                                <div class="center-img text-center">
                                                    <img class="card-img-top lozad" data-src="{{asset('/assets/images/card_default.png')}}" />
                                                </div>
                                            </a>    
                                            @endif                                         
                                            <div class="text-center">
                                                Front
                                            </div>
                                        </div>
                                        <div class="img-back">
                                            @if(isImage($userDetail->id_card_after))
                                            <a href="{{getPathImageUpload($userDetail->id_card_after)}}" data-fancybox data-caption="Image card after">
                                                <div class="center-img text-center">
                                                    <img class="card-img-top lozad" data-src="{{getPathImageUpload($userDetail->id_card_after)}}" />
                                                </div>
                                            </a>    
                                            @else
                                            <a href="{{asset('/assets/images/card_default.png')}}" data-fancybox data-caption="Image card after">
                                                <div class="center-img text-center">
                                                    <img class="card-img-top lozad" data-src="{{asset('/assets/images/card_default.png')}}" />
                                                </div>
                                            </a>    
                                            @endif 
                                            <div class="text-center">
                                                Back
                                            </div>
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
            <!-- END Card for ID Information -->

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
                                                    <img class="card-img-top" src="{{getPathImageUpload($userDetail->profile->avatar)}}" />
                                                </div>
                                            </a>    
                                            @else
                                            <a href="{{asset('/assets/images/card_default.png')}}" data-fancybox data-caption="User avatar image">
                                                <div class="center-img text-center">
                                                    <img class="card-img-top" src="{{asset('/assets/images/card_default.png')}}" />
                                                </div>
                                            </a>    
                                            @endif  
                                            <div class="text-center">
                                                Front
                                            </div>
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
        <div class="col-md-7">
            <div class="card wrap-earnings-information">
                <div class="card-header bg-info p-custom">
                    <div class="head-title">
                        <h4>
                            Earnings
                        </h4>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        {{-- <form class="js-fr-filter js_form_filter_earnings" method="GET">
                            <input type="hidden" name="user_id" value="{{$userDetail->id}}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="">From date</label>
                                    <input type="text" name="from_date" class="form-control js_single_date" autocomplete="off" placeholder="MM-DD-YYYY" value="">
                                </div>
                                <div class="col-sm-4">
                                    <label for="">To date</label>
                                    <input type="text" name="to_date" class="form-control js_single_date" autocomplete="off" placeholder="MM-DD-YYYY" value="">
                                </div>
                                <div class="col-sm-4 flex-item-bottom mt-mb-15">
                                    <button type="submit" class="btn btn-info js_filter_datatable">Search</button>
                                    <a href="{{route('admin.users.worker-detail', [$userDetail->id])}}" class="btn btn-warning" style="margin-left: 5px">
                                        Refresh
                                    </a>
                                </div>
                            </div>
                        </form> --}}
                        <!-- /.tab-pane -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="total-earninged bg-warning text-center">
                                    <p class="text-center">
                                        Total earned <br/>
                                        {{-- <strong class="total-number js_show_total_earninged">{{$totalEarned}}</strong> --}}
                                        <strong class="total-number js_show_total_earninged"></strong>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="total-earninged bg-warning text-center">
                                    <p class="text-center">
                                        Total cash out <br/>
                                        <strong class="total-number js_show_total_cash_out"></strong>
                                    </p>
                                </div>
                            </div>
                            @if(!empty($totalEarned) && $totalEarned != 0)
                                <div class="col-sm-12 text-center mt-3">
                                    <button class="btn btn-success" data-toggle="modal" data-target="#modal-list-worker-activity">
                                        View more detail
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- Card for ADMIN -->
            {{-- <div class="card for-admin">
                <div class="card-header bg-info p-custom">
                    <div class="head-title">
                        <h4>
                            For Admin
                        </h4>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <h5>Please start the meeting and enter Meeting ID and Passcode</h5>
                        <div class="d-flex align-items-center">
                            <div style="width: 45%;">
                                <input class="form-control mb-1" name="zoomNumber" placeholder="Meeting ID" value="3129839105"/>
                                <input class="form-control" name="zoomPasscode" placeholder="Passcode" value="eZT6UJ"/>
                            </div>
                            <a id="adminCallVideoToWorker" href="#" class="btn btn-warning btn-lg ml-3 text-white">
                                <i class="fas fa-user-astronaut text-white"></i> Call now
                            </a>
                        </div>


                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div> --}}
            <!-- END Card for ADMIN -->
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

    <!-- Popup List woker activity -->
    @include('admin.modals.list-woreker-activity')
    <!-- END Popup List woker activity -->
</div>
@stop
@section('css')
<style>
    table#table-earnings {
        width: 100% !important;
    }   
</style>
@stop
@section('js')

<script type="text/javascript">
    $(document).ready(function() {
        $('#adminCallVideoToWorker').click(function(){
            var idNumber = $('input[name="zoomNumber"]').val();
            var passcode = $('input[name="zoomPasscode"]').val();
            if(idNumber && passcode){
                $.ajax({
                    url : "{{route('admin.users.ajax.pushToCallUser')}}",
                    method: "POST",
                    data:{
                        userID: '{{$userDetail->id}}',
                        zoomID: idNumber,
                        zoomPasscode: passcode
                    },
                    success: function(res){
                        if(res){
                            alert('Registration to call a mechanic is successful, please wait a moment...');
                        }
                    },
                    error: function(err){
                        alert('The caller registration failed, please try again.');
                    }
                });
            }else{
                alert('Please start the meeting and enter the Meeting ID and Passcode before clicking this button.');
                if(!idNumber){
                    $('input[name="zoomNumber"]').focus();
                    return;
                }
                
                if(!passcode){
                    $('input[name="zoomPasscode"]').focus();
                    return;
                }
            }
        });

        // Load single daterangepicker
        var formatDateMDY = 'MM-DD-YYYY';
        $('input.js_single_date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            autoApply: true,
            locale: {
                format: formatDateMDY
            },
        });

        $('input.js_single_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(formatDateMDY));
        });

        // Handle load datatables
        var url = '{{route('admin.users.ajax.list_worker_activity')}}';

        // Event show bootstrap modal
        $('#modal-list-worker-activity').on('show.bs.modal', function() {
            createDatatable(url);
        });

        // Event hidden bootstrap modal
        $("#modal-list-worker-activity").on("hidden.bs.modal", function () {
            $('#table-earnings').DataTable().destroy();
        });

        // Get total earned
        getTotalEarned();
        // Refresh list worker activity
        var totalEarned = "{{$totalEarned}}";
        $('.js-refresh-list-worker-activity').on('click', function(e){
            e.preventDefault();
            $('.js_form_filter_earnings_popup').trigger('reset');
            getTotalEarned();
            $('#table-earnings').DataTable().ajax.reload();
        })
        $('.js_filter_datatable').on('click', function(e){
            e.preventDefault();
            getTotalEarned();      
            $('#table-earnings').DataTable().destroy();
            createDatatable(url);
        });

        function createDatatable(url){
            $('#table-earnings').DataTable({
                "ajax": {
                    'url': url,
                    'data': function(d) {
                        var frmData = $('.js-fr-filter').serializeArray();
                        $.each(frmData, function(key, val) {
                            d[val.name] = val.value;
                        });
                    }
                },
                "columns": [
                    {"data": "order_id", 'className': 'all'},
                    {"data": "service", 'className': 'all'},
                    {"data": "date", 'className': 'all'},
                    {"data": "duration", 'className': 'all'},
                    {"data": "fee", 'className': 'all'},
                    {"data": "amount", 'className': 'all'},
                    {"data": "amount_tip", 'className': 'all'},
                    {"data": "fee_app", 'className': 'all'},
                    {"data": "total_earned", 'className': 'all'},
                    {"data": "action", 'className': 'all'},
                ],
                "searching": false,
                "bLengthChange": false,
                "processing": true,
                "serverSide": true,
                "language": table_lang,
                "pagingType": "full_numbers",
                "ordering": false,
                "scrollX" : '{{checkIsMobile()}}',
                drawCallback: function(){
                    const observer = lozad(); // lazy loads elements with default selector as ".lozad"
                    observer.observe();
                }
            });   
        }

        function getTotalEarned(){
            var formData = new FormData($('.js_form_filter_earnings_popup')[0]);
            var url_filter_total_earned = '{{route('admin.users.ajax.filter_total_earned')}}';
            $.ajax({
                url : url_filter_total_earned,
                method : 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    if(res.data){
                        $('.js_show_text_activity').text(res.data.text);
                        $('.js_show_total_earninged').text(res.data.total_earned);
                        $('.js_show_total_cash_out').text(res.data.total_cash_out);
                    }
                },
                error: function(err){
                    console.log(err);  
                }
            });
        }
    } );
</script>

@stop