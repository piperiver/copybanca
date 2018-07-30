
@extends('layout.default')
@section('content')
@include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Usuarios </div>
                    <div class="actions">
                        <a href="{{ route('users.create') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-plus"></i> Crear
                        </a>
                        <div class="btn-group">
                            <a class="btn btn-default btn-sm" href="javascript:;" data-toggle="dropdown">
                                <span class="hidden-xs"> Herramientas </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" id="sample_3_tools">
                                <li>
                                    <a href="javascript:;" data-action="0" class="tool-action">
                                        <i class="icon-printer"></i> Imprimir</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="1" class="tool-action">
                                        <i class="icon-check"></i> Copiar</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="2" class="tool-action">
                                        <i class="icon-doc"></i> PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="3" class="tool-action">
                                        <i class="icon-paper-clip"></i> Excel</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_3">
                        <thead>
                            <tr>
                                <th> UserName </th>
                                <th> Nombre </th>
                                <th> Apellido </th>
                                <th> Perfil </th>
                                <th> Email </th>
                                <th> Estado </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td><a href="{{ route('users.edit', $user->id)}}"> {{ $user->login }}</a></td>
                                    <td>{{ $user->nombre }}</td>
                                    <td>{{ $user->apellido }}</td>
                                    <td>{{ $user->perfil }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
