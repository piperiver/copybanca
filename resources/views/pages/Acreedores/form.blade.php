<div class="modal-dialog">
    <form @if(isset($acreedor->id)) data-url="{{ url('acreedores', ['update'=>'update','id'=>$acreedor->id] ) }}"
          @else data-url="{{ url('acreedores') }}" @endif  class="create-form" style="padding: 15px">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="scroller" data-always-visible="1" data-rail-visible1="1">
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Nombre:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre" value="{{$acreedor->nombre}}"
                                           name="nombre" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Nit:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nit" value="{{$acreedor->nit}}"
                                           name="nit" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Tipo:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_tipo" value="{{$acreedor->tipo}}"
                                           name="tipo" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Sector:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_sector" value="{{$acreedor->sector}}"
                                           name="sector" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Clasificación:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_clasificacion" value="{{$acreedor->clasificacion}}"
                                           name="clasificacion" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Sitio web:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_sitio_web" value="{{$acreedor->sitio_web}}"
                                           name="sitio_web" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Cuenta:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_cuenta" value="{{$acreedor->cuenta}}"
                                           name="cuenta" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Tipo de cuenta:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_tipo_cuenta" value="{{$acreedor->tipo_cuenta}}"
                                           name="tipo_cuenta" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Entidad de desembolso:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_entidad_desembolso"
                                           value="{{$acreedor->entidad_desembolso}}"
                                           name="entidad_desembolso" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Ciudad:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_ciudad"
                                           value="{{$acreedor->ciudad}}"
                                           name="ciudad" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Departamento:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_departamento"
                                           value="{{$acreedor->departamento}}"
                                           name="departamento" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Domicilio:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_domicilio" value="{{$acreedor->domicilio}}"
                                           name="domicilio" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </form>

</div>