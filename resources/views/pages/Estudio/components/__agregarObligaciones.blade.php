<div class="modal-dialog">
    <form method="post" action="/agregar-obligacion/{{$id}}">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Agregar obligación</h4>
            </div>
            <div class="modal-body">

                <div class="scroller" style="height:150px" data-always-visible="1" data-rail-visible1="1">
                    <div class="col-md-12">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Nombre de la entidad:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre"
                                           name="Entidad" maxlength="50"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Valor de la cuota:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre"
                                           name="ValorCuota" maxlength="30"
                                           class="form-control input-circle address inputEditableMiles">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </form>
</div>