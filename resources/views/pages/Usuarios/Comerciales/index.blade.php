@extends('layout.default')
@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Comerciales
                    </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a href="" id="lkSave" name="lkSave" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        @endif
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
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center"
                           id="tabla">
                        <thead>
                        <tr>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Tipo</th>
                            <th> Fecha</th>
                            <th> Telefono</th>
                            <th> Email</th>
                            <th> Estado</th>

                            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                <th> Acción</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($usuarios as $usuario)
                            <tr id="{{$usuario->id}}">
                                <td><a data-show-url="{{url('comerciales_vtm', $usuario->id)}}" class="comercial-detail">{{ $usuario->nombres() }}</a></td>
                                <td><a href="{{url('GestionOficina',$usuario->id)}}" class="show-comercial-data">@if($usuario->tipo_de_persona == "natural"){{ number_format($usuario->cedula) }} @else {{$usuario->cedula}} @endif</a></td>
                                <td>{{ $usuario->perfil }}</td>
                                <td>{{ $usuario->created_at->format('Y-m-d') }}</td>
                                <td>{{ $usuario->telefono }}</td>
                                <td><a target="_blank" href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $usuario->email }}">{{ $usuario->email }} </a></td>
                                <td>{{ $usuario->estado }}</td>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a data-update-url="{{ url('comerciales_vtm', ['id'=>$usuario->id, 'edit'=>'edit'] ) }}"
                                               name='lkEdit' class='btn btn-icon-only yellow-gold lkEdit'>
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <button name='lkDelete' class='btn btn-icon-only red btn-delete'
                                                    data-delete-url="{{ url('comerciales_vtm', ['id'=>$usuario->id]) }}">
                                                <i class='fa fa-close'></i>
                                            </button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventanas modales-->
    <div class="modal fade" id="ventana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" data-create-url="{{url('comerciales_vtm')}}" class="save-form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Usuario</h4>
                    </div>
                    <div class="modal-body">
                        <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="Estado" class="control-label">Tipo de persona:</label>
                                                    <select id="tipo_persona" required name="tipo_de_persona"
                                                            class="form-control select2 circle">
                                                        <option disabled selected></option>
                                                        <option value="natural">Natural</option>
                                                        <option value="juridica">Juridica</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12" style="display: none" id="natural">
                                                    <div class="form-group">
                                                        <label for="Nombre" class="control-label">Nombre:</label>
                                                        <input type="text" name="nombre" id="txNombre" maxlength="255"
                                                               class="form-control input-circle" placeholder="Nombre de Usuario.">
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="Apellido" class="control-label">Apellido:</label>
                                                        <input type="text" name="apellido" id="txApellido" maxlength="255"
                                                               class="form-control input-circle" placeholder="Apellido de Usuario.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Cedula" class="control-label">Cedula:</label>
                                                        <input name="cedula" type="text" id="txCedula" maxlength="11"
                                                               class="form-control input-circle" placeholder="Cedula de Usuario.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Sexo" class="control-label">Sexo:</label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="sexo" id="rdMasculino" value="M" checked>Masculino
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="sexo" id="rdFemenino" value="F">Femenino
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="txFechaNacimiento" class="control-label">Fecha de
                                                            Nacimiento:</label>
                                                        <input name="fecha_nacimiento" type="text" id="txFechaNacimiento"
                                                               class=" desplegarCalendario form-control input-circle"
                                                               placeholder="Fecha de Nacimiento de Usuario.">
                                                    </div>
                                                </div>
                                                <div style="display: none" class="col-md-12" id="juridica">
                                                    <div class="form-group">
                                                        <label for="Cedula" class="control-label">Nit:</label>
                                                        <input name="cedula" type="text" id="txCedula" maxlength="20"
                                                               class="form-control input-circle" placeholder="Nit.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Cedula" class="control-label">Direccion:</label>
                                                        <input name="direccion" type="text" id="txCedula" maxlength="100"
                                                               class="form-control input-circle" placeholder="Dirección.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="nombre" class="control-label">Razón social:</label>
                                                        <input name="nombre" type="text" id="txCedula" maxlength="11"
                                                               class="form-control input-circle" placeholder="Razón social.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Cedula" class="control-label">Nombre de representante legal:</label>
                                                        <input name="representante_legal" type="text" id="txCedula" maxlength="11"
                                                               class="form-control input-circle" placeholder="Nombre representante legal.">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Cedula" class="control-label">Documento de representante legal:</label>
                                                        <input name="documento_representante_legal" type="text" id="txCedula" maxlength="20"
                                                               class="form-control input-circle" placeholder="Documento representante legal.">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                        <div class="form-group">
                                            <label for="txTelefono" class="control-label">Telefono:</label>
                                            <input required type="text" id="txTelefono" maxlength="100" name="telefono"
                                                   class="form-control input-circle" placeholder="Telefono de Usuario.">
                                        </div>
                                        <div class="form-group">
                                            <label for="Email" class="control-label">Email:</label>
                                            <input required type="email" id="txEmail" name="email" maxlength="255"
                                                   class="form-control input-circle"
                                                   placeholder="Correo Electronico de Usuario.">
                                        </div>
                                        <div id="divTxPassword" class="form-group">
                                            <label for="txPassword" class="control-label">Contraseña:</label>
                                            <input required type="password" id="password_id" name="password"
                                                   maxlength="255"
                                                   class="form-control input-circle"
                                                   placeholder="Contraseña de Usuario.">
                                            <input onclick="changePassword()" type="checkbox"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                        </div>
                                        <div id="divTxConfirmacion" class="form-group">
                                            <label for="txConfirmacion" class="control-label">Confirmación
                                                Contraseña:</label>
                                            <input required type="password" id="password_id_confir" name="password_confirm"
                                                   maxlength="255" class="form-control input-circle"
                                                   placeholder="Confirmación Contraseña.">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Email" class="control-label">Número de cuenta:</label>
                                            <input required type="text" id="numeroCuenta" name="numero_de_cuenta"
                                                   maxlength="255"
                                                   class="form-control input-circle" placeholder="Número de cuenta.">
                                        </div>
                                        <div class="form-group">
                                            <label for="Sexo" class="control-label">Tipo de cuenta:</label><br>
                                            <label class="radio-inline">
                                                <input type="radio" name="tipo_cuenta" value="ahorros"
                                                       checked>Ahorros
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="tipo_cuenta" value="">Corriente
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="Estado" class="control-label">Banco:</label>
                                            <select id="banco_select" required name="banco"
                                                    class="form-control select2 circle">
                                                <option disabled selected></option>
                                                @foreach($bancos as $banco)
                                                    <option value="{{$banco->nombre}}">{{$banco->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="Estado" class="control-label">Tipo de comercial:</label>
                                            <select id="slPerfil" required name="perfil"
                                                    class="form-control select2 circle">
                                                <option disabled selected></option>
                                                <option value="COM">Comercial</option>
                                                <option value="LID">Lider comercial</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label class="control-label" for="inputPatient">Comentario:</label>
                                            <div class="field desc">
                                                <textarea class="form-control" id="descripcion" name="comentaro"
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

    <div class="modal fade" id="ventana-update" tabindex="-1" area-hidden="true">

    </div>

    <div class="modal fade" id="vtnPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Cambio de Contraseña</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="col-md-10">
                                <p>
                                <div class="form-group">
                                    <label for="Contraseña1" class="control-label">Contraseña:</label>
                                    <input type="password" id="password_id_change" maxlength="255"
                                           class="form-control input-circle" placeholder="Contraseña.">
                                </div>
                                <input type="checkbox" onclick="changePassword()">Mostrar contraseña
                                </p>
                                <p>
                                <div class="form-group">
                                    <label for="Contraseña2" class="control-label">Confirmación Contraseña:</label>
                                    <input type="password" id="txPassword2" maxlength="255"
                                           class="form-control input-circle" placeholder="Confirmación Contraseña.">
                                </div>
                                </p>
                            </div>{{-- col-md-6 --}}
                        </div>{{-- row --}}
                    </div>{{-- scroller --}}
                </div>{{-- modal body --}}
                <div class="modal-footer">
                    <button type="button" id="btActualizarPass" name="btActualizarPass" class="btn yellow-gold">Cambiar
                        Contraseña
                    </button>
                    <button type="button" id="btAtras" name="btAtras" class="btn dark btn-outline">Atrás</button>
                </div>
            </div>{{-- modal content --}}
        </div>{{-- modal dialog --}}
    </div>{{-- modal fade --}}

    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <input type="hidden" id="hnId" name="hnId" value="">
    <input type="hidden" name="_token" id="token_auth" value="{{ csrf_token() }}">
    <script src="{{ asset('js/Comerciales/index.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        function changePassword(){
            var x = document.getElementById("password_id");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        function formToJson(form) {
            var data = new FormData(form);
            var object = {};
            data.forEach(function (value, key) {
                object[key] = value;
            });
            object['_token'] = $('#token_auth').val();
            return object;
        }
        $(document).on('change', '#tipo_persona', function () {
            $('#natural').hide();
            $('#natural :input').attr("disabled", true);
            $('#juridica').hide();
            $('#juridica :input').attr("disabled", true);
            $('#'+$(this).val()).show();
            $('#'+$(this).val()+' :input').attr('disabled', false);
        });

        $(document).on('submit', '.save-form', function (e) {
            e.preventDefault();
            ruta = "";
            selector = $(this);
            method = $(this).attr('method');
            ruta = selector.data('create-url');
            $.ajax({
                type: method,
                url: ruta,
                data: formToJson($(this)[0]),
                success: function (data) {
                    resultadoEvento(data);
                    $('ventana-update').modal('hide');
                }
            });
        });

        $(document).ready(function () {
            $('#lkSave').click(function (event) {
                $('#id_nombre').val("");
                $('#id_tipo').val("");
                $('#hnAccion').val("");
                $("#txCodigo").prop('disabled', false);
                $('#btGuardar').addClass('green');
                $('#btGuardar').removeClass('yellow-gold');
                $('#btGuardar').text("Guardar");
                $('#ventana').modal('show');
            });
            /****************
             Evento del link en fila para dirigirse a Actualizar
             *****************/
            $(document).on('click', '.lkEdit', function () {
                fetch($(this).data('update-url')).then(respose => respose.text()).then(text => {
                    $('#ventana-update').html(text);
                    $('#ventana-update').modal('show');
                    $('.scroller').slimScroll({
                        height: '300px'
                    });
                });
            });

            $(document).on('click', '.comercial-detail', function () {
                fetch($(this).data('show-url')).then(respose => respose.text()).then(text => {
                    $('#ventana-update').html(text);
                    $('#ventana-update').modal('show');
                    $(".wrapper").css("opacity", "1");
                    $("#cargadorAjax").remove();
                    $('.scroller').slimScroll({
                        height: '300px'
                    });
                });
                $(".wrapper").css("opacity", "0.2");
                $("body").append('<div id="cargadorAjax" style="position: fixed;z-index: 10000000000000;left: 50%;margin-left: -100px;top: 50%;margin-top: -100px;width: 200px;height: 200px;"><style type="text/css">.base{color: #060062}#cargadorAjax h1{position:absolute;font-family:"sans-serif";font-weight:600;font-size:12px;text-transform:uppercase;left:50%;top:58%;margin-left:-20px}#cargadorAjax .body{position:absolute;top:50%;margin-left:-50px;left:50%;animation:speeder .4s linear infinite}#cargadorAjax .body > span{height:5px;width:35px;background:#060062;position:absolute;top:-19px;left:60px;border-radius:2px 10px 1px 0}#cargadorAjax .base span{position:absolute;width:0;height:0;border-top:6px solid transparent;border-right:100px solid #060062;border-bottom:6px solid transparent}#cargadorAjax .base span:after{content:"";height:22px;width:22px;border-radius:50%;background:#060062;position:absolute;right:-110px;top:-16px}#cargadorAjax .base span:before{content:"";position:absolute;width:0;height:0;border-top:0 solid transparent;border-right:55px solid #060062;border-bottom:16px solid transparent;top:-16px;right:-98px}#cargadorAjax .face{position:absolute;height:12px;width:20px;background:#060062;border-radius:20px 20px 0 0;transform:rotate(-40deg);right:-125px;top:-15px}#cargadorAjax .face:after{content:"";height:12px;width:12px;background:#060062;right:4px;top:7px;position:absolute;transform:rotate(40deg);transform-origin:50% 50%;border-radius:0 0 0 2px}#cargadorAjax .body > span > span:nth-child(1),.body > span > span:nth-child(2),.body > span > span:nth-child(3),.body > span > span:nth-child(4){width:30px;height:1px;background:#060062;position:absolute;animation:fazer1 .2s linear infinite}#cargadorAjax .body > span > span:nth-child(2){top:3px;animation:fazer2 .4s linear infinite}#cargadorAjax .body > span > span:nth-child(3){top:1px;animation:fazer3 .4s linear infinite;animation-delay:-1s}#cargadorAjax .body > span > span:nth-child(4){top:4px;animation:fazer4 1s linear infinite;animation-delay:-1s}@keyframes fazer1{0%{left:0}100%{left:-80px;opacity:0}}@keyframes fazer2{0%{left:0}100%{left:-100px;opacity:0}}@keyframes fazer3{0%{left:0}100%{left:-50px;opacity:0}}@keyframes fazer4{0%{left:0}100%{left:-150px;opacity:0}}@keyframes speeder{0%{transform:translate(2px,1px) rotate(0deg)}10%{transform:translate(-1px,-3px) rotate(-1deg)}20%{transform:translate(-2px,0px) rotate(1deg)}30%{transform:translate(1px,2px) rotate(0deg)}40%{transform:translate(1px,-1px) rotate(1deg)}50%{transform:translate(-1px,3px) rotate(-1deg)}60%{transform:translate(-1px,1px) rotate(0deg)}70%{transform:translate(3px,1px) rotate(-1deg)}80%{transform:translate(-2px,-1px) rotate(1deg)}90%{transform:translate(2px,1px) rotate(0deg)}100%{transform:translate(1px,-2px) rotate(-1deg)}}#cargadorAjax .longfazers{position:absolute;width:100%;height:100%}#cargadorAjax .longfazers span{position:absolute;height:2px;width:20%;background:#060062}#cargadorAjax .longfazers span:nth-child(1){top:20%;animation:lf .6s linear infinite;animation-delay:-5s}#cargadorAjax .longfazers span:nth-child(2){top:40%;animation:lf2 .8s linear infinite;animation-delay:-1s}#cargadorAjax .longfazers span:nth-child(3){top:60%;animation:lf3 .6s linear infinite}#cargadorAjax .longfazers span:nth-child(4){top:80%;animation:lf4 .5s linear infinite;animation-delay:-3s}@keyframes lf{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf2{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf3{0%{left:200%}100%{left:-100%;opacity:0}}@keyframes lf4{0%{left:200%}100%{left:-100%;opacity:0}}</style><div class="body"><span><span></span><span></span><span></span><span></span></span><div class="base"><span></span><div class="face"></div></div></div><div class="longfazers"><span></span><span></span><span></span><span></span></div><h1 style="color: #060062; font-weight: bold">PROCESANDO</h1></div>');
            });


            /****************
             en este evento se toma los valores de los campos dados a la modal
             y se valida que accion se ha indicado(Guardar o Actualizar), segun
             sea el caso toma la ruta y hace la petición ajax.
             *****************/





            $(document).on('click', '.btn-delete', function () {
                var ruta = $(this).data('delete-url');
                bootbox.confirm
                ({
                    message: "\u00BFSeguro que desea eliminar el registro?",
                    buttons:
                        {
                            confirm:
                                {
                                    label: 'Si',
                                    className: 'btn-danger'
                                },
                            cancel:
                                {
                                    label: 'No',
                                    className: 'btn-default'
                                }
                        },
                    callback: function (resultado) {
                        if (resultado) {
                            //var ModalC = modalCarga("Por Favor espere...");

                            $.ajax({
                                type: 'delete',
                                data: {
                                    '_token': $('input[name=_token]').val(),
                                },
                                url: ruta,
                                success: function (data) {
                                    resultadoEvento(data);
                                }
                            });
                        }
                    }
                });
            });
        });


    </script>
@endsection
