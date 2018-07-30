@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Perfiles', 'App\Http\Controllers\PerfilesController')
@inject('Formas', 'App\Http\Controllers\FormasController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Permisos </div>
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
                                <th> Perfil </th>
                                <th> Forma </th>
                                <th> Insertar </th>
                                <th> Actualizar </th>
                                <th> Eliminar </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Permisos as $Permiso)
                                <tr id="{{$Permiso->CodigoPerfil}}-{{$Permiso->CodigoForma}}">
                                    <td>{{$Permiso->Perfil}} ({{$Permiso->CodigoPerfil}})</td>
                                    <td>{{$Permiso->Forma}} ({{$Permiso->CodigoForma}})</td>
                                    <td>{{ $Permiso->Insertar }}</td>
                                    <td>{{ $Permiso->Actualizar }}</td>
                                    <td>{{ $Permiso->Eliminar }}</td>
                                    <td>{{ $Permiso->updated_at }}</td>
                                    <td>{{ $Permiso->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                        <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-perfil="{{$Permiso->CodigoPerfil}}" data-forma="{{$Permiso->CodigoForma}}" data-insert="{{$Permiso->Insertar}}" data-update="{{$Permiso->Actualizar}}" data-delete="{{$Permiso->Eliminar}}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-perfil="{{$Permiso->CodigoPerfil}}" data-forma="{{$Permiso->CodigoForma}}">
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
                    <h4 class="modal-title">Permiso</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Perfil:
                            </label>
                            <div class="col-sm-8">
                              <select id="slPerfil" class="form-control select2 circle">
                                  @php($Lista = $Perfiles->listarPerfiles(true))
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
                                Forma:
                            </label>
                            <div class="col-sm-8">
                              <select id="slForma" class="form-control select2 circle">
                                  @php($Lista = $Formas->listarFormas())
                                  @for($i=0; $i < count($Lista); $i++)
                                      <option value="{{$Lista[$i]['Codigo']}}">{{$Lista[$i]['Descripcion']}}</option>
                                  @endfor
                              </select>
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="checkbox-inline">
                                <label><input type="checkbox" id="ckInsertar" value="">  Guardar</label>
                            </div>
                            <div class="checkbox-inline">
                                <label><input type="checkbox" id="ckActualizar" value="">  Actualizar</label>
                            </div>
                            <div class="checkbox-inline">
                                <label><input type="checkbox" id="ckEliminar" value="">  Eliminar</label>
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
    <script src="{{ asset('js/Permisos/index.js') }}" type="text/javascript"></script>
@endsection
