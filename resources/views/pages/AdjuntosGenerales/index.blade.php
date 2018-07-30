@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@extends('layout.default')

@section('encabezado')
    <style type="text/css">
    .field-error{
        color: #ff0039;
    }

    .field-success{
        color: #2780e3;
    }
    </style>
@endsection
@section('content')
<div class="row" style="margin-top: 1em">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <!-- Inicio header -->
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file-pdf-o"></i>Adjuntos Generales
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    <a href="#" id="lkValorar" name="lkValorar"
                       class="btn btn-default btn-sm" data-toggle="modal" data-target="#ModalAdjuntoGeneral">
                        <i class="fa fa-plus"></i> Agregar Adjunto
                    </a>
                </div>
            </div>
            <!-- Fin header -->
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
                    <thead>
                        <tr>
                            <th style="text-align: center"> Fecha </th>
                            <th> Nombre </th>                                
                            <th class="text-center">Ver</th>
                            <th class="text-center">Descargar</th>                                
                            <th class="text-center">Acci&oacute;n</th>                                
                        </tr>
                    </thead>
                    <tbody>                            
                    @foreach($adjuntos as $adjunto)
                        <tr>
                            <td class="text-center">{{substr($adjunto->created_at, 0, 10)}}</td>
                            <td>{{ $adjunto->NombreArchivo }}</td>                                    
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
                            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                            <td class="text-center" style="vertical-align: middle">
                                <a title="Eliminar" style="cursor: pointer" class="deleteAdjunto color-redA margin-left-5" data-adjunto='{{$adjunto->id}}' data-url="{{ config('constantes.RUTA') }}EliminarAdjunto">
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
    
<!--Cargar adjuntos generales-->
    <div class="modal fade modalAdGeneral  " id="ModalAdjuntoGeneral" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">                
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    @if(count($adjuntosGenerales)>0)
                        @foreach($adjuntosGenerales as $adjtG)
                            {{$ComponentAdjuntos->dspFormulario($padre_id, config("constantes.KEY_GENERAL"), $adjtG[0], config("constantes.MDL_VALORACION"), false, "refresh", false, false,$adjtG[1], false,false)}}
                            <br>  
                        @endforeach
                    @else
                        <div class="alert alert-info alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>¡Atenci&oacute;n!</strong> Todos los diferentes tipos de adjuntos generales se han cargado.
                        </div>
                    @endif
                </div>        
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>        
                </div>
            </div>
        </div>
    </div>
<!--Fin de la modal-->


<!--Modal mensaje de respuesta-->
<div class="modal fade" id="mensaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            
            <div class="modal-body"> 
                <span id="descripcion_mensaje"></span>
            </div> 
        </div>
    </div>
</div>

@endsection

