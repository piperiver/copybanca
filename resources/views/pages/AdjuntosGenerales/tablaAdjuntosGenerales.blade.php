<table class="table table-striped table-bordered table-hover table-checkable order-column iniciarDatatable">
<thead>
    <tr>
        <th style="text-align: center"> Fecha </th>
        <th> Nombre </th>                                
        <th class="text-center">Ver</th>
        <th class="text-center">Descargar</th>                                
    </tr>
</thead>
<tbody>                            
@foreach($adjuntos as $adjunto)
    <tr>
        <td class="text-center">{{ $adjunto->created_at }}</td>
        <td>{{ $adjunto->NombreArchivo }}</td>                                    
        <td class="text-center" style="vertical-align: middle">
            <a class="color-negro" title="Visualizar" href="{{ config('constantes.RUTA') }}visualizar/{{ $adjunto->id }}" target="_blank">
                <span class="fa fa-eye fa-2x"></span>
            </a>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <a class="color-green margin-left-5" title="Descargar" href="{{ config('constantes.RUTA') }}descargar/{{ $adjunto->id }}" target="_blank">
                <span class="fa fa-download fa-2x"></span>
            </a>
        </td>                                    
    </tr>  
@endforeach
</tbody>
</table>

