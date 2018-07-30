@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@extends('layout.default')

@section('encabezado')
    <link href="{{ asset('css/Tesoreria/index.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <style>
        h4{
          text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .desc-info label{
            font-weight: bold !important;
        }
    </style>
@endsection
<!--Inicio Modal Detalle cliente-->
<div class="modal fade modalEstudio" id="detalleCliente_{{$infoUserEstudio[0]->users_id}}" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
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
                            <div class="text">{{$infoUserEstudio[0]->nombre}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Cedula</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->cedula}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Telefono</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->telefono}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Correo</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->email}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Direcci&oacute;n</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->direccion}}</div>
                        </div>

                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-right: 2px solid #ccc;">

                        <div class="form-group">
                            <div class="subtitle"><strong>Departamento</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->departamento}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Municipio</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->municipio}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Pagaduria</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->pagaduria}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Banco</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->banco}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Tipo de Cuenta</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->tipo_cuenta}}</div>
                        </div>

                        <div class="form-group">
                            <div class="subtitle"><strong>Numero de Cuenta</strong></div>
                            <div class="text">{{$infoUserEstudio[0]->numero_de_cuenta}}</div>
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
<!--Fin Modal Detalle cliente-->

<div class="modal fade modalEstudio" id="modalPago" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row desc-info" id="modal-pago-data">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="generarPago">Generar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modalEstudio" id="modalPagoOpciones" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row desc-info" style="text-align: center" id="modal-pago-data">

                        <button type="button" style="min-width: 200px" onclick="generarPago(0)" class="btn btn-primary" id="generarPago">TRANSFERENCIA</button>
                        <br>
                        <br>
                        <button type="button" style="min-width: 200px" onclick="generarPago(1)" class="btn btn-primary" id="generarPago">CHEQUE</button>
                        <br>
                        <br>
                        <button type="button" style="min-width: 200px" onclick="generarPago(2)" class="btn btn-primary" id="generarPago">EFECTIVO</button>
                    </div>
                </div>
            </div>
            <div style="padding:3px" class="modal-footer">
                <button type="button" style="padding: 3px 12px !important;" class="btn btn-danger btm-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@section('banner')
    <div class="bannerTop">
        <div class="row" style="margin: 0">
            <div class="col-xs-3 col-sm-2 col-md-2 bannerTop-left">
                <a class="pointer" data-toggle="modal" data-target="#detalleCliente_{{$infoUserEstudio[0]->users_id}}"
                   style="color:white;"><span>{{ ($infoUserEstudio[0]->cedula > 0)? number_format($infoUserEstudio[0]->cedula, 0,",",".") : "N/A" }}</span></a>
            </div>
            <div class="col-xs-9 col-sm-8 col-md-8 text-center sinPaddingRigthMovil texto-derecha">
                <span>{{ (!empty($infoUserEstudio[0]->nombre))? utf8_decode($infoUserEstudio[0]->nombre) : "" }} {{ (!empty($infoUserEstudio[0]->apellido))? utf8_decode($infoUserEstudio[0]->apellido) : "" }}</span>
            </div>
            <div class="hidden-xs col-sm-2 col-md-2 bannerTop-right">
                <span><span class="uppercase">{{ $infoUserEstudio[0]->Pagaduria }}</span></span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="modal fade modalEstudio" id="vtnGiroCliente" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <img src="{{ config('constantes.RUTA') }}/assets/layouts/layout5/img/logosistema.png" alt=""
                         class="logo">
                </div>
                <div class="modal-body">
                    <h1 class="tituloGiro">Nuevo Giro</h1>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Tipo de Giro:
                            </label>
                            <div class="col-sm-8">
                                <select id="slTipoGiro" class="form-control select2">
                                    <option value="Desembolso" selected>Desembolso</option>
                                    <option value="Adelanto">Adelanto</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Valor:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txValor" name="txValor" class="form-control puntosMiles"
                                       required>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal" id="frmAdjCliente" name="frmAdjCliente" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Soporte:
                            </label>
                            <div class="col-sm-8">
                                <input type="file" class="cliente" id="fAdjCliente" name="fAdjCliente"
                                       data-estudio="{{$estudio}}">
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" id="btAdicionar" name="btAdicionar" class="btn btn-default">Adicionar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @if($infoCostosEstudio != false)

    @endif

    @foreach($Obligaciones as $obligacion)
        @if(in_array($obligacion->estadoGestionObligacion, ["RAD", "VEN", "PAG"]))
            <div class="modal fade modalEstudio  " id="ModalAdjunto{{ $obligacion->id }}" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <img src="{{ config('constantes.RUTA') }}/assets/layouts/layout5/img/logosistema.png" alt=""
                                 class="logo">
                        </div>
                        <div class="modal-body">
                            @if(in_array($obligacion->estadoGestionObligacion, ["RAD", "VEN"]))
                                {{$ComponentAdjuntos->dspFormulario($obligacion->id, config("constantes.KEY_OBLIGACION"), config("constantes.SOPORTE_PAGO"), config("constantes.MDL_VALORACION"), false, "refresh", false, true, $obligacion->Entidad, "AdjuntosCargadosTesoreria".$obligacion->id, "cambiarEstadoGestionObligaciones")}}
                                <br>
                            @endif
                            <div id="AdjuntosCargadosTesoreria{{ $obligacion->id }}">
                                {{$ComponentAdjuntos->createTableOfAdjuntos($obligacion->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_OBLIGACION"), config("constantes.SOPORTE_PAGO"), "refresh", "devolverEstadoGestionObligaciones") }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <div class="row tesoreria">
        <div class="col-md-12 col-sm-12 sinPaddingMovil">
            <h1 class="text-center title-tesoreria">
                <i class="fa fa-file-text"></i>
                <span class="caption-subject font-dark sbold uppercase">Detalle Tesorería</span>
            </h1>

            <div class="portlet box gris personalizado">
                <div id="cuerpo" class="portlet-body">
                    <div class="row">

                        <div class="col-md-4 col-xs-4 movil-50">
                            <p class="text-center" style="margin: 0"><strong
                                        class="lblCredito">TASA: </strong>{{ $infoUserEstudio[0]->Tasa }}%</p>
                        </div>
                        <div class="col-md-4 col-xs-4 movil-50">
                            <p class="text-center" style="margin: 0"><strong
                                        class="lblCredito">PLAZO: </strong>{{ $infoUserEstudio[0]->Plazo }}</p>
                        </div>
                        <div class="col-md-4 col-xs-4 movil-50">
                            <p class="text-center" style="margin: 0"><strong
                                        class="lblCredito">CUOTA: </strong>{{ ($infoUserEstudio[0]->Cuota > 0)? "$".number_format(round($infoUserEstudio[0]->Cuota), 0, ",", ".") : 0 }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="portlet-title">
                    <div class="row title-personalizado" style="margin: 0">
                        <div class="col-xs-4 col-md-4 text-center sinPaddingMovil">
                            <span class="label-valor">CREDITO:</span>
                            {{ ($infoUserEstudio[0]->ValorCredito > 0)? "$".number_format(round($infoUserEstudio[0]->ValorCredito), 0, ",", ".") : 0 }}
                        </div>
                        <div class="col-xs-4 col-md-4 text-center sinPaddingMovil">
                            <span class="label-valor">COSTO:</span>
                            {{ ($infoCostosEstudio != false && $infoCostosEstudio["totalCostosV"] > 0)? "$".number_format(round($infoCostosEstudio["totalCostosV"]), 0, ",", ".") : 0 }}
                        </div>
                        <div class="col-xs-4 col-md-4 text-center sinPaddingMovil">
                            <span class="label-valor">DESEMBOLSO:</span>
                            {{ ($infoUserEstudio[0]->Desembolso > 0)? "$".number_format(round($infoUserEstudio[0]->Desembolso), 0, ",", ".") : 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="portlet box main-color">
                <div class="line-progress" style="width: {{ $progressComprasObligaciones }}%"
                     title="Comprado el {{ round($progressComprasObligaciones, 2) }}% de las obligaciones"></div>
                <div class="portlet-title text-center">
                    <div class="caption" style="float: none">
                        <i class="fa fa-shopping-cart" style="color: #fff"></i> TOTAL COMPRAS:
                        <span>{{ ($totalSaldoObligaciones > 0)? "$".number_format($totalSaldoObligaciones, 0, ",", ".") : 0 }}</span>
                    </div>
                    <div class="tools">
                        <a id="cierre" href="javascript:;" class="expand"></a>
                    </div>
                </div>
                <div id="cuerpo" class="portlet-body" style="display: none">
                    <div class="row">
                        <div class="col-md-6 col-xs-6 text-center margin-saldos">
                            <label class="bold descripcionSaldos">COMPRADO: </label><span
                                    class="font-16">{{ ($totalCompradas > 0)? " $".number_format($totalCompradas, 0, ",", ".") : 0 }}</span>
                        </div>
                        <div class="col-md-6 col-xs-6 text-center margin-saldos">
                            <label class="bold descripcionSaldos">RESTANTE: </label><span
                                    class="font-16">{{ ($totalFaltanteCompras > 0)? " $".number_format($totalFaltanteCompras, 0, ",", ".") : 0 }}</span>
                        </div>
                        <div class="col-md-12">
                            <table class="table myTable table-hover text-center iniciarDatatable">
                                <thead>
                                <tr>
                                    <th class="text-center">ENTIDAD</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">VALOR</th>
                                    <th class="text-center">F.V</th>
                                    <th class="text-center">F.P</th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($Obligaciones as $obligacion)
                                    <tr>
                                        <td class="{{ (!is_null($obligacion->idAdjunto) && $obligacion->estadoGestionObligacion == config("constantes.GO_PAGADA"))? "bold" : "" }}">{{ $obligacion->Entidad }}
                                            @if($obligacion->estadoGestionObligacion != config("constantes.GO_PAGADA"))
                                            <a style="    position: absolute;     left: 20px;"
                                               data-beneficiario="{{ $obligacion->Entidad }}"
                                               data-valorpago="{{ ($obligacion->SaldoActual > 0)? "$".number_format($obligacion->SaldoActual, 0, ",", ".") : 0 }}"
                                               class="pointer blue print-document"><span class="fa fa-file"></span></a>
                                            @endif
                                        </td>
                                        <td class="uppercase">{{ $obligacion->estadoGestionObligacion }}</td>
                                        <td>{{ ($obligacion->SaldoActual > 0)? "$".number_format($obligacion->SaldoActual, 0, ",", ".") : 0 }}</td>
                                        <td>{{ date('d-m-Y',strtotime($obligacion->fechaVencimiento)) }}</td>
                                        <td>{{ (isset($obligacion->fechaPago))? date('d-m-Y',strtotime($obligacion->fechaPago)) : "N/A" }}</td>
                                        @if(in_array($obligacion->estadoGestionObligacion, ["RAD", "VEN", "PAG"]))
                                            <td><a class="pointer enlace-black"><span
                                                            class="fa {{ ($obligacion->estadoGestionObligacion == "PAG")? "fa-paperclip" : "fa-arrow-up" }}"
                                                            data-toggle="modal"
                                                            data-target="#ModalAdjunto{{ $obligacion->id }}"></span></a>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portlet box gris">
                <div class="line-progress" style="width: {{ $progressSaldoCliente }}%"
                     title="Comprado el {{ round($progressSaldoCliente, 2) }}% de las obligaciones"></div>
                <div class="portlet-title text-center">
                    <div class="caption" style="float: none; vertical-align: middle">
                        <i class="fa fa-money" style="color: #fff"></i> SALDO: <span
                                id="saldoCliente">{{ ($infoUserEstudio[0]->Saldo > 0)? "$".number_format($infoUserEstudio[0]->Saldo, 0, ",", ".") : 0 }}</span>
                    </div>
                    <div class="tools">
                        <a id="cierre" href="javascript:;" class="expand"></a>
                    </div>
                    @if($progressSaldoCliente < 100)
                        <div class="actions">
                            <a class="btn btn-default transparente btn-sm pointer" data-toggle="modal"
                               data-target="#vtnGiroCliente">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        </div>
                    @endif
                </div>
                <div id="cuerpo" class="portlet-body" style="display: none">
                    <div class="row">
                        <div class="col-md-6 col-xs-6 text-center margin-saldos">
                            <label class="descripcionSaldos bold">GIRADO: </label><span id="ValorGirado"
                                                                                        class="font-16">{{ ($totalGirado > 0)? " $".number_format($totalGirado, 0, ",", ".") : 0 }}</span>
                        </div>
                        <div class="col-md-6 col-xs-6 text-center margin-saldos">
                            <label class="descripcionSaldos bold">RESTANTE: </label><span id="ValorPorGirar"
                                                                                          class="font-16">{{ ($restanteGiro > 0)? " $".number_format($restanteGiro, 0, ",", ".") : 0 }}</span>
                        </div>
                        <div class="col-md-12">
                            <table class="table myTable table-hover text-center iniciarDatatable">
                                <thead>
                                <tr>
                                    <th>TIPO</th>
                                    <th>VALOR</th>
                                    <th>FECHA</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="ListadoGirosRealizados">
                                @foreach($Giros as $giro)
                                    <tr>
                                        <td class="uppercase">{{ $giro->TipoGiro }}</td>
                                        <td>{{ ($giro->Valor > 0)? "$".number_format($giro->Valor, 0, ",", ".") : 0 }}</td>
                                        <td>{{ date('d-m-Y', strtotime($giro->created_at)) }}</td>
                                        <td>
                                            <a class="color-negro pointer" title="Visualizar"
                                               href="{{ config("constantes.RUTA") }}/visualizar/{{ $giro->idAdjunto }}"
                                               target="_blank">
                                                <span class="fa fa-paperclip fa-1x color-negro"></span></a>
                                        </td>
                                        <td><a class="pointer deleteGiro iconEliminar" data-id="{{ $giro->id }}"
                                               data-url="{{ config("constantes.RUTA") }}"
                                               data-valor="{{  intval($giro->Valor) }}"><span
                                                        class="fa fa-trash"></span></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventanas modales-->
            <input type="hidden" id="ValorPorGirarCopy" name="ValorPorGirarCopy"
                   value="{{ ($restanteGiro > 0)? $restanteGiro : 0 }}">
            <input type="hidden" id="ValorGiradoCopy" name="ValorGiradoCopy"
                   value="{{ ($totalGirado > 0)? $totalGirado : 0 }}">
            <input type="hidden" id="hnEstudio" name="hnEstudio" value="{{$estudio}}">
            <input type="hidden" id="dominioPrincipal" name="dominioPrincipal" value="{{ config("constantes.RUTA") }}">

        </div>
    </div>
    <script type="text/javascript">
        let data = {};
        var mediaquery = window.matchMedia("(max-width: 500px)");
        if (mediaquery.matches) {
            var containerNombre = $("#container-nombre").html();
            var containerPagaduria = $("#container-pagaduria").html();
            $("#container-nombre").html(containerPagaduria);
            $("#container-pagaduria").html(containerNombre);
        }
        $('#generarPago').click(function () {
            data = $('#modal-pago-data').find('form').serialize();
            const link = "/generarPagoTesoreria?" + data;
            var win = window.open(link, '_blank');
            win.focus();
            $('#modalPago').modal('hide');
        });

        function generarPago(type) {
            content = "";
            $('#modalPagoOpciones').modal('hide');
            switch (type) {
                case 0:
                    content = `<form action="" class="formName">
                                <h4>Pago por transferencia</h4>
                                <input type="hidden" value="0" name="tipo">
                                <div class="form-group">
                                <label>Beneficiario</label>
                                <input type="text" value="${data.beneficiario}" name="beneficiario" placeholder="Your name" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Valor a pagar</label>
                                <input type="text" value="${data.valorpago}" name="valorpago" placeholder="Your name" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Concepto</label>
                                <input type="text" value="Cancelación saldo crédito de {{$infoUserEstudio[0]->nombre}}" name="concepto" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Cedula o NIT</label>
                                <input type="text" placeholder="" class="form-control" name="documento" required />
                                </div>
                                <div class="form-group">
                                <label>Tipo de cuenta</label>
                                <input type="text" placeholder="" class="form-control" name="tipocuenta" required />
                                </div>
                                <div class="form-group">
                                <label>No. De cuenta</label>
                                <input type="text" placeholder="" class="form-control" name="numero_cuenta" required />
                                </div>
                                <div class="form-group">
                                <label>Entidad</label>
                                <input type="text" placeholder="" class="form-control" name="entidad" required />
                                </div>
                                </form>`;
                    break;
                case 1:
                    content = `<form action="" class="formName">
                                <h4>Cheque gerencia</h4>
                                <input type="hidden" value="1" name="tipo">
                                <div class="form-group">
                                <label>Beneficiario</label>
                                <input type="text" value="${data.beneficiario}" name="beneficiario" placeholder="Your name" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Valor a pagar</label>
                                <input type="text" value="${data.valorpago}" name="valorpago" placeholder="Your name" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Concepto</label>
                                <input type="text" value="Cancelación saldo crédito de {{$infoUserEstudio[0]->nombre}}" name="concepto" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Cedula o NIT</label>
                                <input type="text" placeholder="" class="form-control" name="documento" required />
                                </div>
                                <div class="form-group">
                                <label>Beneficiario</label>
                                <input type="text" placeholder="" class="form-control" name="persona_a_reclamar" required />
                                </div>
                                </form>`;
                    break;
                case 2:
                    content = `<form action="" class="formName">
                                <h4>Efectivo</h4>
                                <input type="hidden" value="2" name="tipo">
                                <div class="form-group">
                                <label>Entidad: </label>
                                <input type="text" value="${data.beneficiario}" name="beneficiario" placeholder="" class="form-control" required />
                                </div>
                                 <div class="form-group">
                                <label>Cedula o NIT</label>
                                <input type="text" placeholder="" class="form-control" name="documento" required />
                                </div>
                                <div class="form-group">
                                <label>Valor a pagar</label>
                                <input type="text" value="${data.valorpago}" name="valorpago" placeholder="Your name" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Concepto</label>
                                <input type="text" value="Pago de obligación" name="concepto" class="form-control" required />
                                </div>
                                <div class="form-group">
                                <label>Beneficiario</label>
                                <input type="text" name="persona_a_reclamar" class="form-control" required />
                                </div>
                                </form>`;
                    break;
            }

            types = ['Pago por transferencia', 'Cheque de gerencia', 'Efectivo'];
            $('#modal-pago-data').html(content);
            $('#modalPago').modal();
            /*data = this.$content.find('form').serialize();
                            const link = "/generarPagoTesoreria?" + data + '&tipo=' + type;
                            document.location = link;*/
        }


        $('.print-document').click(function (e) {
            e.preventDefault();
            data = $(this).data();
            $('#modalPagoOpciones').modal();
        });

    </script>
    <script src="{{ asset('js/Tesoreria/index.js') }}" type="text/javascript"></script>

@endsection