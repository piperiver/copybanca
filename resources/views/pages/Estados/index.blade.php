@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Estados </div>
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
                                <th> Estado Padre </th>
                                <th> Forma </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Estados as $Estado)
                                <tr id="{{$Estado->Codigo}}" class="item{{$Estado->Codigo}}">
                                    <td>{{ $Estado->Codigo }}</td>
                                    <td>{{ $Estado->Descripcion }}</td>
                                    <td>{{ $Estado->idPadre }}</td>
                                    <td>{{ $Estado->Forma }}</td>
                                    <td>{{ $Estado->updated_at }}</td>
                                    <td>{{ $Estado->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                        <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='{{$Estado->Codigo}}' data-descripcion='{{$Estado->Descripcion}}' data-idpadre='{{$Estado->idPadre}}' data-forma='{{$Estado->CodigoForma}}'>
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='{{$Estado->Codigo}}'>
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
                    <h4 class="modal-title">Estado</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Código:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txCodigo" name="txCodigo" maxlength="3" style="text-transform: lowercase;" placeholder="Defina Código de Estado." class="form-control input-circle">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Descripción:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txDescripcion" name="txDescripcion" maxlength="1000" placeholder="Defina Descripción de Estado." class="form-control input-circle">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Estado Padre:
                            </label>
                            <div class="col-sm-8">
                                <select name="slIdPadre" id="slIdPadre" class="form-control input-circle">
                                    <option value=""></option>
                                    @foreach($Estados as $Estado)
                                        <option value="{{$Estado->Codigo}}">{{$Estado->Descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Forma:
                            </label>
                            <div class="col-sm-8">
                                <select name="slForma" id="slForma" class="form-control input-circle">
                                    <option value="TODAS">Todas</option>
                                        @foreach($Formas as $Forma)
                                            <option value="{{$Forma->Codigo}}">{{$Forma->Descripcion}}</option>
                                        @endforeach
                                </select>
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
    <script src="{{ asset('js/Estados/index.js') }}" type="text/javascript"></script>
@endsection
