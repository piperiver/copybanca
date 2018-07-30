<h4 class="text-center bold">Trazabilidad de Aprobaci&oacute;n</h4>
<table class="table table-striped table-hover text-center">
    <thead class="head-inverse">
    <tr>
        <th class="text-center">#</th>
        <th class="text-center">Usuario</th>
        <th class="text-center">Fecha de aprobaci&oacute;n</th>
    </tr>
    </thead>
    <tbody>
    @foreach($aprobaciones as $item)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$item->usuario->nombre." ".$item->usuario->apellido}}</td>
            <td>{{$item->created_at}}</td>
        </tr>
    @endforeach
    </tbody>
</table>