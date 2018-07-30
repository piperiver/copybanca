@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('FuncionesComponente', 'App\Librerias\FuncionesComponente')
@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@inject('EstudioController', 'App\Http\Controllers\EstudioController')
@inject('ValoracionesController', 'App\Http\Controllers\ValoracionesController')
@extends('layout.default')
@section('encabezado')
    <!--  Este es el encabezado -->
    <link href="{{ asset('assets/global/css/nouislider.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/estudio.css') }}" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
        const tiposContrato = {
            PROP: 96,
            PRUE: 96,
            DEF: 72,
            FIJO: 24,
            INDEF: 24,
            PENS: 108,
            OTHER: 0
        };
        const parametros_costos = {!! $parametrosArray !!}
                @if($infoEstudio->DatosCostos != "")
        const costos_data = {!! json_encode($infoEstudio->DatosCostos) !!}
                @else
        const costos_data = {};
        @endif
        let costos_has_change = false;
    </script>
    <script src="{{ asset('assets/global/scripts/nouislider.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/Estudio/estudio.js') }}" type="text/javascript"></script>
    <!--<script src="{{ asset('assets/layouts/layout4/scripts/valoracion.js') }}" type="text/javascript"></script>-->
        <script type="text/javascript">


        function porcentaje(initial, porcentaje) {
            return (initial * porcentaje) / 100;
        }

        function calcularCostos() {
            if(costos_has_change){
                const tasaSeguro = parseFloat(parametros_costos.TASASEGU);
                const tasaAhorro = parseFloat(parametros_costos.TASAHORR);
                const tasaProvectus = parseFloat(parametros_costos.TASPROVE);
                const porcetajeIva = parseFloat(parametros_costos.IVACO);
                const gmf = parseFloat(parametros_costos.GMFCOSTO);
                const provectus = (parametros_costos.PROVECTUS == 1);
                const valorCredito = parseFloat($('#ROcredito').val().replace(/[^0-9]/g, ''));
                const ajusteCostos = parseFloat($('#ajusteCostos').val());
                const ahorroTasaPorcentajeCalculado = porcentaje((tasaAhorro / 2), ajusteCostos);
                let comision_sanemiento_porcentaje = ahorroTasaPorcentajeCalculado - tasaSeguro;
                if (provectus) {
                    comision_sanemiento_porcentaje += tasaProvectus;
                }
                const comisionSaneamientoPorcentajeCalculado = porcentaje(comision_sanemiento_porcentaje, ajusteCostos);
                const comisionSaneamientoValor = parseInt(porcentaje(valorCredito, comisionSaneamientoPorcentajeCalculado));
                const valorIva = parseInt(porcentaje(comisionSaneamientoValor, porcetajeIva));
                const porcetajeIvaCalculado = (valorIva/valorCredito)  * 100;
                const valorComisionAhorroTasa = parseInt(porcentaje(valorCredito, ahorroTasaPorcentajeCalculado));
                const valorSeguroValor = parseInt(porcentaje(valorCredito, tasaSeguro));
                const valorGmf = parseInt(porcentaje(valorCredito, gmf));

                $('#valorComiAhorroTasa').html(format_miles(valorComisionAhorroTasa));
                $('#valorSaneamiento').html(format_miles(comisionSaneamientoValor));
                $('#valorSeguro').html(format_miles(valorSeguroValor));
                $('#valorSubtotal').html(format_miles(parseInt(porcentaje(valorCredito, (tasaSeguro + comisionSaneamientoPorcentajeCalculado + ahorroTasaPorcentajeCalculado)))));
                $('#valorIva').html(format_miles(valorIva));
                $('#valorGMF').html(format_miles(valorGmf));
                const porcentajeTotal = tasaSeguro + comisionSaneamientoPorcentajeCalculado + ahorroTasaPorcentajeCalculado + gmf + porcetajeIvaCalculado;
                const total_costos = parseInt(porcentaje(valorCredito, porcentajeTotal));
                const valorDesembolso = valorCredito-total_costos;

                $('#porcentajeComiAhorroTasa').html(Number((valorComisionAhorroTasa/valorDesembolso)*100).toFixed(2));
                $('#porcentajeSaneamiento').html(Number((comisionSaneamientoValor/valorDesembolso)*100).toFixed(2));
                $('#porcentajeSeguro').html(Number((valorSeguroValor/valorDesembolso)*100).toFixed(2));
                $('#porcentajeSubtotal').html(Number((parseInt(porcentaje(valorCredito, (tasaSeguro + comisionSaneamientoPorcentajeCalculado + ahorroTasaPorcentajeCalculado)))/valorDesembolso)*100).toFixed(2));
                $('#porcentajeIva').html(Number((valorIva/valorDesembolso)*100).toFixed(2));
                $('#porcentajeGMF').html(Number((valorGmf/valorDesembolso)*100).toFixed(2));
                $('#porcentajeTotal').html(Number((total_costos/valorDesembolso)*100).toFixed(2));

                $('#valorTotal').html(format_miles(total_costos));
                $('#valorCreditoCostos').html(format_miles(valorCredito));
                $('#valorTotalCostosResta').html(format_miles(total_costos));
                $('#valorDesembolsoCostos').html(format_miles(valorDesembolso));
                $('#tasaSeguroCostos').html(Number(tasaSeguro).toFixed(2));
                if(provectus){
                    $('#tasaProvectusCostos').html(Number(tasaProvectus).toFixed(2));
                }else{
                    $('#tasaProvectusCostos').html("No aplica");
                }

                $('#porcentajeIvaCostos').html(Number(porcetajeIva).toFixed(2));
                $('#gmfCostos').html(Number(gmf).toFixed(2));
            }else{
                $('#valorTotal').html($('#CMcostos').val());
                costos_has_change = true;
            }
        }

        function guardarCostos(){
            const ajusteCostos = parseFloat($('#ajusteCostos').val());
            const url = $("#dominioPrincipal").val();
            const totalCostos = parseInt($('#valorTotal').html().replace(/[^0-9]/g, ''));
            $('#CMcostos').val(format_miles(totalCostos));
            const valorCredito = parseFloat($('#ROcredito').val().replace(/[^0-9]/g, ''));
            $("#Deselbolso").html(format_miles(parseInt(valorCredito-totalCostos)));
            calcularDesembolsoAndSaldo();
        }
        $(document).ready(function () {

            start = 100;
            if (costos_data.hasOwnProperty('ajusteCostos')){
                start = parseFloat(costos_data.ajusteCostos);
            }
            var sliderFormat = document.getElementById('slider-format');
            noUiSlider.create(sliderFormat, {
                start: [start],
                connect: [true, false],
                step: 2.5,
                range: {
                    'min': [92.5],
                    'max': [107.5]
                },
            });
            var inputFormat = document.getElementById('ajusteCostos');

            sliderFormat.noUiSlider.on('update', function (values, handle) {
                inputFormat.value = values[handle];
                calcularCostos();
            });


            sliderFormat.noUiSlider.on('end', function (values, handle) {
                guardarCostos();
            });

            inputFormat.addEventListener('change', function () {
                sliderFormat.noUiSlider.set(this.value);
                calcularCostos();
            });

            $('#saveCostos').click(function () {
                const ajusteCostos = parseFloat($('#ajusteCostos').val());
            });

        });


    </script>
@endsection
@section('content')

<!--Modal obligaciones Inhabilitadas-->
<div class="modal fade modalEstudio" id="modalObligacionesInhabilitadas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">      
                    <h4 class="text-center bold">OBLIGACIONES ELIMINADAS</h4>
                    <table class="table table-striped table-hover text-center">
                        <thead class="head-inverse">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">ENTIDAD</th>                                                  
                                <th class="text-center">MARCA</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listObligacionesInhabilitadas as $obligacionesInhabilitadas)
                            <tr class="pointer desplegarDetalleInhabilitadas" data-target="#datalleInhabilitada{{ $obligacionesInhabilitadas->id }}">
                                <td>{{ $obligacionesInhabilitadas->NumeroObligacion }}</td>
                                <td>{{ (strlen($obligacionesInhabilitadas->Entidad) <= 12)? $obligacionesInhabilitadas->Entidad : substr($obligacionesInhabilitadas->Entidad, 0, 12) }}</td>                                
                                <td title="{{ $obligacionesInhabilitadas->marca }}">
                                    @if($obligacionesInhabilitadas->marca == "Union Datacredito y Cifin") U @endif
                                    @if($obligacionesInhabilitadas->marca == "Datacredito") D @endif
                                    @if($obligacionesInhabilitadas->marca == "Cifin") C @endif                                
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div>    
<!--Fin modal obligaciones inhabilitadas-->

<!--inicio detalle modal inhabilitadas-->
@foreach($listObligacionesInhabilitadas as $obligacion)
<div class="modal fade modalEstudio grupoModalesDetalleInhabilitadas" id="datalleInhabilitada{{ $obligacion->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>    
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <div class="row desc-info">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">
                            <div class="form-group">                                
                                <div class="subtitle">{{ config('constantes.EST_INFO_NOMBRE') }}</div>
                                <div class="text">{{ $obligacion->Entidad }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_NUMERO') }}</div>
                                <div class="text">{{ $obligacion->NumeroObligacion }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">Naturaleza</div>
                                <div class="text">{{ $obligacion->Naturaleza }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">Calidad</div>
                                <div class="text">{{ $obligacion->Calidad }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_TP') }}</div>
                                <div class="text">{{ $obligacion->tipoCuenta }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_CALIF') }}</div>
                                <div class="text">{{ $obligacion->calificacion }}</div>
                            </div>

                             <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_OFI') }}</div>
                                <div class="text">{{ $obligacion->oficina }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_TITU') }}</div>
                                <div class="text">{{ $obligacion->Calidad }}</div>
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Marca</div>
                                <div class="text">{{ $obligacion->marca }}</div>
                            </div>  
                            
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_ESTOBL') }}</div>
                                <div class="text">{{ $obligacion->EstadoCuenta }}</div>
                            </div>
                        
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FA') }}</div>
                                <div class="text">{{ date('Y-m-d', $obligacion->fechaActualizacion) }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FAP') }}</div>
                                <div class="text">{{ $obligacion->FechaApertura }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FV') }}</div>
                                <div class="text">{{ $obligacion->FechaVencimiento }}</div>
                            </div>                                                   
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc">                          
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUP') }}</div>
                                <div class="text">${{ number_format($obligacion->ValorInicial, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_SALACT') }}</div>
                                <div class="text">${{ number_format($obligacion->SaldoActualOriginal, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_SALMOR') }}</div>
                                <div class="text">${{ number_format($obligacion->SaldoMora, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Número Cuotas Mora</div>
                                <div class="text">${{ number_format($obligacion->NumeroCuotasMora, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUO') }}</div>
                                <div class="text">${{ number_format($obligacion->ValorCuota, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Cuota Total</div>
                                <div class="text">${{ number_format($obligacion->CuotaTotal, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Cuota Proyectada</div>
                                <div class="text">${{ number_format($obligacion->CuotasProyectadas, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_CTAS') }}</div>
                                <div class="text">{{ $obligacion->cuotasVigencia }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Valor Pagar</div>
                                <div class="text">${{ number_format($obligacion->ValorPagar, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_PORDEU') }}</div>
                                <div class="text">{{ $obligacion->PorcentajeDeuda }}%</div>
                            </div> 
                                                   
                            <div class="form-group">
                                <div class="subtitle">Estado Cuenta
                                    <span class="fa fa-question-circle pointer" data-toggle="popover" data-placement="top" title="Descripción" data-content="{{ $obligacion->EstadoCuentaPagoDescripcion }}"></span>                                    
                                </div>
                                
                                <div class="text" >{{ $obligacion->EstadoCuentaPagoNombre }}</div>
                                
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Estado Plastico</div>
                                <div class="text">{{ $obligacion->EstadoPlasticoNombre }}</div>
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Estado Origen</div>
                                <div class="text">{{ $obligacion->EstadoOrigenNombre }}</div>
                            </div>                                                                        
                            <div class="form-group">
                                <div class="subtitle">Estado Obligacion</div>
                                <div class="text">{{ $obligacion->EstadoObligacionNombre }}</div>
                            </div>                          
                        </div>    
                    </div>                      
                    <div class="form-group">
                        <div class="subtitle"><strong>{{ config('constantes.EST_INFO_COMPOR') }}</strong></div>
                        <div>{{ $obligacion->comportamiento }}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Atras</button>        
            </div>
        </div>
    </div>
</div>    
@endforeach
<!--fon modal detalle inhabilitadas-->


<!--Modal obligaciones Cerradas-->
<div class="modal fade modalEstudio" id="modalObligacionesCerradas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">      
                    <h4 class="text-center bold">LISTADO DE OBLIGACIONES CERRADAS</h4>
                    <table class="table table-striped table-hover text-center">
                        <thead class="head-inverse">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">ENTIDAD</th>                                                  
                                <th class="text-center">MARCA</th>
                                <th class="text-center">COMPORTAMIENTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listObligacionesCerradas as $obligacionesCerradas)
                            <tr class="pointer desplegarDetalleCerradas" data-target="#datalleCerrada{{ $obligacionesCerradas->id }}">
                                <td>{{ $obligacionesCerradas->NumeroObligacion }}</td>
                                <td>{{ (strlen($obligacionesCerradas->Entidad) <= 12)? $obligacionesCerradas->Entidad : substr($obligacionesCerradas->Entidad, 0, 12) }}</td>                                
                                <td title="{{ $obligacionesCerradas->marca }}">
                                    @if($obligacionesCerradas->marca == "Union Datacredito y Cifin") U @endif
                                    @if($obligacionesCerradas->marca == "Datacredito") D @endif
                                    @if($obligacionesCerradas->marca == "Cifin") C @endif                                
                                </td>
                                <td>{{ $obligacionesCerradas->ComportamientoEnt }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div>    
<!--Fin modal obligaciones cerradas-->

<!--inicio detalle modal cerradas-->
@foreach($listObligacionesCerradas as $obligacion)
<div class="modal fade modalEstudio grupoModalesDetalleCerradas" id="datalleCerrada{{ $obligacion->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>    
            </div>
            <div class="modal-body row">
                <div class="col-md-12">
                    <div class="row desc-info">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">
                            <div class="form-group">                                
                                <div class="subtitle">{{ config('constantes.EST_INFO_NOMBRE') }}</div>
                                <div class="text">{{ $obligacion->Entidad }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_NUMERO') }}</div>
                                <div class="text">{{ $obligacion->NumeroObligacion }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">Naturaleza</div>
                                <div class="text">{{ $obligacion->Naturaleza }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">Calidad</div>
                                <div class="text">{{ $obligacion->Calidad }}</div>
                            </div>
                            
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_TP') }}</div>
                                <div class="text">{{ $obligacion->tipoCuenta }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_CALIF') }}</div>
                                <div class="text">{{ $obligacion->calificacion }}</div>
                            </div>

                             <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_OFI') }}</div>
                                <div class="text">{{ $obligacion->oficina }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_TITU') }}</div>
                                <div class="text">{{ $obligacion->Calidad }}</div>
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Marca</div>
                                <div class="text">{{ $obligacion->marca }}</div>
                            </div>  
                            
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_ESTOBL') }}</div>
                                <div class="text">{{ $obligacion->EstadoCuenta }}</div>
                            </div>
                        
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FA') }}</div>
                                <div class="text">{{ date('Y-m-d', $obligacion->fechaActualizacion) }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FAP') }}</div>
                                <div class="text">{{ $obligacion->FechaApertura }}</div>
                            </div>

                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_FV') }}</div>
                                <div class="text">{{ $obligacion->FechaVencimiento }}</div>
                            </div>                                                   
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc">                          
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUP') }}</div>
                                <div class="text">${{ number_format($obligacion->ValorInicial, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_SALACT') }}</div>
                                <div class="text">${{ number_format($obligacion->SaldoActualOriginal, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_SALMOR') }}</div>
                                <div class="text">${{ number_format($obligacion->SaldoMora, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Número Cuotas Mora</div>
                                <div class="text">${{ number_format($obligacion->NumeroCuotasMora, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUO') }}</div>
                                <div class="text">${{ number_format($obligacion->ValorCuota, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Cuota Total</div>
                                <div class="text">${{ number_format($obligacion->CuotaTotal, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Cuota Proyectada</div>
                                <div class="text">${{ number_format($obligacion->CuotasProyectadas, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_CTAS') }}</div>
                                <div class="text">{{ $obligacion->cuotasVigencia }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">Valor Pagar</div>
                                <div class="text">${{ number_format($obligacion->ValorPagar, 0, ",", ".") }}</div>
                            </div> 
                            <div class="form-group">
                                <div class="subtitle">{{ config('constantes.EST_INFO_PORDEU') }}</div>
                                <div class="text">{{ $obligacion->PorcentajeDeuda }}%</div>
                            </div> 
                                                   
                            <div class="form-group">
                                <div class="subtitle">Estado Cuenta
                                    <span class="fa fa-question-circle pointer" data-toggle="popover" data-placement="top" title="Descripción" data-content="{{ $obligacion->EstadoCuentaPagoDescripcion }}"></span>                                    
                                </div>
                                
                                <div class="text" >{{ $obligacion->EstadoCuentaPagoNombre }}</div>
                                
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Estado Plastico</div>
                                <div class="text">{{ $obligacion->EstadoPlasticoNombre }}</div>
                            </div>                          
                            <div class="form-group">
                                <div class="subtitle">Estado Origen</div>
                                <div class="text">{{ $obligacion->EstadoOrigenNombre }}</div>
                            </div>                                                                        
                            <div class="form-group">
                                <div class="subtitle">Estado Obligacion</div>
                                <div class="text">{{ $obligacion->EstadoObligacionNombre }}</div>
                            </div>                          
                        </div>    
                    </div>                      
                    <div class="form-group">
                        <div class="subtitle"><strong>{{ config('constantes.EST_INFO_COMPOR') }}</strong></div>
                        <div>{{ $obligacion->comportamiento }}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Atras</button>        
            </div>
        </div>
    </div>
</div>    
@endforeach
<!--fon modal detalle cerradas-->


<!-- Modal detalle capacidad  -->
<div class="modal fade modalEstudio cuotaFijaVariable" id="modalDetalleCapacidad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 content-part-form">
                        <div class="form-group">
                            <label for="EstudioIngresoCapacidadModal" class="bold">INGRESO</label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control miles" id="EstudioIngresoCapacidadModal" name="EstudioIngresoCapacidadModal" value="{{ $infoEstudio->IngresoBase }}" disabled="true" readonly="true">
                            </div>                             
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 content-part-form" style="border-left: 1px solid #ccc;">
                        <div class="form-group">
                            <label for="EstimadoIngresoFamiliar" class="bold">CONSUMO AUTÓNOMO</label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>
                                <input class="form-control miles" id="EstimadoIngresoFamiliar" name="EstimadoIngresoFamiliar" disabled="true" readonly="true">
                            </div>                             
                        </div>
                    </div>                    
                    <!-- Porlet obligaciones de cuota variable -->
                    <div class="col-md-12" style="margin-bottom: 15px;">                        
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title text-center">
                                <div class="caption">
                                    <strong>OBLIGACIONES DE CUOTA VARIABLE: {{ number_format($sumaTotalCuotaVariable, 0, ",", ".") }}</strong>                                    
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div class="portlet-body" style="padding-top: 7px!important;display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if(count($obligacionesCuotaVariable) > 0)
                                        <table class="table table-striped table-hover text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ENTIDAD</th>
                                                    <th class="text-center">CUOTA P</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($obligacionesCuotaVariable as $obligacionCuotaVariable)
                                                <tr>
                                                    <td>{{ $obligacionCuotaVariable->Entidad }}</td>
                                                    <td>{{ number_format($obligacionCuotaVariable->CuotasProyectadas, 0, ",", ".") }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <?php echo $UtilidadesClass->createMessage("No existen obligaciones de Cuota Variable", "warning") ?>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin porlet obligaciones de cuota variable -->

                    <!-- Porlet obligaciones de cuota fija -->
                    <div class="col-md-12" style="margin-bottom: 15px;">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title text-center">
                                <div class="caption">
                                    <strong>OBLIGACIONES DE CUOTA FIJA: <span id="totalSumaObligacionesCuotaFija">{{ number_format($sumaTotalCuotaFija, 0, ",", ".") }}</span></strong>                                    
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div class="portlet-body" style="padding-top: 7px!important;display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if(count($obligacionesCuotaFija) > 0)
                                        <table class="table table-striped table-hover text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ENTIDAD</th>
                                                    <th class="text-center">CUOTA</th>
                                                </tr>
                                            </thead>
                                            <tbody id="containerCapacidadObligacionesCuotaFija">
                                                @foreach($obligacionesCuotaFija as $obligacionCuotaFija)
                                                <?php
                                                $proyectada = false;
                                                if (!empty($obligacionCuotaFija->ValorCuota) && $obligacionCuotaFija->ValorCuota > 0) {
                                                    $cuota = $obligacionCuotaFija->ValorCuota;
                                                }elseif (!empty($obligacionCuotaFija->CuotasProyectadas) && $obligacionCuotaFija->CuotasProyectadas > 0) {
                                                    $proyectada = true;
                                                    $cuota = number_format($obligacionCuotaFija->CuotasProyectadas, 0, ",", ".");
                                                } else {
                                                    $cuota = 0;
                                                }
                                                ?>
                                                <tr id="rowObligacionCuotaFija{{$obligacionCuotaFija->id }}">
                                                    <td>{{ $obligacionCuotaFija->Entidad }}</td>
                                                    <td style="{{ ($proyectada)? "color: blue" : "" }}"  id="keyCuotaFija{{ $obligacionCuotaFija->id }}" class="listObligacionCuotaFija">{{ $cuota }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <?php echo $UtilidadesClass->createMessage("No existen obligaciones de Cuota Fija", "warning") ?>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin porlet obligaciones de cuota fija -->
                    
                    <!-- Porlet compras -->
                    <div class="col-md-12">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title text-center">
                                <div class="caption">
                                    <strong>COMPRAS <span id="totalObligacionesCompradas">{{ number_format($totalCompras, 0, ",", ".") }}</span></strong>                                    
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div class="portlet-body" style="padding-top: 7px!important;display: none;">
                                <div class="row">
                                    <div class="col-md-12" id="containerObligacionesCompradas">
                                        @if(count($obligacionesCompradas) > 0)
                                        <table class="table table-striped table-hover text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ENTIDAD</th>
                                                    <th class="text-center">CUOTA</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cuerpoTablaCompras">
                                                @foreach($obligacionesCompradas as $obligacion)
                                                <?php
                                                    if($obligacion->TipoCuotaEstudio == "CuotaVariable"){
                                                        $cuota = $obligacion->CuotasProyectadas;
                                                    }else{
                                                        $cuota = $obligacion->ValorCuota;
                                                    }                                                
                                                ?>
                                                <tr>
                                                    <td>{{ $obligacion->Entidad }}</td>
                                                    <td>{{ $cuota }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <?php echo $UtilidadesClass->createMessage("No se han seleccionado obligaciones para comprar", "warning") ?>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fin porlet compras -->
                    
                    <div class="col-md-12">
                        <div class="containerCapacidad">
                            <div class="itemCapacidad">CAPACIDAD</div>
                            <div class="itemCapacidad" id="totalCalculoCapacidadModal">{{ $infoEstudio->Capacidad }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>        
            </div>
        </div>
    </div>
</div>
<!-- Fin modal detalle capacidad -->

    <!-- Modal costos -->
    <div class="modal fade modalEstudio modalCostosAndBeneficios" id="modalCostos" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet box main-color sinMarginBottom">
                                <div class="portlet-title">
                                    <strong>COSTOS DE LA TRANSFORMACIÓN</strong>
                                </div>
                                <div class="portlet-body" style="padding-top: 7px!important;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Ajuste de costos</h4>
                                            <div class="form-group">
                                                <div id="slider-format"></div>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-indent-left"></span></div>
                                                    <input onchange="calcularCostos()" type="text" class="form-control"
                                                           id="ajusteCostos" name="ajusteCosto">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Valor de crédito: <label id="valorCreditoCostos" style="font-weight: bold;"></label></h5>
                                            <h5>Costos: <label id="valorTotalCostosResta" style="font-weight: bold;"></label></h5>
                                            <h5>Valor de desembolso: <label id="valorDesembolsoCostos" style="font-weight: bold;"></label></h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table">
                                                <tr style="font-weight: bold">
                                                    <td>CONCEPTO</td>
                                                    <td>PORCENTAJE</td>
                                                    <td>VALOR</td>
                                                </tr>
                                                <tr>
                                                    <td>COMISIÓN AHORRO EN TASA</td>
                                                    <td id="porcentajeComiAhorroTasa"></td>
                                                    <td id="valorComiAhorroTasa"></td>
                                                </tr>
                                                <tr>
                                                    <td>COMISIÓN SANEAMIENTO</td>
                                                    <td id="porcentajeSaneamiento"></td>
                                                    <td id="valorSaneamiento"></td>
                                                </tr>
                                                <tr>
                                                    <td>SEGURO ANUAL</td>
                                                    <td id="porcentajeSeguro"></td>
                                                    <td id="valorSeguro"></td>
                                                </tr>
                                                <tr>
                                                    <td>CONCEPTOS ADICIONALES</td>
                                                    <td id="porcentajeConceptoAdicional"></td>
                                                    <td id="valorConceptoAdicional"></td>
                                                </tr>
                                                <tr class="totales">
                                                    <td class="text-center">SUBTOTAL</td>
                                                    <td ><span id="porcentajeSubtotal"></span>%</td>
                                                    <td id="valorSubtotal"></td>
                                                </tr>
                                                <tr>
                                                    <td>IVA</td>
                                                    <td id="porcentajeIva"></td>
                                                    <td id="valorIva"></td>
                                                </tr>
                                                <tr>
                                                    <td>GMF (4*1.000)</td>
                                                    <td id="porcentajeGMF"></td>
                                                    <td id="valorGMF"></td>
                                                </tr>
                                                <tr class="totales">
                                                    <td class="text-center">TOTAL</td>
                                                    <td ><span id="porcentajeTotal"></span>%</td>
                                                    <td id="valorTotal"></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="saveCostos" data-dismiss="modal" type="button" class="btn btn-danger">Salir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN Modal costos -->
    <!-- Modal beneficios de la transformacion -->
    <div class="modal fade modalEstudio modalCostosAndBeneficios" id="modalBeneficios" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet box main-color sinMarginBottom">
                                <div class="portlet-title">
                                    <strong>BENEFICIOS DE LA TRANSFORMACIÓN</strong>
                                </div>
                                <div class="portlet-body" style="padding-top: 7px!important;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table">
                                                <tr>
                                                    <td>AHORRO CARTERA CASTIGADA</td>
                                                    <td id="porcentajeAhoCarCastigada">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroCarteraCastigadaP"] > 0)? $infoEstudio->DatosBeneficios["ahorroCarteraCastigadaP"]."%" : "" }}</td>
                                                    <td id="valorAhoCarCastigada">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroCarteraCastigadaV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["ahorroCarteraCastigadaV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr>
                                                    <td>AHORRO CARTERA EN MORA</td>
                                                    <td id="porcentajeCarMora">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroCarteraEnMoraP"] > 0)? $infoEstudio->DatosBeneficios["ahorroCarteraEnMoraP"]."%" : "" }}</td>
                                                    <td id="valorCarMora">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroCarteraEnMoraV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["ahorroCarteraEnMoraV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr>
                                                    <td>AHORRO TASA POR AÑO</td>
                                                    <td id="porcentajeAhoTasaXannio">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroTasaXannioP"] > 0)? $infoEstudio->DatosBeneficios["ahorroTasaXannioP"]."%" : "" }}</td>
                                                    <td id="valorAhoTasaXannio">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroTasaXannioV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["ahorroTasaXannioV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr class="totales">
                                                    <td class="text-center">SUBTOTAL AHORRO</td>
                                                    <td id="porcentajeSubtotalAhorro"></td>
                                                    <td id="valorSubtotalAhorro">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["ahorroSubtotalV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["ahorroSubtotalV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr>
                                                    <td>REDUCCIÓN CUOTA</td>
                                                    <td id="porcentajeReduccionCuota"></td>
                                                    <td id="valorReduccionCuota">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["reduccionCuota"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["reduccionCuota"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr>
                                                    <td>DESEMBOLSO Bancarizate</td>
                                                    <td></td>
                                                    <td id="valorDesembolsoVTM">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["desembolsoVtm"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["desembolsoVtm"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr>
                                                    <td>DESEMBOLSO BANCO</td>
                                                    <td></td>
                                                    <td id="valorDesembolsoBanco">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["desembolsoBanco"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["desembolsoBanco"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr class="totales">
                                                    <td class="text-center">SUBTOTAL EFECTIVO</td>
                                                    <td id="porcentajeSubtotalEfectivo">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["subtotalEfectivoP"] > 0)? $infoEstudio->DatosBeneficios["subtotalEfectivoP"]."%" : "" }}</td>
                                                    <td id="valorSubtotalEfectivo">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["subtotalEfectivoV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["subtotalEfectivoV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                                <tr class="totales">
                                                    <td class="text-center">TOTAL BENEFICIOS</td>
                                                    <td></td>
                                                    <td id="valorTotalBenefcios">{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["totalBeneficiosV"] > 0)? "$".number_format($infoEstudio->DatosBeneficios["totalBeneficiosV"], 0, ",", ".") : "" }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN Modal beneficios de la transformacion -->

    <!-- Modal  calculadora-->
    <div class="modal fade modalEstudio modalCostosAndBeneficios " id="modalEstudioMiniCalculadora" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabelEstudio">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="portlet box main-color sinMarginBottom">
                        <div class="portlet-title">
                            <strong>CÁLCULO CUPO</strong>
                        </div>
                        <div class="portlet-body" style="padding-top: 7px!important;">
                            <div class="row">
                                <div class="col-md-12">
                                    <input id="idPagaduria" type="hidden" value="{{$pagaduria_object->id}}">
                                    <table class="table" style="margin-bottom: 0">                                        
                                        <tr>
                                            <td style="vertical-align: middle">INGRESO</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control miles" id="mcIngreso" name="mcIngreso"
                                                           style="background: #fff"
                                                           value="{{ $infoEstudio->IngresoBase }}">
                                                </div>
                                            </td>
                                        </tr>
                                        @if($pagaduria_object->tipo == "Pensionados")
                                            <tr>
                                                <td style="vertical-align: middle">REGIMEN ESPECIAL</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input class="form-control" type="checkbox" id="regimenEspecial"
                                                               name="mcRetencion" style="background: #fff">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="">
                                            <td style="vertical-align: middle">RETENCIÓN EN LA FUENTE</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control puntosMiles" id="mcRetencion"
                                                           name="mcRetencion" style="background: #fff">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="">
                                            <td style="vertical-align: middle">TOTAL EGRESO</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control miles" id="mcEgreso" name="mcEgreso"
                                                           style="background: #fff"
                                                           value="{{ $infoEstudio->TotalEgresos }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="display: none;" id="loadingCupo">
                                            <td colspan="2"
                                                style="vertical-align: middle; color: #0c91e5; text-align: center">
                                                Calculando cupo
                                            </td>
                                        </tr>
                                        <tr class="cupoDiv">
                                            <td style="vertical-align: middle">DESCUENTOS DE LEY</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control readonly miles" id="mcDescuentosLey"
                                                           name="mcDescuentosLey" readonly="true" value="">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="cupoDiv">
                                            <td style="vertical-align: middle">CUPO DEL DESPRENDIBLE</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control miles" id="mcLey1527" name="mcLey1527"
                                                           value="" disabled="true" readonly="true">
                                                </div>
                                            </td>
                                        </tr>

                                        <tr class="cupoDiv">
                                            <td style="vertical-align: middle">CUPO TOTAL</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><span
                                                                class="glyphicon glyphicon-usd"></span></div>
                                                    <input class="form-control readonly miles" id="mcCupo" name="mcCupo"
                                                           readonly="true" value="{{ $infoEstudio->Cupo }}">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-default" id="sendMiniCalculadora">Aplicar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modal Calculadora  -->

<!-- Inicio modal Calculo Banco  -->
<div class="modal fade modalEstudio" id="modalCalculoBanco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-12 margin-bottom-10">                  
                        <div class="text-center">
                            <span>CUOTA: <span class="bold" id="CuotaBancos">{{ (!empty($infoEstudio->Cuota) && $infoEstudio->Cuota > 0)? "$".$infoEstudio->Cuota : 0 }}</span></span>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="containerInformacionBancos"></div>
                    
                    <input type='hidden' id='BancosEncontrados' value="{{ $infoEstudio->DatosBanco}}">
                    <input type="hidden" value="0" id="cantidadBancosEncontrados">
                    <input type="hidden" id="BancoSeleccionadoEstudio">                                                                       
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-danger refrescarBancos" data-idestudio="{{ $infoEstudio->id }}"><span class="fa fa-refresh"></span> Refrescar</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>    
<!-- Fin modal Calculo Banco -->

<!-- Modales Adjuntos obligaciones-->
@foreach($listObligaciones as $obligacion) 
<div class="modal fade modalEstudio modalCargaCDD-PYS" id="modalAdjunto{{ $obligacion->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label>Entidad: </label>
                        <label class="text-center">{{ $obligacion->Entidad}}</label><br>                        
                        <div class="form-group container-option-CDD-PYS">
                            <label for="">Estado:</label>
                            <div class="input-group" style="margin: 0 auto;">
                                <select class="form-control sltAccionObligacion" data-padre="{{ $obligacion->id }}">
                                    <option value="0">Seleccione una opción</option>
                                    @if($obligacion->optionGestionObligacionesCDD != "hidden")
                                    <optgroup label="Certificaciones de Deuda" class="optionCertificadosDeuda">
                                        @if($obligacion->optionGestionObligacionesCDD == "showSol")
                                        <option value="CSOL">Solicitada</option>
                                        @endif
                                        @if($obligacion->optionGestionObligacionesCDD == "showRad")
                                        <option value="CRAD">Radicada</option>
                                        @endif
                                    </optgroup>
                                    @endif

                                    @if($obligacion->optionGestionObligacionesPYS != "hidden")
                                    <optgroup label="Paz y Salvos" class="containerPazYSalvo">
                                        @if($obligacion->optionGestionObligacionesPYS == "showAll")
                                        <option value="PSOL">Solicitada</option>
                                        @endif
                                        @if($obligacion->optionGestionObligacionesPYS == "showRad" || $obligacion->optionGestionObligacionesPYS == "showAll")
                                        <option value="PRAD">Radicada</option>
                                        @endif
                                    </optgroup>
                                    @endif                                    
                                </select>                                    
                            </div>
                        </div>                        
                    </div>
                </div>

                <div class="row containerSolicitado" style="display: none">
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="">Fecha Solicitud</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaSolicitud fechasAdjuntoSolicitud" data-id="{{ $obligacion->id }}" id="fechaSolicitud{{ $obligacion->id }}" name="fechaSolicitud" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="">Fecha Entrega</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaEntrega fechasAdjuntoSolicitud" data-id="{{ $obligacion->id }}" id="fechaEntrega{{ $obligacion->id }}" name="fechaEntrega" value="">                                  
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="containerAdjuntoSolicitudCDD" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacion->id, config("constantes.KEY_OBLIGACION"), config("constantes.SOL_CERTIFICACIONES_DEUDA"), config("constantes.MDL_VALORACION"), false, "function", "saveFechasSolicitud", false, $obligacion->Entidad, false, false)}}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="containerAdjuntoSolicitudPYS" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacion->id, config("constantes.KEY_OBLIGACION"), config("constantes.SOL_PAZ_SALVO"), config("constantes.MDL_VALORACION"), false, "function", "saveFechasSolicitud", false, $obligacion->Entidad, false, false)}}
                        </div>
                    </div>
                    
                </div>

                <div class="row containerRadicada" style="display: none">
                    <div class="col-md-4">
                        <div class="form-group text-center">
                            <label for="">Fecha Radicación</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaRadicacion fechasAdjunto" data-id="{{ $obligacion->id }}" id="fechaRadicacion{{ $obligacion->id }}" name="fechaRadicacion" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 containerFechaVencimiento">
                        <div class="form-group text-center">
                            <label for="">Fecha Vencimiento</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaVencimiento fechasAdjunto" data-id="{{ $obligacion->id }}" id="fechaVencimiento{{ $obligacion->id }}" name="fechaVencimiento" value="">                                  
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 containerValorCertificado">
                        <div class="form-group text-center">
                            <label for="">Valor Certificado</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" class="form-control inputEditableMiles" data-id="{{ $obligacion->id }}" id="valorCertificado{{ $obligacion->id }}" name="valorCertificado" value="{{ $obligacion->SaldoActual }}">                                  
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="containerComponenteCertificacion" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacion->id, config("constantes.KEY_OBLIGACION"), [config("constantes.CERTIFICACIONES_DEUDA"), config("constantes.AUT_CERTIFICACIONES_DEUDA")], config("constantes.MDL_VALORACION"), false, "function", "updateInfoAdjuntos", false, $obligacion->Entidad, false, false)}}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="containerComponentePazySalvo" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacion->id, config("constantes.KEY_OBLIGACION"), config("constantes.PAZ_SALVO"), config("constantes.MDL_VALORACION"), false, "function", "updateInfoAdjuntos", false, $obligacion->Entidad, false, false)}}
                        </div>
                    </div>

                </div>
                <br>      
                <div id="AdjuntosCargados{{ $obligacion->id }}">
                    <?php echo $FuncionesComponente->traerTablaAdjuntos(false, $obligacion->id, false, false) ?>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.EST_CONT2_CERTI') }}</h4>            
                <button type="button" class="btn btn-default btnGuardar" data-id="{{ $obligacion->id }}" style="display: none">Guardar</button>        
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>        
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="modal fade modalEstudio" id="modalValoracion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">                
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                             
            </div>
            <div class="modal-body">
                <iframe src="/Valoraciones/{{$infoEstudio->Valoracion}}" width="100%" height="680" frameborder="0" id="frameValoracion"></iframe>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.EST_MOD_VALORACION') }}</h4>            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div>
<!-- Fin Modales -->

<!--Modal carga desprendible-->
<div class="modal fade modalEstudio" id="modalCargaDesprendible" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-md-12">
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.DESPRENDIBLE"), config("constantes.MDL_VALORACION"), [config("constantes.AUTORIZACION_DE_CONSULTA")], "clear", false, true, false, "container_cargaTablaAdjuntosCargados")}}                                            
                    </div>
                </div>                
                <div class="row margin-top-10">
                    <div class="col-md-12" id="container_cargaTablaAdjuntosCargados">                              
                        {{$ComponentAdjuntos->createTableOfAdjuntos($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.DESPRENDIBLE"))}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Desprendible</h4>
                <button type="button" class="btn btn-danger cerrarModal" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>
<!--Fin Modal carga desprendible-->

@if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CERTIFICADO_LABORAL"))) == 0)
<!--Modal carga adjunto contrato-->
<div class="modal fade modalEstudio" id="modal{{ config("constantes.CERTIFICADO_LABORAL") }}-{{ $infoEstudio->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>           
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-md-12">                       
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.CERTIFICADO_LABORAL"), config("constantes.MDL_VALORACION"), [config("constantes.AUTORIZACION_DE_CONSULTA")], "function", "cambiarUpload")}}                                                                                            
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Certificado Laboral</h4>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>
<!--Fin Modal carga adjunto contrato-->
@endif

    @if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CEDULA_DE_CIUDADANIA"))) == 0)
        <!--Modal carga adjunto Cedula de ciudadania-->
        <div class="modal fade modalEstudio"
             id="modal{{ config("constantes.CEDULA_DE_CIUDADANIA") }}-{{ $infoEstudio->id }}" tabindex="-1"
             role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.CEDULA_DE_CIUDADANIA"), config("constantes.MDL_VALORACION"), false, "function", "cambiarUpload")}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Cedula</h4>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Solicitud Crédito</h4>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>
            </div>
        </div>
        <!--Fin Modal carga adjunto cedula de ciudadania-->
    @endif

@if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.SEGURO_DE_VIDA"))) == 0)
<!--Modal carga adjunto seguro de vida -->
<div class="modal fade modalEstudio" id="modal{{ config("constantes.SEGURO_DE_VIDA") }}-{{ $infoEstudio->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                       
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-md-12">                       
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.SEGURO_DE_VIDA"), config("constantes.MDL_VALORACION"), false, "function", "cambiarUpload")}}                                                                                            
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Seguro de Vida</h4>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>
<!--Fin Modal carga adjunto seguro de vida-->
@endif



@if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_FIRMADA"))) == 0)
<!--Modal carga adjunto Cedula de ciudadania-->
<div class="modal fade modalEstudio" id="modal{{ config("constantes.LIBRANZA_FIRMADA") }}-{{ $infoEstudio->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-md-12">                       
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_FIRMADA"), config("constantes.MDL_VALORACION"), false, "function", "cambiarUpload")}}                                                                                            
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Libranza</h4>            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>
<!--Fin Modal carga adjunto cedula de ciudadania-->
@endif


<!--Modal carga adjunto Cedula de ciudadania-->
<div class="modal fade modalEstudio" id="modalVisado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @if($adjuntoVisado == 0)
                    <div class="row container-select-visado">
                          <div class="col-md-12 text-center">                        
                              <div class="form-group">
                                  <label for="">Adjuntar:</label>
                                  <div class="input-group" style="margin: 0 auto;">                           
                                      <select class="form-control" id="optionVisado">
                                               <option>Seleccione una opción</option>
                                          @if($adjuntoSolicitudVisado == 0 && $adjuntoVisado == 0)
                                                <option value="SOL" class="solicitudVisado">Solicitud</option>
                                          @endif    
                                          @if($adjuntoVisado == 0)
                                                <option value="RAD" class="radicacionVisado">Radicaci&oacute;n</option>
                                          @endif    
                                      </select>
                                  </div>
                              </div>                        
                          </div>
                      <br>          
                      </div>
                @endif
                <div class="row container-Visado-Solicitud" style="display: none">
                    <div class="col-md-12">                       
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.SOLICITUD_VISADO"), config("constantes.MDL_VALORACION"), false, "function", "reemplazarTablaVisado", false, false, false, "tablaVisado")}}                                                                                            
                    </div>
                    <br>
                </div>
                
                <div class="row container-Visado-Radicacion" style="display: none">
                    <div class="col-md-12">                       
                        {{$ComponentAdjuntos->dspFormulario($infoEstudio->id, config("constantes.KEY_ESTUDIO"), config("constantes.VISADO"), config("constantes.MDL_VALORACION"), false, "function", "reemplazarTablaVisado", false, false, false, "tablaVisado")}}                                                                                            
                    </div>
                    <br>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-hover text-center">                                    
                            <thead>
                                <tr>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">TIPO</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="container-tabla-adjuntos-visado">
                                {!!$htmlVisado!!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Visado</h4>            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>        
            </div>
        </div>
    </div>
</div>
<!--Fin Modal carga adjunto cedula de ciudadania-->


<!-- Modales Informacion de cada obligacion-->
@foreach($listObligaciones as $obligacion)     
<div class="modal fade modalEstudio" id="infoObligacion{{ $obligacion->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>  
            </div>
            <div class="modal-body">              
                <div class="row desc-info">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_NOMBRE') }}</div>
                            <div class="text">{{ $obligacion->Entidad }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_NUMERO') }}</div>
                            <div class="text">{{ $obligacion->NumeroObligacion }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_TP') }}</div>
                            <div class="text">{{ $obligacion->tipoCuenta }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_CALIF') }}</div>
                            <div class="text">{{ $obligacion->calificacion }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_ESTOBL') }}</div>
                            <div class="text">{{ $obligacion->Estado }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_FA') }}</div>
                            <div class="text">{{ date('Y-m-d', $obligacion->fechaActualizacion) }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_FAP') }}</div>
                            <div class="text">{{ $obligacion->FechaApertura }}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_FV') }}</div>
                            <div class="text">{{ $obligacion->FechaVencimiento }}</div>
                        </div>  
                        <div class="form-group">
                                <div class="subtitle">Marca</div>
                                <div class="text">{{ $obligacion->marca }}</div>
                            </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc">                          
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUP') }}</div>
                            <div class="text">${{ $obligacion->ValorInicial }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_SALACT') }}</div>
                            <div class="text">${{ (isset($obligacion->SaldoActualOriginal) && $obligacion->SaldoActualOriginal > 0)? number_format($obligacion->SaldoActualOriginal, 0, ",", ".") : 0 }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_SALMOR') }}</div>
                            <div class="text">${{ $obligacion->SaldoMora }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_VLRCUO') }}</div>
                            <div class="text">${{ $obligacion->ValorCuota }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_CTAS') }}</div>
                            <div class="text">{{ $obligacion->cuotasVigencia }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_PORDEU') }}</div>
                            <div class="text">{{ $obligacion->PorcentajeDeuda }}%</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_OFI') }}</div>
                            <div class="text">{{ $obligacion->oficina }}</div>
                        </div> 
                        <div class="form-group">
                            <div class="subtitle">{{ config('constantes.EST_INFO_TITU') }}</div>
                            <div class="text">{{ $obligacion->Calidad }}</div>
                        </div>                          
                    </div>    
                </div>                      
                <div class="form-group">
                    <div class="subtitle"><strong>{{ config('constantes.EST_INFO_COMPOR') }}</strong></div>
                    <div>{{ $obligacion->comportamiento }}</div>
                </div> 
            </div>          
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div>    
@endforeach
<!-- Fin Modales -->

<!--Modal adjuntos ingresos adicionales-->
<div id="contenedorModalesIngresosAdicionales7">
    {!! $ingresosAdicionales["modales"] !!}
</div>
<!--Fin Modal adjuntos ingresos adicionales-->

<!--Modal ingresos adicionales-->
<div class="modal fade modalEstudio" id="ingresosAdicionalesMDL" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">  
                <div class="row">
                    <div class="col-md-12">                       
                        <form class="form-inline text-center" id="formularioIngresosAdicionales">
                            <div class="form-group">
                                <label for="ing_ad_tipo">Tipo</label>
                                <select class="form-control" id="ing_ad_tipo" name="ing_ad_tipo">
                                    <option value="1">Option</option>
                                    <option value="2">Option</option>
                                    <option value="3">Option</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin: 0 10px;">
                                <label for="ing_ad_valor">Valor</label>
                                <input type="text" class="form-control miles" id="ing_ad_valor" name="ing_ad_valor">
                            </div>                                                                
                            <button type="button" class="btn btn-primary" id="send_form_ing_adicionales">Agregar</button>
                        </form>

                        <div id="contenedor_lst_ingresos_adicionales" class="margin-top-10">
                            {!! $ingresosAdicionales["html"] !!}                            
                        </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>
                </div>
            </div>
        </div>
    </div>
    <!--Fin Modal ingresos adicionales-->

<!--Modal Detalle Cliente-->
    <div class="modal fade modalEstudio" id="detalleCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="row desc-info">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle"><strong>Nombre</strong></div>
                            <div class="text">{{$infoUser->nombres()}}</div>
                        </div>
                        <div class="form-group">
                            <div class="subtitle"><strong>Edad</strong></div>
                            <div class="text">{{$infoUser->edad}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Cedula</strong></div>
                            <div class="text">{{$infoUser->cedula}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Telefono</strong></div>
                            <div class="text">{{$infoUser->telefono}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Correo</strong></div>
                            <div class="text">{{$infoUser->email}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Direcci&oacute;n</strong></div>
                            <div class="text">{{$infoUser->direccion." ".$infoUser->ciudad}}</div>
                        </div>

                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle"><strong>Departamento</strong></div>
                            <div class="text">{{$infoUser->departamento}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Municipio</strong></div>
                            <div class="text">{{$infoUser->municipio}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Pagaduria</strong></div>
                            <div class="text">{{$infoUser->pagaduria}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Banco</strong></div>
                            <div class="text">{{$infoUser->banco}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Tipo de Cuenta</strong></div>
                            <div class="text">{{$infoUser->tipo_cuenta}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Numero de Cuenta</strong></div>
                            <div class="text">{{$infoUser->numero_de_cuenta}}</div>
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

<!--Fin modal detalle-->

    <div class="row content-estudio">
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 ">
            <div class="container-uno">

            <div class="Eheader">         
                <div class="background-white">
                    <div class="container-table ">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title">
                                <span class="fa fa-search pointer pull-left cargarModalAjax" data-url="/ajax-content/desprendible?cedula={{$infoUser->cedula}}" data-toggle="modal" title="Agregar obligación"></span>

                                <div id="dspValoracion" style="cursor: pointer">
                                    <strong class="color-white"> {{ utf8_decode($infoUser->nombre) }} {{ utf8_decode($infoUser->apellido) }} </strong></div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-hover text-center todasObligaciones">                                    
                                    <tbody>                                        
                                        <tr>
                                            <td> <a class="pointer" data-toggle="modal" data-target="#detalleCliente" style="text-transform: uppercase">{{ number_format($infoUser->cedula, 0, ",", ".") }}</a></td>
                                            <td>
                                                <a class="pointer" data-toggle="modal" data-target="#modalCargaDesprendible" style="text-transform: uppercase">{{ (empty($infoEstudio->Pagaduria))? "No Reporta" : $infoEstudio->Pagaduria }}</a>
                                            </td>                                                                                                                                 
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                                          
                </div>                
                <div class="background-white margin-top-10">

                    <div class="container-table pMaximo margin-bottom-10">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title">
                                <strong> PLAZO MÁXIMO: </strong><strong class="color-blue"><span id="plazoMaximo">{{ $infoEstudio->PlazoMaximo }}</span> meses</strong>
                            </div>
                            <div class="portlet-body">
                                <div class="">
                                    <div class="formizq">
                                        <div class="input-grupo">
                                            <div class="input-grupo-label" style="margin-top: 7px;">T CONTRATO:</div>
                                            <select class=" input-grupo-input text-center  font-11 tiposDeContrato" style="margin-top: 7px;">
                                                <option value="0" {{ ($infoEstudio->TipoContrato == "")? "selected" : "" }}>Seleccione una opción</option>
                                                <option value="PROP" {{ ($infoEstudio->TipoContrato == "PROP")? "selected" : "" }}>PROPIEDAD</option>
                                                <option value="PRUE" {{ ($infoEstudio->TipoContrato == "PRUE")? "selected" : "" }}>P. PRUEBA</option>
                                                <option value="DEF" {{ ($infoEstudio->TipoContrato == "DEF")? "selected" : "" }}>P. V. DEF</option>
                                                <option value="FIJO" {{ ($infoEstudio->TipoContrato == "FIJO")? "selected" : "" }}>T. FIJO</option>
                                                <option value="INDEF" {{ ($infoEstudio->TipoContrato == "INDEF")? "selected" : "" }}>T. INDEFIN</option>
                                                <option value="PENS" {{ ($infoEstudio->TipoContrato == "PENS")? "selected" : "" }}>PENSIONADO</option>
                                                <option value="OTHER" {{ ($infoEstudio->TipoContrato == "OTHER")? "selected" : "" }}>OTRO</option>
                                            </select>
                                            <div class="input-grupo-respuesta" style="margin-top: 7px;" id="dsp_modal{{ config("constantes.CERTIFICADO_LABORAL") }}-{{ $infoEstudio->id }}">
                                                @if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CERTIFICADO_LABORAL"))) == 0)
                                                <a class="pointer color-negro"  data-toggle="modal" data-target="#modal{{ config("constantes.CERTIFICADO_LABORAL") }}-{{ $infoEstudio->id }}"><span class="fa fa-arrow-up fa-1x color-negro" title="Adjuntar Certificado Laboral"></span></a>
                                                @else                                                                                        
                                                {{ $ComponentAdjuntos->getUrlViewAdjunto($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CERTIFICADO_LABORAL") ) }}
                                                @endif                                                                                         
                                                <span id="mesesRetiroForzoso">{{ (!empty($infoEstudio->MesesRetiroForzoso))? $infoEstudio->MesesRetiroForzoso." meses" : "" }}</span>
                                            </div>                                                                                         
                                        </div>
                                    </div>    

                                    <div class="formizq">
                                        <div class="input-grupo">
                                            <div class="input-grupo-label">                                                                                                    
                                                F NACIMIENTO:
                                            </div>
                                            <input type="text" value="{{$infoUser->fecha_nacimiento}}" class="desplegarCalendario input-grupo-input font-11 calculeEdad" id="FechaNacimientoPlazo">
                                            <div class="input-grupo-respuesta">
                                                <span id="dsp_modal{{ config("constantes.CEDULA_DE_CIUDADANIA") }}-{{ $infoEstudio->id }}">
                                                    @if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CEDULA_DE_CIUDADANIA"))) == 0)
                                                    <a class="pointer color-negro"  data-toggle="modal" data-target="#modal{{ config("constantes.CEDULA_DE_CIUDADANIA") }}-{{ $infoEstudio->id }}"><span class="fa fa-arrow-up fa-1x color-negro" title="Adjuntar Certificado Laboral"></span></a>
                                                    @else                                                                                        
                                                    {{ $ComponentAdjuntos->getUrlViewAdjunto($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                                    @endif
                                                </span>
                                                <span id="EdadUser">{{$infoEstudio->Edad}}</span> años
                                            </div>
                                        </div>
                                    </div>    

                                    <div class="formizq">
                                        <div class="input-grupo">
                                            <div class="input-grupo-label">F INI CONTRATO:</div>
                                            <input type="text" class="desplegarCalendario input-grupo-input font-11 fechasContrato" id="fecha_inicio_contrato" value="{{ $infoEstudio->FechaInicioContrato }}">                                                                                         
                                            <div class="input-grupo-respuesta" id="tiempoTrabajadoAlDia">{{ $infoEstudio->AntiguedadMeses }}</div>
                                        </div>
                                    </div>    

                                    <div class="formizq">
                                        <div class="input-grupo">
                                            <div class="input-grupo-label">CARGO:</div>
                                            <select name="cargo" id="cargo" class=" input-grupo-input text-center  font-11">
                                                @foreach($UtilidadesClass->cargos as $key => $cargo)
                                                    <option value="{{ $key }}" {{ ($infoEstudio->cargo == $key)? "selected='true'" : "" }}>{{ $cargo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>    

                                    <div class="formizq">
                                        <div class="input-grupo">
                                            <div class="input-grupo-label">SEGURO VIDA:</div>
                                            <select class=" input-grupo-input text-center font-11" id="asegurado">
                                                <option value="0" {{ ($infoEstudio->Seguro == "0")? "selected" : "" }}>NO ASEGURADO</option>                                                            
                                                <option value="1" {{ ($infoEstudio->Seguro == "1")? "selected" : "" }}>ASEGURADO</option>
                                            </select>            
                                            <div class="input-grupo-respuesta containerAdjuntoAsegurado" id="dsp_modal{{ config("constantes.SEGURO_DE_VIDA") }}-{{ $infoEstudio->id }}" style="{{ ($infoEstudio->Seguro == "1")? "" : "display: none" }}">
                                                @if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.SEGURO_DE_VIDA"))) == 0)
                                                <a class="pointer color-negro"  data-toggle="modal" data-target="#modal{{ config("constantes.SEGURO_DE_VIDA") }}-{{ $infoEstudio->id }}"><span class="fa fa-arrow-up fa-1x color-negro" title="Adjuntar Seguro de Vida"></span></a>
                                                @else                                                                                        
                                                {{ $ComponentAdjuntos->getUrlViewAdjunto($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.SEGURO_DE_VIDA") ) }}
                                                @endif                                                                                         
                                                <span id="resultMesesSeguro">{{ (!empty($infoEstudio->MesesVigenciaSeguro))? $infoEstudio->MesesVigenciaSeguro." meses" : "" }}</span>
                                            </div>                                                                                         
                                        </div>
                                    </div>    

                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="background-white margin-top-10">
                    <div class="portlet box main-color sinMarginBottom pMaximo">
                        <div class="portlet-title">
                            <strong> {{ config('constantes.EST_CONT1_CAPACIDAD_PAGO') }}: </strong><strong class="color-blue" id="CuotaMaxima">{{ $infoEstudio->CuotaMaxima }}</strong>
                        </div>
                        <div class="portlet-body Cpago">  
                            <div class="row">
                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">
                                    <div class="row totalesObligaciones padding-right-10 sinPadding">                                      
                                        <div class="col-md-12 col-xs-12">
                                            <p class="text-right font-11"><span class="pointer dspModalMiniCalculadora">{{ config('constantes.EST_CONT1_DIS') }}:</span> <span id="DisponibleOriginal">{{ $infoEstudio->Disponible }}</span></p>
                                            <p class="text-right font-11"><span class="pointer dspModalMiniCalculadora">{{ config('constantes.EST_CONT1_DISP') }}:</span> <span id="DisponibleConCompras">{{ $infoEstudio->ValorCompras }}</span></p>
                                            <p class="text-right font-11"><span class="pointer dspModalMiniCalculadora">{{ config('constantes.EST_CONT1_CAPACIDAD') }}:</span> <span><strong id="DescuentoCalculadora">{{ $infoEstudio->CapDescuentoDesprendible }}</strong></span></p>                        
                                        </div>
                                    </div>  
                                </div>

                                <div class="col-md-6 col-xs-6 col-sm-6 col-lg-6 padding-left-10 sinPadding">
                                    <table class="tablaTotales">
                                        <tr>
                                            <td class="text-left font-11">{{ config('constantes.EST_CONT1_CONSUMO_GASTOS') }}:</td>
                                            <td class="text-center">
                                                <select class="font-11" id="porcentajeGastoFijo">
                                                    @for($i = 40; $i >= 25; $i = $i-5)                                        
                                                    <option value="{{ $i }}" {{ ($infoEstudio->GastoFijo == $i)? "selected" : "" }}>{{ $i }}%</option>
                                                    @endfor                                        
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">
                                                <span class="fa fa-plus pointer font-11" data-toggle="modal" data-target="#ingresosAdicionalesMDL"></span> {{ config('constantes.EST_CONT1_OTHERS_INGRESOS') }}:&nbsp; 
                                            </td>
                                            <td class="text-left font-11" id="ingresosAdicionales"> {{ $ingresosAdicionales["totalIngresosAdicionales"] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left font-11 pointer modalDetalleCapacidad">{{ config('constantes.EST_CONT1_CAPACIDAD_DE_PAGO') }}:</td>
                                            <td class="text-left font-11"><strong id="totalCalculoCapacidad">{{ $infoEstudio->Capacidad }}</strong></td>
                                        </tr>
                                    </table> 
                                </div>
                            </div>                                
                        </div>
                    </div>
                </div>
            </div>
            <div class="Ebody">
                <div class="background-white">                       
                    <div class="portlet box main-color sinMarginBottom resumenOperacion">
                        <div class="portlet-title" style="padding: 2px;">
                            <strong>RESUMEN OPERACIÓN</strong>
                        </div>
                        <div class="portlet-body Cpago" style="padding-top: 7px!important;">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right: 0px; padding-left: 12px;">                                                                                
                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon pointer" data-toggle="modal">GARANTIA</div>
                                            <input class="form-control" id="garantia" name="garantia" value="" disabled="true" readonly="true">                                            
                                        </div>
                                    </div>    
                                    <hr style="margin-top: -2px;margin-bottom: 5px;border-top: 1px solid #4266b2;margin-right: -32px;">
                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon">TASA</div>
                                            <input class="form-control" id="ROtasa" name="ROtasa" value="{{ $infoEstudio->Tasa }}">
                                        </div>
                                    </div>

                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon">CUOTA</div>
                                            <input class="form-control inputEditableMiles" id="ROcuota" name="ROcuota" value="{{ $infoEstudio->Cuota }}">
                                        </div>
                                        <span class="color-red" id="error-ROcuota"></span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos" style="padding-right: 19px; padding-left: 0px;">                                        
                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon pointer" data-toggle="modal" data-target="#modalCalculoBanco">BANCO</div>
                                            <input class="form-control" id="cifraBanco" name="cifraBanco" value="{{ (isset($valorCreditoBancoSeleccionado) && !empty($valorCreditoBancoSeleccionado) && $valorCreditoBancoSeleccionado > 0)? number_format($valorCreditoBancoSeleccionado, 0, ",", ".") : 0 }}" disabled="true" readonly="true">                                            
                                        </div>
                                    </div>   
                                    <hr style="margin-top: -2px;margin-bottom: 5px;border-top: 1px solid #4266b2;margin-right: -7px;">
                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon">PLAZO</div>
                                            <input class="form-control" id="ROplazo" name="ROplazo" value="{{ $infoEstudio->Plazo }}">
                                        </div>
                                        <span class="color-red" id="error-ROplazo"></span>
                                    </div>

                                    <div class="myStyleInput">
                                        <div class="input-group">
                                            <div class="input-group-addon">CRÉDITO</div>
                                            <input class="form-control resultado" id="ROcredito" name="ROcredito" value="{{ $infoEstudio->ValorCredito }}" disabled="true" readonly="true">
                                        </div>
                                    </div>

                                </div>  
                            </div>                                              
                        </div>
                    </div>                


                        <div class="portlet box main-color sinMarginBottom resumenOperacion">
                            <div class="portlet-title" style="padding: 2px;">
                                <strong>CREDITO MÁXIMO</strong>
                            </div>
                            <div class="portlet-body Cpago" style="padding-top: 7px!important;">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"
                                         style="padding-right: 0px; padding-left: 12px;">
                                        <div class="myStyleInput">
                                            <div class="input-group">
                                                <div class="input-group-addon pointer" id="dspModalCostos">COSTOS</div>
                                                <input disabled class="form-control" id="CMcostos"
                                                       name="CMcostos"
                                                       value="{{ ($infoEstudio->DatosCostos != false && $infoEstudio->DatosCostos["totalCostosV"] > 0)? number_format($infoEstudio->DatosCostos["totalCostosV"], 0, ",", ".") : "" }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos"
                                         style="padding-right: 22px; padding-left: 0px;">
                                        <div class="myStyleInput">
                                            <div class="input-group">
                                                <div class="input-group-addon pointer" data-toggle="modal"
                                                     data-target="#modalBeneficios">BENEFICIOS
                                                </div>
                                                <input class="form-control" id="beneficios" name="beneficios"
                                                       value="{{ ($infoEstudio->DatosBeneficios != false && $infoEstudio->DatosBeneficios["totalBeneficiosV"] > 0)? number_format($infoEstudio->DatosBeneficios["totalBeneficiosV"], 0, ",", ".") : "" }}"
                                                       disabled="true" readonly="true">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="portlet box main-color sinMarginBottom pMaximo creditoMaximo"
                             style="margin-top: -1px;">
                            <div class="portlet-title">
                                <div class="inline-block" style="padding: 0 5px;">
                                    <strong>DESEMBOLSO: </strong><strong class="color-blue"
                                                                         id="Deselbolso">{{ $infoEstudio->Desembolso }}</strong>
                                </div>
                                <div class="inline-block" style="padding: 0 5px;">
                                    <strong>SALDO: </strong><strong class="color-blue"
                                                                    id="DeselbolsoCliente">{{ $infoEstudio->Saldo }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- Fin primera parte -->

    <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 " >
        <div class="container-uno right">
            <div class="Eheader">                               
                <div class="background-white">
                    <div class="container-table " style="max-height: 20.4em;overflow: auto;">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title">
                                <strong> {{ config('constantes.EST_CONT2_TITLE') }}</strong>
                                @if(count($listObligacionesCerradas) > 0)
                                    <span class="fa fa-ban pointer pull-left" data-toggle="modal" data-target="#modalObligacionesCerradas" title="Obligaciones Cerradas"></span>
                                    <span class="fa fa-plus pointer pull-left cargarModalAjax" data-url="/agregar-obligacion/{{$infoEstudio->id}}" data-toggle="modal" title="Agregar obligación"></span>
                                @endif
                                @if(count($listObligacionesInhabilitadas) > 0)
                                    <span class="fa fa-trash pointer pull-right" data-toggle="modal" data-target="#modalObligacionesInhabilitadas" title="Obligaciones Inhabilitadas"></span>
                                @endif
                            </div>
                            <div class="portlet-body portlet-todasObligaciones">
                                <table class="table table-striped table-hover text-center todasObligaciones">
                                    <thead>
                                        <tr>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_ENTIDAD') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_ESTADO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_SALDO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT1_CUO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_PAGO') }}</th>
                                            <!--<th class="text-center">{{ config('constantes.EST_CONT2_CERTI') }}</th>-->
                                            <th class="text-center"></th>
                                            <!--<th class="text-center"></th>-->
                                        </tr>
                                    </thead>
                                    <tbody id="obligacionesCompletas">
                                        <?php $cont = 0 ?>
                                            @foreach($listObligaciones as $obligacion)                                       
                                        <?php $cont++ ?>                                                                                    
                                        <tr class="{{ (in_array($obligacion->id, $arrayFaltaAdjunto))? "danger" : "" }}" data-obligacion="{{ $obligacion->id }}">
                                    <input type="hidden"  class="listaObligacionesFormulaEstudio"    data-desprendible="{{ $obligacion->Desprendible }}" 
                                           data-tipocuentaobligacion="{{ $obligacion->TipoCuotaEstudio }}" 
                                           data-compra="{{ $obligacion->Compra }}" 
                                           data-estadoobligacion="{{ $obligacion->EstadoCuenta }}"  
                                           data-valorcuota = "{{ $obligacion->ValorCuota }}" 
                                           data-valorsaldo = "{{ $obligacion->SaldoActual }}" 
                                           data-valorsaldooriginal = "{{ $obligacion->SaldoActualOriginal }}" 
                                           data-tipoobligacion="{{ $obligacion->tipoCuenta }}" 
                                           data-obligacion="{{ $obligacion->id }}" 
                                           data-adjunto="{{ $obligacion->idAdjunto}}" 
                                           data-pazsalvo="{{ $obligacion->pazSalvo }}" 
                                           data-soporte="{{ ($obligacion->soportePago == false)? 0 : $obligacion->soportePago}}" 
                                           data-cddrad="{{ ($obligacion->optionGestionObligacionesCDD == "hidden")? "S" : "N" }}" 
                                           data-cuotasproyectadas="{{ $obligacion->CuotasProyectadas }}" id="Sumatoria{{ $obligacion->id }}">                                        
                                    
                                    <td>                                        
                                        <a class="pointer text-center color-negro" data-toggle="modal" data-target="#infoObligacion{{ $obligacion->id }}" title="{{ $obligacion->Entidad }}">
                                            {{ (strlen($obligacion->Entidad) <= 12)? $obligacion->Entidad : substr($obligacion->Entidad, 0, 12) }}
                                        </a>
                                    </td>
                                    <td>
                                        <a id="Estado" name="Estado" class="llaveLock pointer estadoCuenta{{ $obligacion->id }} optionEstadoObligacion color-negro text-normal" data-value="{{ $obligacion->EstadoCuenta }}" data-title="Seleccione acción" data-disabled="{{ ($obligacion->EstadoCuenta == "PYS")? true : false }}">    
                                            {{ $obligacion->EstadoCuenta }}
                                        </a>
                                    </td>
                                    <td>
                                        <a id="SaldoActual" name="SaldoActual" class="llaveLock pointer saldoActual{{ $obligacion->id }} color-negro font-11 editableSaldo" data-inputclass="inputEditableMiles" data-title="Ingrese el Saldo Actual" style="{{ ((str_replace(".", "", $obligacion->SaldoActual) - $obligacion->SaldoActualOriginal) == 0)? "" : "color: red" }}" >{{ $obligacion->SaldoActual }}</a>                                        
                                    </td>
                                    <td>                                                
                                        <?php $valorCuota = (!empty($obligacion->ValorCuota) && $obligacion->ValorCuota > 0) ? false : (!empty($obligacion->CuotasProyectadas) && $obligacion->CuotasProyectadas > 0) ? true : false; ?>                                        
                                        <a  style=" {{ ($valorCuota)? "color: blue" : "" }}" id="ValorCuota" name="ValorCuota" class="llaveLock pointer valorCuota{{ $obligacion->id }} editableCuota color-negro font-11 {{ ($obligacion->Desprendible == "S")? "bold" : "" }}" data-inputclass="inputEditableMiles" data-type="text" data-title="Ingrese el valor de la cuota">{{ ($valorCuota)? number_format($obligacion->CuotasProyectadas, 0, ",", ".") : $obligacion->ValorCuota }}</a>
                                    </td>
                                    <td>
                                        <a id="Pago" name="Pago" class="llaveLock pointer compras{{ $obligacion->id }} {{ ($obligacion->EstadoCuenta == "MORA")? "editablePagoWithParcial" : "editablePago" }} color-negro text-normal" data-inputclass="changePago" data-type="select" data-title="Seleccione acción" data-value="{{ $obligacion->Compra }}">
                                            @if($obligacion->Compra == "S")
                                            Si
                                            @elseif($obligacion->Compra == "N")
                                            No
                                            @elseif($obligacion->Compra == "P")
                                            Parcial
                                            @endif
                                        </a>                                        
                                    </td>
                                    <td>    
                                        @if($obligacion->tieneAdjuntos)
                                            <a class="pointer text-center" data-toggle="modal" data-target="#modalAdjunto{{ $obligacion->id }}" id="Enlace{{ $obligacion->id }}"><span class="fa fa-paperclip color-negro" title="Cargar Adjunto"></span></a>                                                
                                        @else
                                            <a class="pointer text-center desplegarModalCDD-PYS" data-toggle="modal" data-target="#modalAdjunto{{ $obligacion->id }}" id="Enlace{{ $obligacion->id }}"><span class="fa fa-arrow-up color-negro" title="Cargar Adjunto"></span></a>                                                
                                        @endif
                                    </td>
                                    </tr>
                                    @endforeach                                    
                                    @if($cont < 11)
                                    @for($i = $cont; $i < 11; $i++)
                                    <tr class="EliminarCeldaEnMovil">
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    @endfor
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 
                    <div class="font-11 container-totales-obligaciones border-red" style="height: 18px;">
                        <div class="inline-block pull-left  negrita" style="min-width: 28%;">TOTALES</div>
                        <div class="inline-block pull-left height-celda-vacia espacio"></div>
                        <div class="inline-block negrita pull-left " style="min-width: 20%;" id="sumaComprasSaldo">{{ $sumaComprasSaldo }}</div>
                        <div class="inline-block negrita pull-left " style="min-width: 20%;" id="sumaComprasCuotas">{{ $sumaComprasCuotas }}</div>                                                    
                        <div class="inline-block  height-celda-vacia" style="min-width: 16%;"></div>
                    </div>                                           
                </div>
            </div>
            <div class="Ebody">
                <div class="background-white">

                    <div class="container-table ">
                        <div class="portlet box main-color sinMarginBottom">
                            <div class="portlet-title">
                                <strong> REQUISITOS </strong>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-hover text-center todasObligaciones">
                                    <!--<thead>
                                        <tr>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_ENTIDAD') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_ESTADO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_SALDO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT1_CUO') }}</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT2_PAGO') }}</th>
                                            <!--<th class="text-center">{{ config('constantes.EST_CONT2_CERTI') }}</th>-->
                                            <!--<th class="text-center"></th>
                                            <!--<th class="text-center"></th>-->
                                    <!--</tr-->
                                    <!--</thead>-->
                                    <tbody>                                        
                                        <tr>
                                            <td>Solicitud de Crédito</td>
                                            <td>
                                                <span id="dsp_modal{{ config("constantes.LIBRANZA_FIRMADA") }}-{{ $infoEstudio->id }}">
                                                    @if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_FIRMADA"))) == 0)
                                                        @php
                                                            $adjuntoLbzFirmada = 0
                                                        @endphp
                                                        <a class="pointer color-negro" data-toggle="modal"
                                                           data-target="#modal{{ config("constantes.LIBRANZA_FIRMADA") }}-{{ $infoEstudio->id }}"><span
                                                                    class="fa fa-arrow-up fa-1x color-negro"
                                                                    title="Adjuntar Libranza"></span></a>
                                                    @else
                                                        @php
                                                            $adjuntoLbzFirmada = 1
                                                        @endphp
                                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_FIRMADA") ) }}
                                                    @endif
                                                </span>
                                            </td>                                            
                                            <td>
                                                <label for="cuotaVisado">VISADO:&nbsp;</label>
                                                <a id="cuotaVisado" name="cuotaVisado" class="pointer" data-inputclass="puntosMiles" data-type="text" data-title="Ingrese la Cuota del Visado">{{ (!empty($infoEstudio->cuotaVisado) && $infoEstudio->cuotaVisado > 0)? number_format($infoEstudio->cuotaVisado, 0, ",", ".") : "0" }}</a>                                                
                                            </td>
                                            <td>
                                                <span>
                                                    <a class="pointer color-negro" data-toggle="modal"
                                                       data-target="#modalVisado"><span
                                                                class="fa {{ ($adjuntoVisado > 0)? "fa-paperclip" :  "fa-arrow-up" }} fa-1x color-negro"
                                                                title="Adjuntar Visado"
                                                                style="{{ ($adjuntoVisado > 0)? "font-size: 15px" :  "" }}"></span></a>
                                                </span>
                                            </td>                                              
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> 

                    <div class="container-table margin-bottom-10  margin-top-10">
                        <div class="portlet box main-color">
                            <div class="portlet-title">
                                <div class="caption">
                                    <strong>{{ config('constantes.EST_CONT1_HUELLA') }} ({{ $listHuellasCount }})</strong>
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div class="portlet-body" style="display: none;">
                                <table class="table table-striped table-hover text-center todasObligaciones">
                                    <thead>
                                        <tr>
                                            <th class="text-center">{{ config('constantes.EST_CONT1_ENT') }}</th>
                                            <th class="text-center">CENTRAL</th>
                                            <th class="text-center">{{ config('constantes.EST_CONT1_FECHA') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($listHuellas as $huella)                                       
                                        <tr>
                                            <td>{{ $huella->Entidad }}</td>
                                            <td>{{ $huella->CentralInformacion }}</td>
                                            <td>{{ $huella->Fecha }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  

                    <div class="container-table">
                        <div class="portlet box main-color" style="margin-bottom: 0">
                            <div class="portlet-title">
                                <span class="fa fa-refresh pointer pull-left getProcesosJuridicos" style="margin-top: 3px;" title="Obligaciones Cerradas" data-options='{"cedula": {{ $infoUser->cedula }}, "idValoracion": {{$infoEstudio->Valoracion}}, "url": "{{ config("constantes.RUTA") }}Estudio/getDataJuridico"}'></span>
                                <div class="caption">
                                    <strong>{{ config('constantes.EST_CONT1_PROCE_JURI') }}</strong>
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>                                    
                            </div>
                            <div class="portlet-body" style="display: none;" id="container_procesosJuridicos">
                                <?php echo $vistaProcesosJuridicos  ?>
                            </div>
                        </div>
                    </div>  

                </div>
                <div class="background-white margin-top-10">

                        <div class="container-table ">
                            <div class="portlet box main-color sinMarginBottom">
                                <div class="portlet-title">
                                    <strong> ACCIONES</strong>
                                </div>
                                <div class="portlet-body container-acciones">
                                    <div class="row" style="margin: 6px 0; padding: 0">
                                        <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 desicion">
                                            PREAPROBADO: <span id="DesicionEstudio"></span>
                                        </div>
                                        <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 estado">
                                            <span class="cargarModalAjax" data-url="/trackingEstudio/{{$infoEstudio->id}}" style="cursor: pointer; color: #0b94ea;">ESTADO:</span> <span class="uppercase" id="EstadoEstudioLbl"
                                                          data-codigo="{{ strtoupper($infoEstudio->Estado) }}">{{ $estadosEstudio[strtoupper($infoEstudio->Estado)] }}</span>
                                        </div>
                                        <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 buttonUltimo">
                                            @if(strtoupper($infoEstudio->Estado) == config("constantes.ESTUDIO_NEGADO") && $dias <= 40)
                                                <select name="accionEstudio" id="accionEstudio" class="form-control"
                                                        style="height: 17px; padding: 0; margin: 0 auto">
                                                    <option value="0">Sel acción</option>
                                                    <option value="GUA">Guardar</option>
                                                    <option value="PEN">Pendiente</option>
                                                </select>
                                            @endif
                                            @if(strtoupper($infoEstudio->Estado) != config("constantes.ESTUDIO_NEGADO"))
                                                <select name="accionEstudio" id="accionEstudio" class="form-control"
                                                        style="height: 17px; padding: 0; margin: 0 auto">
                                                    <option value="0">Sel acción</option>
                                                    <option value="GUA">Guardar</option>
                                                    @if($login->perfil == config("constantes.PERFIL_ROOT") && $infoEstudio->Estado == "COM" && count($aprobaciones) < 2 )
                                                        <option value="APR">Aprobar</option>
                                                    @endif
                                                    <option value="NEG">Negado</option>
                                                    <option value="PEN">Pendiente</option>
                                                    <option value="DES">Desistio</option>
                                                </select>
                                            @endif

                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"
                                             style="margin-top: 22px!important">
                                            <button data-url="/mostrarComentarios/estudio/{{$infoEstudio->id}}" type="button" class="btn btn-primary  btn-two cargarModalAjax"><span
                                                        class="fa fa-tasks"></span> COMENTARIOS
                                            </button>
                                            @if(strtoupper($infoEstudio->Estado) !== config("constantes.ESTUDIO_NEGADO"))
                                                @if(count($aprobaciones) == 1)
                                                <button type="button" class="btn btn-primary  btn-one" id="aprobacionLbl" data-toggle="modal" data-target="#modalAprobacion"> APRUEBA: {{substr($aprobaciones[0]->usuario->nombre,0,3)}}
                                                </button>
                                                @elseif(count($aprobaciones) >= 2)
                                                <button type="button" class="btn btn-primary  btn-one" id="aprobacionLbl" data-toggle="modal" data-target="#modalAprobacion"> APROBADO
                                                </button>
                                                @else
                                                <button type="button" class="btn btn-primary  btn-one" id="aprobacionLbl" data-toggle="modal" data-target="#modalAprobacion">APRUEBA:
                                                </button>
                                                @endif
                                                <button type="button" class="btn btn-primary  btn-three"
                                                        id="btn_viabilizar" {{ ($infoEstudio->viabilizado)? 'data-viabilizado="'.$infoEstudio->viabilizado.'" disabled' : "" }}>
                                                    <span class="fa fa-magic"></span> VIABILIZAR
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>                    

            </div>     
        </div>     
    </div>              
    <input class="uppercase" type="hidden" value="{{ strtoupper($infoEstudio->Estado) }}" id="EstadoEstudio">
    <input type="hidden" value="{{ config("constantes.RUTA") }}" id="dominioPrincipal">
        <input type="hidden" value="{{ $infoEstudio->aprobado }}" id="aprobado">
        <input type="hidden" value="{{ $dias }}" id="diasCreacion">
        <input type="hidden" value="{{ count($aprobaciones) }}" id="cant_apro">
        <input type="hidden" value="{{ $infoEstudio->id }}" id="Identificacion_Estudio">
        <input type="hidden" value="{{ $login->id }}" id="Identificacion_UserLogin">
        <input type="hidden" value="{{ $login->nombre }}" id="Nombre_UserLogin">
        <input type="hidden" value="{{ $EdadSeguro  }}" id="edadSeguro">
    <input type="hidden" value="{{ $retiroForzoso }}" id="retiroForzoso">
    <input type="hidden" value="{{ $infoEstudio->Saldo }}" id="DeselbolsoClienteReal">
    <input type="hidden" id="EstudioIngresoCapacidad" value="{{ $infoEstudio->IngresoBase }}">
    <input type="hidden"  name="_token" id="_token" value="{{ csrf_token() }}">
    <input type="hidden"  name="parameters" id="parameters" value="{{ $parametros }}">
    <input type="hidden"  name="idValoracion" id="idValoracion" value="{{ $infoValoracion->id  }}">
    <input type="hidden"  name="pv" id="pv" value="{{ json_encode(["PuntajeData" => $infoValoracion->PuntajeData, "PuntajeCifin" => $infoValoracion->PuntajeCifin])  }}">
    <input type="hidden"  name="entidadesDondeQuedoEnMora" id="entidadesDondeQuedoEnMora" value="{{ json_encode($entidadesDondeQuedoEnMora)  }}">
    <input type="hidden"  name="idEntidadesDondeQuedoEnMora" id="idEntidadesDondeQuedoEnMora" value="{{ json_encode($idEntidadesDondeQuedoEnMora)  }}">
    <input type="hidden"  name="costoSeguro" id="costoSeguro" value="{{ $infoEstudio->costoSeguro }}">
    <input type="hidden"  name="valorXmillon" id="valorXmillon" value="{{ $valorXmillon }}">
    <input type="hidden" value="{{ $adjuntoVisado }}" class="adjuntoVisado" id="adjunto{{ config("constantes.VISADO") }}-{{ $infoEstudio->id }}">
    <input type="hidden" value="{{ $adjuntoLbzFirmada }}" class="adjuntoLbzFirmada" id="adjunto{{ config("constantes.LIBRANZA_FIRMADA") }}-{{ $infoEstudio->id }}">
    <input type="hidden" value="{{ $adjuntoAutorizacionConsulta }}" id="adjuntoAutorizacionConsulta">
    <input type="hidden" value="{{ json_encode($ValoracionesController->cuentasCuotaVariable) }}" id="obligacionesCuotaVariable">
    <input type="hidden" value="{{ base64_encode(utf8_encode(json_encode($listObligaciones))) }}" id="listObligaciones">
    <input type="hidden" value="{{ $infoEstudio->IngresoBase }}" id="cupo_ingreso">
    <input type="hidden" value="{{ $infoEstudio->TotalEgresos }}" id="cupo_egreso">
    <input type="hidden" value="{{ $infoEstudio->Ley1527 }}" id="cupo_ley1527">
    <input type="hidden" value="{{ $infoEstudio->Cupo }}" id="cupo_cupo">
    
    <!--solucion para cambiar los costos solamente si el estudio se encuentra en un estado igual o superior a Tesoreria-->
    @if(in_array($infoEstudio->Estado, [
                                                                    config("constantes.ESTUDIO_SAVE"), 
                                                                    config("constantes.ESTUDIO_APROBADO"), 
                                                                    config("constantes.ESTUDIO_RADICADO"), 
                                                                    config("constantes.ESTUDIO_INGRESADO"),
                                                                    config("constantes.ESTUDIO_NOVIABLE"),
                                                                    config("constantes.ESTUDIO_VIABLE"),
                                                                    config("constantes.ESTUDIO_FIRMADO"),
                                                                    config("constantes.ESTUDIO_COMITE"),
                                                                    config("constantes.ESTUDIO_VISADO"),
                                                                    config("constantes.ESTUDIO_PENDIENTE"),
                                                                    config("constantes.ESTUDIO_DESISTIO"),
                                                                    config("constantes.ESTUDIO_NEGADO")]))
        <input type="hidden" value="1" id="correrAlgoritmoCostosAndBeneficios">
    @else
        <input type="hidden" value="0" id="correrAlgoritmoCostosAndBeneficios">
    @endif
</div>

<!-- Modal Detalle trazabilidad aprobacion estudio-->
<div class="modal fade modalEstudio" id="modalAprobacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12" id="table_logAprobacion">
                    <h4 class="text-center bold">Trazabilidad de Aprobaci&oacute;n</h4>
                    <table class="table table-striped table-hover text-center">
                        <thead class="head-inverse">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Fecha de aprobaci&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($aprobaciones as $item)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->usuario->nombre." ".$item->usuario->apellido}}</td>
                                <td>{{$item->created_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Fin de modal -->
@endsection
