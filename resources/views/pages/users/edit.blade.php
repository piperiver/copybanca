
@extends('layout.default')

@section('content')

<div class="tab-pane" id="tab_1">
    <div class="portlet box main-color">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user-plus"></i>Modificar Usuario {{ $user->nombre }}</div>
            <div class="tools">
                <a href="javascript:;" class="collapse"> </a>
            </div>
        </div>
                                            
        <div class="portlet-body form">
           <!-- BEGIN FORM-->
           {!! Form::open(['route' => array('users.update', $user), 'method' => 'PUT', 'class' => 'horizontal-form']) !!}
                <div class="form-body">
                    <h3 class="form-section">Información Personal</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('nombre', 'Nombres:', ['class' => 'control-label']) !!}
                                {!! Form::text('nombre', $user->nombre, ['class' => 'form-control', 'placeholder' => 'Nombre completos', 'required']) !!}
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('apellido', 'Apellidos:', ['class' => 'control-label'])!!}
                                {!! Form::text('apellido', $user->apellido, ['class' => 'form-control', 'placeholder' => 'Apellidos completos', 'required']) !!}
                            </div>
                        </div>
                        <!--/span-->
                    </div>
                    <!--/row-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cedula', 'Cedula:', ['class' => 'control-label'])!!}
                                {!! Form::text('cedula', $user->cedula, ['class' => 'form-control', 'placeholder' => 'Numero identificacion', 'required']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('sexo', 'Sexo:', ['class' => 'control-label'])!!}
                                {!! Form::select('sexo', ['' => 'Selecciona', 'F' => 'Femenino', 'M' => 'Masculino'], $user->sexo, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <!--/span-->
                    </div>
                    <!--/row-->
                    <div class="row">
                        <div class="col-md-6">
                        	<div class="form-group">
                        		{!! Form::label('fecha_nacimiento', 'Fecha Nacimiento:', ['class' => 'control-label'])!!}
	                    		<div class="input-group" data-date="12-02-2012" data-date-formyears">
                    				{!! Form::text('fecha_nacimiento', $user->fecha_nacimiento, ['class' => 'form-control form-control-inline date-picker', 'placeholder' => '12/08/1999', 'required']) !!}
				                    <span class="input-group-btn">
				                        <button class="btn default" type="button">
				                            <i class="fa fa-calendar"></i>
				                        </button>
				                    </span>
                 			</div>
	                		</div>	
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('email', 'Email:', ['class' => 'control-label'])!!}
                                <div class="input-group">
                                    {!! Form::text('email', $user->email, ['class' => 'form-control', 'placeholder' => 'Email', 'required']) !!} 
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/row-->
                    <h3 class="form-section">Datos Sistema</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('login', 'Login/Usuario:', ['class' => 'control-label'])!!}
                                <div class="input-group">
                                    {!! Form::text('login', $user->login, ['class' => 'form-control', 'placeholder' => 'Login Usuario', 'required']) !!}
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                        	</div>
                       	</div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('password', 'Password:', ['class' => 'control-label'])!!}
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user font-red"></i>
                                    </span>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <!--/row-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('estado', 'Estado:', ['class' => 'control-label'])!!}
                                {!! Form::select('estado', ['' => 'Selecciona', '1' => 'Activo', '0' => 'Inactivo'], $user->estado, ['class' => 'form-control']) !!}
                        	</div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('perfil', 'Perfil:', ['class' => 'control-label'])!!}
                                {!! Form::select('perfil', ['' => 'Selecciona', 'ADM' => 'Administrador', 'OFI' => 'Oficina', 'COM' => 'Comercial', 'FRE' => 'Freelance', 'ALI' => 'Aliado'], $user->perfil, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <!--/span-->
                    </div>
                    
                    <div class="form-actions right">
                        <button type="submit" class="btn green">
                            <i class="fa fa-check"></i>Modificar
                        </button>
                    </div>
            {!! Form::close() !!}
           <!-- END FORM-->
        </div>
    </div>	
</div>
@endsection