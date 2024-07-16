<!DOCTYPE html>
<html lang="en" style="height: 100%">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ASSIST - Send sms to phone number</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" />
    <style>
        #recaptcha-container div div {
            display: block;
            margin: 0 auto;
        }

        .bt_code_received,
        .bt_resend_code {
            width: 100%;
        }

        .wrap_button {
            display: none;
            text-align: center;
            padding-top: 15px;
            width: 100%;
        }

        .wrap_button p {
            margin: 10px 0;
        }

        p.text-confirm-send-code {
            text-align: center;
            padding-top: 10px;
        }

        .loading_icon {
            position: relative;
            width: 100%;
            display: none;
            height: 80px;
        }

        .lds-ellipsis {
            display: inline-block;
            position: absolute;
            width: 80px;
            height: 80px;
            top: 50%;
            left: 50%;
            margin-left: -40px;
            margin-top: -70px;
        }

        .lds-ellipsis div {
            position: absolute;
            top: 33px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #aaa;
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }

        .lds-ellipsis div:nth-child(1) {
            left: 8px;
            animation: lds-ellipsis1 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(2) {
            left: 8px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(3) {
            left: 32px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        .lds-ellipsis div:nth-child(4) {
            left: 56px;
            animation: lds-ellipsis3 0.6s infinite;
        }

        @keyframes lds-ellipsis1 {
            0% {
                transform: scale(0);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes lds-ellipsis3 {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(0);
            }
        }

        @keyframes lds-ellipsis2 {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(24px, 0);
            }
        }
    </style>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
</head>

<body style="height: 100%">
    <div class="container" style="height: 100%; display:table">
        <div class="row" style="display: table-cell; vertical-align: middle">
            <div class="col-sm-12">
                <form action="" class="js_form_send_otp">
                    @csrf
                    <div id="recaptcha-container" style="margin-top: 10px"></div>
                    <input type="hidden" id="phone_number" class="js_get_phone_value" name="phone" value="+{{request()->get('phone')}}">
                    <input type="hidden" name="type" value="{{request()->get('type') ?? 'USER_GET_OTP'}}">
                    <input type="hidden" class="js_get_recaptcha_token" name="recaptcha_token" value="">
                    <input type="hidden" class="js_get_url_send_otp" value="{{route('api.users.web.send-otp')}}">
                    <!-- <p class="text-confirm-send-code js_text_confirm_send_code">Please confirm checkbox to send the code!</p> -->
                    <div class="loading_icon mt-2">
                        <div class="lds-ellipsis">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <!-- <p class="js_show_mess_success text-success text-center" style="padding-top: 10px"></p> -->
                    <div class="wrap_button">
                        <div class="js_show_mess_success text-success text-center">
                            <i class="far fa-check-circle fa-6x"></i>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <hr>
                            </div>
                            <div class="col-auto">OR</div>
                            <div class="col">
                                <hr>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger bt_resend_code js_resend_code" disabled>
                            Resend OTP
                            <b id="countdown" class="text-primary">({{config('constant.firebase.time_resend_code')}})</b>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-analytics.js"></script>
    <script src="{{asset('/assets/js/firebase.js')}}"></script>
    <script src="{{asset('/assets/js/firebase-connection.js')}}"></script>
    <script type="text/javascript">
        var timeleft = parseInt("{{config('constant.firebase.time_resend_code')}}");
        window.onload = function() {
            // Render recaptcha
            renderRecaptcha();
        }

        function renderRecaptcha() {
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'callback': (recaptchaToken) => {
                    $('.js_send_otp').css({
                        'opacity': '1',
                        'pointer-events': 'inherit'
                    });
                    $('.js_get_recaptcha_token').val(recaptchaToken);
                    var url = $('.js_get_url_send_otp').val();
                    var icon = $('.loading_icon');
                    icon.css({
                        'display': 'block'
                    });
                    // Setup ajax header
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                    });
                    var formData = new FormData($('.js_form_send_otp')[0]);
                    // Handle send sms
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            icon.css({
                                'display': 'none'
                            });
                            if (res) {
                                $('.js_send_otp').remove();
                                $('.wrap_button').css({
                                    'display': 'inline-block',
                                });
                                $('.js_text_confirm_send_code').remove();
                                $('#recaptcha-container').remove();
                                var downloadTimer = setInterval(function() {
                                    if (timeleft <= 0) {
                                        clearInterval(downloadTimer);
                                        $('.js_resend_code').prop('disabled', false);
                                    } else {
                                        $("#countdown").html('(' + timeleft + ')');
                                    }
                                    timeleft -= 1;
                                }, 1000);
                            }
                        },
                        error: function(err) {
                            icon.css({
                                'display': 'none'
                            });
                            console.log(err);
                        }
                    });
                }
            }).render();
        }
        $('.js_resend_code').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    </script>
</body>

</html>