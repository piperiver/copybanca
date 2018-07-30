@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@extends('layout.default')
@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i>Solicitudes de consulta
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="expand"></a>
                    </div>
                    <div class="actions">
                        <a href="{{ url('solicitudes/create') }}" id="lkValorar" name="lkValorar"
                           class="btn btn-default btn-sm" data-toggle="modal">
                            <i class="fa fa-plus"></i> Crear solicitud de consulta
                        </a>
                        <a class="btn btn-default btn-sm">
                            <i class="fa fa-check-square-o" ></i> <span id="completas">{{ $contSolicitudes }}</span>
                        </a>
                    </div>
                </div>
                <div id="contenido_completas" class="portlet-body portlet-collapsed" style="display: none;">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                        <tr>
                            <th>
                                <center> Nº</center>
                            </th>
                            <th style="width: 25%"> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estado</th>
                            @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                <th> Comercial</th>
                            @endif
                            <th style="text-align: right !important;"> Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($solicitudes as $solicitud)

                            <tr id="{{$solicitud->id}}" class="item{{$solicitud->id}}">

                                <td>{{$loop->iteration}}</td>
                                <td><span style="font-weight: bold; cursor: pointer;" data-url="/mostrarComentarios/solicitud/{{$solicitud->id}}"
                                          class="cargarModalAjax">{{$solicitud->nombre." ".$solicitud->apellido}}</span>
                                </td>
                                <td>{{$solicitud->cedula}}</td>
                                <td>{{$solicitud->pagaduriaNombre}}</td>
                                <td>{{substr($solicitud->updated_at, 0, 10)}}</td>
                                <td>
                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                                        <label>Foto documento</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                    @else
                                        <label>Foto documento</label>
                                    @endif
                                    <br>
                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                                        <label>Autorizaci&oacute;n</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                                    @else
                                        <label>Autorizaci&oacute;n</label>
                                    @endif
                                    <br>
                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD"))) > 0)
                                        <label>Formato Banco</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD") ) }}
                                    @else
                                        <label>Formato Banco</label>
                                    @endif

                                </td>
                                <td>{{$solicitud->estadoEstudio}}</td>

                                @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN") )
                                    <td>
                                        {{$solicitud->usuarioNombre." ".$solicitud->usuarioPrimerApellido." ".$solicitud->usuarioApellido}}
                                    </td>
                                @endif
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="...">

                                    @if(!empty($solicitud->valoracion_id))
                                        @if($solicitud->estadoEstudio == "NEG")
                                            <a href="#" class='btn red'>
                                                <i class='fa fa-file fa-xs' title="Llenar solicitud"></i>
                                            </a>
                                        @else
                                            <a href="{{url('formulario-registro', $solicitud->valoracion_id)}}"
                                               class='btn blue'>
                                                <i class='fa fa-file fa-xs' title="Llenar solicitud"></i>
                                            </a>
                                        @endif
                                    @elseif($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                        <a href="{{url('solicitudes/'.$solicitud->id)}}"
                                           class='btn yellow-gold'>
                                            <i class='fa fa-dollar fa-xs' title="Realizar valoraci&oacute;n"></i>
                                        </a>
                                    @endif
                                    <a data-url="/solicitudes/ver-detalle/{{$solicitud->id}}"
                                       class='btn btm-sm yellow-gold cargarModalAjax'>
                                        <i class='fa fa-eye fa-xs' title="Revisar"></i>
                                    </a>
                                    @if($solicitud->estadoEstudio != "NEG" && count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD"))) == 0 )
                                        <a data-url="/solicitudes/ver-bancos/{{$solicitud->id}}"
                                           class='btn green cargarModalAjax btm-sm'>
                                            <i class='fa fa-university fa-xs' title="Actualizar banco"></i>
                                        </a>
                                    @endif
                                    </div>
                                </td>


                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i>Solicitudes Incompletas
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="expand"></a>
                    </div>

                    <div class="actions">
                        <a class="btn btn-default btn-sm">
                            <i class="fa fa-check-square-o" ></i> <span id="pendientes">{{ $contSolicitudesPendientes }}</span>
                        </a>
                    </div>
                </div>
                <div id="contenido_pendientes" class="portlet-body portlet-collapsed" style="display: none;">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                        <tr>
                            <th>
                                <center> Nº</center>
                            </th>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estados</th>
                            @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                <th> Comercial</th>
                            @endif
                            <th> Acci&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($solicitudesPendientes as $solicitudePendiente)
                            <tr id="{{$solicitudePendiente->id}}" class="item{{$solicitudePendiente->id}}">

                                <td>{{$loop->iteration}}</td>
                                <td>{{$solicitudePendiente->nombre." ".$solicitudePendiente->apellido}}</td>
                                <td>{{$solicitudePendiente->cedula}}</td>
                                <td>{{$solicitudePendiente->pagaduriaNombre}}</td>
                                <td>{{substr($solicitudePendiente->created_at, 0, 10)}}</td>
                                <td>
                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitudePendiente->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                                        <label>Foto documento</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitudePendiente->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                    @else
                                        <label>Foto documento</label>
                                    @endif
                                    <br>

                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitudePendiente->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                                        <label>Autorizacion</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitudePendiente->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                                    @else
                                        <label>Autorizacion</label>
                                    @endif

                                </td>

                                <td>{{$solicitud->estadoEstudio}}</td>
                                @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                    <td>
                                        {{$solicitudePendiente->usuarioNombre." ".$solicitudePendiente->usuarioPrimerApellido." ".$solicitudePendiente->usuarioApellido}}
                                    </td>
                                @endif
                                <td>
                                    <a href="{{url('solicitudes/'.$solicitudePendiente->id.'/edit')}}"
                                       class='btn btn-icon-only yellow-gold'>
                                        <i class='fa fa-edit'></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i>Solicitudes Devueltas
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="expand"></a>
                    </div>
                    <div class="actions">
                        <a class="btn btn-default btn-sm">
                            <i class="fa fa-check-square-o" ></i> <span id="devueltas">{{ $contSolicitudesDevueltas }}</span>
                        </a>
                    </div>
                </div>
                <div id="contenido_devueltas" class="portlet-body portlet-collapsed" style="display: none;">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable">
                        <thead>
                        <tr>
                            <th>
                                <center> Nº</center>
                            </th>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estados</th>
                            <th> Descripci&oacute;n</th>
                            @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                <th> Comercial</th>
                            @endif
                            <th> Acci&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($solicitudesDevueltas as $solicitudDevuelta)
                            <tr id="{{$solicitudDevuelta->id}}" class="item{{$solicitudDevuelta->id}}">

                                <td>{{$loop->iteration}}</td>
                                <td>{{$solicitudDevuelta->nombre." ".$solicitudDevuelta->apellido}}</td>
                                <td>{{$solicitudDevuelta->cedula}}</td>
                                <td>{{$solicitudDevuelta->pagaduriaNombre}}</td>
                                <td>{{substr($solicitudDevuelta->created_at, 0, 10)}}</td>
                                <td>
                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitudDevuelta->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                                        <label>Foto documento</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitudDevuelta->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                    @else
                                        <label>Foto documento</label>
                                    @endif
                                    <br>

                                    @if(count($ComponentAdjuntos->adjunto_exist($solicitudDevuelta->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                                        <label>Autorizacion</label>
                                        {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitudDevuelta->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                                    @else
                                        <label>Autorizacion</label>
                                    @endif

                                </td>

                                <td>{{$solicitud->estadoEstudio}}</td>
                                <td>
                                    {{$solicitudDevuelta->descripcion_devolucion}}
                                </td>
                                @if($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN"))
                                    <td>
                                        {{$solicitudDevuelta->usuarioNombre." ".$solicitudDevuelta->usuarioPrimerApellido." ".$solicitudDevuelta->usuarioApellido}}
                                    </td>
                                @endif
                                <td>
                                    <a href="{{url('solicitudes/'.$solicitudDevuelta->id.'/edit')}}"
                                       class='btn btn-icon-only yellow-gold'>
                                        <i class='fa fa-edit'></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--Modal mensaje de respuesta-->
    <div class="modal fade" id="mensaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>

                <div class="modal-body">
                    <span id="descripcion_mensaje"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de modal -->

    <!-- Modal adjunto solicitud del banco
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">Carga Libranza</h4>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Terminar</button>
                </div>
            </div>
        </div>
    </div>
    Fin de la modal -->
    <script src="{{ asset('js/SolicitudConsulta/index.js') }}" type="text/javascript"></script>
@endsection