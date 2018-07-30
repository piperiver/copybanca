@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
            <h2 class="modal-title">Actualizar banco</h2>
            <div class="container-fluid">
                    <form>
                        <div class="form-group">
                            <label for="comment">Nombre del banco:</label>
                            <input type="text" maxlength="50" class="form-control" id="banco">
                        </div>
                        <input type="hidden" id="id_estudioBanco">
                    </form>
                    <div class="form-group">
                        {{$ComponentAdjuntos->dspFormulario($solicitud->id, config("constantes.KEY_SOLICITUD"), "FBA", config("constantes.MDL_VALORACION"), false, "refresh", "guardarBanco", false,"FORMATO BANCO DE LA SOLICITUD", false,false)}}
                    </div>

            </div>
        </div>
    </div>
</div>