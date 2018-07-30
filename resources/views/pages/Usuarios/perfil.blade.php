@extends('layout.default')
@section('encabezado')
    <link href="{{asset('assets/pages/css/profile.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}}" rel="stylesheet" type="text/css" />
@endsection
@inject('fotoPerfil', 'App\Http\Controllers\UsersController')
@section('content')
@include('flash::message')
<div class="row">
    <div class="col-md-12">
        <div class="profile-sidebar">
            <div class="portlet light profile-sidebar-portlet bordered">
                <div class="profile-userpic">
                    <img src="{{asset('fotosperfiles')}}/{{$fotoPerfil->fotoPerfil()}}" class="img-responsive" alt="" style="max-width: 150px;">
                </div>
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> {{Auth::user()->nombre}} </div>
                    <div class="profile-usertitle-job"> {{Auth::user()->pagaduria}} </div>
                </div>
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="#">
                                <i class="icon-settings"></i> Ajustes de Perfil
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Ajustes de Perfil</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab_1_1" data-toggle="tab">Información Personal</a>
                                </li>
                                <li>
                                    <a href="#tab_1_2" data-toggle="tab">Foto de Perfil</a>
                                </li>
                                <li>
                                    <a href="#tab_1_3" data-toggle="tab">Cambio de Contraseña</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1_1">
                                    <p class="font-red bold">Campos Obligatorios (*)</p>
                                    <form id="frmInfoPersonal" name="frmInfoPersonal" action="actualizarPerfil" method="POST" role="form">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">* Nombre(s):</label>
                                                <input type="text" name="Nombre" id="Nombre" value="{{Auth::user()->nombre}}" placeholder="Digita tu Nombre Completo." class="form-control input-circle" required maxlength="255"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">* Apellidos:</label>
                                                <input type="text" name="Apellido" id="Apellido" value="{{Auth::user()->apellido}}" placeholder="Digita tus Apellidos." class="form-control input-circle" required maxlength="255"/>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">* Telefono:</label>
                                                <input type="text" name="Telefono" id="Telefono" value="{{Auth::user()->telefono}}" placeholder="Digita Telefono Fijo o Celular." class="form-control input-circle" required maxlength="100"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">* Fecha Nacimiento:</label>
                                                @if(is_null(Auth::user()->fecha_nacimiento) || Auth::user()->fecha_nacimiento == "")
                                                    <input type="text" name="FechaNacimiento" id="FechaNacimiento" value="" class="desplegarCalendario form-control input-circle" />
                                                @else
                                                    <input type="text" name="FechaNacimiento" id="FechaNacimiento" value="{{Carbon\Carbon::parse(Auth::user()->fecha_nacimiento)->format('Y-m-d')}}" class="desplegarCalendario form-control input-circle" />
                                                @endif
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="margiv-top-10">
                                            <input type="submit" id="btCambioInfo" name="btCambioInfo" class="btn green btn-circle" value="Guardar">
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_1_2">
                                    <form id="frmCambioFoto" name="frmCambioFoto" action="cambiarFoto" method="POST" enctype="multipart/form-data" role="form">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                    <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" />
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                        <span class="fileinput-new"> Seleccionar Imagen </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="fFotoPerfil" id="fFotoPerfil" required/>
                                                    </span>
                                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <input type="submit" id="btCambioFoto" name="btCambioFoto" class="btn green btn-circle" value="Guardar">
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_1_3">
                                    <p class="font-red bold">Campos Obligatorios (*)</p>
                                    <form id="frmCambioClave" name="frmCambioClave" action="cambioClave" method="POST">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">* Contraseña Actual:</label>
                                                    <input type="password" id="Clave" name="Clave" class="form-control input-circle" placeholder="Digita tu Contraseña Actual." required maxlength="255"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">* Nueva Contraseña:</label>
                                                    <input type="password" id="NuevaClave" name="NuevaClave" class="form-control input-circle" placeholder="Digita tu Nueva Clave." required maxlength="255"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">* Confirmar Nueva Contraseña: </label>
                                                    <input type="password" id="Confirmacion" name="Confirmacion" class="form-control input-circle" placeholder="Confirma tu Nueva Clave." required maxlength="255"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <input type="submit" id="btCambioClave" name="btCambioClave" class="btn green btn-circle" value="Cambiar Contraseña">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($errors->any())
      <script>
          alert("{{ $errors->first() }}");
      </script>
  @endif
  @if (session('msg'))
      <script>
          alert("{{ session('msg') }}");
      </script>
  @endif   
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<script src="{{asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/global/plugins/jquery.sparkline.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/pages/scripts/profile.min.js')}}" type="text/javascript"></script>
@endsection
