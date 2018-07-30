@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@inject('FuncionesComponente', 'App\Librerias\FuncionesComponente')
@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@extends('layout.default')

@section('encabezado')
    <link href="{{ asset('css/Cartera/detalle.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('js/Cartera/cartera.js') }}" type="text/javascript"></script>
@endsection

@section('banner')
   <div class="bannerTop">
        <div class="row" style="margin: 0">
            <div class="col-xs-9 col-sm-4 col-md-4  bannerTop-left">
                <span>{{ substr(utf8_decode($infoEstudio->nombre)." ".utf8_decode($infoEstudio->apellido), 0, 22)  }}</span>
            </div>
            <div class="hidden-xs col-sm-4 col-md-4   texto-derecha text-center">
                <span><span class="uppercase">{{ $infoEstudio->pagaduriaEstudio }}</span></span>
            </div>
            <div class="col-xs-3 col-sm-4 col-md-4 bannerTop-right">
                <span>{{ number_format($infoEstudio->cedula, 0, ",", ".") }}</span>
            </div>
        </div>
    </div>    
@endsection

@section('content')
<!--Modal Pago -->
<div class="modal fade modalEstudio" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-12">      
                    <form action="{{ url('formPago') }}" enctype="multipart/form-data" id="formAddPago" method="POST">
                        <fieldset>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <legend style="margin-bottom: 0; display: none">Deuda a la fecha: {{ number_format($deudaLaFecha, 0, ",", ".") }}</legend>
                            <h3 class="text-center uppercase bold">Tipo de Pago</h3>
                            <div class="containerOptionPago row margin-bottom-15">
                                <div class="col-md-12">                                
                                    <div class="col-md-6 padding-none">
                                        <div class="optionPago text-center bold pointer optionSelect" data-opcion="1">INDIVIDUAL</div>    
                                    </div>
                                    @if($infoCertificacion !== false)
                                        <div class="col-md-6 padding-none">
                                            <div class="optionPago text-center bold pointer" data-opcion="2" data-infocertificacion="{{ ($infoCertificacion !== false)? json_encode($infoCertificacion[0]) : false }}">CERTIFICACI&Oacute;N</div>                                            
                                        </div>
                                    @endif
                                    <input type="hidden" id="tipoPago" name="tipoPago" value="Individual">
                                </div>
                            </div>                       
                            
                            <div class="col-md-6">                                
                                <div class="form-group">
                                    <label for="fechaPago" class="bold">FECHA:</label>
                                     <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>                                        
                                        <input class="form-control desplegarCalendario" type="text" id="fechaPago" name="fechaPago" readonly="true" value="{{ (session('fechaPago', false) != false)? session('fechaPago') : ""  }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ValorPago" class="bold">VALOR:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-usd"></span></div>                             
                                        <input class="form-control puntosMiles" type="text" id="ValorPago" name="ValorPago" value="{{ (session('ValorPago', false) != false)? session('ValorPago') : ""  }}">
                                    </div>
                                </div>
                            </div>                            
                            <div class="col-md-12 center-text">
                                <div class="form-group">                                    
                                    <input type="file" class="desplegarFile" id="soporte" name="soporte">
                                </div>
                            </div>                            
                        </fieldset>
                        <input type="hidden" name="idEstudio" id="idEstudio" value="{{ $idEstudio }}">    
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">    
                    </form>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn" data-dismiss="modal">Cerrar</button>        
                <button type="button" class="btn lockClick" onclick="event.preventDefault();$('#formAddPago').submit();">Pagar</button>        
            </div>
        </div>
    </div>
</div>    
<!--Fin modal Pago -->



@if($valorSaldoDevolucionCliente !== false && $valorSaldoDevolucionCliente > 0)
<div class="modal fade modalEstudio" id="modalDevoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <h4 class="uppercase text-center bold">DEVOLUCIONES</h4>                         
                <p>El Cliente cuenta con un saldo a favor de:</p>
                <p class="text-center valorDevolucion">${{ number_format($valorSaldoDevolucionCliente, 3, ",", ".") }}</p>
                <div class="text-center">
                    <button class="btn btn-rojo" onclick="confirmar('Esta seguro de que se ha realizado la devolución del saldo a favor al cliente', '{{ config("constantes.RUTA")."Devolucion/Reintegro/".$infoEstudio->id }}')">
                        REINTEGRADO
                        <span class="fa fa-check"></span>
                    </button>
                </div>    
            </div> 
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.CERTIFICACION_VTM') }}</h4>                            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>       
    </div>
</div>
@endif

<!--Modal Certificacion -->
<div class="modal fade modalEstudio" id="modalCertificacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">                
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">      
                            <form action="{{ url('GenerarCertificacion') }}" enctype="multipart/form-data" id="formAddCertificacion" method="POST">
                                <fieldset>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif                                       
                                    <div class="col-md-6 col-md-offset-3 center-text">
                                        <div class="input-group">
                                            <div class="input-icon">
                                                <i class="fa fa-dollar fa-fw"></i>
                                                <input class="form-control miles" id="vlrCertificacion" {{(isset($infoCertificacion[0]->valorProyectado))?"disabled":""}} value="{{ (isset($infoCertificacion[0]->valorProyectado))? $infoCertificacion[0]->valorProyectado: $proyeccionCertificacion }}"> 
                                            </div>
                                            <span class="input-group-btn">
                                                <button id="genCertificacion" class="btn btn-rojo uppercase" type="button" {{(isset($infoCertificacion[0]->valorProyectado))?"disabled":""}}>
                                                    <i class="fa fa-arrow-left fa-fw"></i> Generar
                                                </button>
                                            </span>
                                        </div>
                                    </div>                                                                                    
                                </fieldset>
                                <input type="hidden" name="idEstudio" id="idEstudio" value="{{ $idEstudio }}">    
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">    
                            </form>
                        </div>
                    </div>
                    <div class="row" id="seccionCertificaciones">
                    @if(isset($infoCertificacion[0]->valorProyectado))                   
                    
                        <div class="col-md-12">                    
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Solicitud</th>
                                        <th class="text-center">Usuario</th>
                                        <th class="text-center">Valor</th>                            
                                        <th class="text-center">Vigencia</th>    
                                        <th class="text-center"></th>    
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>                                
                                <tbody id="containerListaAdjuntosObligaciones107">
                                    @foreach($infoCertificacion as $info)
                                    <tr id="{{ $info->id }}">
                                        <td class="text-center uppercase">{{ date_format($info->created_at, 'd-m-Y')}}</td>
                                        <td class="text-center uppercase">{{ $info->comercial}}</td>
                                        <td class="text-center uppercase">{{ number_format($info->valorProyectado,0,",",".") }}</td>
                                        <td class="text-center uppercase">{{ date_format(new DateTime($info->diaCorte."-".$info->mesVigencia."-".$info->anioVigencia), 'd-m-Y') }}</td>                                        
                                        <td class="text-center uppercase">
                                            <a class="color-negro" title="Visualizar" href="{{ config("constantes.RUTA") }}VerCertificacion/{{ $info->id_estudio }}" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a>
                                        </td>                                        
                                        <td class="text-center">
                                            <a title="Eliminar" style="cursor: pointer" class="deleteAdjuntoCertificaciones color-redA" data-adjunto="{{ $info->id }}" data-url="{{ config("constantes.RUTA") }}Cartera/EliminarCertificacion"><span class="fa fa-remove"></span></a>                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                
                        </div>                    
                    @endif
                    </div>
            </div>
            <div class="modal-footer">                
                    <button type="button" class="btn" data-dismiss="modal">Cerrar</button>                                        
                </div>
        </div>
    </div>
</div>     
<!--Fin modal Certificacion -->

<div class="modal fade modalEstudio" id="modalAdjuntoDspVtm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="containerComponenteCertificacion">
                            {{$ComponentAdjuntos->dspFormulario($idEstudio, config("constantes.KEY_ESTUDIO"), config("constantes.DESPRENDIBLECUOTA_VTM"), config("constantes.MDL_VALORACION"), false, "clear", false, true, 'DESPCUOTA VTM', "container_cargaTablaAdjuntosDspVTM", false)}}
                        </div>
                    </div>
                </div>  
                <div class="row margin-top-10">
                    <div class="col-md-12" id="container_cargaTablaAdjuntosDspVTM">                              
                        {{$ComponentAdjuntos->createTableOfAdjuntos($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.DESPRENDIBLECUOTA_VTM"))}}
                    </div>
                </div>                               
            </div> 
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.CERTIFICACION_VTM') }}</h4>                            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>       
    </div>
</div>

<div class="modal fade modalEstudio" id="modalAdjuntoVisBanco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="containerComponenteCertificacion">
                            {{$ComponentAdjuntos->dspFormulario($idEstudio, config("constantes.KEY_ESTUDIO"), config("constantes.VISADO_BANCO"), config("constantes.MDL_VALORACION"), false, "clear", false, true, 'VISADO BANCO', "container_cargaTablaAdjuntosVisBanco", false)}}
                        </div>
                    </div>
                </div>  
                <div class="row margin-top-10">
                    <div class="col-md-12" id="container_cargaTablaAdjuntosVisBanco">                              
                        {{$ComponentAdjuntos->createTableOfAdjuntos($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.VISADO_BANCO"))}}
                    </div>
                </div>                               
            </div> 
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.CERTIFICACION_VTM') }}</h4>                            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>       
    </div>
</div>

<div class="modal fade modalEstudio" id="modalAdjuntoLbzBanco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="containerComponenteCertificacion">
                            {{$ComponentAdjuntos->dspFormulario($idEstudio, config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_BANCO"), config("constantes.MDL_VALORACION"), false, "clear", false, true, 'LIBRANZA BANCO', "container_cargaTablaAdjuntosLbzBanco", false)}}
                        </div>
                    </div>
                </div>  
                <div class="row margin-top-10">
                    <div class="col-md-12" id="container_cargaTablaAdjuntosLbzBanco">                              
                        {{$ComponentAdjuntos->createTableOfAdjuntos($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.LIBRANZA_BANCO"))}}
                    </div>
                </div>                               
            </div> 
            <div class="modal-footer">
                <h4 class="modal-title pull-left uppercase text-white" id="myModalLabel">{{ config('constantes.CERTIFICACION_VTM') }}</h4>                            
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>       
    </div>
</div>
@foreach($obligacionesUsuario as $obligacionUsuario)
<div class="modal fade modalEstudio" id="modalAdjunto{{$obligacionUsuario->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>                                                                            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label>Entidad: </label>
                        <label class="text-center">{{ $obligacionUsuario->Entidad}}</label><br>                        
                        <div class="form-group">
                            <label for="">Estado:</label>
                            <div class="input-group" style="margin: 0 auto;">
                                <select class="form-control sltAccionObligacion" data-padre="{{ $obligacionUsuario->id }}">                                    
                                   <option value="0">Seleccione una opción</option>
                                   @if($obligacionUsuario->optionGestionObligacionesPYS != "hidden")
                                    <optgroup label="Paz y Salvos" class="containerPazYSalvo">
                                        @if($obligacionUsuario->optionGestionObligacionesPYS == "showAll")
                                        <option value="PSOL">Solicitada</option>
                                        @endif
                                        @if($obligacionUsuario->optionGestionObligacionesPYS == "showRad" || $obligacionUsuario->optionGestionObligacionesPYS == "showAll")                                    
                                        <option value="PRAD">Radicada</option>
                                        @endif
                                    </optgroup>
                                    @endif                                  
                                </select>                                    
                            </div>
                        </div>                        
                    </div>
                </div>
                
                <div class="row containerSolicitado" style="display: none">
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="">Fecha Solicitud</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaSolicitud fechasAdjuntoSolicitud" data-id="{{ $obligacionUsuario->id }}" id="fechaSolicitud{{ $obligacionUsuario->id }}" name="fechaSolicitud" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="">Fecha Entrega</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaEntrega fechasAdjuntoSolicitud" data-id="{{ $obligacionUsuario->id }}" id="fechaEntrega{{ $obligacionUsuario->id }}" name="fechaEntrega" value="">                                  
                            </div>
                        </div>
                    </div>                                        

                    <div class="col-md-12">
                        <div class="containerAdjuntoSolicitudPYS" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacionUsuario->id, config("constantes.KEY_OBLIGACION"), config("constantes.SOL_PAZ_SALVO_CARTERA"), config("constantes.MDL_VALORACION"), false, "function", "saveFechasSolicitud", false, $obligacionUsuario->Entidad, false, false)}}
                        </div>
                    </div>
                    
                </div>

                <div class="row containerRadicada" style="display: none">
                    <div class="col-md-4">
                        <div class="form-group text-center">
                            <label for="">Fecha Radicación</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaRadicacion fechasAdjunto" data-id="{{ $obligacionUsuario->id }}" id="fechaRadicacion{{ $obligacionUsuario->id }}" name="fechaRadicacion" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 containerFechaVencimiento">
                        <div class="form-group text-center">
                            <label for="">Fecha Vencimiento</label>
                            <div class="input-group" style="margin: 0 auto">
                                <input type="text" readonly class="form-control fechaVencimiento fechasAdjunto" data-id="{{ $obligacionUsuario->id }}" id="fechaVencimiento{{ $obligacionUsuario->id }}" name="fechaVencimiento" value="">                                  
                            </div>
                        </div>
                    </div>                                                            

                    <div class="col-md-12">
                        <div class="containerComponentePazySalvo" style="display: none">
                            {{$ComponentAdjuntos->dspFormulario($obligacionUsuario->id, config("constantes.KEY_OBLIGACION"), config("constantes.RAD_PAZ_SALVO_CARTERA"), config("constantes.MDL_VALORACION"), false, "function", "updateInfoAdjuntos", false, $obligacionUsuario->Entidad, false, false)}}
                        </div>
                    </div>
                </div>
                <br>      
                <div id="AdjuntosCargados{{ $obligacionUsuario->id }}">
                    <?php echo $FuncionesComponente->traerTablaAdjuntos(false, $obligacionUsuario->id, false, false, config("constantes.PAZ_SALVO_CARTERA")) ?>
                </div>
            </div> 
            <div class="modal-footer">                
                <button type="button" class="btn btn-default btnGuardar" data-id="{{ $obligacionUsuario->id }}" style="display: none">Guardar</button>        
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>       
            </div>
        </div>       
    </div>
</div>
@endforeach
<!-- FIN MODALES-->
<div class="row">
    <div class="col-md-12 col-xs-12 col-lg-12 col-sd-12">
        <h1 class="text-center tituloPrincipal">CARTERA BANCARIZATE [{{ $infoEstudio->Estado }}]</h1>
    </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 columna margin-15-movil">
      <p class="nombre uppercase titulo">DETALLE DE LA  OPERACI&Oacute;N</p>        
        <div class="row margin-top-5 margin-bottom-5">
            <div class="col-xs-6 col-sd-6 col-md-6 col-lg-6 text-center contenedor">                
                        <p class="text-center uppercase margin-bottom-5 subtitulo margin-none bold">CR&Eacute;DITO </p>
                        <!--<p class="zero-margin uppercase text-center">crÃ©dito <span class="fa fa-plus pointer"></span></p>-->
                        <p class="zero-margin text-center vlrcredito">{{ $utilidadesClass->format_number($infoEstudio->ValorCredito) }}</p>                    
                        <ul class="label-credito margin-top-25 texto">
                        <li class="uppercase">tasa</li>
                        <li class="uppercase">plazo</li>
                        <li class="uppercase">CUOTA</li>
                      </ul>
                      <ul class="value-credito margin-top-25 titulo">
                        <li class="bold">{{ $infoEstudio->Tasa }}%</li>
                        <li class="bold">{{ $infoEstudio->Plazo }}</li>
                        <li class="bold">{{ $utilidadesClass->format_number($infoEstudio->Cuota) }}</li>
                      </ul>              
            </div>    
            <div class="col-xs-6 col-sd-6 col-md-6 col-lg-6 text-center border-left contenedor">
                <p class="text-center uppercase subtitulo margin-bottom-5 margin-none bold">RENTABILIDAD</p>
                <div class="circulo">
                    <span class="value">{{$porcentajeRentabilidad}}%</span>    
                </div>                
                <p class="text-center margin-none texto bold">${{ number_format($rentabilidad, 0, ",", ".") }} - {{ $diferencia }} D&iacute;as</p>                
            </div>            
        </div>        
        <div class="row border-top">          
          <div class="col-xs-12 col-sd-12 col-md-12 col-lg-12 text-center contenedor">
              <p class="text-center uppercase bold  titulo margin-5">GESTI&Oacute;N DE PAZ Y SALVOS</p>              
              <div class="container-table" style="height: 161px;overflow: auto;">
                <table class="table table-striped table-hover text-center tabla">
                  <thead>
                    <tr>                  
                      <th class="subtitulo">#</th>
                      <th class="subtitulo">ENTIDAD</th>
                      <th class="subtitulo">FECHA E.</th>
                      <th class="subtitulo"></th>                  
                    </tr>
                  </thead>
                  <tbody>
                    @php                    
                    $contTabla = 1;
                    $filas1 = count($obligacionesUsuario);
                    $completarFilas1 = ($filas1 >=6)? 0 : 6 - $filas1;
                    @endphp
                    @foreach($obligacionesUsuario as $obligacionUsuario)
                      <tr>
                        <td class="texto">{{ $contTabla++ }}</td>
                        <td class="texto">{{ substr($obligacionUsuario->Entidad, 0, 14) }}</td>
                        <td class="texto">{{ date("d/m/y") }}</td>
                        <td class="texto">
                            @if($obligacionUsuario->tieneAdjuntos)
                                <a class="pointer text-center" data-toggle="modal" data-target="#modalAdjunto{{$obligacionUsuario->id}}" id="Enlace{{$obligacionUsuario->id}}"><span class="fa fa-paperclip color-negro" title="Cargar Adjunto"></span></a>                        
                            @else
                                <a class="pointer text-center" data-toggle="modal" data-target="#modalAdjunto{{$obligacionUsuario->id}}" id="Enlace{{$obligacionUsuario->id}}"><span class="fa fa-arrow-up color-negro" title="Cargar Adjunto"></span></a>                        
                            @endif
                        </td>                       
                      </tr>
                    @endforeach
                     <!--Bloque para completar el tamaÃ±o con celdas vacias-->
                    @for($i = 1; $i <= $completarFilas1; $i++)
                      <tr>
                        <td class="texto"><span style="visibility: hidden">1</span></td>                    
                        <td class="texto"></td>                    
                        <td class="texto"></td>                    
                        <td class="texto"></td>                                            
                      </tr>
                    @endfor
                  </tbody>
                </table>           
              </div>
          </div>
        </div>
  </div>

  <div class="col-xs-12 col-sm-12 col-lg-6 col-md-6 columna">
      <div class="row">
          <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12 contenedor">
              <p class="nombre uppercase titulo margin-movil">estado de cuenta
                  @if($valorSaldoDevolucionCliente !== false && $valorSaldoDevolucionCliente > 0)
                    <span class="fa fa-mail-reply-all pointer" data-toggle="modal" data-target="#modalDevoluciones"></span>
                  @endif
              </p>
        <div class="container-table" style="height: 115px;overflow: auto;">
            <table class="table table-striped table-hover text-center tabla">
              <thead>
                <tr>
                  <th class="subtitulo">#</th>
                  <th class="subtitulo">PAGO</th>
                  <th class="subtitulo">FECHA P</th>                  
                  <th class="subtitulo">SALDO</th>
                  <th class="subtitulo"></th>
                </tr>
              </thead>
              <tbody>
                @php
                  $credito = 38761000;
                  $filas = count($infoPagos);
                  $completarFilas = ($filas >= 4)? 0 : 4 - $filas;
                  $cont = 1;
                @endphp
                @foreach($infoPagos as $pago)                
                  <tr>
                    <td class="texto">{{ $cont++ }}</td>
                    <td class="texto">{{ number_format($pago["infoPago"]->pago, 0, ",", ".") }}</td>
                    <td class="texto">{{ date("d/m/y", strtotime($pago["infoPago"]->fecha)) }}</td>                    
                    <td class="texto">{{ number_format(ceil($pago["saldoCapital"]), 0, ",", ".") }}</td>                   
                    <td><a class="pointer text-center color-negro" target="_blank" href="{{ config("constantes.RUTA") }}visualizar/{{ $pago["infoPago"]->idAdjunto }}"><span class="fa fa-paperclip" title="Cargar Adjunto"></span></a></td>
                  </tr>                  
                @endforeach
                <!--Bloque para completar el tamaÃ±o con celdas vacias-->
                @for($i = 1; $i <= $completarFilas; $i++)
                  <tr>
                    <td class="texto"><span style="visibility: hidden">1</span></td>                    
                    <td class="texto"></td>                    
                    <td class="texto"></td>                    
                    <td class="texto"></td>                    
                    <td class="texto"></td>                    
                  </tr>
                @endfor
                
              </tbody>
            </table>
        </div>
          <div class="row sumatorias">
            <div class="col-xs-6 col-sm-7 col-md-6 col-lg-6 text-center texto contenedor">
                RECAUDADO: <span class="bold">{{ number_format($totalRecaudo[0]->pago,0,",",".") }}</span>
            </div>
            <div class="col-xs-6 col-sm-5 col-md-6 col-lg-6 text-center texto contenedor">
                CUOTAS: <span class="bold">{{ number_format($estadoCuentaCuotas, 0, ",", ".") }}</span>
            </div>            
          </div>
    </div>          
    </div>           
        <div class="row border-top">            
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center contenedor movilW100">
            <p class="zero-margin uppercase text-center bold titulo">funciones</p>            
            <button type="button" name="button" class="btn btn-rojo uppercase botones border-top-left border-top-right margin-top-20 texto" data-toggle="modal" data-target="#modalPago" {{($infoEstudio->Estado == config("constantes.ESTUDIO_BANCO"))? "disabled" : "" }}><span class="fa fa-usd"></span> PAGO</button>
            <button type="button" name="button" class="btn btn-rojo uppercase botones texto" {{($infoEstudio->Estado == config('constantes.ESTUDIO_BANCO')) ? "" : "disabled"}} onclick="window.location.href ='{{ config('constantes.RUTA').'PazSalvo/'.$infoEstudio->id}}'"><span class="fa fa-check"></span> PAZ Y SALVO</button>
            <button type="button" name="button" class="btn btn-rojo uppercase botones texto" data-toggle="modal" data-target="#modalCertificacion"><span class="fa fa-file-pdf-o"></span> CERTIFICACI&Oacute;N</button>
            <a type="button" name="button" class="btn btn-rojo uppercase botones border-bottom-left border-bottom-right texto" href="{{ config("constantes.RUTA") }}EstadoCuenta/{{ $idEstudio }}" target="_blank"><span class="fa fa-calculator"></span> ESTADO DE CUENTA</a>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center border-left contenedor movilW100 content-mercado">
            <p class="text-center uppercase zero-margin bold titulo">GESTI&Oacute;N MERCADO</p>
            <ul class="check-list texto margin-top-20">
                <li class="uppercase rojo">
                    <a id="comercialCartera" class="iniciarInputEditableComercial" data-pk="{{ $idEstudio }}" data-value="{{ $comercialSeleccionado }}" data-source="{{ json_encode($comerciales) }}" data-type="select" data-url="{{config('constantes.RUTA')}}Cartera/setComercialCartera"  data-title="Seleccione el comercial"></a>  
                </li>
                <li class="uppercase cafe">
                    <a id="bancoCartera" class="iniciarInputEditableBancos" data-pk="{{ $idEstudio }}" data-value="{{ $bancoSeleccionado }}" data-source="{{ json_encode($opcionesBancos) }}" data-type="select" data-url="{{config('constantes.RUTA')}}Cartera/setBancoCartera"  data-title="Seleccione el comercial"></a>  
                </li>
                <li class="uppercase azul">
                    <a id="ValorAprobado" class="iniciarInputEditableValorAprobado" data-pk="{{ $idEstudio }}" data-value="{{ $valorAprobadoBanco }}" data-type="text" data-inputclass="puntosMiles" data-url="{{config('constantes.RUTA')}}Cartera/setValorAprobadoBanco"  data-title="Digite un valor"></a>
                </li>
                <li class="uppercase azul">
                    <a id="estadoCartera" class="iniciarInputEditableEstado" data-pk="{{ $idEstudio }}" data-value="{{ $estadoCarteraSeleccionado }}" data-source="{{ json_encode($listEstadosCartera) }}" data-type="select" data-url="{{config('constantes.RUTA')}}Cartera/setEstadoCartera"  data-title="Seleccione el Estado"></a>                    
                </li>
            </ul>          
          </div>
        </div>
  </div>

</div>
<input type="hidden" value="{{ config("constantes.RUTA") }}" id="dominioPrincipal">
<input type="hidden" value="{{ $idEstudio }}" id="idEstudio">
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> 
@if(session('OK'))
    <script>
      $(function(){
          displayMessageMini("{{ session('OK') }}");
      })
  </script>  
@endif

@if($errors->any())
  <script>
      $(function(){
          $("#modalPago").modal("show");
      })
  </script>  
@endif
@php
        Session::forget('fechaPago');
        Session::forget('ValorPago');
@endphp
 
@endsection

