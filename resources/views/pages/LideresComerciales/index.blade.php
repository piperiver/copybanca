@extends('layout.default')
@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bar-chart"></i>Registro de cargues
                    </div>
                    <div class="actions">
                        <a href="#ventana" id="lkValorar" name="lkValorar"
                           class="btn btn-default btn-sm" data-toggle="modal">
                            <i class="fa fa-plus"></i> Crear cargue
                        </a>
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center"
                           id="tabla">
                        <thead>
                        <tr>
                            <th> Documento</th>
                            <th> Nombre</th>
                            <th> Fecha de subida</th>
                            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                <th>Comercial</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td><a  download href="{{url('descargar',$solicitud->archivo()->id)}}"><i class="fa fa-download"></i></a></td>
                                    <td title="{{$solicitud->comentario}}">{{$solicitud->nombre}}</td>
                                    <td>{{$solicitud->created_at->format('Y-m-d')}}</td>
                                    @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                        <td>{{$solicitud->usuario->nombres()}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ventana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" data-create_url="{{url('lideres')}}" class="save-model-form">
                    {{ csrf_field() }}

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Cargue</h4>
                    </div>
                    <div class="modal-body">
                        <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">

                            <div class="row">
                                {!! csrf_field() !!}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="Nombre" class="control-label">Documento:</label>
                                        <input required type="file" name="archivo" id="txNombre" maxlength="255"
                                               class="form-control input-circle" placeholder="Documento.">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label class="control-label" for="inputPatient">Comentario:</label>
                                        <div class="field desc">
                                            <textarea class="form-control" id="descripcion" name="comentario"
                                                      placeholder="Comentario"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>{{--  FIN DE MODAL  --}}

@endsection