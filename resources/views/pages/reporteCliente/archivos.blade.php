@extends('layout.default')
@section('content')
<h2>Archivos Encontrados</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Fecha Modificacion</th>
            <th>Ver</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $archivo)
            <tr>
                <td>{{ $archivo->label }}</td>
                <td>{{ date("F d Y H:i:s.", $archivo->fecha) }}</td>
                <td></td>
            </tr>
        @endforeach         
    </tbody>
</table>
    


@endsection