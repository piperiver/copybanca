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
     <div class="row">
        <div class="col-md-6 ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-file-text-o font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Reportes Centrales</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form id="formulario">
                       
                        <div class="form-body">
                            
                            <div class="form-group">
                                <label for="anno" class="control-label">A&ntilde;o: &nbsp;* </label>
                                <select class="form-control" id="anno" name="anno">
                                <option value="">seleccione el año</option>
                                       @for($i = 2010; $i<=$annoActual; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mes" class="control-label">Mes: &nbsp;*</label>
                                    <select class="form-control" id="mes" name="mes">
                                        <option value="">Seleccione el mes</option>
                                       @for($i = 1; $i<=count($meses); $i++)
                                            <option value="{{$i}}">{{$meses[$i]}}</option>
                                        @endfor
                                    </select>
                            </div>
                            <div class="form-group">
                                <label for="tipo_reporte">Tipo de Reporte: &nbsp;*</label>
                                    <select name="tipo_reporte" class="form-control required">
                                        <option selected disabled value>Seleccione un tipo de reporte</option>
                                            <option value="1">Data Credito</option>
                                            <option value="2">Cifin</option>
                                    </select>
                            </div>
                            
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" value="{{ config("constantes.RUTA") }}" id="dominioPrincipal">
                            <input type="submit" value="Generar Reporte" class="btn-primary" id="generarReporte">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i><spa id="title_reporte">Reporte</spa>
                    </div>
                    <div class="actions">
                        
                        <div class="btn-group">
                            <a class="btn btn-default btn-sm" href="javascript:;" data-toggle="dropdown">
                                <span class="hidden-xs"> Herramientas </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" id="sample_3_tools">
                                
                                <li>
                                    <a href="#" data-source="tabla" data-action="1" class="tool-action copyToClipboard"><i class="icon-check"></i> Copiar</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                </div>
                <div id="tabla_reporte" class="portlet-body portlet-collapsed data reports_table">
                    
                </div>
            </div>
        </div>
    </div>

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


<script src="{{ asset('js/Reporte/index.js') }}" type="text/javascript"></script>  
@endsection

