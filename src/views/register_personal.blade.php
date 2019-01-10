<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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

        <p class='login-box-msg'>Fill the following fields with your information.</p>
        <form autocomplete='off' action="{{ route('postRegisterPersonal') }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <div class="form-group">
                <select name="salutation" id="salutation" class="form-control" required>
                    <option value="">-- Salutation --</option>
                    <option value="Prof." {{ old('salutation') == 'Prof.' ? "selected" : "" }}>Prof.</option>
                    <option value="Dr." {{ old('salutation') == 'Dr.' ? "selected" : "" }}>Dr.</option>
                    <option value="Mr." {{ old('salutation') == 'Mr.' ? "selected" : "" }}>Mr.</option>
                    <option value="Ms." {{ old('salutation') == 'Ms.' ? "selected" : "" }}>Ms.</option>
                </select>
            </div>
            <div class="form-group has-feedback">
                <input autocomplete='off' type="text" class="form-control" name='name' value="{{ old('name') }}" required placeholder="Full Name"/>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>            
            
            <div class="form-group">
                <textarea name="institution" id="institution" rows="3" class="form-control" placeholder="Institution">{{ old('institution') }}</textarea>
            </div>
            
            <div class="form-group">
              <select name="participation" id="participation" class="form-control">
                <option value="">-- Your Participation --</option>
                <option value="Presenter" {{ old('participation') == 'Presenter' ? "selected" : "" }}>Presenter</option>
                <option value="Non Presenter" {{ old('participation') == 'Non Presenter' ? "selected" : "" }}>Non Presenter</option>
              </select>
            </div>

            <div class="form-group has-feedback">
              <input autocomplete='off' type="text" class="form-control" name='street' value="{{ old('street') }}" required placeholder="Street"/>
              <span class="glyphicon glyphicon-road form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
              <input autocomplete='off' type="text" class="form-control" name='city' value="{{ old('city') }}" required placeholder="City"/>
              <span class="glyphicon glyphicon-road form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
              <input autocomplete='off' type="text" class="form-control" name='zip_code' value="{{ old('zip_code') }}" required placeholder="Zip Code"/>
              <span class="glyphicon glyphicon-road form-control-feedback"></span>
            </div>

            <div class="form-group">
              <select name="id_country" id="id_country" class="form-control">
                <option value="">-- Select Country --</option>
                @foreach ($countries as $country)
                  <option value="{{$country->ID}}" {{ old('id_country') == $country->ID ? "selected" : "" }}>{{$country->name.' ('.$country->code.')'}}</option>
                @endforeach
              </select>
              
            </div>
            <div class="form-group has-feedback">
                <input autocomplete='off' type="text" class="form-control" name='phone' value="{{ old('phone') }}" required placeholder="Phone"/>
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input autocomplete='off' type="text" class="form-control" name='email' value="{{ old('email') }}" required placeholder="Email"/>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>            
            <div class="form-group has-feedback">
                <input autocomplete='off' type="password" class="form-control" name='password' value="{{ old('password') }}" required placeholder="Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input autocomplete='off' type="password" class="form-control" name='password_confirmation' value="{{ old('password_confirmation') }}" required placeholder="Confirm Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div style="margin-bottom:10px" class='row'>
                <div class='col-xs-12'>
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><i class='fa fa-lock'></i> Register</button>
                    <a href="{{route("getRegister")}}" class="btn btn-block btn-warning btn-flat"><i class="fa fa-arrow-left"></i> Cancel</a>
                </div>
            </div>
        </form>
        <br/>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->


<!-- jQuery 2.1.3 -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
</body>
</html>