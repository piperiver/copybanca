
@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@extends('layout.default')
@section('content')
<link href="{{ asset('css/styleOb.css') }}" rel="stylesheet" type="text/css">
<div class="modal fade modalEstudio" id="ModalDefinirObligaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">                                                       
                        <img src="{{ config('constantes.RUTA') }}/assets/layouts/layout5/img/logosistema.png" alt="" class="logo">
                    </div>
                    <div class="modal-body">
                        <div id="divParrafo">
                            <p style="margin: -5px 0px 5px 0px;" class="text-center uppercase"><strong>Ahora elige aquellas obligaciones que aparecen en el desprendible..</strong></p>
                        </div>
                        <div class="table-responsive div-obligaciones">

                            <table class="table table-hover text-center listaSelObligaciones">
                                <thead class="head-inverse">
                                    <tr>
                                        <th class="text-center">#</th>                        
                                        <th class="text-center">{{ config('constantes.EST_CONT2_ENTIDAD') }}</th>
                                        <th class="text-center">-</th>
                                        <th class="text-center">{{ config('constantes.EST_CONT2_SALDO') }}</th>                                        
                                        <!--/*<th class="text-center">{{ config('constantes.EST_CONT2_TIPO') }}</th>*/-->
                                        <th class="text-center">{{ config('constantes.EST_CONT1_CUO') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Obligaciones as $obligacion)                       
                                    <tr class="pointer obligacionProc" data-obligacion="{{ $obligacion->id }}">
                                        <td>                            
                                            {{ $obligacion->NumeroObligacion }}
                                        </td>                        
                                        <td>                                                                        
                                            {{ (strlen($obligacion->Entidad) <= 11)? $obligacion->Entidad : substr($obligacion->Entidad, 0, 11) }}
                                        </td>
                                        <td title="{{ $obligacion->EstadoCuenta }}/{{ $obligacion->marca }}">
                                            {{ 
                                                ($obligacion->EstadoCuenta == 'Al Día')? 'A/' : (($obligacion->EstadoCuenta == 'En Mora')? 'M/' : (($obligacion->EstadoCuenta == 'Castigada')? 'C/' : 'N/'))
                                            }}
                                            {{ 
                                                ($obligacion->marca == 'Datacredito')? 'D' : (($obligacion->marca == 'Cifin')? 'C' : (($obligacion->marca == 'Union Datacredito y Cifin')? 'U' : 'N'))
                                            }}                                                                                                                                           
                                        </td>
                                        <td>
                                            {{ number_format($obligacion->SaldoActual,0,'.','.') }}
                                        </td>
                                        <!--/*<td>
                                            {{ (strlen($obligacion->Naturaleza) <= 12)? $obligacion->Naturaleza : substr($obligacion->Naturaleza, 0, 12) }}
                                        </td>*/--> 
                                        <td>
                                            {{ number_format($obligacion->ValorCuota,0,'.','.')}}
                                        </td>                            
                                    </tr>
                                    @endforeach                                    
                                </tbody>
                            </table>            
                        </div>                                 
                    </div>
                    <div class="modal-footer" id="divFooter">
                        <div id="divTitle">
                            <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Obligaciones Repetidas</h4>
                        </div>
                        <button type="button" class="btn btn-default" id="Anterior" data-url="{{ config('constantes.RUTA') }}Consultas">Cancelar</button>                        
                        <button type="button" style="border-radius: 4px!important;border:1px solid #000!important;" class="btn btn-danger btnPrueba" id="Procesar" data-url="{{ config('constantes.RUTA') }}GestionObligaciones/procesarObligacionesDesprendible">Siguiente</button>                        
                    </div>
                </div>
            </div>
    <form id="formularioObligacionesAEliminar">
                            <input type="hidden"  name="_token" id="_token" value="{{ csrf_token() }}">
                            <input type="hidden"  name="idValoracion" id="idValoracion" value="{{ $idValoracion }}">
                            <input type="hidden"  name="idEstudio" id="idEstudio" value="{{ $idEstudio }}">
                        </form>
        </div>

        <input type="hidden" name="base" id="base" data-url="{{ config('constantes.RUTA') }}">
<script src="{{ asset('js/GestionObligaciones/index.js') }}" type="text/javascript"></script>
@endsection

