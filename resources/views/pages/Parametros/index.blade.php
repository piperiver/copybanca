@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Modulos', 'App\Http\Controllers\ModulosController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Parámetros </div>
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
                                <th> Código </th>
                                <th> Descripción </th>
                                <th> Valor </th>
                                <th> Tipo </th>
                                <th> Módulo </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Parametros as $Parametro)
                                <tr>
                                    <td>{{ $Parametro->Codigo }}</td>
                                    <td>{{ $Parametro->Descripcion }}</td>
                                    <td>{{ $Parametro->Valor }}</td>
                                    <td>{{ $Parametro->Tipo }}</td>
                                    <td>{{ $Parametro->Modulo }}</td>
                                    <td>{{ $Parametro->updated_at }}</td>
                                    <td>{{ $Parametro->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                        <td>
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='{{$Parametro->Codigo}}' data-descripcion='{{$Parametro->Descripcion}}' data-valor='{{$Parametro->Valor}}' data-tipo='{{$Parametro->Tipo}}' data-modulo='{{$Parametro->CodigoModulo}}'>
                                                <i class='fa fa-edit'></i>
                                            </a>
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
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Parámetro</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Codigo:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txCodigo" name="txCodigo" maxlength="8" placeholder="Defina Código de Parametro." style="text-transform: uppercase;" class="form-control input-circle" required />
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Descripción:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txDescripcion" name="txDescripcion" maxlength="1000" placeholder="Defina Descripción de Parametro." class="form-control input-circle" required>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Valor:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txValor" name="txValor" maxlength="100" placeholder="Digite Valor de Parametro." class="form-control input-circle" required>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Tipo:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txTipo" name="txTipo" maxlength="50" placeholder="Defina Tipo de Valor del Parametro." class="form-control input-circle" required>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Módulo:
                            </label>
                            <div class="col-sm-8">
                              <select id="slModulo" name="slModulo" class="form-control select2 circle">
                                  @php($Lista = $Modulos->listarModulos())
                                  @for($i=0; $i < count($Lista); $i++)
                                      <option value="{{$Lista[$i]['Codigo']}}">{{$Lista[$i]['Descripcion']}}</option>
                                  @endfor
                              </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div id="divCalcularIva" class="form-group">
                            <label class="col-sm-2 control-label">
                                
                            </label>
                            <div class="col-sm-8">
                                <div class="col-sm-3"></div> <button type="button" id="btCalcularIva" name="btCalcularIva" class="btn green">Calcular Iva Credito</button>
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
    <script src="{{ asset('js/Parametros/index.js') }}" type="text/javascript"></script>
@endsection
