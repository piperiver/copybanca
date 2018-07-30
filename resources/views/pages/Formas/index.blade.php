@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Modulos', 'App\Http\Controllers\ModulosController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Formas </div>
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
                                <th> Módulo </th>
                                <th> Ruta </th>
                                <th> Visible </th>
                                <th> Icono </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Formas as $Forma)
                                <tr id="{{$Forma->Codigo}}" class="item{{$Forma->Codigo}}">
                                    <td>{{ $Forma->Codigo }}</td>
                                    <td>{{ $Forma->Descripcion }}</td>
                                    <td>{{ $Forma->Modulo }}</td>
                                    <td>{{ $Forma->Ruta }}</td>
                                    <td>{{ $Forma->Visible }}</td>
                                    <td class="text-center"><span title="{{ $Forma->Icono }}" class="fa {{ $Forma->Icono }}"></span></td>
                                    <td>{{ $Forma->updated_at }}</td>
                                    <td>{{ $Forma->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo="{{$Forma->Codigo}}" data-descripcion="{{$Forma->Descripcion}}" data-modulo="{{$Forma->CodigoModulo}}" data-ruta="{{$Forma->Ruta}}" data-visible="{{$Forma->Visible}}" data-icono="{{$Forma->Icono}}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo="{{$Forma->Codigo}}">
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
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Forma</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Código:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txCodigo" name="txCodigo" maxlength="5" style="text-transform: uppercase;" placeholder="Defina Código de Forma." class="form-control input-circle">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Descripción:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txDescripcion" name="txDescripcion" maxlength="1000" placeholder="Defina Descripción de Forma." class="form-control input-circle">
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
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Ruta:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txUrl" name="txUrl" maxlength="200" placeholder="Escriba Ruta ya definida en web." class="form-control input-circle">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="Visible" class="col-sm-2 control-label">Visible:</label>
                            <form>
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio"name="rdVisible" id="rdSi" value="S" checked>Si
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="rdVisible" id="rdNo" value="N">No
                                    </label>
                                </div>
                            </form>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Icono:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txIcono" name="txIcono" maxlength="4000" placeholder="Defina Icono de Forma." class="form-control input-circle list-icons">
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
    </div>
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Formas/index.js') }}" type="text/javascript"></script>
@endsection
