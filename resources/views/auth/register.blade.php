
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
            <form                  class="login-form" role="form" method="POST" action="{{ url('/register') }}">
            {{ csrf_field() }}
            <div class="logo">
                <img src="{{ asset('/assets/layouts/layout5/img/logosistema.png') }}" alt="" />
                <div class="title">Te conecta con lo que <span class="rojo">quieres</span></div>
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
                    <label for="Email" class="text-input"><span class="glyphicon glyphicon-envelope"></span><span class="contenido"> Email</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="Email" type="email" name="Email" value="{{ old('email') }}" required autofocus/> 
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                 <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label for="Celular" class="text-input"><span class="glyphicon glyphicon-phone"></span><span class="contenido"> Celular</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="Celular" type="text" name="Celular" value="{{ old('email') }}" required autofocus/> 
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                 <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label for="Nombre" class="text-input"><span class="glyphicon glyphicon-user"></span><span class="contenido"> Nombre</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="Nombre" type="text" name="Nombre" value="{{ old('email') }}" required autofocus/> 
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="Contraseña" class="text-input"><span class="glyphicon glyphicon-lock"></span><span class="contenido"> Contraseña</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="Contraseña" type="password" name="Contraseña" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                             <strong>{{ $errors->first('password') }}</strong>
                         </span>
                    @endif 
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="Confirmacion" class="text-input"><span class="glyphicon glyphicon-lock"></span><span class="contenido">Confirmación</span></label>
                    <input class="form-control form-control-solid placeholder-no-fix input" id="Confirmacion" type="password" name="Confirmacion" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                             <strong>{{ $errors->first('password') }}</strong>
                         </span>
                    @endif 
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn red-intense uppercase redondo">Registrarme</button>                    
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
            <form class="forget-form" role="form" method="POST" action="{{ url('/register') }}">
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
            <script src="{{ asset('js/login.js') }}" type="text/javascript"></script>
            <!-- END FORGOT PASSWORD FORM -->
            <div>
        </div>
        
        @include('includes.scripts')

    </body>

</html>