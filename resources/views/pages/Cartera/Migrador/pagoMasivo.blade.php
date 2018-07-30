@extends('layout.default')
@section('encabezado')
    <link href="{{ asset('css/Cartera/detalle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div id="contentPagoMasivo">
    
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center tituloPrincipal">PAGO MASIVO</h2>

            <form enctype="multipart/form-data" class="sombra well" id="formPagoMasivo" data-url="{{ config("constantes.RUTA") }}">
                <fieldset>
                    <legend>Cargar Archivo a Procesar</legend>     
                    
                    <div class="col-md-12">
                        <div id="dspAlertas" class="margin-top-10"></div>
                    </div>
                    
                    <div class="col-md-12">                                
                        <div class="form-group">
                            <label for="archivo" class="bold">ARCHIVO:</label>
                             <div class="">
                                <input type="file" class="filestyle" data-buttonText="Cargar Archivo" name="archivo" id="archivo">                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-12 col-xs-12 text-center">
                        <button type="button" id="sendForm" class="btn btn-primary">PROCESAR <span class="fa fa-upload"></span></button>                            
                    </div>
                    
                    
                    
                    <!--<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                        <input type="file" class="filestyle" data-buttonText="Cargar Archivo" name="archivo" id="archivo">
                    </div>
                    <div id="dspAlertas" class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 margin-top-10"></div>
                    <div class="col-md-12 text-center">
                        <button type="button" id="sendForm" class="btn btn-primary margin-top-10">PROCESAR <span class="fa fa-upload"></span></button>
                    </div> -->        
                    
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                </fieldset>
            </form>
        </div>
    </div>    
    <!-- Paso 1 -->
    <div class="row tableUsuariosEncontrados margin-top-20" style="display: none">
        <div class="col-md-12">
            <div class="sombra well">
                <h3 class="text-center bold">USUARIOS <span id="Pagaduria"></span></h3>
                <div class="btn-group margin-top-10 margin-bottom-10">
                    <button type="button" class="btn btn-primary" id="SeleccionarTodo">Sel. Todo / Limpiar</button>
                </div>
                <div>
                    <table class="table table-striped text-center table-bordered" id="tablaUsuarios"> 
                        <caption class="text-center">Selecciolne el check(<input type="checkbox">) del usuario al que desea adicionar el pago y presione <strong>SIGUIENTE.</strong></caption>
                        <thead class="fondoHeaderTabla">
                            <tr>
                                <th class="text-center">PAGAR</th>
                                <th class="text-center">CÉDULA</th>
                                <th class="text-center">NOMBRE</th>
                                <th class="text-center">VALOR</th>
                            </tr>
                        </thead>
                        <tbody id="containerUsuariosEncontrados">
                        </tbody>
                        <tfoot class="fondoFooterTabla">
                            <tr>
                                <th colspan="3" class="text-center">TOTAL</th>
                                <th id="totalUsuariosEncontrados" class="text-center"></th>
                            </tr>                            
                        </tfoot>
                    </table>  
                </div>
                <div class="margin-top-10 text-right">
                    <button class="btn btn-primary" id="botonSiguientePaso">SIGUIENTE</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin paso 1 -->
    
    <!-- Paso 2 -->
    <div class="row tableUsuariosFiltrados margin-top-20" style="display: none">
        <div class="col-md-12 col-xs-12">
            <div class="sombra well">
                <h3 class="text-center uppercase bold">CONFIRMACI&Oacute;N</h3>
                <table class="table table-striped table-hover table-condensed text-center table-bordered" id="tablaConfirmacion"> 
                    <caption class="text-center">Verifique la informaci&oacute;n de los pagos que se realizar&aacute;n y presione <strong>PAGAR TODOS.</strong> Si desea realizar alguna modificaci&oacute;n seleccione <strong>ATRAS.</strong></caption>
                    <thead class="fondoHeaderTabla">
                        <tr>
                            <th class="text-center">CÉDULA</th>
                            <th class="text-center">NOMBRE</th>
                            <th class="text-center">VALOR PAGO</th>
                        </tr>
                    </thead>
                    <tbody id="containerUsuariosFiltrados">
                    </tbody>
                    <tfoot class="fondoFooterTabla">
                            <tr>
                                <th colspan="2" class="text-center">TOTAL</th>
                                <th id="totalUsuariosFiltrados" class="text-center"></th>
                            </tr>                            
                        </tfoot>
                </table>       
                <input type="hidden" readonly="true" class="readonly" id="dataUsuariosFiltrados">            
            </div>
        </div>
        <div class="col-md-12">
            <button class="btn btn-success pull-left" id="botonVolver">ATRAS</button>
            <button class="btn btn-success pull-right" id="botonPagar">PAGAR TODOS</button>            
        </div>
    </div>
    <!-- Fin paso 2 -->
    
    <div class="row margin-top-20">
        <div class="col-md-12">
            <div class="sombra well" id="pasoFinal" style="display: none">

            </div>
        </div>
    </div>
    
</div>

<input type="hidden" value="{{ config("constantes.RUTA") }}" id="urlPrincipal">
<input type="hidden" value="{{ csrf_token() }}" id="_token">    
<script src="{{ asset('js/Cartera/cartera.js') }}" type="text/javascript"></script>
@endsection