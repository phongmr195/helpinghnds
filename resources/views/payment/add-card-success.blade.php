<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notify add card</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .wrap-page {
            max-width: 600px;
            display: block;
            margin: 100px auto
        }

        .wrap-page img {
            max-width: 396px;
            display: block;
            margin: 0 auto;
            width: 100%;
        }

        p.title i {
            font-size: 30px;
        }

        .wrap-img {
            position: relative;
        }

        .text-center {
            text-align: center
        }

        p.title {
            font-size: 18px;
            font-family: sans-serif;
            margin: 0;
            font-weight: 400;
            margin-top: 15px
        }

        a.btn.btn-back {
            display: block;
            margin: 20px auto;
            padding: 12px 40px;
            background: #309930;
            border-radius: 4px;
            border: 0;
            color: #fff;
            font-size: 16px;
            font-weight: 400;
            margin-top: 40px;
            text-decoration: none;
            text-align: center;
            max-width: 150px;
            font-family: sans-serif;
        }

        .wrap-img i {
            font-size: 30px;
            position: absolute;
            left: 50%;
            bottom: 0;
            margin-left: -15px;
        }

        .icon_red {
             color: #eb0e0e !important;
        }

        .icon_success {
            color: #0aaa45;
        }

        .icon_orange {
            color: orange;
        }

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
        }

        .content img {
            max-width: 70%;
            margin: 35px 0;
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
           <div class="col-md-6 mx-auto mt-5 js-show-result">
                <div class="payment">
                    <div class="payment_header">
                        <div class="check">{!! $iconClass !!}</div>
                    </div>
                    <div class="content">
                        <img src="{{asset('/assets/images/card-success.png')}}" alt=""> 
                        <h1><?= $title ?></h1>
                        <a href="#" style="display: block; margin:20px auto">Back to payment</a>
                    </div>
                </div>
           </div>
        </div>
     </div>
</body>
<script>
    var resultCd = "{{request()->resultCd}}";
    var userId = "{{request()->userId}}";
    var invoiceNo = "{{request()->invoiceNo}}";
    var payToken = "{{request()->payToken}}";
    var merId = "{{request()->merId}}";
    var bankName = "{{request()->bankName}}";
    var cardNo = "{{request()->cardNo}}";
    var payType = "{{request()->payType}}";
    var cardType = "{{request()->cardType}}";
    var trxId = "{{request()->trxId}}";
    var amount = "{{request()->amount}}";
    var merTrxId = "{{request()->merTrxId}}";
    var timeStamp = "{{request()->timeStamp}}";

    if(resultCd == '00_000'){
        $.ajax({
            url : "{{route('notify.payment_verify_addcard')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method : "POST",
            data : {
                'userId' : userId,
                'invoiceNo': invoiceNo,
                'payToken': payToken,
                'merId': merId,
                'bankName': bankName,
                'cardNo': cardNo,
                'resultCd': resultCd,
                'payType': payType,
                'cardType': cardType,
                'trxId' : trxId,
                'amount': amount,
                'merTrxId': merTrxId,
                'timeStamp': timeStamp
            },
            success: function(res){
                if(res.data.status){
                    $('.js-show-result').html(res.data.html_result);
                }
            },
            error: function(err){
                console.log(err);
            }
        });
    }
</script>
</html>