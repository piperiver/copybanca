<table class="table table-striped table-bordered table-hover table-checkable order-column text-center"
       id="tabla">
    <thead>
    <tr>
        <th> Nombre</th>
        <th> Tipo</th>
        <th> Sector</th>
        <th> Nit </th>
        <th> Contactos </th>
        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
            <th> Acción</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($acreedores as $acreedor)
        <tr id="{{$acreedor->id}}" class="item{{$acreedor->id}}">
            <td><a class="cargarModalAjax" style="font-weight: bold" data-url="/mostrarComentarios/acreedor/{{$acreedor->id}}">{{ $acreedor->nombre }}</a></td>
            <td>{{ $acreedor->tipo }}</td>
            <td>{{ $acreedor->sector }}</td>
            <td>{{$acreedor->nit}}</td>
            <td><a class="cargarModalAjax" data-url="/contactos/acreedores/{{$acreedor->id}}" style="cursor: pointer; color: #007d9b;">{{$acreedor->contactos->count()}}</a></td>
            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                <td>
                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                        <a class='btn btn-icon-only yellow-gold update cargarModalAjax'
                           data-url="/acreedores/{{$acreedor->id}}/edit">
                            <i class='fa fa-edit'></i>
                        </a>
                    @endif
                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                        <a class='btn btn-icon-only red delete-button'
                           data-url="{{url('pagadurias', ['id'=>$acreedor->id])}}" data-id='{{$acreedor->id}}'>
                            <i class='fa fa-close'></i>
                        </a>
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>