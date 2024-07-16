<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('/vendor/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title>Cash out</title>
    <style>
        .wrap-payout {
            max-width: 600px;
            margin: 30px 0;
            padding: 0 10px;
        }
        .tags-money {
            display: inline-table;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        a.tag-money-item {
            color: #1a1a1a;
            font-size: 14px;
            padding: 8px 15px;
            border: 1px solid #ccc;
            margin-right: 6px;
            border-radius: 3px;
            text-align: center;
            display: inline-block;
            margin-bottom: 12px;
            min-width: 110px;
        }
        a.tag-money-item:hover {
            text-decoration: none;
        }
        .item-checked {
            border: 1px solid #28a745 !important;
            background: #28a745;
            color: #fff !important;
        }
        .bank-item {
            margin-bottom: 15px;
            display: flex;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            align-items:center;
            min-height: 114px;
        }
        .list-banks {
            display: block;
            margin-top: 20px;
        }
        .bank-left-icon {
            display: block;
            width: 25%;
            float: left;
        }
        .bank-right-info {
            display: block;
            width: 70%;
            float: left;
        }
        .bank-left-icon img {
            max-width: 75px;
            margin: 0 auto;
        }
        .bank-right-info p, .form-confirm-payout p {
            margin-bottom: 5px;
        }
        .header {
            position: relative;
            padding: 15px 0;
        }
        .header a {
            color: #fff;
            font-size: 14px;
            font-weight: 400;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            position: absolute;
            right: 0;
        }
        .header h6 {
            margin: 0;
        }
        .btn-show-popup-result {
            margin: 25px 0;
            padding: 8px 40px;
        }
        .bank-item.item-checked-bank {
            border: 2px solid #28a745;
        }
        .btn-add-card {
            margin-top: 20px;
        }
        .select2-container{
            max-width: 100%;
        }
        span.help-block {
            margin: 5px 0;
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #cf0c0c;
        }
        .note {
            display: block;
            margin-bottom: 30px;
        }
        .bank-info {
            display: block;
            margin: 40px 0;
        }
        .bank-info p {
            position: relative;
        }
        .bank-info p span {
            position: absolute;
            right: 0;
            text-align: right;
        }

        .wrap-payout h6 {
            font-size: 16px;
            font-weight: bold;
        }

        .disable-links {
            pointer-events: none;
            background: #ccc !important;
            opacity: 0.5;
        }

        #toast-container > div {
            opacity:1;
        }

        span.password-error {
            font-size: 14px;
            font-weight: 500;
            color: #e60a0a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="wrap-payout">
                    @if(is_null($worker))
                    <h2 class="text-center">Invalid accessToken!</h2>
                    @else
                    <form class="js_form_payout" action="" method="POST" data-url="{{route('admin.confirm_payout')}}">
                        @csrf
                        <input type="hidden" value="{{$firstCard}}" name="card_id" class="js_set_card_id">
                        <input type="hidden" value="{{$worker->id}}" name="user_id">
                        <input type="hidden" value="{{$worker->balance < 100000 ? 0 : 100000}}" name="payout_money" class="js_set_value_money">
                        <input type="hidden" value="{{$worker->balance}}" class="js_get_current_balance">
                        <div class="row header-payout">
                            <div class="col-6 col-sm-6">
                                <h6 for="" class="text-left">
                                    Số dư khả dụng (đ)
                                </h6>
                                <p class="text-left">
                                    <b class="js_set_balance">{{number_format((int)$worker->balance - (int)$pendingBalance, 0, ',', '.')}}</b>
                                </p>
                            </div>
                            <div class="col-6 col-sm-6">
                                <h6 for="" class="text-right">
                                    Số tiền chờ duyệt (đ)
                                </h6>
                                <p class="text-right">
                                    <b class="js_set_pending_balance">{{number_format($pendingBalance, 0, ',', '.')}}</b>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <h6 for="">
                                    Chọn số tiền cần rút
                                </h6>
                                <div class="tags-money js_tags_money_default">
                                    <a class="tag-money-item js_select_money {{((int)$worker->balance - (int)$pendingBalance) < 200000 ? 'disable-links' : ''}}" data-number="200000">
                                        200.000
                                    </a>
                                    <a class="tag-money-item js_select_money {{((int)$worker->balance - (int)$pendingBalance) < 500000 ? 'disable-links' : ''}}" data-number="500000">
                                        500.000
                                    </a>
                                    <a class="tag-money-item js_select_money {{((int)$worker->balance - (int)$pendingBalance) < 1000000 ? 'disable-links' : ''}}" data-number="1000000">
                                        1.000.000
                                    </a>
                                    <a class="tag-money-item js_select_money {{((int)$worker->balance - (int)$pendingBalance) < 2000000 ? 'disable-links' : ''}}" data-number="2000000">
                                        2.000.000
                                    </a>
                                    <a class="tag-money-item js_select_money {{((int)$worker->balance - (int)$pendingBalance) < 5000000 ? 'disable-links' : ''}}" data-number="5000000">
                                        5.000.000
                                    </a>
                                </div>
                                <div class="other-number">
                                    <div class="form-group">
                                        <label for="">
                                            Số khác (đ)
                                        </label>
                                        <input type="text" class="form-control js_input_other_money" placeholder="200.000"></span>
                                    </div>
                                    <span class="help-block number_money-error"></span>
                                    {{-- <p>Số tiền sẽ rút là: <b class="js_total_money">200.000</b></p> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="header">
                                    <h6>
                                        Chọn tài khoản nhận tiền:
                                        <span class="f-right">
                                            <a href="#" class="btn-success" data-toggle="modal" data-target="#modal-add-card"><i class="fa fa-plus" aria-hidden="true"></i> Thêm thẻ</a>
                                        </span>
                                    </h6>
                                </div>
                                <div class="list-banks js_wrap_list_card">
                                    @if(isset($listCards) && count($listCards))
                                        @foreach ($listCards as $key => $card)
                                        @php
                                            $image = !is_null($card->img_url) ? $card->img_url : asset('/assets/images/atm-card.png');
                                        @endphp
                                            <div class="bank-item js_select_bank {{$key == 0 ? 'item-checked-bank' : ''}}" data-card-id="{{$card->id}}">
                                                <div class="bank-left-icon">
                                                    <img src="{{$image}}" alt="">
                                                </div>
                                                <div class="bank-right-info">
                                                    <p> <b>{{$card->code}}</b> - {{$card->bank_name}}</p>
                                                    <p>
                                                        @include('admin.partials.card-hidden', ['cardItem' => $card])
                                                    </p>
                                                    {{-- <p>{{str_replace(substr($card->bank_no, $num, strlen($card->bank_no) - $num), $cardHidden[$key] . substr($card->bank_no, strlen($card->bank_no) - $num, strlen($card->bank_no)), $card->bank_no)}}</p> --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p><b>Danh sách thẻ hiện đang trống, vui lòng thêm thẻ của bạn vào!</b></p>
                                    @endif
                                    <span class="help-block card-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12 text-center">
                                <button type="submit" class="btn btn-success btn-show-popup-result">
                                    Tiếp tục
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- MODALS -->
    <div class="modal fade" id="modal-add-card" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm tài khoản nhận tiền</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Profile -->
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <form class="form-horizontal js_form_worker_addcard" action="" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" value="{{$worker->id ?? null}}" name="user_id">
                                    <input type="hidden" value="{{route('admin.worker.add_card')}}" class="js_get_url_addcard">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Danh sách ngân hàng hỗ trợ</label>
                                            <select class="form-control js_load_select2" name="bank_name">
                                                @foreach ($listBank as $item)
                                                    <option value="{{$item['bin'] .' - ' . $item['short_name'] .' - '. $item['name'] . ' - ' . $item['logo']}}">
                                                        {{$item['short_name'] . ' - ' . $item['name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block bank_name-error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label> Số tài khoản</label>
                                            <input class="form-control" type="number" placeholder="1234123412341234" name="bank_no">
                                            <span class="help-block bank_no-error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label> Tên tài khoản</label>
                                            <input class="form-control" type="text" placeholder="Nhập họ tên" value="{{$worker->name ?? null}}" name="fullname">
                                            <span class="help-block fullname-error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" class="btn btn-success btn-add-card js_add_card">Thêm tài khoản</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- END Profile -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="modal-payout" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận giao dịch rút tiền</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Profile -->
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <form class="form-horizontal js_form_confirm_payout form-confirm-payout" data-url="{{route('admin.worker.cash_out')}}" action="" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="js_content_form_payout"></div>
                                </form>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- END Profile -->
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- END MODALS -->
</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://adminlte.io/themes/v3/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://baohanh.vinaled.com/assets/js/3rd/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function(){
            // Set options toastr
            toastr.options = {
                "closeButton": true,
                "newestOnTop": true,
                "progressBar": true,
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "3000",
                "timeOut": "5000",
                "extendedTimeOut": "3000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            $(document).on('click', '.js_select_money', function(){
                $('.tags-money a').removeClass("item-checked");
                $(this).addClass("item-checked");
                var totalMoney = $(this).attr('data-number').replace(/([-,.€~!@#$%^&*()_+=`{}\[\]\|\\:;'<>])+/g, '');
                $('.js_input_other_money').val('');
                $('.js_set_value_money').val(totalMoney);
                $('.js_total_money').text(totalMoney);
            });
    
            $(document).on('click', '.js_select_bank', function(){
                var cardId = $(this).attr('data-card-id');
                $('.list-banks .bank-item').removeClass("item-checked-bank");
                $(this).addClass("item-checked-bank");
                $('.js_set_card_id').val(cardId);
            });
            $('.js_input_other_money').mask("#.##0" , {reverse: true});
    
            $('.js_input_other_money').on('focus change keyup', function(e){
                $('.tags-money a').removeClass("item-checked");
                $('.js_total_money').text($(this).val());
                $('.js_set_value_money').val($(this).val().replace(/([-,.€~!@#$%^&*()_+=`{}\[\]\|\\:;'<>])+/g, ''));

            })

            // Load select2
            $('.js_load_select2').select2();

            // Handle add card
            $('.js_form_worker_addcard').on('submit', function(e){
                e.preventDefault();
                var url = $('.js_get_url_addcard').val();
                var formData = new FormData($('.js_form_worker_addcard')[0]);

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res){
                        var data = res.data;
                        if(data){
                            $("#modal-add-card").modal('hide');
                            toastr.success("Thêm thẻ thành công!");
                            $('.js_wrap_list_card').html(data.html_list_card);
                            $('.js_set_card_id').val(data.first_card);
                        }
                    },
                    error: function(err){
                        if(err.status === 422){
                            var errors = err.responseJSON;
                            $('.help-block').html('');
                            $.each(errors['errors'], function (index, value) {
                                $('.'+index+'-error').html(value);
                            });
                        } else {
                            toastr.error("Thêm thẻ thất bại, xin vui lòng thử lại!");
                        }  
                    }
                });
            });

            // Handle worker payout
            $('.js_form_payout').on('submit', function(e){
                e.preventDefault();
                var cardId = $('.js_set_card_id').val();
                var numberMoney = $('.js_set_value_money').val().replace(/([-,.€~!@#$%^&*()_+=`{}\[\]\|\\:;'<>])+/g, '');
                var currentBalance = $('.js_get_current_balance').val();
                
                if(!numberMoney || !cardId || parseInt(numberMoney) < 100000 || parseInt(numberMoney) > parseInt(currentBalance)){
                    if(numberMoney == ''){
                        $('.number_money-error').html('Vui lòng chọn hoặc nhập số tiền cần rút.');
                    }

                    if(cardId == ''){
                        $('.card-error').html('Vui lòng chọn 1 thẻ.');
                    }

                    if(parseInt(numberMoney) < 200000){
                        $('.number_money-error').html('Số tiền rút tối thiểu là 200000.');
                    }

                    if(parseInt(numberMoney) > parseInt(currentBalance)) {
                        $('.number_money-error').html('Số tiền bạn muốn rút đã vượt quá số dư khả dụng.');
                    }

                    return false;
                }
                
                $('.number_money-error').html('');
                $('.card-error').html('');

                var url = $(this).attr('data-url');
                var formData = new FormData($('.js_form_payout')[0]);

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res){
                        var data = res.data;
                        if(data){
                            $("#modal-payout").modal('show');
                            $('.js_content_form_payout').html(data.html_content_popup);
                        }
                    },
                    error: function(err){
                        console.log(err);
                    }
                });
            });

            // Handle confirm cashout for worker
            $('.js_form_confirm_payout').on('submit', function(e){
                e.preventDefault();
                var url = $(this).attr('data-url');
                var formData = new FormData($('.js_form_confirm_payout')[0]);
                var btn = $('.js_btn_confirm_cashout');
                btn.text('Đang xử lý...');
                btn.attr('disabled', true);

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res){
                        btn.text('Xác nhận giao dịch');
                        btn.attr('disabled', false);
                        var data = res.data;
                        if(data){
                            $('.password-error').text('');
                            $('.js_tags_money_default').html(data.html_tags_money);
                            $('.js_set_balance').html(data.balance_temp);
                            $('.js_set_pending_balance').html(data.pending_balance);
                            toastr.success("Giao dịch được thực hiện thành công, bạn vui lòng chờ hệ thống xử lý!");

                            setTimeout(() => {
                                $("#modal-payout").modal('hide');
                            }, 2000);
                        }
                    },
                    error: function(err){
                        if(err.status === 422){
                            $.each(err.responseJSON.errors, function(key, messgae){
                                $('.password-error').text(messgae);
                            });
                        } else {
                            toastr.error("Giao dịch thất bại, xin vui lòng thử lại!");
                            $('.password-error').text(err.responseJSON.message);
                        }
                        btn.text('Xác nhận giao dịch');
                        btn.attr('disabled', false);
                    }
                });
            })
        });
    </script>
</html>
