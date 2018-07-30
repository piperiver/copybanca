<div class="modal-dialog">
    <form @if(isset($acreedor->id)) data-url="{{ url('acreedores', ['contacto'=>'update','id'=>$acreedor->id] ) }}"
          @else data-url="{{ url('acreedores') }}" @endif  class="create-form" style="padding: 15px">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="scroller" data-always-visible="1" data-rail-visible1="1">
                    @foreach($acreedor->contactos as $contacto)
                        <div>
                            {{$contacto->Nombre}}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
        </div>
    </form>

</div>