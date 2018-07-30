
@section('content')


<html lang="es">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <link rel="shortcut icon" href="favicon.ico" />
        @include('includes.head')
                <link href="{{ asset('assets/pages/css/register.css') }}" rel="stylesheet" type="text/css" />

        </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->

        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}
                        <div class="logo">
                            <img src="{{ asset('assets/pages/img/vtm.png') }}" alt="" /> 
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="Email" class="col-md-4 control-label">Correo electrónico</label>

                            <div class="col-md-6">
                                <input id="Email" name="Email" value="{{ old('Email') }}" type="email" class="form-control" required autofocus>

                                @if ($errors->has('Email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="Celular" class="col-md-4 control-label">Celular</label>

                            <div class="col-md-6">
                                <input id="Celular" name="Celular" value="{{ old('Celular') }}" type="text" class="form-control" required>

                                @if ($errors->has('Celular'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Celular') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="Nombre" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="Nombre" name="Nombre" value="{{ old('Nombre') }}" type="text" class="form-control" required>

                                @if ($errors->has('Nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="Contraseña" class="col-md-4 control-label">Contraseña</label>

                            <div class="col-md-6">
                                <input id="Contraseña" name="Contraseña" type="password" class="form-control" required>

                                @if ($errors->has('Contraseña'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Contraseña') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="Confirmacion" class="col-md-4 control-label">Confirmación Contraseña</label>

                            <div class="col-md-6">
                                <input id="Confirmacion" name="Confirmacion" type="password" class="form-control" required>

                                @if ($errors->has('Confirmacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Confirmacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="text-center">
                                <button type="submit" class="btn red-intense">
                                    Registrarte
                                </button>
                            </div>
                        </div>
                       
                        <div class="create-account">
                            <p>
                                <span id="register-btn" class="uppercase modified"><strong>¡Te Conectamos con lo que quieres!</strong></span>
                            </p>
                        </div>
                    </form>
            <!-- END LOGIN FORM -->
            <script src="{{ asset('js/login.js') }}" type="text/javascript"></script>
        </div>
        <div class="copyright">2017 © BANCARIZATE</div>
        @include('includes.scripts')

    </body>

</html>