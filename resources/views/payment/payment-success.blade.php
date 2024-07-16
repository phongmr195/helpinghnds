<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thank you</title>
    <link rel="stylesheet" href="{{asset('/vendor/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
    </style>
</head>

<body>
    <div class="wrap-page js-show-result">
        <h2><?= $message ?></h2>
    </div>
</body>
<script>

</script>

</html>