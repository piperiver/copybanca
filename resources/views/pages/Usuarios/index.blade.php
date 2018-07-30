@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Estados', 'App\Http\Controllers\EstadosController')
@inject('Perfiles', 'App\Http\Controllers\PerfilesController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Usuarios </div>
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
                                <th> Nombre </th>
                                <th> Apellido </th>
                                <th> Cedula </th>
                                <th> Sexo </th>
                                <th> Fecha Nacimiento </th>
                                <th> Telefono </th>
                                <th> Email </th>
                                <th> Estado </th>
                                <th> Perfil </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Usuarios as $Usuario)
                                <tr id="{{$Usuario->id}}">
                                    <td>{{ $Usuario->nombre }}</td>
                                    <td>{{ $Usuario->apellido }}</td>
                                    <td>{{ $Usuario->cedula }}</td>
                                    <td>{{ $Usuario->sexo }}</td>
                                    <td>{{ $Usuario->fecha_nacimiento }}</td>
                                    <td>{{ $Usuario->telefono }}</td>
                                    <td>{{ $Usuario->email }}</td>
                                    <td>{{ $Usuario->estado }}</td>
                                    <td>{{ $Usuario->perfil }}</td>
                                    <td>{{ $Usuario->updated_at }}</td>
                                    <td>{{ $Usuario->created_at }}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                        <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id="{{$Usuario->id}}" data-nombre="{{$Usuario->nombre}}" data-apellido="{{$Usuario->apellido}}" data-cedula="{{$Usuario->cedula}}"
                                            data-sexo="{{$Usuario->sexo}}" data-fechanacimiento="{{$Usuario->fecha_nacimiento}}" data-telefono="{{$Usuario->telefono}}" data-email="{{$Usuario->email}}" data-password="{{$Usuario->password}}" data-estado="{{$Usuario->CodigoEstado}}" data-perfil="{{$Usuario->CodigoPerfil}}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id="{{$Usuario->id}}" data-nombre="{{$Usuario->nombre}}">
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
    <div class="modal fade" id="ventana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Usuario</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <div class="form-group">
                                        <label for="Nombre" class="control-label">Nombre:</label>
                                        <input type="text" id="txNombre" maxlength="255" class="form-control input-circle" placeholder="Nombre de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Apellido" class="control-label">Apellido:</label>
                                        <input type="text" id="txApellido" maxlength="255" class="form-control input-circle" placeholder="Apellido de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Cedula" class="control-label">Cedula:</label>
                                        <input type="text" id="txCedula" maxlength="11" class="form-control input-circle" placeholder="Cedula de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                      <label for="Sexo" class="control-label">Sexo:</label>
                                      <form>
                                          <label class="radio-inline">
                                              <input type="radio"name="rdSexo" id="rdMasculino" value="M" checked>Masculino
                                          </label>
                                          <label class="radio-inline">
                                              <input type="radio" name="rdSexo" id="rdFemenino" value="F">Femenino
                                          </label>
                                      </form>
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="txFechaNacimiento" class="control-label">Fecha de Nacimiento:</label>
                                        <input type="text" id="txFechaNacimiento" class=" desplegarCalendario form-control input-circle" placeholder="Fecha de Nacimiento de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="txTelefono" class="control-label">Telefono:</label>
                                        <input type="text" id="txTelefono" maxlength="100" class="form-control input-circle" placeholder="Telefono de Usuario.">
                                    </div>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <div class="form-group">
                                        <label for="Email" class="control-label">Email:</label>
                                        <input type="email" id="txEmail" maxlength="255" class="form-control input-circle" placeholder="Correo Electronico de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div id="divTxPassword" class="form-group">
                                        <label for="txPassword" class="control-label">Contraseña:</label>
                                        <input type="password" id="txPassword" maxlength="255" class="form-control input-circle" placeholder="Contraseña de Usuario.">
                                    </div>
                                </p>
                                <p>
                                    <div id="divTxConfirmacion" class="form-group">
                                        <label for="txConfirmacion" class="control-label">Confirmación Contraseña:</label>
                                        <input type="password" id="txConfirmacion" maxlength="255" class="form-control input-circle" placeholder="Confirmación Contraseña.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Estado" class="control-label">Estado:</label>
                                        <select id="slEstado" class="form-control select2 circle">
                                            @php($Lista = $Estados->listarEstados())
                                            @for($i=0; $i < count($Lista); $i++)
                                                <option value="{{$Lista[$i]['Codigo']}}">{{$Lista[$i]['Descripcion']}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Perfil" class="control-label">Perfil:</label>
                                        <select id="slPerfil" class="form-control select2 circle">
                                            @php($Lista = $Perfiles->listarPerfiles())
                                            @for($i=0; $i < count($Lista); $i++)
                                                <option value="{{$Lista[$i]['Codigo']}}">{{$Lista[$i]['Descripcion']}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </p>
                                <p>
                                    <div id="divBtActualizarPass" class="form-group">
                                        <a href='' id='lkCambioPassword' name='lkCambioPassword' class='btn yellow-gold' data-toggle='modal'>
                                            Cambiar Contraseña
                                        </a>
                                    </div>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>{{--  FIN DE MODAL  --}}

    <div class="modal fade" id="vtnPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Cambio de Contraseña</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="col-md-10">
                                <p>
                                    <div class="form-group">
                                        <label for="Contraseña1" class="control-label">Contraseña:</label>
                                        <input type="password" id="txPassword1" maxlength="255" class="form-control input-circle" placeholder="Contraseña.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Contraseña2" class="control-label">Confirmación Contraseña:</label>
                                        <input type="password" id="txPassword2" maxlength="255" class="form-control input-circle" placeholder="Confirmación Contraseña.">
                                    </div>
                                </p>
                            </div>{{-- col-md-6 --}}
                        </div>{{-- row --}}
                    </div>{{-- scroller --}}
                </div>{{-- modal body --}}
                <div class="modal-footer">
                    <button type="button" id="btActualizarPass" name="btActualizarPass" class="btn yellow-gold">Cambiar Contraseña</button>
                    <button type="button" id="btAtras" name="btAtras" class="btn dark btn-outline">Atrás</button>
                </div>
            </div>{{-- modal content --}}
        </div>{{-- modal dialog --}}
    </div>{{-- modal fade --}}

    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <input type="hidden" id="hnId" name="hnId" value="">
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Usuarios/index.js') }}" type="text/javascript"></script>
@endsection
