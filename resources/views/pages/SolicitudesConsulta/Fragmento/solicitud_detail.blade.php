@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
<div class="modal-dialog" role="document">
    <form id="formulario">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box main-color">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-bar-chart"></i>Detalle de la solicitud
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div id="contenido_completas" class="portlet-body portlet-collapsed" style="display: none;">
                                <div class="form-group">
                                    <label>Cedula</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                        <input type="text" id="id_cedula"
                                               data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                               name="cedula" class="form-control " placeholder="Cedula del cliente"
                                               required
                                               autofocus value="{{$solicitud->cedula}}" disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                        <input type="text" class="form-control " placeholder="Apellido" autofocus
                                               value="{{$solicitud->nombre." ".$solicitud->apellido}}" disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Teléfono celular</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-phone"></i>
                                </span>
                                        <input type="text" id="id_telefono" name="telefono" class="form-control "
                                               data-inputmask="'numericInput': true, 'mask': '999 999-9999', 'rightAlignNumerics':false"
                                               placeholder="Teléfono" autofocus value="{{$solicitud->telefono}}"
                                               disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Pagaduria</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-building"></i>
                                </span>
                                        <input type="text" id="pagaduria" name="pagaduria" class="form-control"
                                               autofocus
                                               value="{{$solicitud->pagaduriaNombre}}" disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Clave para consulta del desprendible online</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-key"></i>
                                </span>
                                        <input type="text"
                                               id="id_clave" name="clave_desprendible" class="form-control "
                                               value="{{$solicitud->clave_desprendible}}" autofocus disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Correo</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                        <input type="email" id="id_email" name="email" class="form-control "
                                               value="{{$solicitud->email}}" autofocus disabled="true">
                                    </div>
                                </div>

                                @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"))) > 0)
                                    <label>Foto documento</label>
                                    {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.SolicitudConsulta"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA") ) }}
                                @else
                                    <label>Foto documento</label>
                                @endif
                                <br>
                                <br>
                                @if(count($ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"))) > 0)
                                    <label>Autorizacion</label>
                                    {{ $ComponentAdjuntos->getUrlViewAdjunto($solicitud->id, config("constantes.SolicitudConsulta"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA") ) }}
                                @else
                                    <label>Autorizacion</label>
                                @endif
                                <br>
                                <br>
                            </div>
                        </div>
                        <div class="portlet box main-color">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-bar-chart"></i>Detalle del comercial
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="expand"></a>
                                </div>
                            </div>
                            <div id="contenido_completas" class="portlet-body portlet-collapsed" style="display: none;">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-user"></i>
                                </span>
                                        <input type="text" class="form-control" autofocus
                                               value="{{$solicitud->usuarioNombre}} {{$solicitud->usuarioPrimerApellido}} {{$solicitud->usuarioApellido}}"
                                               disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                        <input type="text" class="form-control" autofocus
                                               value="{{$solicitud->usuarioEmail}}" disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                        <input type="text" class="form-control" autofocus
                                               value="{{$solicitud->usuarioTelefono}}" disabled="true">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Perfil</label>
                                    <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                        <input type="text" class="form-control" autofocus
                                               value="{{$solicitud->usuarioPerfil}}" disabled="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($solicitud->valoracion_id))
                            <div class="portlet box main-color">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-bar-chart"></i>Detalle del Estudio
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="expand"></a>
                                    </div>
                                </div>
                                <div id="contenido_completas" class="portlet-body portlet-collapsed"
                                     style="display: none;">
                                    <div class="form-group">
                                        <label>Estado Estudio</label>
                                        <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                            <input type="text" class="form-control" autofocus
                                                   value="{{$solicitud->estadoEstudio}}" disabled="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Tasa</label>
                                        <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                            <input type="text" class="form-control" autofocus
                                                   value="{{$solicitud->estudioTasa}}" disabled="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Plazo</label>
                                        <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                            <input type="text" class="form-control" autofocus
                                                   value="{{$solicitud->estudioPlazo}}" disabled="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Cuota</label>
                                        <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                            <input type="text" class="form-control" autofocus
                                                   value="{{number_format($solicitud->estudioCuota, 0, ",", ".")}}"
                                                   disabled="true">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Cr&eacute;dito</label>
                                        <div class="input-group">
                                <span class="input-group-addon ">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                            <input type="text" class="form-control" autofocus
                                                   value="{{number_format($solicitud->estudioValorCredito, 0, ",", ".")}}"
                                                   disabled="true">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($solicitud->estadoEstudio == "NEG")
                                <div class="portlet box main-color">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-bar-chart"></i>Detalle de compras
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="expand"></a>
                                        </div>
                                    </div>
                                    <div id="contenido_completas" class="portlet-body portlet-collapsed"
                                         style="display: none;">
                                        <div class="background-white">
                                            <div class="container-table " style="max-height: 20.4em;overflow: auto;">
                                                <div class="portlet box main-color sinMarginBottom">
                                                    <div class="portlet-title">

                                                    </div>
                                                    <div class="portlet-body portlet-todasObligaciones">
                                                        <table class="table table-striped table-hover text-center todasObligaciones">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-center">Entidad</th>
                                                                <th class="text-center">Estado</th>
                                                                <th class="text-center">Saldo</th>
                                                                <th class="text-center">Cuota</th>
                                                                <th class="text-center">Pago</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="obligacionesCompletas">
                                                            <?php $cont = 0 ?>
                                                            @foreach($solicitud->obligaciones as $obligacion)
                                                                <?php $cont++ ?>
                                                                <tr>
                                                                    <td>
                                                                    <!--<a href="#" id="Entidad" name="Entidad" class="inputEditable" data-type="text" data-pk="{valoracion: '{{ $obligacion->Valoracion }}', obligacion: '{{ $obligacion->NumeroObligacion }}' }" data-url="{{config('constantes.RUTA')}}/Estudio/updEntidadEstadoSaldo" data-title="Ingrese el nombre de la Entidad">{{ $obligacion->Entidad }}</a>-->
                                                                        <a class="pointer text-center color-negro" data-toggle="modal" data-target="#infoObligacion{{ $obligacion->id }}" title="{{ $obligacion->Entidad }}">
                                                                            {{ (strlen($obligacion->Entidad) <= 12)? $obligacion->Entidad : substr($obligacion->Entidad, 0, 12) }}
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <a id="Estado" name="Estado" class="llaveLock pointer estadoCuenta{{ $obligacion->id }} inputEditableSelectEstadoObl color-negro text-normal" data-idobl="{{ $obligacion->id }}" data-inputclass="inputEstado{{ $obligacion->id }}" data-type="select"  data-pk="{valoracion: '{{ $obligacion->Valoracion }}', obligacion: '{{ $obligacion->id }}' }" data-url="{{config('constantes.RUTA')}}/Estudio/updEntidadEstadoSaldo"  data-title="Seleccione acción">
                                                                            {{ $obligacion->EstadoCuenta }}
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <a id="SaldoActual" name="SaldoActual" class="llaveLock pointer saldoActual{{ $obligacion->id }} inputEditable color-negro font-11" data-inputclass="inputEditableMiles" data-type="text"  data-pk="{valoracion: '{{ $obligacion->Valoracion }}', obligacion: '{{ $obligacion->id }}' }" data-url="{{config('constantes.RUTA')}}/Estudio/updEntidadEstadoSaldo" data-title="Ingrese el Saldo Actual" style="{{ ((str_replace(".", "", $obligacion->SaldoActual) - $obligacion->SaldoActualOriginal) == 0)? "" : "color: red" }}" >{{ number_format($obligacion->SaldoActual, 0, ",", '.') }}</a>
                                                                    </td>
                                                                    <td>
                                                                        <?php $valorCuota = (!empty($obligacion->ValorCuota) && $obligacion->ValorCuota > 0) ? false : (!empty($obligacion->CuotasProyectadas) && $obligacion->CuotasProyectadas > 0) ? true : false; ?>
                                                                        <a  style=" {{ ($valorCuota)? "color: blue" : "" }}" id="ValorCuota" name="ValorCuota" class="llaveLock pointer valorCuota{{ $obligacion->id }} inputEditable color-negro font-11 {{ ($obligacion->Desprendible == "S")? "bold" : "" }}" data-inputclass="inputEditableMiles" data-type="text"  data-pk="{valoracion: '{{ $obligacion->Valoracion }}', obligacion: '{{ $obligacion->id }}' }" data-url="{{config('constantes.RUTA')}}/Estudio/updEntidadEstadoSaldo" data-title="Ingrese el valor de la cuota">{{ ($valorCuota)? number_format($obligacion->CuotasProyectadas, 0, ",", ".") : number_format($obligacion->ValorCuota, 0, ",", ".") }}</a>
                                                                    </td>
                                                                    <td>
                                                                        <a id="Pago" name="Pago" class="llaveLock pointer compras{{ $obligacion->id }} {{ ($obligacion->EstadoCuenta == "MORA")? "inputEditableSelectWithParc" : "inputEditableSelect" }} color-negro text-normal" data-inputclass="changePago" data-type="select"  data-pk="{valoracion: '{{ $obligacion->Valoracion }}', obligacion: '{{ $obligacion->id }}' }" data-url="{{config('constantes.RUTA')}}/Estudio/updEntidadEstadoSaldo"  data-title="Seleccione acción">
                                                                            @if($obligacion->Compra == "S")
                                                                                Si
                                                                            @elseif($obligacion->Compra == "N")
                                                                                No
                                                                            @elseif($obligacion->Compra == "P")
                                                                                Parcial
                                                                            @endif
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
                                    </div>
                                </div>
                            @endif
                        @endif
                        <hr/>
                        @if((Auth::user()->perfil == config("constantes.PERFIL_ROOT") || Auth::user()->perfil == config("constantes.PERFIL_ADMIN")) and !isset($solicitud->valoracion_id))
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message-text" class="col-form-label">Descripci&oacute;n Devoluci&oacute;n:</label>
                                    <textarea class="form-control" id="descripcion_devolucion_{{$solicitud->id}}"
                                              name="descripcion_devolucion_{{$solicitud->id}}" cols="20" rows="2"
                                              maxlength="50" style="resize: vertical;" required></textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                @if((Auth::user()->perfil == config("constantes.PERFIL_ROOT") || Auth::user()->perfil == config("constantes.PERFIL_ADMIN")) and !isset($solicitud->valoracion_id))
                    <button type="button" class="btn btn-primary" id="enviar_descripcion" data-id="{{$solicitud->id}}">
                        Devolver
                    </button>
                    <input type="hidden" name="_token" id="token_{{$solicitud->id}}" value="{{ csrf_token() }}">
                    <input type="hidden" value="{{ config("constantes.RUTA") }}"
                           id="dominioPrincipal_{{$solicitud->id}}">
                @endif
            </div>
        </div>
    </form>
</div>
