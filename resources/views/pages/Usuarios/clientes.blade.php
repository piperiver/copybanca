@extends('layout.default')
@section('content')
@include('flash::message')
@inject('Estados', 'App\Http\Controllers\EstadosController')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Clientes </div>
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
                                <th> Nombres </th>
                                <th> Apellidos </th>
                                <th> Cedula </th>
                                <th> Fecha Nacimiento </th>
                                <th> Email </th>
                                <th> Telefono </th>
                                <th> Pagaduria </th>
                                <th> Estado </th>
                                <th> Ultima Actualización </th>
                                <th> Fecha Creación </th>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                    <th> Acción </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Clientes as $Cliente)
                                <tr id="{{$Cliente->id}}">
                                    <td>{{ utf8_decode($Cliente->nombre) }}</td>
                                    <td>{{ utf8_decode($Cliente->apellido) }}</td>
                                    <td>{{ $Cliente->cedula }}</td>
                                    <td>{{ $Cliente->fecha_nacimiento }}</td>
                                    <td>{{ $Cliente->email }}</td>
                                    <td>{{ $Cliente->telefono }}</td>
                                    <td>{{ $Cliente->pagaduria }}</td>
                                    <td>{{ $Cliente->estado }}</td>
                                    <td>{{ $Cliente->updated_at }}</td>
                                    <td>{{ $Cliente->created_at }}</td>
                                    <td>
                                        <a href='' id='lkDesprendible' name='lkDesprendible' class='btn btn-icon-only green' data-toggle='modal' data-id='{{$Cliente->id}}'>
                                            <i class='fa fa-plus'></i>
                                        </a>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id="{{$Cliente->id}}" data-nombre="{{$Cliente->nombre}}" data-apellido="{{$Cliente->apellido}}" data-cedula="{{$Cliente->cedula}}"
                                            data-email="{{$Cliente->email}}" data-telefono="{{$Cliente->telefono}}" data-pagaduria="{{$Cliente->pagaduria}}" data-password="{{$Cliente->password}}" data-estado="{{$Cliente->CodigoEstado}}" data-fechanacimiento="{{$Cliente->fecha_nacimiento}}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                    </td>
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
                    <h4 class="modal-title">Cliente</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <div class="form-group">
                                        <label for="Nombre" class="control-label">Nombre(s):</label>
                                        <input type="text" id="txNombre" maxlength="255" class="form-control input-circle" placeholder="Nombre(s) del Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Apellido" class="control-label">Apellidos:</label>
                                        <input type="text" id="txApellido" maxlength="255" class="form-control input-circle" placeholder="Apellidos del Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Cedula" class="control-label">Cedula:</label>
                                        <input type="text" id="txCedula" maxlength="11" class="form-control input-circle" placeholder="Cedula de Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="txFechaNacimiento" class="control-label">Fecha de Nacimiento:</label>
                                        <input type="text" id="txFechaNacimiento" class="desplegarCalendario form-control input-circle" placeholder="Fecha de Nacimiento de Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="Email" class="control-label">Email:</label>
                                        <input type="email" id="txEmail" maxlength="255" class="form-control input-circle" placeholder="Correo Electronico del Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div class="form-group">
                                        <label for="txTelefono" class="control-label">Telefono:</label>
                                        <input type="text" id="txTelefono" maxlength="100" class="form-control input-circle" placeholder="Telefono de Cliente.">
                                    </div>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <div class="form-group">
                                        <label for="txPagaduria" class="control-label">Pagaduria:</label>
                                        <input type="text" id="txPagaduria" name="txPagaduria" maxlength="255" class="form-control input-circle" placeholder="Empleador del Cliente.">
                                    </div>
                                </p>
                                <p>
                                    <div id="divTxPassword" class="form-group">
                                        <label for="txPassword" class="control-label">Contraseña:</label>
                                        <input type="password" id="txPassword" maxlength="255" class="form-control input-circle" placeholder="Contraseña del Cliente.">
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
                                    <div id="divBtActualizarPass" class="form-group">
                                        <a href='' id='lkCambioPassword' name='lkCambioPassword' class='btn yellow-gold' data-toggle='modal' data-id="{{$Cliente->id}}">
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
    
    <div class="modal fade" id="vtnDesprendibles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Claves Desprendibles</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="divDesprendibles">
                                </div>
                            </div>
                        </div>{{-- row --}}
                    </div>{{-- scroller --}}
                </div>{{-- modal body --}}
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>{{-- modal content --}}
        </div>{{-- modal dialog --}}
    </div>{{-- modal fade --}}
    
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <input type="hidden" id="hnId" name="hnId" value="">
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Usuarios/clientes.js') }}" type="text/javascript"></script>
@endsection
