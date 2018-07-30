
@section('content')


<html lang="es">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <link rel="shortcut icon" href="favicon.ico" />
        @include('includes.head')
                <link href="{{ asset('assets/pages/css/login.min.css') }}" rel="stylesheet" type="text/css" />

        </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->

        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <div class="content-children">
            <!-- BEGIN LOGIN FORM -->
            <form                  class="login-form" role="form" method="POST" action="{{ url('/Iniciar') }}">
            {{ csrf_field() }}
            <div class="logo">
                <img src="{{ asset('img/logosistema.png') }}" alt="" />
            </div>
            @if($errors->any())
            <div class="alert alert-danger" style="display: block;">
                    <button class="close" data-close="alert"></button>
                    <span> {{ $errors->first() }} </span>
            </div>
            @endif
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span>El nombre de usuario que has introducido no pertenece a ninguna cuenta. Comprueba tu nombre de usuario y vuelve a intentarlo.</span>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label  for="email" class="text-input"><span class="glyphicon glyphicon-user"></span><span class="contenido"> Email</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus/> 
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="text-input"><span class="glyphicon glyphicon-lock"></span><span class="contenido"> Contraseña</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="password" type="password" name="password" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                             <strong>{{ $errors->first('password') }}</strong>
                         </span>
                    @endif 
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn main-color uppercase redondo">Ingresar</button>
                    <a href="{{ url('/register') }}" class="btn white uppercase registrarme redondo">Registrarme</a>
                </div>                
            </form>
            <!-- END LOGIN FORM -->
            <!-- BEGIN FORGOT PASSWORD FORM -->
            @if (session('status'))
                        <div class="alert alert-success">
                        <button class="close" data-close="alert"></button>
                            {{ session('status') }}
                        </div>
                    @endif
            <form class="forget-form" role="form" method="POST" action="{{ url('/password/email') }}">
                {{ csrf_field() }}
                <h3 class="font-green">¿Olvidast tu Contraseña?</h3>
                <p> Ingresa tu dirección de correo para enviarte un link con el cual podr&aacute;s restablecer tu contraseña. </p>
                <div class="form-group">
                    <input class="form-control placeholder-no-fix" type="email" placeholder="Email" name="email" id="email" required/> 
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>
                <div class="form-actions">
                    <input type="submit" class="btn btn-success uppercase pull-right" value="Enviar">
                </div>
            </form>
            <!-- END FORGOT PASSWORD FORM -->
            <div>
        </div>
        
        @include('includes.scripts')

    </body>

</html>