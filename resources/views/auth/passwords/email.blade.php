@section('content')
<html lang="es">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <link rel="shortcut icon" href="favicon.ico" />
        @include('includes.head')
        <link href="{{ asset('assets/pages/css/login.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
                 
        </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGIN -->
        <div class="content">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- BEGIN FORGOT PASSWORD FORM -->
            <form class="login-form" role="form" method="POST" action="{{ url('/password/email') }}">
                {{ csrf_field() }}
                <h3 class="font-green">¿Olvidaste tu Contraseña?</h3>
                <p> Ingresa tu direccion de correo para enviarte un link con el cual podras restablecer tu contraseña. </p>
                <div class="form-group">
                    <input class="form-control placeholder-no-fix" type="email" placeholder="Email" name="email" id="email" required/> 
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn red-intense uppercase pull-right">Enviar</button>
                </div>
            </form>
         </div>
        <div class="copyright">2017 © BANCARIZATE</div>
        @include('includes.scripts')
        <script src="/js/app.js"></script>
        <script>
            function redirect(){
                window.locationf('https://vtm.dev:8080/login');
            }
        </script>
    </body>
</html>