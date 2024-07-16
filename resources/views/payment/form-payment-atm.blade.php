<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <title>Payment order</title>
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Css 3rd -->
        <link rel="stylesheet" href="{{config('constant.vnpt.css_link')}}" type="text/css" media="screen"/>
        <!-- Js 3rd -->
        <script type="text/javascript" src="{{config('constant.vnpt.js_link')}}"></script>

        <script type="text/javascript">
            $(function () {
                //Open form init payment
                var domain = "{{config('constant.vnpt.domain')}}";
                openPayment(1, domain);
                window.addEventListener('message', function (e) {
                    if (e.data.closeLayer === 'close') {
                        window.history.back();
                    }
                });
            });
        </script>
    </head>
    <body>
        <form id="megapayForm" name="megapayForm" method="POST">
            <input type="hidden" name="invoiceNo" value="{{$invoiceNo}}">
            <input type="hidden" name="amount" value="{{$amount}}">
            <input type="hidden" name="currency" value="VND">
            <input type="hidden" name="goodsNm" value="{{$goodsNm}}">
            <input type="hidden" name="fee" value="">
            <input type="hidden" name="buyerFirstNm" value="{{$firstName}}">
            <input type="hidden" name="buyerLastNm" value="{{$lastName}}">
            <input type="hidden" name="buyerPhone" value="{{$phone}}">
            <input type="hidden" name="buyerEmail" value="{{$email}}">
            <input type="hidden" name="callBackUrl" value="{{route('admin.payment.callback_payment_with_atm')}}">
            <input type="hidden" name="notiUrl" value="{{$notiUrl}}">
            <input type="hidden" name="merId" value="{{$merId}}">
            <input type="hidden" name="userId" value="{{$userId}}"> <!-- Use for update payment_logs after payment done -->
            <input type="hidden" name="reqDomain" value="{{url('')}}">
            <input type="hidden" name="userLanguage" value="VN">
            <input type="hidden" name="merchantToken" value="{{$merchantToken}}">
            <input type="hidden" name="payToken" id="payToken" value="{{$encryptedPayToken}}">
            <input type="hidden" name="timeStamp" value="{{$timeStamp}}">
            <input type="hidden" name="merTrxId" value="{{$merTrxId}}">
            <input type="hidden" name="windowType" value="1">
            <input type="hidden" name="windowColor" value="#89C4EC">
            <input type="hidden" name="payType" value="{{$payType}}">
            <input type="hidden" name="payOption" value="{{$payOption}}">
            <input type="hidden" name="description" value="User payment order #{{$orderId}}">
        </form>
    </body>
</html>

