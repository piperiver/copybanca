@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@inject('GestionOficinaController', 'App\Http\Controllers\GestionOficinaController')
@extends('layout.default')
@section('encabezado')
    <link href="{{ asset('css/gestionoficina.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/css/nouislider.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('assets/global/scripts/nouislider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/GestionOficina/gestionoficina.js') }}" type="text/javascript"></script>
@endsection
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Creditos Valoracion
                </div>
                <div class="tools">                    
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">                   
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i> {{ $contGValoracion }}
                    </a>                    
                </div>
                
                
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>C&eacute;dula</th>
                                <th>Comercial</th>
                                <th>Estado</th>
                                <th>Pagaduria</th>
                                <th>Fecha</th>
                                <th>Acci&oacute;n</th>                                
                            </tr>
                        </thead> 
                        <tbody>
                            
                            @foreach($gestionValoraciones as $gesVal)
                                <tr id="{{$gesVal->id}}">
                                    <td>{{$gesVal->cliente}}</td>
                                    <td>{{number_format($gesVal->cedula)}}</td>
                                    <td>{{$gesVal->Comercial}}</td>
                                    <td>{{$gesVal->estado}}</td>
                                    <td>{{$gesVal->Pagaduria}}</td>
                                    <td>{{isset($gesVal->fecha_ingreso)? date('Y-m-d', strtotime($gesVal->fecha_ingreso)) : "N/A"}}</td>                                    
                                    <td>
                                        <a class="pointer text-center" target="_blank" href="{{ config('constantes.RUTA') }}Valoraciones/{{$gesVal->id}}"><span class="fa fa-arrow-circle-left color-negro" title="Cargar Adjunto"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>No Viables
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i>{{ $contNoViable }}
                    </a>
                </div>
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cedula</th>
                        <th>Telefono</th>
                        <th>email</th>
                        <th>Pagaduria</th>
                        <th>Comercial</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($estudiosNoViables as $value)
                        <tr>
                            <td>{{$value->cliente}}</td>
                            <td>{{number_format($value->cedula)}}</td>
                            <td>{{$value->telefono}}</td>
                            <td>{{$value->email}}</td>
                            <td>{{$value->Pagaduria}}</td>
                            <td>{{$value->Comercial}}</td>
                            <td>{{$value->created_at}}</td>
                            <td>
                                <a class="pointer text-center" target="_blank" href="{{ config('constantes.RUTA') }}Estudio/{{$value->id}}"><span class="fa fa-arrow-circle-left color-negro" title="Detalle Estudio"></span></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Gestion Comercial
                </div>
                <div class="tools">                    
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    {{number_format($sumComercial)}}
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i> {{ $contGComercial }}
                    </a>                    
                </div>
                
                
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acci&oacute;n</th>                                
                            </tr>
                        </thead> 
                        <tbody>
                            
                            @foreach($gestionComercial as $gesCom)
                                @if($gesCom->estado != 'RAD' && $gesCom->estado != 'Cargada')
                                <tr id="{{$gesCom->idObligacion}}">
                                    <td>{{$gesCom->nombre}}</td>
                                    <td>{{$gesCom->entidad}}</td>
                                    <td>{{$gesCom->estado}}</td>
                                    <td>
                                        @if($gesCom->estado == 'CAN' || $gesCom->estado == 'VEN')
                                            {{isset($gesCom->FechaVencimiento)? date('Y-m-d', strtotime($gesCom->FechaVencimiento)) : "N/A"}}
                                        @elseif($gesCom->estado == 'SOL' )
                                            {{isset($gesCom->FechaEntrega)? date('Y-m-d', strtotime($gesCom->FechaEntrega)) : "N/A"}}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <a class="pointer text-center" target="_blank" href="{{ config('constantes.RUTA') }}Estudio/{{$gesCom->estudio}}/" @if($gesCom->entidad == 'Libranza') config('constantes.LIBRANZA_FIRMADA') @elseif($gesCom->entidad == 'Autorizacion') config('constantes.AUTORIZACION_DE_CONSULTA') @else config('constantes.AUTORIZACION_DE_CONSULTA') @endif ><span class="fa fa-arrow-circle-left color-negro" title="Cargar Adjunto"></span></a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Comité de Credito
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    {{number_format($sumComite)}}
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i>{{$contComite}}
                    </a>                    
                </div>
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Cedula</th>
                                <th>Telefono</th>
                                <th>email</th>
                                <th>Pagaduria</th>
                                <th>Valor del Credito</th>
                                <th>Fecha</th>
                                <th>Acción</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comiteCredito as $value)
                            <tr>
                                <td>{{$value->cliente}}</td>
                                <td>{{number_format($value->cedula)}}</td>
                                <td>{{$value->telefono}}</td>
                                <td>{{$value->email}}</td>
                                <td>{{$value->Pagaduria}}</td>
                                <td>{{number_format($value->ValorCredito)}}</td>
                                <td>{{$value->created_at}}</td>
                                <td>
                                    <a class="pointer text-center" target="_blank" href="{{ config('constantes.RUTA') }}Estudio/{{$value->id}}"><span class="fa fa-arrow-circle-left color-negro" title="Detalle Estudio"></span></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Visado
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    {{number_format($sumFabrica)}}
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i>{{ $contGFabrica }}
                    </a>                    
                </div>
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acción</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gestionFabrica as $gesFab)
                                <tr>
                                    @if($gesFab->estado != 'Cargado')
                                    <td>{{$gesFab->nombre}}</td>
                                    <td>{{$gesFab->entidad}}</td>
                                    <td>{{$gesFab->estado}}</td>
                                    <td>{{isset($gesFab->FechaVencimiento)?  date('Y-m-d', strtotime($gesFab->FechaVencimiento)) : "N/A"}}</td>
                                    
                                    <td>
                                       <a class="pointer text-center" href="{{ config('constantes.RUTA') }}Estudio/{{$gesFab->estudio}}/{{$gesFab->tipoadj}}" target="_blank"><span class="fa fa-arrow-circle-left color-negro" title="Cargar Adjunto"></span></a>
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

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Tesorer&iacute;a
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    {{number_format($pendiente)}}
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i>{{$contTesoreria}}
                    </a>                    
                </div>
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Estado</th>                                  
                                <th class="text-center">Fecha</th>   
                                <th class="text-center">ACCI&Oacute;N</th>   
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gestionTesoreria as $item)
                                @foreach($item as $value)

                                    <tr>
                                        <td>{{$value->nombre}}</td>
                                        @if($value->entidad == 'Saldo Cliente')
                                            <td>{{($value->valorXgirar > 0)? $value->entidad." Restante por Girar $".number_format($value->valorXgirar, 0, ",", ".") : $value->entidad." Restante por Girar $0"}}</td>
                                        @else
                                            <td>{{$value->entidad}}</td>
                                        @endif
                                        <td>{{$value->estado}}</td>
                                        <td>{{ (is_null($value->fechaPago))? "N/A" : date('Y-m-d',strtotime($value->fechaPago)) }}</td>
                                        <td>
                                            <a class="pointer text-center" href="{{config('constantes.RUTA')}}DetalleTesoreria/{{$value->estudio}}/{{$value->valoracion}}" target="_blank"><span class="fa fa-arrow-circle-left color-negro" title="Cargar Adjunto"></span></a>
                                        </td>

                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>Gestion Cartera
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand"></a>
                </div>
                <div class="actions">
                    {{number_format($sumCartera)}}
                    <a class="btn btn-default btn-sm">
                        <i class="fa fa-check-square-o"></i>{{$contGCartera}}
                    </a>                    
                </div>
            </div>
            <div class="portlet-body portlet-collapsed" style="display: none;">
                <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                                <th>Paz y Salvo Obligaci&oacute;n Total</th>
                                <th>Fecha</th>
                                <th>Acci&oacute;n</th>                                
                            </tr>
                        </thead>                        
                        <tbody>
                            @foreach($gestionCartera as $gesCar)
                                <tr id="{{$gesCar->id_obligacion}}">
                                    <td>{{$gesCar->nombre}}</td>
                                    <td>{{$gesCar->entidad}}</td>
                                    <td>{{$gesCar->estado}}</td>
                                    <td>
                                        @if($gesCar->estado == config('constantes.ESTUDIO_BANCO')) 
                                        <a class="color-negro" title="Visualizar" href="{{config('constantes.RUTA').'PazSalvo/'.$gesCar->estudio}}" target="_blank"><span class="fa fa-paperclip  color-negro" style="font-size:15px"></span></a>
                                        @else
                                         N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($gesCar->estado == 'CAN' || $gesCar->estado == 'VEN')
                                            {{(isset($gesCar->FechaVencimiento))? date('Y-m-d',strtotime($gesCar->FechaVencimiento)) : "N/A" }}
                                        @elseif($gesCar->estado == 'SOL' )
                                            {{(isset($gesCar->FechaEntrega))? date('Y-m-d', strtotime($gesCar->FechaEntrega)) : "N/A"}}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a class="pointer text-center" href="{{ config('constantes.RUTA') }}DetalleCartera/{{$gesCar->estudio}}" target="_blank"><span class="fa fa-arrow-circle-left color-negro" title="Cargar Adjunto"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden"  name="_token" id="_token" value="{{ csrf_token() }}">
<input type="hidden" name="dom" id="dom" value="{{ config('constantes.RUTA') }}">

@endsection

