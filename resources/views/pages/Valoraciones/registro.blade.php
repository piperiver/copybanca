<html lang="es">
    <head>
        <link rel="shortcut icon" href="favicon.ico" />
    </head>
    <body class=" login">
        <div class="content">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">Correo electrónico</label>
                    <div class="col-md-6">
                        <input id="Email" type="email" class="form-control" name="Email" required autofocus>
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
                        <input id="Celular" type="text" class="form-control" name="Celular" required>
                        @if ($errors->has('Celular'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Celular') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="text-center">
                        <a class="btn btn-primary" href="login">
                            Iniciar Sesión
                        </a>
                        <button type="submit" class="btn red-intense">Registrarte</button>   
                    </div>
                </div>     
                <div class="create-account">
                    <p>
                        <span id="register-btn" class="uppercase modified"><strong>¡Te Conectamos con lo que quieres!</strong></span>
                    </p>
                </div>
            </form>
        </div>
    </body>
</html>