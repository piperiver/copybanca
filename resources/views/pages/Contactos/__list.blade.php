<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
            <div class="scroller" style="max-height: 300px; overflow-y: auto" data-always-visible="1" data-rail-visible1="1">
                <div class="row" style="margin: 15px">

                @forelse($contacts as $contacto)
                    @if($loop->index != 0 && $loop->index % 2 == 0)
                    </div>
                    <div class="row" style="margin: 15px">
                @endif
                    <div class="col-md-6">
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title">{{$contacto->Nombre}}</h5>
                                <p class="card-text">{{$contacto->Entidad}}, {{$contacto->Cargo}}, {{$contacto->Telefono}}, {{$contacto->Area}}</p>
                                <a target="_blank" href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $contacto->Correo }}">{{$contacto->Correo}}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <h5>Sin contactos registrados</h5>
                @endforelse
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button data-url="/contactos-create/{{$type}}/{{$id}}" class="btn btn-primary cargarModalAjax" type="submit">Agregar nuevo
            </button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
        </div>
    </div>
</div>