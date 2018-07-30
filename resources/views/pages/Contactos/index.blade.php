@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list-alt"></i> Contactos
                    </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a href="" id="lkSave" name="lkSave" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        @endif
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="tabla">
                        <thead>
                            <tr>
                                <th> Nombre </th>
                                <th> Entidad </th>
                                <th> Telefono </th>
                                <th> Acción </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($Contactos as $Contacto)
                                <tr id="{{$Contacto->id}}">
                                    <td>{{$Contacto->Nombre}}</td>
                                    <td>{{$Contacto->Entidad}}</td>
                                    <td>{{$Contacto->Telefono}}</td>
                                    <td>
                                        <a href='' id='lkVer' name='lkVer' class='btn btn-icon-only green' data-toggle='modal'
                                           data-nombre='{{$Contacto->Nombre}}' data-entidad='{{$Contacto->Entidad}}' data-cargo='{{$Contacto->Cargo}}' 
                                           data-telefono='{{$Contacto->Telefono}}' data-correo='{{$Contacto->Correo}}' data-area='{{$Contacto->Area}}'>
                                            <i class='fa fa-plus'></i>
                                        </a>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                        <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal'
                                           data-id='{{$Contacto->id}}' data-nombre='{{$Contacto->Nombre}}' data-entidad='{{$Contacto->Entidad}}' data-cargo='{{$Contacto->Cargo}}' 
                                           data-telefono='{{$Contacto->Telefono}}' data-correo='{{$Contacto->Correo}}' data-area='{{$Contacto->Area}}'>
                                            <i class='fa fa-edit'></i>
                                        </a>
                                    @endif
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                        <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='{{$Contacto->id}}'>
                                            <i class='fa fa-close'></i>
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
    <input type="hidden" id="hnId" name="hnId" value="">
    <div class="modal fade" id="ventana" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Contacto</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Nombre:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txNombre" name="txNombre" maxlength="255" class="form-control input-circle" placeholder="Nombre del Contacto a ingresar.">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Entidad:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txEntidad" name="txEntidad" maxlength="255" class="form-control input-circle" placeholder="Entidad del Contacto a ingresar.">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Cargo:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txCargo" name="txCargo" maxlength="255" class="form-control input-circle" placeholder="Cargo del Contacto a ingresar.">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Telefono:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txTelefono" name="txTelefono" maxlength="20" class="form-control input-circle" placeholder="Telefono del Contacto a ingresar.">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Correo:
                            </label>
                            <div class="col-sm-8">
                                <input type="email" id="txCorreo" name="txCorreo" maxlength="255" class="form-control input-circle" placeholder="Correo del Contacto a ingresar.">
                            </div>
                        </div>
                    </form>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                Area:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" id="txArea" name="txArea" maxlength="255" class="form-control input-circle" placeholder="Area del Contacto a ingresar.">
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
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <script src="{{ asset('js/Contactos/index.js') }}" type="text/javascript"></script>
@endsection