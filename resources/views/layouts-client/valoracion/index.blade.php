@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <title>Bancarizate</title>
        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
        <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/layouts/layout4/css/style.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
        <link src="{{ asset('assets/global/css/bootstro.css') }}" rel="stylesheet" type="text/css"/>
        <link src="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/menu.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/global/css/nouislider.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/global/plugins/fontawesome-iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/global.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/datatables/datatables.all.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery-knob/js/jquery.knob.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/scripts/knobs.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/scripts/bootstro.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/cleave.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/scripts/nouislider.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/global/plugins/fontawesome-iconpicker/fontawesome-iconpicker.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/global.js') }}" type="text/javascript"></script>

    </head>
    <body>
        <!-- Inicio MODALES -->
        
        <!-- Modales con la informacion de las obligaciones castigadas $tEnMora $tAlDia-->
@php
    $allObligaciones = array_merge($tCastigadas, $tEnMora, $tAlDia);
@endphp
@foreach($allObligaciones as $obligacion)    
<div class="modal fade modalEstudio informacionDeLasObligaciones" id="infoObligacion{{ $obligacion->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>  
            </div>
            <div class="modal-body">              
                <div class="row desc-info">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_NOMBRE') }}</div>
                            <div class="text">{{ $obligacion->Entidad }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_NUMERO') }}</div>
                            <div class="text">{{ $obligacion->NumeroObligacion }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_TP') }}</div>
                            <div class="text">{{ $obligacion->tipoCuenta }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_CALIF') }}</div>
                            <div class="text">{{ $obligacion->calificacion }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_ESTOBL') }}</div>
                            <div class="text">{{ $obligacion->Estado }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_FA') }}</div>
                            <div class="text">{{ date('Y-m-d', $obligacion->fechaActualizacion) }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_FAP') }}</div>
                            <div class="text">{{ $obligacion->FechaApertura }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_FV') }}</div>
                            <div class="text">{{ $obligacion->FechaVencimiento }}</div>
                        </div>                                                   
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc">                          
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_VLRCUP') }}</div>
                            <div class="text">${{ (isset($obligacion->ValorInicial) && $obligacion->ValorInicial > 0)? number_format($obligacion->ValorInicial, 0, ",", ".") : 0 }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_SALACT') }}</div>
                            <div class="text">${{ (isset($obligacion->SaldoActualOriginal) && $obligacion->SaldoActualOriginal > 0)? number_format($obligacion->SaldoActualOriginal, 0, ",", ".") : 0 }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_SALMOR') }}</div>
                            <div class="text">${{ (isset($obligacion->SaldoMora) && $obligacion->SaldoMora > 0)? number_format($obligacion->SaldoMora, 0, ",", ".") : 0 }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_VLRCUO') }}</div>
                            <div class="text">${{ (isset($obligacion->ValorCuota) && $obligacion->ValorCuota > 0)? number_format($obligacion->ValorCuota, 0, ",", ".") : 0 }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_CTAS') }}</div>
                            <div class="text">{{ $obligacion->cuotasVigencia }}</div>
                        </div>                         
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_OFI') }}</div>
                            <div class="text">{{ $obligacion->oficina }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle bold">{{ config('constantes.EST_INFO_TITU') }}</div>
                            <div class="text">{{ $obligacion->Calidad }}</div>
                        </div>                          
                    </div>    
                </div>                      
                <div class="form-group">
                    <div class="subtitle bold">{{ config('constantes.EST_INFO_COMPOR') }}</div>
                    <div>{{ $obligacion->comportamiento }}</div>
                </div> 
            </div>          
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Regresar</button>        
            </div>
        </div>
    </div>
</div>    
@endforeach
        
        
        <!-- modal estudio -->
        <!-- Modal -->
        <div class="modal fade" id="modalEstudio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <h2 class="modal-title" id="myModalLabel">CALCULADORA</h2>

                  <form id="form-estudio">
                  <div class="row">
                       <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos borde-lateral">
                           <input id="pagaduriaId" type="hidden" value="{{$pagaduria->id}}">
                           @if($pagaduria->tipo === "Pensionados")
                               <div class="form-group">
                                   <label for="regimenEspecial">Regimen especial</label>
                                   <div class="input-group">
                                       <div class="input-group-addon"></div>
                                       <input class="form-control desplegarCalendario" id="regimenEspecial" name="regimenEspecial" type="checkbox">
                                   </div>
                               </div>
                               <div class="input-group">
                                   <input class="form-control" type="checkbox" id="regimenEspecial"
                                          name="mcRetencion" style="background: #fff">
                               </div>
                           @endif
                           <input id="pagaduriaId" type="hidden" value="{{$pagaduria->id}}">

                           <div class="form-group">
                             <label for="FechaNacimiento">Fecha de Nacimiento</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                 <input class="form-control desplegarCalendario" id="FechaNacimiento" name="FechaNacimiento" type="text" value="{{ (isset($infoUser->fecha_nacimiento))? $infoUser->fecha_nacimiento : "" }}">
                             </div>
                          </div>

                          <div class="form-group">
                             <label for="EstudioIngreso">Ingreso</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                     <input class="form-control inputEstudio miles" id="EstudioIngreso" name="EstudioIngreso" value="{{ ($Estudio != false)? number_format($Estudio->IngresoBase, 0, ',', '.') : '' }}">
                             </div>
                          </div>

                          <div class="form-group">
                             <label for="EstudioEgreso">Total Egreso</label>
                             <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control inputEstudio miles" id="EstudioEgreso" name="EstudioEgreso" value="{{ ($Estudio != false)? number_format($Estudio->TotalEgresos, 0, ',', '.') : '' }}">
                             </div>
                          </div>

                          <div class="form-group">
                             <label for="EstudioCompras">Total Compras</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control inputEstudio miles" id="EstudioCompras" name="EstudioCompras"value="{{ ($Estudio != false)? number_format($Estudio->ValorCompras, 0, ',', '.') : '0' }}">
                             </div>
                          </div>

                          <div class="form-group">
                             <label for="vlrCupo">Valor Cupo</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control readonly miles" id="vlrCupo" name="vlrCupo" readonly="true" value="{{ ($Estudio != false)? number_format($Estudio->IngresoBase, 0, ',', '.') : '' }}">
                             </div>
                          </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos">
                          <div class="form-group">
                             <label for="vlrCredito">Tasa de Interes</label>
                             <div class="input-group">
                                <div class="input-group-addon"><span class="fa fa-percent">%</span></div>
                                <input class="form-control readonly" id="tasa" name="tasa" readonly="true" value="{{ json_decode($parametros)->{'tasaCredito'} * 100 }}">
                                <input type="hidden" class="form-control miles" id="vlrCredito" name="vlrCredito" value="{{ ($Estudio != false)? number_format($Estudio->ValorCredito, 0, ',', '.') : '' }}">
                             </div>
                          </div>

                            <div class="form-group">
                            <label for="vlrCredito">Plazo</label>
                            <div id="slider-format"></div>
                            <br>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-indent-left"></span></div>
                                <input type="text" class="form-control" id="Plazo" name="Plazo" onchange="calculoInfo(parseInt(limiparPuntos($('#vlrCuota').val())))">

                            </div>
                           </div>

                            <div class="form-group">
                             <label for="vlrCuota">Valor Cuota</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control miles" id="vlrCuota" name="vlrCuota">
                             </div>
                             <p class="text-danger"id="vlrCuotaText" style="display:none">El valor de la Cuota no puede superar los $<span id="cifra"></span></p>
                          </div>
                          <div class="form-group">
                             <label for="vlrDesembolso">Valor Desembolso</label>
                             <div class="input-group">
                                 <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control readonly miles" id="vlrDesembolso" name="vlrDesembolso" readonly="true" value="{{ ($Estudio != false)? number_format($Estudio->ValorDesembolso, 0, ',', '.') : '' }}">
                             </div>
                          </div>
                        </div>
                  </div>
                  @if(Auth::user()->perfil == "COM")
                   <input type="hidden"  name="comercialAsignado" id="comercialAsignado" value="{{ Auth::user()->id }}">
                  @endif
                  <input type="hidden"  name="parameters" id="parameters" value="{{ $parametros }}">
                  <input type="hidden"  name="idValoracion" id="idValoracion" value="{{ $idValoracion }}">
                  <input type="hidden"  name="_token" id="_token" value="{{ csrf_token() }}">

                  </form>
              </div>
              <div class="modal-footer">
                  @if($comerciales != false)
                  <div class="container-comercial">
                    <label for="comercialAsignado"  class="lbl-comercial">Asignar:</label>
                        <select  name="comercialAsignado" class="form-control select-comercial" id="comercialAsignado">
                             <option selected disabled value>Seleccione un comercial</option>
                            @foreach($comerciales as $comercial)
                                <option value="{{ $comercial->id }}" {{ ($comercialSeleccionado != false)? (($comercial->id == $comercialSeleccionado)?  "selected" : "") : ""}} >{{ $comercial->nombre }} {{ $comercial->apellido }}</option>
                            @endforeach
                        </select>
                  </div>
                  @endif
                <button type="button" class="btn btn-danger" data-dismiss="modal">Regresar</button>
                @if($Estudio == false)
                    <button type="button" class="btn btn-danger" id="send-form" data-url="{{ config('constantes.RUTA') }}Valoracion/updateEstudio" data-urlestudio="{{ config('constantes.RUTA') }}">Radicar</button>
                @endif
              </div>
            </div>
          </div>
        </div>
        <!-- fin modal estudio  -->
        <!-- Inicio modal consultas centrales -->
        <div class="modal fade" id="modalCCentrales" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title" id="myModalLabel">{{ config('constantes.VALORACION_DATA_HIST_CENTER') }}</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center">
                        <tbody>
                            <tr>
                                <th colspan="2" class="text-center">{{ config('constantes.VALORACION_HISTORICOS') }}</th>
                            </tr>
                            <tr>
                                <th class="text-center">{{ config('constantes.VALORACION_TBL_HISTORICOS_FECHA') }}</th>
                                <th class="text-center">{{ config('constantes.VALORACION_TBL_HISTORICOS_NOMBRE') }}</th>
                            </tr>
                            @foreach($huellaData as $item)
                            <tr>
                                <td>{{$item->fecha}}</td>
                                <td>{{$item->entidad}}</td>
                            </tr>
                            @endforeach

                            <tr>
                                <th colspan="2" class="text-center">{{ config('constantes.VALORACION_HISTORICOS_TRANSUNION') }}</th>
                            </tr>
                            <tr>
                                <th class="text-center">{{ config('constantes.VALORACION_TBL_HISTORICOS_FECHA') }}</th>
                                <th class="text-center">{{ config('constantes.VALORACION_TBL_HISTORICOS_NOMBRE') }}</th>
                            </tr>
                            @foreach($huellaCifin as $item)
                            <tr>
                                <td>{{$item->fecha}}</td>
                                <td>{{$item->entidad}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                </div>
                </div>
            </div>
        </div>
         <!-- Inicio modal  procesos juridicos   -->
        <div class="modal fade" id="modalPJuridicos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title" id="myModalLabel">{{ config('constantes.VALORACION_DATA_HIST_JURI') }}</h4>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ config('constantes.VALORACION_CLOSE') }}</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Inicio modal castigadas   -->
        <div class="modal fade" id="modalCastigadas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title" id="myModalLabel">{{ config('constantes.VALORACION_OBL_CAST') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class='  table table-striped table-bordered table-hover table-checkable order-column text-center' id='tEnMora'>
                        <thead>
                            <tr>
                                <th>{{ config('constantes.VALORACION_MODAL_CASTIGADAS_ENTIDAD') }}</th>
                                <th>{{ config('constantes.VALORACION_MODAL_CASTIGADAS_VLR') }}</th>
                                <th>{{ config('constantes.VALORACION_MODAL_CASTIGADAS_TITU') }}</th>
                                <th>{{ config('constantes.VALORACION_MODAL_CASTIGADAS_OBLIGA') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($tCastigadas as $item)
                            <tr>
                                <td>
                                    <a class="pointer dspInfoObligacion" data-obligacion="{{ $item->id }}" data-padre="modalCastigadas">{{$item->Entidad}}</a>
                                </td>
                                <td>${{number_format($item->SaldoMora, 0, ",", ".")}}</td>
                                <td>{{$item->Calidad}}</td>
                                <td>{{$item->Naturaleza}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Inicio modal en mora   -->
        <div class="modal fade" id="modalMora" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title" id="myModalLabel">{{ config('constantes.VALORACION_OBL_MORA') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                    <table class=' table table-striped table-bordered table-hover table-checkable order-column text-center dataTable no-footer dtr-inline collapsed' id='tEnMora'>
                    <thead>
                        <tr>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_ENTIDAD') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_SALDO') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_TITU') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_OBL') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tEnMora as $item)
                        <tr>
                            <td>
                                <a class="pointer dspInfoObligacion" data-obligacion="{{ $item->id }}" data-padre="modalMora">{{$item->Entidad}}</a>
                            </td>
                            <td>${{number_format($item->SaldoActualOriginal, 0, ",", ".")}}</td>
                            <td>{{$item->Calidad}}</td>
                            <td>{{$item->Naturaleza}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Inicio modal al dia   -->
        <div class="modal fade" id="modalDia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    <h4 class="modal-title" id="myModalLabel">{{ config('constantes.VALORACION_OBL_DIA') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                    <table class=' table table-striped table-bordered table-hover table-checkable order-column text-center' id='tEnMora'>
                    <thead>
                        <tr>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_ENTIDAD') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_SALDO') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_TITU') }}</th>
                            <th>{{ config('constantes.VALORACION_MODAL_MORA_OBL') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tAlDia as $item)
                        <tr>
                            <td>
                                <a class="pointer dspInfoObligacion" data-obligacion="{{ $item->id }}" data-padre="modalDia">{{$item->Entidad}}</a>
                            </td>
                            <td>${{number_format($item->SaldoActualOriginal, 0, ",", ".")}}</td>
                            <td>{{$item->Calidad}}</td>
                            <td>{{$item->Naturaleza}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Inicio modal al puntaje   -->
        <div class="modal fade" id="modalPuntaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="encabezado">
                    <h2 class="modal-title">{{utf8_decode($NombreCompleto)}}</h2>
                    <h2 class="modal-title2">{{ config('constantes.VALORACION_PLAN_ACCION') }}</h2>
                  </div>
                    <h2 class="title-sec title-size">OBLIGACIONES NO PAGAS</h2>
                    <div class="table-responsive">
                        <table class='table table-bordered table-hover table-checkable order-column text-center dataTable no-footer dtr-inline collapsed' id='tEnMora'>
                        <thead>
                            <tr>
                                <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_ENTIDAD') }}</th>
                                <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_OBL') }}</th>
                                <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_TITU') }}</th>
                                <th class="text-center">{{ config('constantes.VALORACION_MODAL_NUMERO_OBLIGACION') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                        @foreach($tEnMora as $item)
                            <tr>
                                <td>{{$item->Entidad}}</td>
                                <td>{{$item->Naturaleza}}</td>
                                <td>{{$item->Calidad}}</td>
                                <td>{{$item->NumeroObligacion}}</td>
                            </tr>
                        @endforeach
                        @foreach($tCastigadas as $item)
                            <tr>
                                <td>{{$item->Entidad}}</td>
                                <td>{{$item->Naturaleza}}</td>
                                <td>{{$item->Calidad}}</td>
                                <td>{{$item->NumeroObligacion}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        </table>
                    </div>
                    <p class="desc desc-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_NPAGAS1') }}</p>
                    <p class="desc desc-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_NPAGAS2') }}</p>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 list-check">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
                            <span class="fa fa-check-circle"></span>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-10 background-white min-h">
                            <h2 class="title-circle title-size"><span>1</span> CONFIRMAR</h2>
                            <p class="desc-check">{{ config('constantes.VALORACION_MODAL_PUNTAJE_C1') }}</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 list-check">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
                            <span class="fa fa-check-circle"></span>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-10 background-white min-h">
                            <h2 class="title-circle title-size"><span>2</span> NEGOCIAR</h2>
                            <p class="desc-check">{{ config('constantes.VALORACION_MODAL_PUNTAJE_C2') }}</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 list-check">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
                            <span class="fa fa-check-circle"></span>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-10 background-white min-h">
                            <h2 class="title-circle title-size"><span>3</span> PAGAR</h2>
                            <p class="desc-check">{{ config('constantes.VALORACION_MODAL_PUNTAJE_C3') }}</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 list-check marginX2">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center">
                            <span class="fa fa-check-circle"></span>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-10 background-white min-h">
                            <h2 class="title-circle title-size"><span>4</span> VERIFICAR</h2>
                            <p class="desc-check">{{ config('constantes.VALORACION_MODAL_PUNTAJE_C4') }}</p>
                        </div>
                    </div>

                    <h2 class="title-sec title-size color-blue">OBLIGACIONES PAGAS</h2>
                    <div class="table-responsive">
                        <table class=' table table-bordered table-hover table-checkable order-column text-center' id='tEnMora'>
                            <thead>
                                <tr>
                                    <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_ENTIDAD') }}</th>
                                    <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_OBL') }}</th>
                                    <th class="text-center">{{ config('constantes.VALORACION_MODAL_MORA_TITU') }}</th>
                                    <th class="text-center">{{ config('constantes.VALORACION_MODAL_NUMERO_OBLIGACION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($tAlDia as $item)
                                <tr>
                                    <td>{{$item->Entidad}}</td>
                                    <td>{{$item->Naturaleza}}</td>
                                    <td>{{$item->Calidad}}</td>
                                    <td>{{$item->NumeroObligacion}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="desc color-blue desc-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_PAGAS_DES') }}</p>
                    <h2 class="recomendaciones">OTRAS RECOMENDACIONES</h2>
                    <h3 class="title-size">HUELLAS CONSULTA</h3>
                    <span class="nota">mayor 6 consultas</span>
                    <p class="desc-recomendaciones desc-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_REC_HUE') }}</p>
                    <h3 class="title-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_INFO_CONTACTO') }}</h3>
                    <p class="desc-recomendaciones desc-size">{{ config('constantes.VALORACION_MODAL_PUNTAJE_INFO_CONTACTO_DESC') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Fin MODALES -->

    <div class="container-fluid menu center-block" id="cabezeraPrincipal">
                <a class="pointer pull-left login" title="Salir" style="margin: 0" href="{{ config("constantes.RUTA") }}Consultas">
                    <span class="fa fa-arrow-left" style="vertical-align: middle"></span>	
                </a>
                <img src="{{ asset('/assets/layouts/layout5/img/logosistema.png') }}" alt="" style="margin: 0"/>
	<a class="pointer pull-right login" title="Salir" onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                                    <span class="fa fa-power-off" style="vertical-align: middle"></span>
	</a>
	<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
		{{ csrf_field() }}
	</form>
</div>
@if(Auth::user()->perfil == "CLI")
    <div class="menu-vtm text-left duennoValoracion" id="cabezeraSecundaria">{{Auth::user()->nombre}}</div>
@else
    <div class="menu-vtm text-left duennoValoracion" id="cabezeraSecundaria">{{utf8_decode($NombreCompleto)}}</div>
@endif

        <!--<div class="container-fluid menu"><span class="text-muted float-left"><strong>{{$NombreCompleto}}</strong></span><img src="{{ asset('assets/layouts/layout4/imgValoracion/logo.png') }}" alt=""/></div>-->
        <div class="container conte center-block">
            <div class="row">
                <div class="col-xs-12">
                    <h1 class="titulo-principal">{{ config('constantes.VALORACION_TITLE') }}</h1>

                </div>
            </div>
            <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 uno " >
                <div class="container-uno">
                    <div class="container-fluid interno">
                        <div class="row">
                            <div class="col-lg-12 bootstro"
        	data-bootstro-title=' Seccion Puntajes'
        	data-bootstro-content="Los puntajes van desde 0 hasta 999 siendo 999 el puntaje mas alto y mas probable de aprobacion."
        	data-bootstro-width="500px"
        	data-bootstro-placement='bottom' data-bootstro-step='0'>
                                <h1><span class="color-blue">{{ config('constantes.VALORACION_PUNTAJE') }}</span> {{ config('constantes.VALORACION_TITLE_PUNTAJE') }} <span class="fa fa-question-circle pointer popoverAbajo visitaGuiada"></span></h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 sub-title"> <h2>{{ config('constantes.VALORACION_DATA') }}</h2>
                                <strong class="{{(number_format($PuntajeData) >= 0 && number_format($PuntajeData) <= 399)? 'color-red' : '' }}
                                                {{(number_format($PuntajeData) >= 400 && number_format($PuntajeData) <= 599)? 'color-coffe' : '' }}
                                                {{(number_format($PuntajeData) >= 600 && number_format($PuntajeData) <= 1000)? 'color-blue' : '' }} ">
                                                {{number_format($PuntajeData)}}
                                </strong>
                            </div>
                            <div class="col-xs-6 sub-title"> <h2 >{{ config('constantes.VALORACION_TUNION') }}</h2>
                                <strong class="{{(number_format($PuntajeCifin) >= 0 && number_format($PuntajeCifin) <= 399)? 'color-red' : '' }}
                                                {{(number_format($PuntajeCifin) >= 400 && number_format($PuntajeCifin) <= 599)? 'color-coffe' : '' }}
                                                {{(number_format($PuntajeCifin) >= 600 && number_format($PuntajeCifin) <= 1000)? 'color-blue' : '' }} ">
                                    {{number_format($PuntajeCifin)}}
                                </strong>
                            </div>
                        </div>
                        <div class="panel1 panel-default ">
                            <ul class="list-rangos">
                                    <li>
                                        <span class="glyphicon glyphicon-stop icon1 cuadro"></span>
                                        <span class="cifra">{{ config('constantes.VALORACION_RANGE_BAD') }}</span>
                                    </li>
                                    <li>
                                        <span class="glyphicon glyphicon-stop icon2 cuadro"></span>
                                        <span class="cifra">{{ config('constantes.VALORACION_RANGE_MEDIUM') }}</span>
                                    </li>
                                    <li>
                                        <span class="glyphicon glyphicon-stop icon3 cuadro">  </span>
                                        <span class="cifra">{{ config('constantes.VALORACION_RANGE_GOOD') }}</span>
                                    </li>
                                </ul>
                        </div>
                    </div>
                    <div class="row abajo marginX4">
                        <h2 class="title-abajo" >{{ config('constantes.VALORACION_DATA_HIST') }}</h2>
                        <!--<div class=" centrales">
                            <span class="glyphicon glyphicon-search"> </span>{{ config('constantes.VALORACION_DATA_HIST_CENTER') }}<input  type="text" name="" class="datos" value="{{$TotalHuellas}}" disabled> </div>
                        <div class="juri">
                            <span class="glyphicon glyphicon-tower"></span>{{ config('constantes.VALORACION_DATA_HIST_JURI') }}<input type="text" name="" class="datos" value="0" disabled>
                        </div>-->

                        <div class="pointer centrales abrirModal" data-nameModal="modalCCentrales">
                            <div href="#?" class="nav-link bootstro"
                                data-bootstro-title='Seccion datos historicos'
                                data-bootstro-content="La consulta en centrales relaciona las entidades que han consultado sus datos recientemente."
                                data-bootstro-width="500px"
                                data-bootstro-placement='top' data-bootstro-step='1'>
                                <i class="fa fa-search"></i>
                                <span class="title">{{ config('constantes.VALORACION_DATA_HIST_CENTER') }}</span>
                                <span class="badge badge-success">{{$TotalHuellas}}</span>
                            </div>
                        </div>
                        <div class="pointer juri abrirModal" data-nameModal="modalPJuridicos">
                            <div href="#?" class="nav-link bootstro"
                                data-bootstro-title='Seccion datos historicos'
                                data-bootstro-content="La conulta de procesos juridicos muestra la informacion historica (procesos inactivos) y actual (procesos activos) de manera que se pueda identificar si una obligacion ya se encuentra en este estado."
                                data-bootstro-width="500px"
                                data-bootstro-placement='bottom' data-bootstro-step='2'>
                                <i class="fa fa-bank"></i>
                                <span class="title">{{ config('constantes.VALORACION_DATA_HIST_JURI') }}</span>
                                <span class="badge badge-success">0</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-lg-6 col-md-6 dos">
                <div class="container-dos">
                    <div class="interno">
                        <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <h1 class="title-section">{{ config('constantes.VALORACION_OBL_TITLE') }}</h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6 col-sm-6 container-round">
                                    <div class="color-red negrita font-20 container-round-title"><span class="fa fa-times"></span>{{ config('constantes.VALORACION_OBL_CAST') }}</div>
                                    <input class="knob mediaQueryJS uno abrirModal pointer bootstro" data-nameModal="modalCastigadas" data-width="110" data-height="113" data-displayPrevious=true id="roundUno" data-fgColor="#ec1c24" data-angleOffset="0" data-skin="tron" data-thickness=".2" value="{{$NumCastigadas}}" data-bootstro-title='Resumen Obligaciones'
                                    data-bootstro-content="El resumen de carteras castigadas muestra cuales obligaciones pueden estar en proceso jurídico o prejurídico son las mas urgentes para cancelar y son las que le restas mas valor a tu puntaje."
                                    data-bootstro-width="500px"
                                    data-bootstro-placement='bottom' data-bootstro-step='3' readonly>
                                    <div class="center-text color-red negrita font-20 container-round-desc">${{number_format($TotalCastigadas, 0, ",", ".")}}</div>
                                </div>

                                <div class="col-md-4 col-xs-6 col-sm-6 container-round">
                                    <div class="color-coffe negrita font-20 container-round-title">
                                    <span class="glyphicon glyphicon-time"></span>{{ config('constantes.VALORACION_OBL_MORA') }}</div>
                                    <input class="knob mediaQueryJS dos abrirModal pointer bootstro" data-nameModal="modalMora" data-width="110" data-height="113" data-displayPrevious=true data-fgColor="#911b1d" data-skin="tron" data-thickness=".2" value="{{$NumEnMora}}" data-bootstro-title='Resumen Obligaciones'
                                    data-bootstro-content="El resumen de obligaciones en mora, muestra cuales obligaciones están en camino a ser cobradas a traves de un proceso y le restan valor a tu puntaje."
                                    data-bootstro-width="500px"
                                    data-bootstro-placement='bottom' data-bootstro-step='4' readonly>
                                    <div class="center-text color-coffe negrita font-20 container-round-desc">${{number_format($TotalEnMora, 0, ",", ".")}}</div>
                                </div>

                                <div class="col-md-4 col-xs-12 col-sm-12 container-round">
                                    <div class="color-blue negrita font-20 container-round-title">
                                    <span class="fa fa-thumbs-o-up"></span>{{ config('constantes.VALORACION_OBL_DIA') }}</div>
                                    <input class="knob mediaQueryJS tres abrirModal pointer bootstro" data-nameModal="modalDia" data-width="110" data-height="113" data-displayPrevious=true data-fgColor="#1b508e" data-skin="tron" data-thickness=".2" value="{{$NumAlDia}}" data-bootstro-title='Resumen Obligaciones'
                                    data-bootstro-content="En el resumen de obligaciones al dia encontrarás la informacion general de las obligaciones que tienen un buen comportamiento. Estas son las que dan valor a tu puntaje"
                                    data-bootstro-width="500px"
                                    data-bootstro-placement='bottom' data-bootstro-step='5' readonly>
                                    <div class="center-text color-blue negrita font-20 container-round-desc">${{number_format($TotalAlDia, 0, ",", ".")}}</div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>
                    <div class="row abajo">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6 container-desc-plan-accion padding-left mediaWidth paddingLeftX4">
                                <div class="col-md-12 col-xs-12"
                                    data-bootstro-title='Hello, I am a normal BOOTSTRAP second'
                                    data-bootstro-content="Because bootstrap rocks. Life before bootstrap was sooo miserable"
                                    data-bootstro-width="500px"
                                    data-bootstro-placement='left' data-bootstro-step='7'>
                                    <h2 class="title-abajo">{{ config('constantes.VALORACION_PLAN_ACCION') }}</h2>
                                </div>
                                <img class="img-lista abrirModal pointer"
        	                        data-bootstro-title='Hello, I am a normal BOOTSTRAP second'
        	                        data-bootstro-content="Because bootstrap rocks. Life before bootstrap was sooo miserable"
        	                        data-bootstro-width="500px"
        	                        data-bootstro-placement='right' data-bootstro-step='8' src="{{ asset('assets/layouts/layout4/imgValoracion/lista.svg') }} " data-nameModal="modalPuntaje"/>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 container-desc-plan-accion padding-right mediaWidth paddingRightX4">
                                    <!--<p class="text-descripcion">{{ config('constantes.VALORACION_PLAN_ACCION_DESC') }}<span class="text-blue abrirModal pointer" data-nameModal="modalPuntaje">{{ config('constantes.VALORACION_PUNTAJE') }}</span></p>-->
                                    <h2 class="title-abajo">CALCULADORA</h2>
                                    <img src="{{ asset('img/calculatorValoracion.svg') }}" class="img-calculadoraValoracion" data-toggle="modal" data-target="#modalEstudio"/>
                                    <!--<button class="buttonCalculadora" data-toggle="modal" data-target="#modalEstudio"><i class="fa fa-calculator fa-5x iconCalculadora" aria-hidden="true" style="color: #fff"></i></button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="btn-material morado" data-toggle="modal" data-target="#modalEstudio"><i class="fa fa-calculator fa-2x" aria-hidden="true" style="color: #fff"></i></div>-->
        <script src="{{ asset('js/global.js') }}" type="text/javascript"></script>

        <script src="{{ asset('assets/layouts/layout4/scripts/valoracion.js') }}" type="text/javascript"></script>

    <script>
            var mediaquery = window.matchMedia("(max-width: 330px)");
            if (mediaquery.matches) {
                $(".mediaQueryJS").data("width", "80");
                $(".mediaQueryJS").data("height", "80");
            }
    </script>
    <script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
</body>
</html>
