@extends('layout.default')
@section('content')
@include('flash::message')

                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>403 Error de autorizaci&oacute;n</h1>                        
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
                        <div class="col-md-12 page-404">
                            <div class="number font-green"> 401 </div>
                            <div class="details">
                                <h3>Oops!</h3>
                                <p> No cuentas con los permisos necesarios para acceder a la funcionalidad.
                                    <br/>
                                    Para resolver este problema, comun&iacute;quese con el administrador. </p>                                
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE BASE CONTENT -->

@endsection