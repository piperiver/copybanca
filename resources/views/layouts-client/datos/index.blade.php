@extends('layout-client.default')
@section('content')
<div class="container">
        <div class="row">
            <div id="pasoApaso" class="carousel slide" data-ride="carousel" data-interval="0" data-wrap="false">
              <!-- Wrapper for slides -->
              <div class="carousel-inner" role="listbox">
                <div class="item active">
                    <div class="col-md-6 col-md-offset-3 col-xs-12 content-item">
                        <h2 class="text-center title-pasos design-title"><span class="decoration-barra"><span class="fa fa-user-o decotarion-icon"></span>DATOS PERSONALES</span></h2>
                        <form class="form-datos">
                            <fieldset>
                                
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                <label for="nombre">Nombre(s):</label>
                                <input class="campo form-control" name="nombre" id="nombre" value="{{Auth::user()->nombre}}">
                                <div class="cNombre"></div>
                                
                                <label for="pApellido">Primer Apellido:</label>
                                <input class="campo form-control" id="pApellido" name="pApellido" value="{{Auth::user()->apellido}}">
                                <div class="cApellido"></div>
                                
                                <label for="cedula">Cédula:</label>
                                <input class="campo form-control" name="cedula" id="cedula">
                                <div class="cCedula"></div>
                                
                                <label for="fecha">Fecha Expedición cédula:</label>
                                <input class="campo form-control desplegarCalendario" type="text" id="fecha" name="fecha">
                                <div class="cFecha"></div>
                                
                                <label for="pagaduria">Pagaduría:</label>
                                <input class="campo form-control" type="text" id="pagaduria" name="pagaduria" data-url="{{ config('constantes.RUTA') }}">
                                <div class="cPagaduria"></div>
                            </fieldset>
                        </form>
                        <a class="btnSiguiente pull-right btnTransition" data-nexContainer="2" data-event="validationForm">Siguiente <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    </div>        
                </div>                               
                <div class="item">
                    <div class="col-md-6 col-md-offset-3 col-xs-12 lateral-ancho content-item">
                        <h2 class="text-center title-pasos design-title"><span class="decoration-barra"><span class="fa fa-dollar decotarion-icon"></span>2 FORMAS DE PAGO</span></h2>
                            <form class="form-datos">
                                <fieldset>
                                    <a class="btnPagos">PAYU</a>
                                    <a id="lkCodigoPromocional" class="btnPagos">CÓDIGO PROMOCIONAL</a>
                                    <a class="btnPagos">LIBRANZA</a>                                    
                                </fieldset>
                            </form>
                        <!--<a class="btnSiguiente pull-right btnTransition" data-nexContainer="4">Siguiente <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>-->
                    </div>
                </div>
               <!-- <div class="item">
                    <div class="col-md-6 col-md-offset-3 col-xs-12 lateral-ancho">
                        <h2 class="text-center title-pasos design-title"><span class="decoration-barra"><span class="fa fa-check decotarion-icon"></span>VALIDACIÓN</span></h2>
                        <p class="text-pregunta">¿Cuando realizo su última transacción bancaria?</p>
                            <form action="prueba.php" method="post">
                                <fieldset class="respuestas">                                    
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="nombres" id="Radios1" value="1">
                                            <i class="fa fa-circle-o fa-2x"></i>
                                            <i class="fa fa-dot-circle-o fa-2x"></i>        
                                            Hay muchas variaciones de los pasajes de Lorem Ipsum disponibles, pero la mayoría sufrió alteraciones en alguna manera, ya sea porque se le agregó humor.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="nombres" id="Radios1" value="2">
                                            <i class="fa fa-circle-o fa-2x"></i>
                                            <i class="fa fa-dot-circle-o fa-2x"></i>        
                                            03 - 12 - 2017
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="nombres" id="Radios1" value="3">
                                            <i class="fa fa-circle-o fa-2x"></i>
                                            <i class="fa fa-dot-circle-o fa-2x"></i>        
                                            Hay muchas variaciones de los pasajes de Lorem Ipsum disponibles, pero la mayoría sufrió alteraciones en alguna manera, ya sea porque se le agregó humor.
                                        </label>
                                    </div>                                    
                                </fieldset>                                
                            </form>
                        <a class="btnSiguiente pull-right btnTransition" data-nexContainer="4">Siguiente <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    </div>
                </div>-->
              </div>              
            </div>
        </div><!-- row -->      
      </div><!-- container -->
      <div class="modal fade" id="ventana" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Código Promocional</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-8">
                                <label for="txCodigoPromocional">Digite Código para acceder a la Valoración: <br></label>
                                <input type="text" id="txCodigoPromocional" name="txCodigoPromocional" maxlength="6" class="form-control input-circle" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btValidar" name="btValidar" class="btn green">Validar</button>
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
      <input type="hidden"  id="urlPrincipal" value="{{ config('constantes.RUTA') }}">
@endsection