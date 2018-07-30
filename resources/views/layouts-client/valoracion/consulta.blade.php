@extends('layout-client.default')
@section('head')
    <link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ asset('assets/layouts/layout5/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <br/>
    <div class="container-fluid">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="portlet box main-color">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-bar-chart"></i>Valoraciones
                            </div>
                        </div>
                        <div id="contenido" class="portlet-body">
                            <table class="table table-striped table-bordered table-hover table-checkable order-column text-center tablaDatatable">
                                <thead>
                                    <tr>
                                        <th> Valoración </th>
                                        <th> Puntaje Data Credito </th>
                                        <th> Puntaje TransUnión </th>
                                        <th> Pagaduria </th>
                                        <th> Fecha de Valoración </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($Valoraciones as $Valoracion)
                                        <tr id="{{$Valoracion->id}}" class="item{{$Valoracion->id}}">
                                            <td>
                                                <a href="Valoraciones/{{$Valoracion->id}}">{{$Valoracion->id}}</a>
                                            </td>
                                            <td>{{number_format($Valoracion->PuntajeData)}}</td>
                                            <td>{{number_format($Valoracion->PuntajeCifin)}}</td>
                                            <td>{{$Valoracion->Pagaduria}}</td>
                                            <td>{{$Valoracion->created_at}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection