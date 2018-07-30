@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-6 ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-file-text-o font-red-sunglo"></i>
                            <span class="caption-subject bold uppercase"> Datos Para Valorar</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @if($erroresFormulario != false)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Alerta!!</strong> {{ $erroresFormulario }}
                        </div>                        
                    @endif
                    <form action="Valoracion" method="POST" role="form" enctype="multipart/form-data" id="formulario-valoracion">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <div class="form-body">
                            <div class="form-group">
                                <label>Correo</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <input type="email" id="txEmail" name="txEmail" value="{{ ($txEmail != false)? $txEmail : "" }}" class="form-control " placeholder="Correo del Cliente." required autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Celular</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-mobile-phone"></i>
                                    </span>
                                    <input type="text" id="txCelular" name="txCelular" class="form-control " placeholder="Celular del Cliente." required value="{{ ($txCelular != false)? $txCelular : "" }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="txPagaduria">Pagadur&iacute;a &nbsp;*</label>
                                    <select id="txPagaduria" name="txPagaduria" class="form-control" data-url="{{ config('constantes.RUTA') }}" required>
                                        <option selected disabled value>Seleccione una opción</option>
                                        @foreach($pagadurias as $pagaduria)
                                            <option value="{{$pagaduria->nombre}}" @if($txPagaduria == $pagaduria->nombre) selected @endif >{{$pagaduria->nombre}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label># Cédula</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" id="txCedula" name="txCedula" class="form-control " placeholder="Cedula del Cliente."  required value="{{ ($txCedula != false)? $txCedula : "" }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Primer Apellido</label>
                                <div class="input-group">
                                    <span class="input-group-addon ">
                                        <i class="fa fa-info-circle"></i>
                                    </span>
                                    <input type="text" id="txPrimerApellido" name="txPrimerApellido" class="form-control " placeholder="Primer Apellido del Cliente."  required value="{{ ($txPrimerApellido != false)? $txPrimerApellido : "" }}">
                                </div>
                            </div>
                            <div class="form-group">                                
                                <input type="file" id="adjuntoAutorizacion" name="adjuntoAutorizacion" class="form-control" required="true">                                
                            </div>
                        </div>
                        <div class="form-actions">
                            <input type="submit" value="Valorar" class="btn blue" id="botonEnviarFormulario">
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <script src="{{ asset('js/SubEstados/index.js') }}" type="text/javascript"></script>
@endsection
@section('encabezado')
<script src="{{ asset('js/Valoraciones/index.js') }}" type="text/javascript"></script>
@endsection
@php
    session()->forget('erroresValoracion');
    session()->forget('txCedula');
    session()->forget('txPrimerApellido');
    session()->forget('txCelular');
    session()->forget('txEmail');
    session()->forget('txPagaduria');
@endphp
