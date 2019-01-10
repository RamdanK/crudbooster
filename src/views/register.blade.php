<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{trans("crudbooster.page_title_login")}} : {{Session::get('appname')}}</title>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon"
          href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>

    <!-- support rtl-->
    @if (in_array(App::getLocale(), ['ar', 'fa']))
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ asset("vendor/crudbooster/assets/rtl.css")}}" rel="stylesheet" type="text/css"/>
@endif

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css")}}'/>
    <style type="text/css">
        .login-page, .register-page {
            background: {{ CRUDBooster::getSetting("login_background_color")?:'#dddddd'}} url('{{ CRUDBooster::getSetting("login_background_image")?asset(CRUDBooster::getSetting("login_background_image")):asset('vendor/crudbooster/assets/bg_blur3.jpg') }}');
            color: {{ CRUDBooster::getSetting("login_font_color")?:'#ffffff' }}  !important;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .login-box, .register-box {
            margin: 2% auto;
        }

        .login-box-body, .register-box-body {
            box-shadow: 0px 0px 50px rgba(0, 0, 0, 0.8);
            background: rgba(255, 255, 255, 0.9);
            color: {{ CRUDBooster::getSetting("login_font_color")?:'#666666' }}  !important;
        }

        html, body {
            overflow: hidden;
        }
    </style>
</head>

<body class="register-page">

<div class="register-box">
    <div class="register-logo">
        <a href="{{url('/')}}">
            <img title='{!!(Session::get('appname') == 'CRUDBooster')?"<b>CRUD</b>Booster":CRUDBooster::getSetting('appname')!!}'
                 src='{{ CRUDBooster::getSetting("logo")?asset(CRUDBooster::getSetting('logo')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}'
                 style='max-width: 100%;max-height:170px'/>
        </a>
    </div><!-- /.login-logo -->
    <div class="register-box-body">

        @if ( Session::get('message') != '' )
            <div class='alert alert-warning'>
                {{ Session::get('message') }}
            </div>
        @endif

        <p class='login-box-msg'>{{trans("crudbooster.register_type")}}</p>
          <div class="text-center">
            <a href="{{route("getRegisterPersonal")}}" class="btn btn-app bg-yellow"><i class="fa fa-user"></i> Register as Personal</a>
            <a href="{{route("getRegisterGroup")}}" class="btn btn-app bg-light-blue"><i class="fa fa-users"></i> Register as Group</a>
          </div>
          <div class="row text-center">
                <a href="{{route("getLogin")}}"><i class="fa fa-arrow-left"></i> Back to Login</a>
          </div>
        <br/>
        <!--a href="#">I forgot my password</a-->

    </div><!-- /.login-box-body -->

</div><!-- /.login-box -->


<!-- jQuery 2.1.3 -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
</body>
</html>