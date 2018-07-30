
@extends('layout.default')
@section('content')
    @include('flash::message')
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="portlet box main-color">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>Parametros del Sistema 
                        </div>
                        <div class="actions">
                        <a href="#create" class="btn btn-default btn-sm">
                            <i class="fa fa-plus"></i> Crear 
                        </a>
                    </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                        <th>Modificar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>SMLV</td>
                                        <td>Salario Minimo</td>
                                        <td>700.000</td>
                                        <td>SIMULADOR</td>
                                        <td>
                                            <a href="#edit" class="btn btn-icon-only red" data-toggle="modal">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
        <!-- Ventanas modales-->

        <div class="modal fade" id="edit" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Modal Title</h4>
                    </div>
                    <div class="modal-body"> 
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Codigo: 
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-circle">
                                </div>
                            </div>
                        </form> 
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Nombre: 
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-circle">
                                </div>
                            </div>
                        </form> 
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Valor: 
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-circle">
                                </div>
                            </div>
                        </form>
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    Tipo: 
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-circle">
                                </div>
                            </div>
                        </form>  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="button" class="btn green">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
@endsection