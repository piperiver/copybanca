@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('Utilidades', 'App\Librerias\UtilidadesClass')
@extends('layout.default')

@section('content')
<div class="row">
<div class="col-md-12 col-sm-12">
    <div class="portlet box main-color">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-cogs"></i>Lista de Estudios
            </div>
        </div>
        <div id="contenido" class="portlet-body">
            <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="tabla">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">CÉDULA</th>
                        <th class="text-center">NOMBRES</th>
                        <th class="text-center">APELLIDOS</th>
                        <th class="text-center">PAGADURIA</th>
                        <th class="text-center">ESTADO</th>
                        <th class="text-center">COMERCIAL</th>
                        <th class="text-center">VALOR CRÉDITO</th>
                        <th class="text-center">CUOTA</th>
                        <th class="text-center">CREACION</th>
                        <th class="text-center">ADJUNTOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Estudios as $estudio)
                        @php
                            $comercial =  $Utilidades->getInfoUser($estudio->Comercial)
                        @endphp

                    <tr data-idestudio="{{ $estudio->id }}" data-idusuario="{{ $estudio->idUsuario }}">
                        <td style="vertical-align: middle"><a href="{{ ($estudio->Estado == "ing")? config("constantes.RUTA")."GestionObligaciones/".$estudio->id : config("constantes.RUTA")."Estudio/".$estudio->id }}" title="Estudio"><i class="fa fa-calculator" aria-hidden="true"></i></a></td>
                        <td>{{ (isset($estudio->cedula) && $estudio->cedula > 0)? number_format($estudio->cedula, 0, ",", ".") : "N/A" }}</td>
                        <td style="text-transform: uppercase">{{ utf8_decode($estudio->nombre) }}</td>
                        <td style="text-transform: uppercase">{{ utf8_decode($estudio->apellido) }}</td>
                        <td style="text-transform: uppercase">{{ $estudio->Pagaduria }}</td>
                        <td style="text-transform: uppercase">{{ $estudio->Estado }}</td>
                        <td style="text-transform: uppercase">{{ ($comercial != false)? $comercial->nombre." ".$comercial->apellido  : "No Asignado" }}</td>
                        <td>{{ number_format($estudio->ValorCredito, 0, ",", ".") }}</td>
                        <td>{{ number_format($estudio->Cuota, 0, ",", ".") }}</td>
                        <td>{{ substr($estudio->created_at, 0, 10)}}</td>
                        <td style="vertical-align: middle"><a href="{{ config("constantes.RUTA")."Adjuntos_Estudio/".$estudio->id."/".$estudio->Valoracion }}" title="Cargar Adjuntos a Estudio"><i class="fa fa-paperclip" aria-hidden="true"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
