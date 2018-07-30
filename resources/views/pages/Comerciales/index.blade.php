@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-bell-o"></i> Registros de Datos
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center" id="tabla">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Nombre </th>
                                <th> Correo </th>
                                <th> Telefono </th>
                                <th> Fecha de Registro </th>
                                {{--@if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                    <th> Acción </th>
                                @endif--}}
                            </tr>
                        </thead>
                        <tbody>
                            @php($conteo = 0)
                            @foreach($Comerciales as $Comercial)
                                @php($conteo += 1)
                                <tr id="{{$Comercial->id}}">
                                    <td> {{$conteo}} {{-- $Comercial->id --}} </td>
                                    <td>{{$Comercial->Nombre}}</td>
                                    <td>{{$Comercial->Email}}</td>
                                    <td>{{$Comercial->Telefono}}</td>
                                    <td>{{ $Comercial->created_at }}</td>
                                    {{--@if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                        <td>
                                            <a href='' id='lkCheck' name='lkCheck' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='{{$Comercial->id}}'>
                                                <i class='fa fa-check'></i>
                                            </a>
                                        </td>
                                    @endif--}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Comerciales/index.js') }}" type="text/javascript"></script>
@endsection