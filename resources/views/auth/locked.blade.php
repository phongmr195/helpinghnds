<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AdminLTE 3 | Lockscreen</title>
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="/vendor/fontawesome-free/css/all.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css">
        <style>
            .has-error .invalid-feedback{
                display: block;
            }
            .lockscreen-logo img {
                max-width: 50%;
            }
        </style>
    </head>
    <body class="hold-transition lockscreen">
        <!-- Automatic element centering -->
        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <img src="{{asset('/assets/images/logo.png')}}" alt="">
            </div>
            <!-- User name -->
            <div class="lockscreen-name"><span style="text-transform: capitalize"><i class="fas fa-user-lock"></i> {{Auth::user()->name}}</span></div>
            <!-- START LOCK SCREEN ITEM -->
            <div class="lockscreen-item">
                <!-- lockscreen image -->
                <div class="lockscreen-image">
                    <i class="fas fa-user-circle fa-5x"></i>
                </div>
                <!-- /.lockscreen-image -->
                <!-- lockscreen credentials (contains the form) -->
                <form class="lockscreen-credentials" method="POST" action="{{route('unlock')}}">
                    @csrf
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Enter password" name="password">
                        <div class="input-group-append">
                            <button type="submit" class="btn">
                            <i class="fas fa-arrow-right text-muted"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <!-- /.lockscreen credentials -->
            </div>
            @if($errors->any())
                <div class="text-center has-error">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first() }}</strong>
                    </span>
                </div>
            @endif

            <div class="text-center">
                <a href="javascript:void(0)" style="color:#1a1a1a" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout <i class="fas fa-sign-out-alt"></i></a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
            <!-- /.lockscreen-item -->
        </div>
        <!-- /.center -->
    </body>
</html>