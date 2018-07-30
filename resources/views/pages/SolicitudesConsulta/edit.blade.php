@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@extends('layout.default')
@section('encabezado')
    
    <script type="text/javascript">
    
    function hiddenCedula(){
        $('#edula_exists').hide();
        $( "#cedula_not_exists" ).show();
    }
    
    function hiddenAutorizacion(){
        $('#autorizacion_exists').hide();
        $('#autorizacion_not_exists').show();
    }
    </script>
    
@endsection
@section('content')
    @include('pages.SolicitudesConsulta.Fragmento.msj')
    <div class="row">
        <div class="col-md-6 ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-file-text-o font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Datos de la solicitud</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form action="{{url('solicitudes/update')}}" method="POST" role="form" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="form-group">
                                <label>Cedula &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_cedula"
                                           data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                           name="cedula" class="form-control " placeholder="Cedula del cliente" required autofocus value="{{$solicitud->cedula}}">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Nombre &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_nombre" name="nombre" class="form-control "  value="{{$solicitud->nombre}}" placeholder="Nombre"  required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Apellido</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_apellido" name="apellido" class="form-control " placeholder="Apellido"  autofocus value="{{$solicitud->apellido}}">
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
                                           placeholder="Teléfono" autofocus value="{{$solicitud->telefono}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Departamento &nbsp;*</label>
                                    <select id="departamento" name="departamento" class="form-control select2 circle" required>
                                        <option selected disabled value>Seleccione una opci&oacute;n</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{$departamento->departamento}}" data-id="{{$departamento->id_departamento}}" @if($solicitud->departamento == $departamento->departamento) selected @endif >{{$departamento->departamento}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>Municipios &nbsp;*</label>
                                <select id="municipio" name="municipio" class="form-control select2 circle" required>
                                        <option selected disabled value>Seleccione una opci&oacute;n</option>
                                        @foreach($municipios as $municipio)
                                            <option value="{{$municipio->municipio}}" data-id="{{$municipio->id_municipio}}" @if($solicitud->municipio == $municipio->municipio) selected @endif >{{$municipio->municipio}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>Pagaduria&nbsp;*</label>
                                    <select name="pagaduria_id" class="form-control select2 circle">
                                        <option selected disabled value>Seleccione una opción</option>
                                        @foreach($pagadurias as $pagaduria)
                                            <option @if($solicitud->pagaduria_id == $pagaduria->id) selected @endif
                                                value="{{$pagaduria->id}}">{{$pagaduria->nombre}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>Clave para consulta del desprendible online</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-key"></i>
                                    </span>
                                    <input type="text"
                                           id="id_clave" name="clave_desprendible" class="form-control " value="{{$solicitud->clave_desprendible}}" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Correo</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" id="id_email" name="email" class="form-control " value="{{$solicitud->email}}" autofocus>
                                </div>
                            </div>
                            @if(count($comerciales) > 0)
                                <div class="form-group">
                                    <label>Comercial</label>
                                        <select name="user_id" class="form-control select2 circle">
                                            <option selected disabled value>Seleccione una opción</option>
                                            @foreach($comerciales as $comercial)
                                                <option value="{{$comercial->id}}" @if($comercial->id == $solicitud->user_id) selected @endif
                                                        >{{$comercial->nombre." ".$comercial->primerApellido." ".$comercial->apellido}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            @endif
                            @if(!empty($solicitud->descripcion_devolucion))
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Descripci&oacute;n Devoluci&oacute;n:</label>
                                    <textarea class="form-control" id="descripcion_devolucion" name="descripcion_devolucion" cols="20" rows="2" maxlength="50" style="resize: vertical;" disabled="disabled">{{$solicitud->descripcion_devolucion}}</textarea>
                                </div>
                            @endif
                            
                            @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                            <div id="cedula_exists">
                                <label>Foto documento</label>
                                {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                <a title="Eliminar" style="cursor: pointer" class="deleteAdjunto color-redA margin-left-5" onclick="hiddenCedula()" data-adjunto='{{$cedula[0]->id}}' data-url="{{ config('constantes.RUTA') }}EliminarAdjunto">
                                    <span class="fa fa-remove fa-2x"></span>
                                </a>
                                <input type="hidden" name="foto_documento_oculta" value="0">
                            </div>
                            @else
                            <div id="cedula_not_exists">
                                <label>Foto documento</label>
                                <input type="file" id="adjuntoAutorizacion" name="foto_documento" class="form-control" accept="image/*,.pdf">
                            </div>
                            @endif
                            
                            <br>
                            
                            @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                            <div id="autorizacion_exists">
                                <label>Autorizacion</label>   
                                {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                                <a title="Eliminar" style="cursor: pointer" class="deleteAdjunto color-redA margin-left-5" onclick="hiddenAutorizacion()" data-adjunto='{{$autorizacion[0]->id}}' data-url="{{ config('constantes.RUTA') }}EliminarAdjunto">
                                    <span class="fa fa-remove fa-2x"></span>
                                </a>
                                <input type="hidden" name="autorizacion_oculta" value="0">
                            </div>
                            @else
                            <div id="autorizacion_not_exists">
                                <label>Autorizacion</label>
                                <input type="file" id="adjuntoAutorizacion" name="autorizacion" class="form-control"  accept="image/*,.pdf">
                            </div>
                            @endif
                            <p></p>
                            <div class="form-group">
                                <label>Formato Autorizaci&oacute;n de consulta</label>
                                <p></p>
                                <a class="color-green margin-left-5" title="Descargar" href="{{asset('formatos/autorizacion_consulta.docx')}}">
                                    <span class="fa fa-download fa-2x"></span>
                                </a>
                            </div>
                            
                        </div>
                        <div class="form-actions">
                            <input type="submit" value="Modificar solicitud" class="btn blue">
                            <input type="hidden" name="solicitud_id" value="{{$solicitud->id}}">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="{{ asset('js/SolicitudConsulta/index.js') }}" type="text/javascript"></script>  
@endsection
