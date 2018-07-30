@extends('layout.default')
@section('content')
@include('flash::message')
@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('Utilidades', 'App\Librerias\UtilidadesClass')
<link href="{{ asset('css/adjuntos.css') }}" rel="stylesheet" type="text/css" />
<p class="text-center">Adjuntos: {{ $usuario->nombre }} {{ utf8_decode($usuario->apellido) }}</p>

<div class="row">
    <div class="col-md-12 col-sd-12 col-lg-12 col-xs-12">
        {{$ComponentAdjuntos->dspFormulario($idEstudio,config("constantes.KEY_ESTUDIO"), false, "VALO", $arrayIgnore, "refresh")}}                    
        {{ csrf_field() }}
    </div>        
</div>

    @if($autorizaciones != false)
    <div class="row" style="margin-top: 1em">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <!-- Inicio header -->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-pdf-o"></i>AUT - Autorizaci贸n de Consulta
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="expand"></a>
                    </div>
                </div>
                <!-- Fin header -->
                <div class="portlet-body" style="display: none">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
                        <thead>
                            <tr>
                                <th style="text-align: center"> Fecha </th>
                                <th> Nombre </th>                                
                                <th class="text-center">Ver</th>
                                <th class="text-center">Descargar</th>                                
                            </tr>
                        </thead>
                        <tbody>                            
                                @foreach($autorizaciones as $autorizacion)
                                    <tr>
                                        <td class="text-center">{{ $autorizacion->created_at }}</td>
                                        <td>{{ $autorizacion->NombreArchivo }}</td>                                    
                                        <td class="text-center" style="vertical-align: middle">
                                            <a class="color-negro" title="Visualizar" href="{{ config('constantes.RUTA') }}visualizar/{{ $autorizacion->id }}" target="_blank">
                                                <span class="fa fa-eye fa-2x"></span>
                                            </a>
                                        </td>
                                        <td class="text-center" style="vertical-align: middle">
                                            <a class="color-green margin-left-5" title="Descargar" href="{{ config('constantes.RUTA') }}descargar/{{ $autorizacion->id }}" target="_blank">
                                                <span class="fa fa-download fa-2x"></span>
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
            
        @endif
        
@foreach($informacion as $item)
    @if($item["adjuntos"] != false)
   <div class="row" style="margin-top: 1em">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <!-- Inicio header -->
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-pdf-o"></i>{{ $item["infoTipo"]["Codigo"] }} - {{ $item["infoTipo"]["Descripcion"] }} 
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="expand"></a>
                    </div>
                </div>
                <!-- Fin header -->
                <div class="portlet-body" style="display: none">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
                        <thead>
                            <tr>
                                <th style="text-align: center"> Fecha </th>
                                <th> Nombre </th>
                                <!--<th class="text-center"> Extensi贸n </th>-->
                                <th class="text-center">Ver</th>
                                <th class="text-center">Descargar</th>
                                @if($Utilidades::ValidarAcceso($forma,"Eliminar"))
                                <th class="text-center">Borrar</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>                            
                                @foreach($item["adjuntos"] as $adjunto)
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
                                    @if($Utilidades::ValidarAcceso($forma,"Eliminar"))
                                    <td class="text-center" style="vertical-align: middle">
                                        <a title="Eliminar" style="cursor: pointer" class="deleteAdjunto color-redA margin-left-5" data-adjunto='{{ $adjunto["id"] }}' data-url="{{ config('constantes.RUTA') }}EliminarAdjunto">
                                            <span class="fa fa-remove fa-2x"></span>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach                            
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
    </div>  
@endif
@endforeach




@foreach($adjuntosGestionObligacionesOrdenado as $clave => $valores)    
        <div class="row" style="margin-top: 1em">
             <div class="col-md-12 col-sm-12">
                 <div class="portlet box main-color">
                     <!-- Inicio header -->
                     <div class="portlet-title">
                         <div class="caption">
                             <i class="fa fa-file-pdf-o"></i>{{ $clave }} - {{ $informacionTiposAdjuntos[$clave] }} 
                         </div>
                         <div class="tools">
                             <a href="javascript:;" class="expand"></a>
                         </div>
                     </div>
                     <!-- Fin header -->
                     <div class="portlet-body" style="display: none">
                         <table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
                             <thead>
                                 <tr>
                                     <th style="text-align: center"> Fecha </th>
                                     <th> Nombre </th>
                                     <th class="text-center"> Extensión </th>
                                     <th class="text-center">Ver</th>
                                     <th class="text-center">Descargar</th>                                     
                                 </tr>
                             </thead>
                             <tbody>                            
                                     @foreach($valores as $adjunto)
                                            <tr>
                                                <td class="text-center">{{ $adjunto->created_at }}</td>
                                                <td>{{ $adjunto->NombreArchivo }}</td>
                                                <td class="text-center">{{ $adjunto->Extension }}</td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <a class="color-negro" title="Visualizar" href="{{ config('constantes.RUTA') }}visualizar/{{ $adjunto->id }}" target="_blank">
                                                        <span class="fa fa-eye fa-2x"></span>
                                                    </a>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <a class="color-green margin-left-5" title="Descargar" href="{{ config('constantes.RUTA') }}descargar/{{ $adjunto->id }}" target="_blank">
                                                        <span class="fa fa-download fa-2x"></span>
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
@endforeach



<script src="{{ asset('js/Valoraciones/adjuntos.js') }}" type="text/javascript"></script>
@endsection
