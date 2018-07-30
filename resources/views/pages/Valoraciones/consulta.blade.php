@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i>Valoraciones
                    </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a href="" id="lkGenerarCodigo" name="lkGenerarCodigo" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Generar Código
                            </a>
                            <a href="/Valorar" id="lkValorar" name="lkValorar" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Valorar
                            </a>
                        @endif
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="tabla">
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
                               <!-- @if($user->perfil == config("constantes.PERFIL_ROOT"))
                                <th> Acci&oacute;n </th> -->
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
                                   <!-- @if($user->perfil == config("constantes.PERFIL_ROOT"))
                                        <td>
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='{{$Valoracion->id}}'>
                                                <i class='fa fa-close'></i>
                                            </a>
                                        </td>
                                    @endif-->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ventana" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Código Promocional</h4>
                </div>
                <div class="modal-body">
                    <h3 id="lblCodigo"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>{{-- Fin Modal --}}

    <!--Modal mensaje de respuesta-->
    <div class="modal fade" id="mensaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>

                <div class="modal-body" id="descripcion_mensaje">

                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modal -->

    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Valoraciones/consulta.js') }}" type="text/javascript"></script>
@endsection