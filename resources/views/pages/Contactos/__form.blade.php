<div class="modal-dialog">
    <form data-url="/contactos-create/{{$type}}/{{$id}}" id="comment-form" style="padding: 15px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="scroller" data-always-visible="1" data-rail-visible1="1">
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Nombre:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="txNombre" name="Nombre" maxlength="255"
                                           class="form-control input-circle"
                                           placeholder="Nombre del Contacto a ingresar.">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Entidad:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="txEntidad" name="Entidad" maxlength="255"
                                           class="form-control input-circle"
                                           placeholder="Entidad del Contacto a ingresar.">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Cargo:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="txCargo" name="Cargo" maxlength="255"
                                           class="form-control input-circle"
                                           placeholder="Cargo del Contacto a ingresar.">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Telefono:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="txTelefono" name="Telefono" maxlength="20"
                                           class="form-control input-circle"
                                           placeholder="Telefono del Contacto a ingresar.">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Correo:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="email" id="txCorreo" name="Correo" maxlength="255"
                                           class="form-control input-circle" placeholder="Correo del Contacto a ingresar.">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Area:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="txArea" name="Area" maxlength="255"
                                           class="form-control input-circle" placeholder="Area del Contacto a ingresar.">
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