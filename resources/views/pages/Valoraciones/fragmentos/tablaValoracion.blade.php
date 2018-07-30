<table class='table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable' id='tabla'>
    <thead>
    <tr>
        <th style="text-align: right !important;"> ID </th>
        <th> Nombres </th>
        <th> Apellidos </th>
        <th> Cedula </th>
        <th> Pagaduria </th>
        <th> Adjuntos </th>
        @if(Auth::user()->perfil != config('constantes.PERFIL_COMERCIAL'))
            <th> Comercial </th>
        @endif
        <th> Fecha </th>
        @if($user->perfil == config("constantes.PERFIL_ROOT"))
            <th> Acci&oacute;n </th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($Valoraciones as $Valoracion)
        <tr id="{{$Valoracion->id}}" class="item{{$Valoracion->id}}">
            <td>
                @if($Valoracion->Filtro)
                    <a href="Valoraciones/{{$Valoracion->id}}">{{ $Valoracion->id }}</a>
                @else
                    <a href="{{ config("constantes.RUTA")."GestionObligacionesValoracion/".$Valoracion->id }}">{{ $Valoracion->id }}</a>
                @endif
            </td>
            <td>{{utf8_decode($Valoracion->nombre)}}</td>
            <td>{{utf8_decode($Valoracion->apellido)}}</td>
            <td>{{ number_format($Valoracion->cedula, 0, ",", ".") }}</td>
            <td style="text-transform: uppercase">{{$Valoracion->Pagaduria}}</td>
            <td style="vertical-align: middle"><a href="AdjuntosValoraciones/{{$Valoracion->id}}" title="Ver adjuntos de esta valoración"><span class="fa fa-file-pdf-o fa-2x"></span></a></td>
            @if(Auth::user()->perfil != config('constantes.PERFIL_COMERCIAL'))
                <td>{{$Valoracion->Comercial}}</td>
            @endif
            <td>{{ $Valoracion->created_at }}</td>
            @if($user->perfil == config("constantes.PERFIL_ROOT"))
                <td>
                    <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='{{$Valoracion->id}}'>
                        <i class='fa fa-close'></i>
                    </a>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>