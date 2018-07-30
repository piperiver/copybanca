@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Bancos', 'App\Http\Controllers\EntidadesBancariasController')
@inject('Desembolsos', 'App\Http\Controllers\EntidadesDesembolsoController')
@inject('TiposCuenta', 'App\Http\Controllers\TiposCuentaController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Cuentas Bancarias </div>
                    <div class="actions">
                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                        <a href="" id="lkSave" name="lkSave" class="btn btn-default btn-sm" data-toggle="modal">
                            <i class="fa fa-plus"></i> Crear
                        </a>
                    @endif
                        <div class="btn-group">
                            <a class="btn btn-default btn-sm" href="javascript:;" data-toggle="dropdown">
                                <span class="hidden-xs"> Herramientas </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" id="sample_3_tools">
                                <li>
                                    <a href="javascript:;" data-action="0" class="tool-action">
                                        <i class="icon-printer"></i> Imprimir</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="1" class="tool-action">
                                        <i class="icon-check"></i> Copiar</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="2" class="tool-action">
                                        <i class="icon-doc"></i> PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="3" class="tool-action">
                                        <i class="icon-paper-clip"></i> Excel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="tabla">
                        <thead>
                            <tr>
                                <th> Entidad Bancaria </th>
                                <th> Entidad de Desembolso </th>
                                <th> Tipo de Cuenta </th>
                                <th> Cuenta </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($CuentasBancarias as $CuentaBancaria)
                                <tr id="{{$CuentaBancaria->Banco.'-'}}">
                                    <td>{{ $CuentaBancaria->Banco }}</td>
                                    <td>{{ $CuentaBancaria->EntidadDesembolso }}</td>
                                    <td>{{ $CuentaBancaria->TipoCuenta }}</td>
                                    <td>{{ $CuentaBancaria->Cuenta }}</td>
                                    <td>{{ $CuentaBancaria->updated_at }}</td>
                                    <td>{{ $CuentaBancaria->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                        <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-banco="{{$CuentaBancaria->Banco}}" data-entidaddesembolso="{{$CuentaBancaria->EntidadDesembolso}}" data-tipocuenta="{{$CuentaBancaria->TipoCuenta}}" data-cuenta="{{$CuentaBancaria->Cuenta}}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-banco="{{$CuentaBancaria->Banco}}" data-entidaddesembolso="{{$CuentaBancaria->EntidadDesembolso}}" data-tipocuenta="{{$CuentaBancaria->TipoCuenta}}">
                                                <i class='fa fa-close'></i>
                                            </a>
                                        @endif
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

    <!-- Ventanas modales-->
    <div class="modal fade" id="ventana" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
                    <h4 class="modal-title">Cuenta</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Entidad Bancaria:
                            </label>
                            <div class="col-sm-8">
                              <select id="slBanco" class="form-control select2 circle">
                                  @php($Lista = $Bancos->listarBancos())
                                  @foreach($Lista as $Banco)
                                      <option value="{{$Banco->Codigo}}">{{$Banco->Descripcion}}</option>
                                  @endforeach
                              </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Entidad de Desembolso:
                            </label>
                            <div class="col-sm-8">
                              <select id="slDesembolso" class="form-control select2 circle">
                                  @php($Lista = $Desembolsos->listarDesembolsos())
                                  @foreach($Lista as $Desembolso)
                                      <option value="{{$Desembolso->Nit}}">{{$Desembolso->Descripcion}}</option>
                                  @endforeach
                              </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Tipo de Cuenta:
                            </label>
                            <div class="col-sm-8">
                              <select id="slTipoCuenta" class="form-control select2 circle">
                                  @php($Lista = $TiposCuenta->listarTiposCuenta())
                                  @foreach($Lista as $TipoCuenta)
                                      <option value="{{$TipoCuenta->Codigo}}">{{$TipoCuenta->Descripcion}}</option>
                                  @endforeach
                              </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Cuenta:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txCuenta" name="txCuenta" maxlength="200" class="form-control input-circle">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>{{--  FIN DE MODAL  --}}
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <script src="{{ asset('js/CuentasBancarias/index.js') }}" type="text/javascript"></script>
@endsection
