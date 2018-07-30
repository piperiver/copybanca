@inject('Utilidades', 'App\Librerias\UtilidadesClass')
@extends('layout.default')
@section('encabezado')
    <link href="{{ asset('css/Tesoreria/index.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="consultaTesoreria">
    <div class="col-md-12 col-sm-12">
        <!--<h3 class="text-center title-principal" style="margin-top: 0"><b>CARTERA VTM</b></h3>-->
        <h1 class="text-center tituloPrincipal bold">CARTERA BANCARIZATE</h1>
    </div>
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-4 col-sm-4 col-xs-6 col-pagadas text-center">
            <div class="text-center title-secundario" style="color: #1b508e">OPERACIONES REALIZADAS</div>
            <input class="knob uno pointer" id="roundUno" value="{{$contSumatoriaCartera}}" readonly
                   data-max="{{ $contSumatoriaCartera}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#1b508e"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #1b508e">${{number_format($SumatoriaCartera, 0,",",".")}}</div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6 col-pagar text-center">
            <div class="text-center title-secundario" style="color: #ec1c24">OPERACIONES VIGENTES</div>
            <input class="knob dos pointer" id="roundUno" value="{{$contCarteraVigente}}" readonly
                   data-max="{{$contCarteraVigente}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#ec1c24"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #ec1c24">${{number_format($SumatoriaCarteraVigente,0,",",".")}}</div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12 col-cliente text-center">
            <div class="text-center title-secundario" style="color: #3E3F40">RECAUDO ESPERADO</div>
            <input class="knob tres pointer" id="roundUno" value="{{$contRecaudoVigente}}" readonly
                   data-max="{{$contRecaudoVigente}}"
                   data-width="100"
                   data-height="100"
                   data-displayPrevious=true
                   data-fgColor="#3E3F40"
                   data-angleOffset="0"
                   data-skin="tron"
                   data-thickness=".2">
            <div class="text-center title-secundario" style="font-weight: bold; color: #3E3F40">${{number_format($SumatoriaRecaudo,0,",",".")}}</div>
        </div>
    </div><!-- /.row -->

        <!-- BEGIN CONDENSED TABLE PORTLET-->
        <div class="portlet box grisConsulta">
            <div class="portlet-title">
                <div class="caption">
                    <a style="color: #fff" href="{{ config("constantes.RUTA") }}/PagoMasivo" class="pointer" title="Pago Masivo"><span class="fa fa-group"></span> Pago Masivo</a>                    
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"> </a>
                </div>
                <div class="actions">
                    <a class="btn btn-sm" style="color: #fff">
                        <i class="fa fa-check-square-o"></i>                         
                        {{ number_format($sumatoriaCapital,0,",",".") }}
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
                                <th class="text-center"> Pagaduria </th>
                                <th class="text-center">Cuota</th>
                                <th class="text-center"> S.Capital</th>
                                <th class="text-center">P.Pagos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($EstudiosCartera as $Estudio)
                            <tr>
                                <td>
                                    <a href="{{ config("constantes.RUTA") }}DetalleCartera/{{ $Estudio->id }}" id='lkTesoreria' name='lkTesoreria' data-estudio='{{ $Estudio->idEstudio}}'>
                                        {{ $Estudio->idEstudio}}
                                    </a>
                                </td>
                                <td>{{ number_format($Estudio->cedula,0,",",".") }}</td>
                                <td>{{ utf8_decode($Estudio->nombre) }}</td>
                                <td class="uppercase">{{ $Estudio->pagaduriaEstudio }}</td>
                                <td>{{ number_format($Estudio->cuota,0,",",".") }}</td>
                                <td>
                                    @if($Estudio->saldoCapital == NULL)
                                        {{ number_format($Estudio->ValorCredito, 0, ",", ".") }}
                                    @else
                                        {{ number_format($Estudio->saldoCapital, 0, ",", ".") }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ config("constantes.RUTA") }}Plan_de_pagos/{{ $Estudio->id }}/{{$Estudio->idVal}}" target="_blank">
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
