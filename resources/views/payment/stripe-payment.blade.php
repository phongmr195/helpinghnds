<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe payment</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .wrap-form-contact {
            display: block;
            max-width: 600px;
            margin: 20px auto;
        }
        
        #payment-form {
            margin-top: 40px;
        }

        #payment-form p {
            margin-bottom: 0;
        }

        p.error-messgae {
            font-size: 14px;
            color: #de0c0c;
            font-weight: 600;
        }

        p.success-messgae {
            font-size: 16px;
            font-weight: 600;
            color: green;
            text-align: center;
            padding-top: 15px;
        }

        button.btn.btn-add-card {
            border: 1px solid #eee;
            margin-top: 20px;
        }

        /* Variables */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            display: flex;
            justify-content: center;
            align-content: center;
            height: 100vh;
            width: 100vw;
        }

        form {
            width: 30vw;
            min-width: 500px;
            align-self: center;
            box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1),
                0px 2px 5px 0px rgba(50, 50, 93, 0.1),
                0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
            border-radius: 7px;
            padding: 40px 20px;
        }

        .hidden {
            display: none;
        }

        #payment-message {
            color: rgb(105, 115, 134);
            font-size: 16px;
            line-height: 20px;
            padding-top: 12px;
            text-align: center;
        }

        #payment-element {
            margin-bottom: 24px;
        }

        /* Buttons and links */
        button {
            background: #5469d4;
            font-family: Arial, sans-serif;
            color: #ffffff;
            border-radius: 4px;
            border: 0;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            transition: all 0.2s ease;
            box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
            width: 100%;
        }
        button:hover {
            filter: contrast(115%);
        }
        button:disabled {
            opacity: 0.5;
            cursor: default;
        }

        /* spinner/processing state, errors */
        .spinner,
        .spinner:before,
        .spinner:after {
            border-radius: 50%;
        }
        .spinner {
            color: #ffffff;
            font-size: 22px;
            text-indent: -99999px;
            margin: 0px auto;
            position: relative;
            width: 20px;
            height: 20px;
            box-shadow: inset 0 0 0 2px;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
        }
        .spinner:before,
        .spinner:after {
            position: absolute;
            content: "";
        }
        .spinner:before {
            width: 10.4px;
            height: 20.4px;
            background: #5469d4;
            border-radius: 20.4px 0 0 20.4px;
            top: -0.2px;
            left: -0.2px;
            -webkit-transform-origin: 10.4px 10.2px;
            transform-origin: 10.4px 10.2px;
            -webkit-animation: loading 2s infinite ease 1.5s;
            animation: loading 2s infinite ease 1.5s;
        }
        .spinner:after {
            width: 10.4px;
            height: 10.2px;
            background: #5469d4;
            border-radius: 0 10.2px 10.2px 0;
            top: -0.1px;
            left: 10.2px;
            -webkit-transform-origin: 0px 10.2px;
            transform-origin: 0px 10.2px;
            -webkit-animation: loading 2s infinite ease;
            animation: loading 2s infinite ease;
        }

        .logo-stripe img {
            max-width: 100%;
            width: 80px;
            display: block;
            margin: 0 auto;
            margin-top: 15px;
        }

        .form-control {
            border-radius: 0;
        }

        #toast-container > div {
            opacity:1;
        }

        button.btn-back-to-app {
            background: #03ba03;
        }
        @-webkit-keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @media only screen and (max-width: 600px) {
            form {
                width: 100%;
                min-width: initial;
            }
        }


    </style>
</head>

<body>
    <div class="container">
        <form id="payment-form">
            <div class="wrap-form-add-card js_show_form_addcard">
                <div class="row">
                    <div class="col-12">
                        <p style="margin-bottom: 10px">
                            <b>Amount to pay for the order:</b> <i class="fa fa-usd" aria-hidden="true"></i> {{number_format($amount, 0, ',', '.')}}
                        </p>
                    </div>
                </div>
                <label>Name on Card</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                    </div>
                    <input type="text" class="form-control" id="name-on-card" placeholder="Name on card">
                    <div class="input-group-append">
                    </div>
                </div>
                <label>Card Number</label>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                    </div>
                    <span id="card-number" class="form-control">
                        <!-- Stripe Card Element -->
                    </span>
                    <div class="input-group-append">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6" style="padding-right: 5px">
                        <label>CVC</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                            </div>
                            <span id="card-cvc" class="form-control">
                                <!-- Stripe CVC Element -->
                            </span>
                        </div>
                    </div>
                    <div class="col-6" style="padding-left: 5px">
                        <label>Expiration Date</label>
                        <div class="input-group mb-2">
                            <span id="card-exp" class="form-control">
                                <!-- Stripe Card Expiry Element -->
                            </span>
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <p class="error-messgae js_show_error_message"></p>
                    </div>
                </div>
                <button type="submit" style="margin-top: 25px" class="js_bt_add_card">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Pay now</span>
                </button>
            </div>
            <div class="wrap-bt-add-more-and-back-to-app" style="display: none">
                <button type="button" style="margin-top: 25px" class="btn-back-to-app">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> Back to app</span>
                </button>
            </div>
            <div class="logo-stripe">
                <img src="{{asset('/assets/images/stripe-logo.png')}}" alt="">
            </div>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // Create a Stripe client
            const stripeKey = '{{config('constant.stripe.key_client')}}';
            var stripe = Stripe(stripeKey);

            // Create an instance of Elements
            var elements = stripe.elements();

            // Try to match bootstrap 4 styling
            var style = {
                base: {
                    'fontSize': '15px',
                    'color': '#495057',
                    'fontFamily': 'apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif'
                }
            };

            // Card number
            var cardNumber = elements.create('cardNumber', {
                'placeholder': '0000 0000 0000 0000',
                'style': style,
                showIcon: true,
            });
            cardNumber.mount('#card-number');

            // CVC
            var cardCvc = elements.create('cardCvc', {
                'placeholder': 'CVC',
                'style': style,
            });
            cardCvc.mount('#card-cvc');

            // Card expiry
            var cardExp = elements.create('cardExpiry', {
                'placeholder': 'MM / YY',
                'style': style
            });
            cardExp.mount('#card-exp');

            // Submit
            var cardHolderName = document.getElementById('name-on-card');
            var form = document.getElementById('payment-form');
            var url = "{{route('admin.payment.stripe.pay')}}";
            var userId = "{{$userId}}";
            var buttonAddCard = $('.js_bt_add_card');
            var orderId = '{{$orderId}}';
            var amount = '{{$amount}}';

            form.addEventListener('submit', function(event) {
                buttonAddCard.prop('disabled', true);
                buttonAddCard.text('Processing...');
                event.preventDefault();
                stripe.createToken(cardNumber, {
                    name: cardHolderName.value
                }).then(function(result) {
                    if (result.error) {
                        // handle error
                        buttonAddCard.prop('disabled', false);
                        buttonAddCard.text('Pay now');
                        $('.js_show_error_message').text(result.error.message);
                    } else {
                        // Gửi token đến máy chủ để xử lý thanh toán
                        $.ajax({
                            url : url,
                            method : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            data: JSON.stringify({
                                card_holder_name: cardHolderName.value,
                                stripe_token: result.token.id,
                                user_id: userId,
                                order_id: orderId,
                                amount: amount
                            }),
                            success: function(res){
                                buttonAddCard.prop('disabled', false);
                                buttonAddCard.text('Pay now');
                                $('#name-on-card').val('');
                                cardNumber.clear();
                                cardCvc.clear();
                                cardExp.clear();
                                $('.js_show_error_message').text('');
                                $('.js_show_form_addcard').css({'display': 'none'});
                                $('.wrap-bt-add-more-and-back-to-app').css({'display': 'block'});
                                toastr.success(res.message);
                            },
                            error: function(err){
                                $('#name-on-card').val('');
                                cardNumber.clear();
                                cardCvc.clear();
                                cardExp.clear();
                                buttonAddCard.prop('disabled', false);
                                buttonAddCard.text('Pay now');
                                toastr.warning(err.responseJSON.message);
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>