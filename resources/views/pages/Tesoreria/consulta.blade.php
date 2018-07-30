@include('flash::message')
@inject('Estados', 'App\Http\Controllers\EstadosController')
@extends('layout.default')
@section('encabezado')
    <link href="{{ asset('css/Tesoreria/index.css') }}" rel="stylesheet" type="text/css" />
@endsection

<!--Modal Detalle Cliente-->
@foreach($Tesorerias as $Tesoreria)
    <div class="modal fade modalEstudio" id="detalleCliente_{{$Tesoreria->cedula}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">  
              <img src="{{ config('constantes.RUTA') }}img/logosistema.png" alt="" class="logo">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>  
            </div>
            <div class="modal-body">              
                <div class="row desc-info">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle"><strong>Nombre</strong></div>
                            <div class="text">{{$Tesoreria->nombre}}</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Cedula</strong></div>
                            <div class="text">{{$Tesoreria->cedula}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Telefono</strong></div>
                            <div class="text">{{$Tesoreria->telefono}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Correo</strong></div>
                            <div class="text">{{$Tesoreria->email}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Direcci&oacute;n</strong></div>
                            <div class="text">{{$Tesoreria->direccion}}</div>
                        </div>

                    </div> 
                    
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Departamento</strong></div>
                            <div class="text">{{$Tesoreria->departamento}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Municipio</strong></div>
                            <div class="text">{{$Tesoreria->municipio}}</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Pagaduria</strong></div>
                            <div class="text">{{$Tesoreria->pagaduria}}</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Banco</strong></div>
                            <div class="text">{{$Tesoreria->banco}}</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Tipo de Cuenta</strong></div>
                            <div class="text">{{$Tesoreria->tipo_cuenta}}</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="subtitle"><strong>Numero de Cuenta</strong></div>
                            <div class="text">{{$Tesoreria->numero_de_cuenta}}</div>
                        </div>

                    </div>
                </div>                      
                 
            </div>          
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div>    
@endforeach
<!--Fin modal detalle-->

@section('content')
<div class="consultaTesoreria">
    <div class="col-md-12 col-sm-12">
        <h3 class="text-center title-principal" style="margin-top: 0">TOTAL TESORERIA: <span class="bold">${{number_format($totalDesembolso, 0, ",", ".")}}</span></h3>
    </div>
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-4 col-sm-4 col-xs-6 col-pagadas text-center">
            <div class="text-center title-secundario" style="color: #1b508e">OBLIGACIONES PAGADAS</div>
            <input class="knob uno pointer" id="roundUno" value="{{ $cantPagadas + $cantClientesPagados }}" readonly
                   data-max="{{ $cantPagadas + $cantRestantes + $cantClientesPagados + $cantClientesPorPagar}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#1b508e"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #1b508e">${{number_format($totalPagadas + $totalClientesPagados, 0, ",", ".")}}</div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6 col-pagar text-center">
            <div class="text-center title-secundario" style="color: #ec1c24">OBLIGACIONES POR PAGAR</div>
            <input class="knob dos pointer" id="roundUno" value="{{ $cantRestantes }}" readonly
                   data-max="{{ $cantPagadas + $cantRestantes + $cantClientesPagados + $cantClientesPorPagar}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#ec1c24"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #ec1c24">${{number_format($totalRestantes, 0, ",", ".")}}</div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12 col-cliente text-center">
            <div class="text-center title-secundario" style="color: #3E3F40">DESEMBOLSO CLIENTE</div>
            <input class="knob tres pointer" id="roundUno" value="{{ $cantClientesPorPagar }}" readonly
                   data-max="{{ $cantPagadas + $cantRestantes + $cantClientesPagados + $cantClientesPorPagar}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#3E3F40"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #3E3F40">${{number_format($totalClientesPorPagar, 0, ",", ".")}}</div>
        </div>
    </div><!-- /.row -->


        <!-- BEGIN CONDENSED TABLE PORTLET-->
        <div class="portlet box grisConsulta">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-arrow-right"></i>Operaciones
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"> </a>
                </div>
                <div class="actions">
                    <a class="btn btn-sm" style="color: #fff">
                        <i class="fa fa-check-square-o"></i> {{ count($Tesorerias) }}
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="resultado">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center"> Cedula </th>
                                <th class="text-center"> Nombre </th>
                                <th class="text-center"> Estado </th>
                                <th class="text-center"> Vence </th>
                                <th class="text-center"> Desembolso</th>
                                <th class="text-center"> Ejecutado </th>
                                <th class="text-center"> Pendiente </th>
                                <th class="text-center">P. Pagos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Tesorerias as $Tesoreria)
                            <tr>
                                <td>
                                    <a href="{{ route('Detalle',['estudio'=>$Tesoreria->Estudio,'valoracion'=>$Tesoreria->Valoracion])}}" id='lkTesoreria' name='lkTesoreria' data-estudio='{{$Tesoreria->Estudio}}'>
                                        {{$Tesoreria->Estudio}}
                                    </a>
                                </td>
                                <td><a class="pointer" data-toggle="modal" data-target="#detalleCliente_{{$Tesoreria->cedula}}" style="text-transform: uppercase">{{ number_format($Tesoreria->cedula, 0, ",", ".") }}</a></td>
                                <td>{{ $Tesoreria->nombre }}</td>
                                <td>{{ (is_null($Tesoreria->valorComprado) && is_null($Tesoreria->valorGirado))? "Nuevo" : "Parcial" }}</td>
                                <td title="Vence el: {{ $Tesoreria->fechaVencimientoGestionObligacion }}">{{ (round( (strtotime($Tesoreria->fechaVencimientoGestionObligacion) - strtotime(date("Y-m-d"))) / 86400) > 0)? round( (strtotime($Tesoreria->fechaVencimientoGestionObligacion) - strtotime(date("Y-m-d"))) / 86400) : 0 }}</td>
                                <td>{{ number_format($Tesoreria->Desembolso, 0, ",", ".") }}</td>
                                <td>{{ number_format($Tesoreria->valorComprado + $Tesoreria->valorGirado, 0, ",", ".") }}</td>
                                <td>{{ number_format($Tesoreria->Desembolso - $Tesoreria->valorComprado - $Tesoreria->valorGirado, 0, ",", ".") }}</td>
                                <td>
                                    <a href="{{ config("constantes.RUTA") }}Plan_de_pagos/{{ $Tesoreria->Estudio }}/{{ $Tesoreria->Valoracion }}" target="_blank">
                                        <span class="fa fa-list" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- END CONDENSED TABLE PORTLET-->
</div>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" id="hnAccion" name="hnAccion" value="">
        <script src="{{ asset('assets/global/plugins/jquery-knob/js/jquery.knob.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/scripts/knobs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/Tesoreria/consulta.js') }}" type="text/javascript"></script>

@endsection
