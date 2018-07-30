@extends('layout.default')

@section('encabezado')
    <script type="text/javascript">
        function cleanInput(){
           $('input[name="foto_documento"]').val("");
           $('input[name="autorizacion"]').val("");
        }
        $(document).ready(function () {
           $('input').inputmask();
        });
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
                    <form action="{{url('solicitudes')}}" method="POST" role="form" enctype="multipart/form-data" id="formulario-valoracion">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <div class="form-body">
                            <div class="form-group">
                                <label>Cedula &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_cedula"
                                           data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                           name="cedula" class="form-control " placeholder="Cedula del cliente" value="{{old('cedula')}}" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Nombre &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_nombre" name="nombre" class="form-control "  value="{{old('nombre')}}" placeholder="Nombre"  required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Apellido &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="id_apellido" name="apellido" class="form-control "  value="{{old('apellido')}}" placeholder="Apellido" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Teléfono celular &nbsp;*</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                    <input type="text" id="id_telefono" name="telefono" class="form-control "
                                           data-inputmask="'numericInput': true, 'mask': '999 999-9999', 'rightAlignNumerics':false"
                                           placeholder="Teléfono" value="{{old('telefono')}}" required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Departamento &nbsp;*</label>
                                    <select id="departamento" name="departamento" class="form-control select2 circle" required>
                                        <option selected disabled value>Seleccione una opci&oacute;n</option>
                                        @foreach($departamentos as $departamento)
                                            <option value="{{$departamento->departamento}}" data-id="{{$departamento->id_departamento}}" @if(old('departamento_id') == $departamento->id_departamento) selected @endif >{{$departamento->departamento}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>Municipios &nbsp;*</label>
                                <select id="municipio" name="municipio" class="form-control select2 circle" required>
                                        <option selected disabled value>Seleccione una opci&oacute;n</option>
                                        
                                    </select>
                            </div>
                            <div class="form-group">
                                <label>Pagaduria &nbsp;*</label>
                                    <select name="pagaduria_id" class="form-control select2 circle" required>
                                        <option selected disabled value>Seleccione una opción</option>
                                        @foreach($pagadurias as $pagaduria)
                                            <option value="{{$pagaduria->id}}" @if(old('pagaduria_id') == $pagaduria->id) selected @endif >{{$pagaduria->nombre}}</option>
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
                                           id="id_clave" name="clave_desprendible" value="{{old('clave_desprendible')}}" class="form-control " placeholder="Clave" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Correo</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" id="id_email" name="email" class="form-control " value="{{old('email')}}" placeholder="Correo del Cliente." autofocus>
                                </div>
                            </div>
                            @if(count($comerciales) > 0)
                                <div class="form-group">
                                    <label>Comercial</label>
                                        <select name="user_id" class="form-control select2 circle">
                                            <option selected disabled value>Seleccione una opción</option>
                                            @foreach($comerciales as $comercial)
                                                <option value="{{$comercial->id}}" @if(old('user_id') == $comercial->id) selected @endif>{{$comercial->nombre." ".$comercial->primerApellido." ".$comercial->apellido}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label>Cedula escaneada o foto por lado y lado</label>
                                <input type="file" name="foto_documento" class="form-control" value="{{old('foto_documento')}}" accept="image/*,.pdf">
                                
                            </div>
                            <div class="form-group">
                                <label>Autorización de consulta a centrarles de riesgo</label>
                                <input type="file" name="autorizacion" class="form-control" value="{{old('autorizacion')}}" accept="image/*,.pdf">
                                
                            </div>
                            <div class="form-group">
                                
                                <label>Formato Autorizaci&oacute;n de consulta</label>
                                <p></p>
                                <a class="color-green margin-left-5" title="Descargar" href="{{asset('formatos/autorizacion_consulta.docx')}}">
                                    <span class="fa fa-download fa-2x"></span>
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            
                            <input type="submit" value="Enviar solicitud" class="btn-primary" id="botonEnviarFormulario">
                            <input type="button" value="Limpiar documentos" class="btn-primary" onclick="cleanInput()">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    
<script src="{{ asset('js/SolicitudConsulta/index.js') }}" type="text/javascript"></script>  
@endsection
