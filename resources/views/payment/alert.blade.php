<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$alert_message ?? 'Payment successful'}}</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style type="text/css">

        body
        {
            background:#f2f2f2;
        }

        .payment
        {
            border:1px solid #f2f2f2;
            height:auto;
            border-radius:20px;
            background:#fff;
            margin-top: 100px
        }
        .payment_header
        {
            background:rgba(255,102,0,1);
            padding:20px;
            border-radius:20px 20px 0px 0px;
            
        }
        
        .check
        {
            margin:0px auto;
            width:50px;
            height:50px;
            border-radius:100%;
            background:#fff;
            text-align:center;
        }
        
        .check i
        {
            vertical-align:middle;
            line-height:50px;
            font-size:30px;
        }

        .content 
        {
            text-align:center;
            padding-bottom: 25px;
        }

        .content  h1
        {
            font-size:25px;
            padding: 25px 15px 0 15px
        }

        .content a
        {
            width:200px;
            height:35px;
            color:#fff;
            border-radius:30px;
            padding:5px 10px;
            background:rgba(255,102,0,1);
            transition:all ease-in-out 0.3s;
            margin-bottom: 40px !important;
        }

        .content a:hover
        {
            text-decoration:none;
            background:#000;
        }

        .icon_red {
            color: #e80d0d;
        }
        .icon_green {
            color: #0a910a;
        }    
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
           <div class="col-md-6 mx-auto mt-5">
              <div class="payment">
                 <div class="payment_header">
                    <div class="check">{!! $icon ?? '<i class="fa fa-check icon_green" aria-hidden="true"></i>' !!}</div>
                 </div>
                 <div class="content">  
                    <h1>{!!$alert_message ?? 'Payment successful!' !!}</h1>
                    {{-- @if($resultCd == 'PG_ER1')
                        <a href="{{$linkPaymentWithATM}}" style="display: block; margin:20px auto">Back to payment</a>
                    @else
                        <a href="#" style="display: block; margin:20px auto">Back to app</a>
                    @endif --}}
                 </div>
              </div>
           </div>
        </div>
     </div>
     <script>
        var orderId = "{{request()->get('order_id')}}";
        var confirmPayment = "{{request()->get('confirm_payment')}}";
        var paymentIntentId = "{{request()->get('payment_intent')}}";
        var requestPaymentData = "{{request()->get('request_payment_data')}}";
        var url = "{{route('admin.payment.handle_payment_done_3ds')}}";
        if(orderId && confirmPayment == 'succeeded' && paymentIntentId){
            $.ajax({
                url: url,
                method: "POST",
                data: {
                    order_id: orderId,
                    payment_intent_id: paymentIntentId,
                    request_payment_data: requestPaymentData
                },
                success: function(res){
                    if(res.data){
                    }
                },
                error: function(err){
                }
            });
        }
     </script>
</body>
</html>