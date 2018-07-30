@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@extends('layout.default')
@section('encabezado')
@endsection
@section('content')
<div class="row">
        <div class="col-md-6 ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-file-text-o font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase">Detalle de la solicitud</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <fieldset>
                        <div class="form-group">
                            <label>Cedula &nbsp;*</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="text" id="id_cedula"
                                       data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                       name="cedula" class="form-control " placeholder="Cedula del cliente" required autofocus value="{{$solicitud->cedula}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Apellido</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="text" id="id_apellido" name="apellido" class="form-control " placeholder="Apellido"  autofocus value="{{$solicitud->apellido}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Teléfono celular</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <input type="text" id="id_telefono" name="telefono" class="form-control "
                                       data-inputmask="'numericInput': true, 'mask': '999 999-9999', 'rightAlignNumerics':false"
                                       placeholder="Teléfono" autofocus value="{{$solicitud->telefono}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pagaduria</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-building"></i>
                                </span>
                                <input type="text" id="pagaduria" name="pagaduria" class="form-control" autofocus value="{{$solicitud->pagaduria->nombre}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Clave para consulta del desprendible online</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-key"></i>
                                </span>
                                <input type="text"
                                       id="id_clave" name="clave_desprendible" class="form-control " value="{{$solicitud->clave_desprendible}}" autofocus disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Correo</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="email" id="id_email" name="email" class="form-control " value="{{$solicitud->email}}" autofocus disabled="true">
                            </div>
                        </div>
                        @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                        <label>Foto documento</label>
                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.SolicitudConsulta"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                        @endif
                        <br>
                        <br>
                        @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                        <label>Autorizacion</label>
                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.SolicitudConsulta"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                        @endif
                        <br>
                        <br>
                    </fieldset>
                    <fieldset>
                        <legend>Detalle del Comercial</legend>
                        <div class="form-group">
                            <label>Nombre</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="text" class="form-control"  autofocus value="{{$solicitud->usuario->nombre}} {{$solicitud->usuario->primerApellido}} {{$solicitud->usuario->apellido}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control"  autofocus value="{{$solicitud->usuario->email}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Telefono</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control"  autofocus value="{{$solicitud->usuario->telefono}}" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Perfil</label>
                            <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="text" class="form-control"  autofocus value="{{$solicitud->usuario->perfil}}" disabled="true">
                            </div>
                        </div>
                    </fieldset>
                    @if(!empty($solicitud->valoracion_id))
                        <h2>Ya valorado</h2>
                    @else
                        <div class="row">
                            <form action="{{url('valorarSolicitud', $solicitud->id)}}" method="post">
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                <input type="hidden" value="{{ config("constantes.RUTA") }}" id="dominioPrincipal">
                                <input type="hidden" value="{{$solicitud->id }}" id="solicitud_id" name="id" >
                                <div class="col-md-6">
                                    <button value="1" type="submit" class="btn green">Enviar a valoración</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
</div>
  
@endsection
