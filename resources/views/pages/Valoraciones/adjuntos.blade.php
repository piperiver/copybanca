@extends('layout.default')
@section('content')
@include('flash::message')
@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
<link href="{{ asset('css/adjuntos.css') }}" rel="stylesheet" type="text/css" />
<p class="text-center">Adjuntos: {{ $usuario->nombre }} {{ utf8_decode($usuario->apellido) }}</p>

<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sd-8 col-sd-offset-2 col-lg-8 col-lg-offset-2 col-xs-12">
        {{$ComponentAdjuntos->dspFormulario($idValoracion, config("constantes.KEY_AUTORIZACION"), "AUT", "VALO", false, "refresh")}}                        
    </div>        
</div>

@if($archivos != false)
   <div class="row ValoracionAdjuntos" style="margin-top: 1em">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <!-- Inicio header -->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-pdf-o"></i>{{ $infoTipoAdjunto->Codigo }} - {{ $infoTipoAdjunto->Descripcion }} 
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"></a>
                    </div>
                </div>
                <!-- Fin header -->
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
                        <thead>
                            <tr>
                                <th style="text-align: center"> Fecha </th>
                                <th> Nombre </th>
                                <!--<th class="text-center"> Extensión </th>-->
                                <th class="text-center">Ver</th>
                                <th class="text-center">Descargar</th>
                                <th class="text-center">Borrar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($archivos as $adjunto)
                            <tr>
                                <td class="text-center">{{ $adjunto["created_at"] }}</td>
                                <td>{{ $adjunto["NombreArchivo"] }}</td>
                                <!--<td class="text-center">{{ $adjunto["Extension"] }}</td>-->
                                <td class="text-center" style="vertical-align: middle">
                                    <a class="color-negro" title="Visualizar" href="{{ config('constantes.RUTA') }}visualizar/{{ $adjunto["id"] }}" target="_blank">
                                        <span class="fa fa-eye fa-2x"></span>
                                    </a>
                                </td>
                                <td class="text-center" style="vertical-align: middle">
                                    <a class="color-green margin-left-5" title="Descargar" href="{{ config('constantes.RUTA') }}descargar/{{ $adjunto["id"] }}" target="_blank">
                                        <span class="fa fa-download fa-2x"></span>
                                    </a>
                                </td>
                                <td class="text-center" style="vertical-align: middle">
                                    <a title="Eliminar" style="cursor: pointer" class="deleteAdjunto color-redA margin-left-5" data-adjunto='{{ $adjunto["id"] }}' data-url="{{ config('constantes.RUTA') }}EliminarAdjunto">
                                        <span class="fa fa-remove fa-2x"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
    </div>   
{{ csrf_field() }}
@else
    <div class="row">
        <div class="col-md-8 col-md-offset-2 col-sd-8 col-sd-offset-2 col-lg-8 col-lg-offset-2 col-xs-12">
            <div class="alert alert-warning margin-top-10" role="alert">No existe autorización de consulta para esta valoración</div>                    
        </div>
    </div>        
@endif
    <script src="{{ asset('js/Valoraciones/adjuntos.js') }}" type="text/javascript"></script>
@endsection
