@extends('layout.default')
@section('encabezado')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"
            type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"
            type="text/javascript"></script>
    <link href="{{ asset('formulario_solicitud/css/jquery.steps.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('formulario_solicitud/css/main.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('formulario_solicitud/css/normalize.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('formulario_solicitud/build/jquery.steps.js') }}" type="text/javascript"></script>
    <style>
        @media (max-width: 600px) {
            .wizard > .steps > ul > li, .wizard > .actions > ul > li {
                float: left;
                width: 100%;
            }

            .wizard > .content {
                width: 100%;
                min-height: 1000px;
            }
        }

        .error {
            color: red;
        }

        .wizard > .content {
            overflow: auto;
        }

        .done {
            background-color: white;
        }

        .wizard > .steps .done a, .wizard > .steps .done a:hover, .wizard > .steps .done a:active {
            background: white;
            color: #337ab7;
        }

        .tabcontrol > .steps > ul > li.current {
            background: #32c5d2;
            border: 1px solid #bbb;
            border-bottom: 0 none;
            padding: 0 0 1px 0;
            margin-top: 0;
        }

        .tabcontrol > .steps > ul > li.current > a {
            color: white;
        }

        .tabcontrol > .content {
            height: 2000px;
        }
        .label-small {
            font-size: 12px;
        }
    </style>
@endsection
@section('content')
    <script type="text/javascript">
        var has_change = false;
        let user = {!!$json_user !!};
        $(document).on('click', '#add_beneficiario', function () {
            $('#add_beneficiario').hide();
            data = $(this).data('template');
            $('#beneficiario-template-' + data).show();
            $(this).data('template', data + 1)
        });
        $(function () {
            $('#imprimir-solicitud').click(function (e) {
                var html_data = ``;
                for ( var key in user ) {
                    html_data+=`<input name="${key}" value="${user[key]}">`
                }
                $('#formulario-impresion').html(html_data);
                $('#formulario-impresion').submit();

            });
            $(document).on('change','.select-salud', function () {
                if($(this).val()==="si"){
                    $('#'+$(this).data('explicacion')).show();
                }else{
                    $('#'+$(this).data('explicacion')).hide();
                };
            });
            $(document).on('change','#idTipoDeVivienda', function () {
                if($(this).val()==="arrendada"){
                    $('#arriendo_alquiler').show();
                }else{
                    $('#arriendo_alquiler').hide();
                };
            });
            $('.select-salud').trigger('change');
            $('#idTipoDeVivienda').trigger('change');
            $(document).on('change', '.participacion', function () {
                var value = 0;
                $('.participacion').each(function () {
                    var val = parseInt($(this).val().replace('_', ''));
                    if (!isNaN(val) && val) {
                        value += val;
                    }
                });
                if (value < 100) {
                    $('#add_beneficiario').show();
                } else if (value > 100) {
                    bootbox.alert("La suma de los beneficiarios debe ser igual a 100%")
                } else {
                    $('#add_beneficiario').hide();
                }
            });

            $.extend($.validator.messages, {
                required: "Este campo es obligatorio.",
                remote: "Por favor, rellena este campo.",
                email: "Por favor, escribe una dirección de correo válida.",
                url: "Por favor, escribe una URL válida.",
                date: "Por favor, escribe una fecha válida.",
                dateISO: "Por favor, escribe una fecha (ISO) válida.",
                number: "Por favor, escribe un número válido.",
                digits: "Por favor, escribe sólo dígitos.",
                creditcard: "Por favor, escribe un número de tarjeta válido.",
                equalTo: "Por favor, escribe el mismo valor de nuevo.",
                extension: "Por favor, escribe un valor con una extensión aceptada.",
                maxlength: $.validator.format("Por favor, no escribas más de {0} caracteres."),
                minlength: $.validator.format("Por favor, no escribas menos de {0} caracteres."),
                rangelength: $.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
                range: $.validator.format("Por favor, escribe un valor entre {0} y {1}."),
                max: $.validator.format("Por favor, escribe un valor menor o igual a {0}."),
                min: $.validator.format("Por favor, escribe un valor mayor o igual a {0}."),
                nifES: "Por favor, escribe un NIF válido.",
                nieES: "Por favor, escribe un NIE válido.",
                cifES: "Por favor, escribe un CIF válido."
            });
            $("#example-manipulation").steps({
                headerTag: "h3",
                bodyTag: "section",
                enablePagination: false,
                enableFinishButton: false,
                enableAllSteps: true,
                titleTemplate: "#title#",
                cssClass: "tabcontrol",
                onStepChanging: function (event, currentIndex, newIndex) {
                    if (has_change) {
                        has_change = false;
                        bootbox.confirm({
                            message: "Usted cambio información en esta pantalla, ¿Desea guardarla antes de continuar?",
                            buttons: {
                                confirm: {
                                    label: 'Si, guardar',
                                    className: 'btn-success'
                                },
                                cancel: {
                                    label: 'No',
                                    className: 'btn-danger'
                                }
                            },
                            callback: function (result) {
                                if (result) {
                                    save(currentIndex);
                                }
                            }
                        });
                        return true;
                    } else {
                        return true;
                    }
                },
            });
            $(document).on('change', 'input, select', function () {
                has_change = true;
            });
            $('.save-button').click(function () {
                index = $(this).data('index');
                if (!validate(index)) {
                    bootbox.alert("El formulario tiene errores,se guardara pero recuerde que no podrá imprimir el documento hasta que este llenado correctamente.",
                        function () {
                            save(index);
                        }
                    );
                } else {
                    save(index);
                }
                ;
                $('#example-manipulation').steps("next");
            });
            var validate = function (currentIndex) {
                var form = $('#form-step-' + currentIndex);
                var validator = form.validate({
                    invalidHandler: function () {
                        $('#title-' + currentIndex).html(validator.numberOfInvalids());
                    }
                });
                return form.valid();
            };
            var save = function (currentIndex) {

                var formElement = document.getElementById("form-step-" + currentIndex);
                form_data = new FormData(formElement);
                form_data.forEach(function (value, key) {
                    console.log(key)
                });
                var request = new XMLHttpRequest();
                request.open("POST", "{{ route('update-solicitud-data', ['id'=>$user->id]) }}");
                request.onreadystatechange = function () {
                    if (request.readyState == 4) {
                        user = JSON.parse(request.responseText);
                        $(".wrapper").css("opacity", "1");
                        $("#cargadorAjax").remove();
                        has_change = false;
                        update_form_data();
                    }
                };
                request.send(new FormData(formElement));
                $(".wrapper").css("opacity", "0.2");
                $("body").append('<div id="cargadorAjax" style="position: fixed;z-index: 10000000000000;left: 50%;margin-left: -100px;top: 50%;margin-top: -100px;width: 200px;height: 200px;"><style type="text/css">.base{color: #060062}#cargadorAjax h1{position:absolute;font-family:"sans-serif";font-weight:600;font-size:12px;text-transform:uppercase;left:50%;top:58%;margin-left:-20px}#cargadorAjax .body{position:absolute;top:50%;margin-left:-50px;left:50%;animation:speeder .4s linear infinite}#cargadorAjax .body > span{height:5px;width:35px;background:#060062;position:absolute;top:-19px;left:60px;border-radius:2px 10px 1px 0}#cargadorAjax .base span{position:absolute;width:0;height:0;border-top:6px solid transparent;border-right:100px solid #060062;border-bottom:6px solid transparent}#cargadorAjax .base span:after{content:"";height:22px;width:22px;border-radius:50%;background:#060062;position:absolute;right:-110px;top:-16px}#cargadorAjax .base span:before{content:"";position:absolute;width:0;height:0;border-top:0 solid transparent;border-right:55px solid #060062;border-bottom:16px solid transparent;top:-16px;right:-98px}#cargadorAjax .face{position:absolute;height:12px;width:20px;background:#060062;border-radius:20px 20px 0 0;transform:rotate(-40deg);right:-125px;top:-15px}#cargadorAjax .face:after{content:"";height:12px;width:12px;background:#060062;right:4px;top:7px;position:absolute;transform:rotate(40deg);transform-origin:50% 50%;border-radius:0 0 0 2px}#cargadorAjax .body > span > span:nth-child(1),.body > span > span:nth-child(2),.body > span > span:nth-child(3),.body > span > span:nth-child(4){width:30px;height:1px;background:#060062;position:absolute;animation:fazer1 .2s linear infinite}#cargadorAjax .body > span > span:nth-child(2){top:3px;animation:fazer2 .4s linear infinite}#cargadorAjax .body > span > span:nth-child(3){top:1px;animation:fazer3 .4s linear infinite;animation-delay:-1s}#cargadorAjax .body > span > span:nth-child(4){top:4px;animation:fazer4 1s linear infinite;animation-delay:-1s}@keyframes fazer1{0%{left:0}100%{left:-80px;opacity:0}}@keyframes fazer2{0%{left:0}100%{left:-100px;opacity:0}}@keyframes fazer3{0%{left:0}100%{left:-50px;opacity:0}}@keyframes fazer4{0%{left:0}100%{left:-150px;opacity:0}}@keyframes speeder{0%{transform:translate(2px,1px) rotate(0deg)}10%{transform:translate(-1px,-3px) rotate(-1deg)}20%{transform:translate(-2px,0px) rotate(1deg)}30%{transform:translate(1px,2px) rotate(0deg)}40%{transform:translate(1px,-1px) rotate(1deg)}50%{transform:translate(-1px,3px) rotate(-1deg)}60%{transform:translate(-1px,1px) rotate(0deg)}70%{transform:translate(3px,1px) rotate(-1deg)}80%{transform:translate(-2px,-1px) rotate(1deg)}90%{transform:translate(2px,1px) rotate(0deg)}100%{transform:translate(1px,-2px) rotate(-1deg)}}#cargadorAjax .longfazers{position:absolute;width:100%;height:100%}#cargadorAjax .longfazers span{position:absolute;height:2px;width:20%;background:#060062}#cargadorAjax .longfazers span:nth-child(1){top:20%;animation:lf .6s linear infinite;animation-delay:-5s}#cargadorAjax .longfazers span:nth-child(2){top:40%;animation:lf2 .8s linear infinite;animation-delay:-1s}#cargadorAjax .longfazers span:nth-child(3){top:60%;animation:lf3 .6s linear infinite}#cargadorAjax .longfazers span:nth-child(4){top:80%;animation:lf4 .5s linear infinite;animation-delay:-3s}@keyframes lf{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf2{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf3{0%{left:200%}100%{left:-100%;opacity:0}}@keyframes lf4{0%{left:200%}100%{left:-100%;opacity:0}}</style><div class="body"><span><span></span><span></span><span></span><span></span></span><div class="base"><span></span><div class="face"></div></div></div><div class="longfazers"><span></span><span></span><span></span><span></span></div><h1 style="color: #060062; font-weight: bold">PROCESANDO</h1></div>');

            };
            $('#idEstadoCivil').on('change', function () {
                if ($(this).val() === "soltero") {
                    $('#fieldsetConyuge').hide();
                } else {
                    $('#fieldsetConyuge').show();

                }
                ;
            })
            $('#idEstadoCivil').trigger('change');
            $("input").inputmask();
            Object.keys(user).map(function (key, index) {
                if (user[key] !== null) {
                    var selector = $("input[name='" + key + "']");
                    if (selector.length > 0) {
                        selector.val(user[key]);
                    } else {
                        selector = $("textarea[name='" + key + "']");
                        if (selector.length > 0) {
                            selector.val(user[key]);
                        }
                    }
                }
            });
            update_form_data();

        });


        update_form_data = function () {
            $('.json-data').val(JSON.stringify(user));
        };


    </script>
    <h1>{{$user->nombre }} {{$user->primerApellido}}</h1>
    <form style="display: none" id="formulario-impresion" method="post" action="http://smc.upgradecrediticio.com/aliados/print-document/7/" content='text/html; charset=UTF-8'>

    </form>
    <button id="imprimir-solicitud" class="btn blue">Imprimir solicitud de crédito</button>
    <br>
    <div id="example-manipulation">
        <h3>Datos basicos<span class="counter" style="color: red !important;" id="title-0"></span></h3>
        <section>
            <form method="post" id="form-step-0">
                {{ csrf_field() }}
                <div class="row">
                    <fieldset>
                        <legend>Datos basicos</legend>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Ciudad:
                                </label>
                                <div class="col-md-8">
                                    <select id="idCiudad" name="ciudad" class="form-control select2 circle">
                                        <option @if($user->ciudad==null) selected @endif disabled value>Seleccione una
                                            opción
                                        </option>

                                        @foreach($ciudades as $ciudad)
                                            <option @if($ciudad->municipio == $user->ciudad) selected @endif>{{$ciudad->municipio}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Tipo de vivienda:
                                </label>
                                <div class="col-md-8">
                                    <select id="idTipoDeVivienda" required name="tipo_de_vivienda"
                                            class="form-control select2 circle">
                                        <option @if($user->tipo_de_vivienda==null) selected @endif disabled value>
                                            Seleccione una opción
                                        </option>

                                        @foreach($tipos_de_vivienda as $tipo)
                                            <option @if($tipo==$user->tipo_de_vivienda) selected @endif>{{$tipo}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Dirección:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" maxlength="30" required id="idDireccion" name="direccion"
                                           style="text-transform: lowercase;"
                                           placeholder="Dirección" class="form-control address input-circle">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Estrato:
                                </label>
                                <div class="col-md-8">
                                    <select id="idEstrato" name="estrato" class="form-control select2 circle">
                                        <option @if($user->estrato==null) selected @endif disabled value>Seleccione una
                                            opción
                                        </option>

                                        @foreach($estratos as $estrato)
                                            <option @if($estrato==$user->estrato) selected @endif>{{$estrato}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Teléfono fijo:
                                </label>
                                <div class="col-md-8">
                                    <input required type="text" id="idTelefonoFijo" name="telefono_fijo"
                                           style="text-transform: lowercase;"
                                           data-inputmask="'numericInput': true, 'mask': '999-9999', 'rightAlignNumerics':false"
                                           placeholder="teléfono fijo."
                                           class="form-control masked input-circle">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Nivel de estudios:
                                </label>
                                <div class="col-md-8">
                                    <select id="idNivelDeEstudios" name="nivel_de_estudios"
                                            class="form-control select2 circle">
                                        <option @if($user->nivel_de_estudios ==null) selected @endif disabled value>
                                            Seleccione una opción
                                        </option>

                                        @foreach($niveles_de_estudio as $nivel)
                                            <option @if($nivel==$user->nivel_de_estudios) selected @endif>{{$nivel}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Estado civil:
                                </label>
                                <div class="col-md-8">
                                    <select id="idEstadoCivil" name="estado_civil" class="form-control select2 circle">
                                        <option @if($user->estado_civil==null) selected @endif disabled value>Seleccione
                                            una opción
                                        </option>

                                        @foreach($estados_civiles as $estado)
                                            <option @if($estado==$user->estado_civil) selected @endif >{{$estado}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Personas a cargo:
                                </label>
                                <div class="col-md-8">
                                    <input required type="number" min="0" max="6" id="idPersonasACargo"
                                           name="personas_a_cargo"
                                           style="text-transform: lowercase;"
                                           placeholder="personas a cargo" class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Dirección de correspondencia:
                            </label>
                            <div class="col-md-8">
                                <select id="slModulo" name="direccion_correspondencia"
                                        class="form-control select2 circle">
                                    <option @if($user->direccion_correspondencia==null) selected @endif disabled value>
                                        Seleccione una opción
                                    </option>
                                    @foreach($direcciones_de_correspondencia as $direccion)
                                        <option @if($direccion == $user->direccion_correspondencia) selected @endif>{{$direccion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <br>
                    <fieldset id="fieldsetConyuge">
                        <legend>Información del cónyuge</legend>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Número de identificación:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idNumeroIdentificacionConyuge"
                                           name="numero_identificacion_conyuge"
                                           style="text-transform: lowercase;"
                                           placeholder="número" class="form-control input-circle">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Nombre y Apellidos:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idNombreConyuge" name="nombre_conyuge"
                                           style="text-transform: lowercase;"
                                           placeholder="nombre" class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Teléfono celular:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idCelularConyuge" name="celular_conyuge"
                                           style="text-transform: lowercase;"
                                           placeholder="celular" class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="modal-footer">
                            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Limpiar</button>
                            <button type="button" data-index="0" name="btValidar" class="btn green save-button">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <h3>Referencias personales <span class="counter" style="color: red !important;" id="title-1"></span></h3>
        <section>
            <form id="form-step-1">
                {{ csrf_field() }}

                <fieldset>
                    <legend>Referencia personal 1</legend>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Nombre y Apellidos:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp1Nombre" name="rp1_nombre"
                                       style="text-transform: lowercase;"
                                       placeholder="nombre" class="form-control input-circle">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Ciudad:
                            </label>
                            <div class="col-md-8">
                                <select id="idCiudad" name="rp1_ciudad" class="form-control select2 circle">
                                    <option @if($user->ciudad==null) selected @endif disabled value>Seleccione una
                                        opción
                                    </option>

                                    @foreach($ciudades as $ciudad)
                                        <option @if($ciudad->municipio == $user->ciudad) selected @endif>{{$ciudad->municipio}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Dirección:
                            </label>
                            <div class="col-md-8">
                                <input type="text" maxlength="30" required id="Rp1Direccion" name="rp1_direccion"
                                       style="text-transform: lowercase;"
                                       placeholder="dirección" class="form-control input-circle address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Teléfono fijo:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp1TelefonoFijo" name="rp1_telefono_fijo"
                                       style="text-transform: lowercase;"
                                       placeholder="teléfono fijo."
                                       data-inputmask="'numericInput': true, 'mask': '999-9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Celular:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp1Celular" name="rp1_celular"
                                       style="text-transform: lowercase;"
                                       placeholder="celular"
                                       data-inputmask="'numericInput': true, 'mask': '999-999 9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br>
                <fieldset>
                    <legend>Referencia personal 2</legend>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Nombre y Apellidos:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp2Nombre" name="rp2_nombre"
                                       style="text-transform: lowercase;"
                                       placeholder="nombre" class="form-control input-circle">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Ciudad:
                            </label>
                            <div class="col-md-8">
                                <select id="idCiudad" name="rp2_ciudad" class="form-control select2 circle">
                                    <option @if($user->ciudad==null) selected @endif disabled value>Seleccione una
                                        opción
                                    </option>

                                    @foreach($ciudades as $ciudad)
                                        <option @if($ciudad->municipio == $user->ciudad) selected @endif>{{$ciudad->municipio}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Dirección:
                            </label>
                            <div class="col-md-8">
                                <input type="text" maxlength="30" required id="Rp2Direccion" name="rp2_direccion"
                                       style="text-transform: lowercase;"
                                       placeholder="dirección" class="form-control input-circle address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Teléfono fijo:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp2TelefonoFijo" name="rp2_telefono_fijo"
                                       style="text-transform: lowercase;"
                                       placeholder="teléfono fijo."
                                       data-inputmask="'numericInput': true, 'mask': '999-9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Celular:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rp2Celular" name="rp2_celular"
                                       style="text-transform: lowercase;"
                                       placeholder="celular"
                                       data-inputmask="'numericInput': true, 'mask':  '999-999 9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Limpiar</button>
                        <button type="button" data-index="1" id="btValidar" name="btValidar"
                                class="btn green save-button">Guardar
                        </button>
                    </div>
                </div>

            </form>

        </section>
        <h3>Referencias familiares <span class="counter" style="color: red !important;" id="title-2"></span></h3>
        <section>
            <form id="form-step-2">
                {{ csrf_field() }}

                <fieldset>
                    <legend>Referencia personal 2</legend>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Nombre y Apellidos:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf1Nombre" name="rfa1_nombre"
                                       style="text-transform: lowercase;"
                                       placeholder="nombre" class="form-control input-circle">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Ciudad:
                            </label>
                            <div class="col-md-8">
                                <select id="idCiudad" name="rfa1_ciudad"  class="form-control select2 circle">
                                    <option @if($user->ciudad==null) selected @endif disabled value>Seleccione una
                                        opción
                                    </option>

                                    @foreach($ciudades as $ciudad)
                                        <option @if($ciudad->municipio == $user->ciudad) selected @endif>{{$ciudad->municipio}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Dirección:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf1Direccion" maxlength="30" name="rfa1_direccion"
                                       style="text-transform: lowercase;"
                                       placeholder="dirección" class="form-control input-circle address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Teléfono fijo:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf1TelefonoFijo" name="rfa1_telefono_fijo"
                                       style="text-transform: lowercase;"
                                       placeholder="teléfono fijo."
                                       data-inputmask="'numericInput': true, 'mask': '999-9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Celular:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf1Celular" name="rfa1_celular"
                                       style="text-transform: lowercase;"
                                       placeholder="celular"
                                       data-inputmask="'numericInput': true, 'mask': '999-999 9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Parentesco:
                            </label>
                            <div class="col-md-8">
                                <select id="Rf1Parentesco" name="rfa1_parentesco" class="form-control select2 circle">
                                    <option @if($user->rfa1_parentesco==null) selected @endif disabled value>Seleccione
                                        una opción
                                    </option>
                                    @foreach($parentescos as $parentesco)
                                        <option>{{$parentesco}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br>
                <fieldset>
                    <legend>Referencia personal 2</legend>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Nombre:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf2Nombre" name="rfa2_nombre"
                                       style="text-transform: lowercase;"
                                       placeholder="nombre" class="form-control input-circle">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Ciudad:
                            </label>
                            <div class="col-md-8">
                                <select id="idCiudad" name="rfa2_ciudad" class="form-control select2 circle">
                                    <option @if($user->ciudad==null) selected @endif disabled value>Seleccione una
                                        opción
                                    </option>

                                    @foreach($ciudades as $ciudad)
                                        <option @if($ciudad->municipio == $user->ciudad) selected @endif>{{$ciudad->municipio}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Dirección:
                            </label>
                            <div class="col-md-8">
                                <input type="text" maxlength="30" required id="Rf2Direccion" name="rfa2_direccion"
                                       style="text-transform: lowercase;"
                                       placeholder="dirección" class="form-control input-circle address">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Teléfono fijo:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf2TelefonoFijo" name="rfa2_telefono_fijo"
                                       style="text-transform: lowercase;"
                                       placeholder="teléfono fijo."
                                       data-inputmask="'numericInput': true, 'mask': '999-9999', 'rightAlignNumerics':false"
                                       class="form-control input-circle masked">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Celular:
                            </label>
                            <div class="col-md-8">
                                <input type="text" required id="Rf2Celular" name="rfa2_celular"
                                       style="text-transform: lowercase;"
                                       placeholder="celular" class="form-control input-circle masked">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Parentesco:
                            </label>
                            <div class="col-md-8">
                                <select id="Rf2Parentesco" name="rfa2_parentesco" class="form-control select2 circle">
                                    <option @if($user->rfa2_parentesco==null) selected @endif disabled value>Seleccione
                                        una opción
                                    </option>
                                    @foreach($parentescos as $parentesco)
                                        <option>{{$parentesco}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Limpiar</button>
                        <button data-index="2" type="button" id="btValidar" name="btValidar"
                                class="btn green save-button">Guardar
                        </button>
                    </div>
                </div>
            </form>

        </section>
        <h3>Información economica <span class="counter" style="color: red !important;" id="title-3"></span></h3>
        <section>
            <form id="form-step-3">
                {{ csrf_field() }}

                <fieldset>
                    <legend>Activos</legend>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Total activos:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idTotalActivos" name="total_activos"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder="total activos"
                                   data-inputmask="'numericInput': true, 'mask': '$999,999,999', 'rightAlignNumerics':false"
                                   class="form-control input-circle">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Ingresos</legend>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="col-md-4 control-label">
                                Otros ingresos:
                            </label>
                            <div class="col-md-8">
                                <input type="text" id="idOtrosIngresos" name="otros_ingresos"
                                       style="text-transform: lowercase;"
                                       required
                                       placeholder="otros ingresos."
                                       data-inputmask="'numericInput': true, 'mask': '$999,999,999', 'rightAlignNumerics':false"
                                       class="form-control input-circle">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Datos bancarios</legend>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Número de cuenta:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idNumeroDeCuenta" name="numero_de_cuenta"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder=""
                                   class="form-control input-circle">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Tipo de cuenta:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idTipoDeCuenta" name="tipo_cuenta"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder=""
                                   class="form-control input-circle">
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Banco:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idBanco" name="banco"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder=""
                                   class="form-control input-circle">
                        </div>
                    </div>
                </fieldset>
                <br>
                <fieldset>
                    <legend>Egresos</legend>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Gastos familiares:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idGastosFamiliares" name="gastos_familiares"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder="gastos familiares"
                                   data-inputmask="'numericInput': true, 'mask': '$999,999,999', 'rightAlignNumerics':false"
                                   class="form-control input-circle">
                        </div>
                    </div>
                    <div id="arriendo_alquiler" class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Arriendo o alquiler:
                        </label>
                        <div class="col-md-8">
                            <input type="text" id="idTotalPasivos" name="arriendo_alquiler"
                                   style="text-transform: lowercase;"
                                   required
                                   placeholder="arriendo o alquiler"
                                   data-inputmask="'numericInput': true, 'mask': '$999,999,999', 'rightAlignNumerics':false"
                                   class="form-control input-circle">
                        </div>
                    </div>
                </fieldset>
                <br>
                @if(isset($user->solicitud))
                    @if($user->solicitud->pagaduria->tipo === "pensionado")
                        <fieldset>
                            <legend>Datos de la pensión</legend>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Fecha de ingreso:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idFechaDeIngreso" required name="fecha_ingreso"
                                           style="text-transform: lowercase;"
                                           placeholder="fecha de ingreso." class="form-control date input-circle">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Tipo de pensión:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idTipoPension" required name="tipo_pension"
                                           style="text-transform: lowercase;"
                                           placeholder="tipo de pension" class="form-control input-circle">
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset>
                            <legend>Datos titular de la pensión</legend>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Nombre del titular:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idNombreDelTitular" required name="nombre_titular"
                                           style="text-transform: lowercase;"
                                           placeholder="nombre del titular." class="form-control input-circle">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-md-4 control-label">
                                    Cedula del titular:
                                </label>
                                <div class="col-md-8">
                                    <input type="text" id="idCedulaDelTitular" required name="cedula_titular"
                                           style="text-transform: lowercase;"
                                           placeholder="cedula del titular." class="form-control masked input-circle">
                                </div>
                            </div>
                        </fieldset>
                    @endif
                @endif

                <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Limpiar</button>
                        <button type="button" data-index="3" id="btValidar" name="btValidar"
                                class="btn green save-button">Guardar
                        </button>
                    </div>
                </div>
            </form>
        </section>
        <h3>Datos del seguro de vida <span class="counter" style="color: red !important;" id="title-4"></span></h3>
        <section>
            <form id="form-step-4">
                {{ csrf_field() }}

                <fieldset>
                    <legend>Información corporal</legend>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Estatura(MTS):
                        </label>
                        <div class="col-md-8">
                            <input type="text" required id="idEstatura" name="estatura"
                                   style="text-transform: lowercase;"
                                   placeholder="Defina Código de Estado."
                                   data-inputmask="'numericInput': true, 'mask': '9.99', 'rightAlignNumerics':false"
                                   class="form-control input-circle">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-md-4 control-label">
                            Peso(kg):
                        </label>
                        <div class="col-md-8">
                            <input type="text" required id="idPeso" name="peso"
                                   style="text-transform: lowercase;"
                                   placeholder="Defina Código de Estado."
                                   data-inputmask="'numericInput': true, 'mask': '999', 'rightAlignNumerics':false"
                                   class="form-control input-circle">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Cuestionario estado de salud</legend>
                    <p>Conteste si o no para cada una de las siguientes preguntas. Sí alguna de las respuestas es
                        afirmativa por favor indique diagnostico, fecha, tratamiento, estado actual y nombre del médico
                        o insitución tratante.</p>
                    <div class="row">
                        <label class="col-md-12 control-label label-small">
                            En la fecha me encuentro con buen estado de salud:
                        </label>
                        <div class="col-md-12">
                            <select data-explicacion="estado_salud" name="estado_salud" class="form-control select2 circle select-salud">
                                <option @if(!$user->estado_salud) selected @endif disabled value>Seleccione una opción
                                </option>
                                @foreach($estados_seguro as $estado)
                                    <option @if($user->estado_salud == $estado) selected @endif value="{{$estado}}">{{$estado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="estado_salud" class="col-md-12">
                            <div class="control-group">
                                <label class="control-label" for="inputPatient">Explicación:</label>
                                <div class="field desc">

                            <textarea maxlength="40" class="form-control" name="estado_salud_explicacion">

                            </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-12 control-label label-small">
                            ¿Padece o ha padecido, le han diagnosticado o ha sido tratado o está siendo estudiado por:
                            cualquier tipo de cáncer, tumores malignos, leucemias, linfomas, trastornos cardiacos,
                            arritmias cardiacas, infartos o dolor torácico cardiacos, trastornos en las válvulas
                            cardiacas, trastornos coronarios, aneurismas, malformaciones arteriovenosas cerebrales,
                            hipertensión arterial de difícil tratamiento, eventos o derrames cerebrales o isquemias
                            cerebrales, trastornos neurológicos crónicos, hepatitis B o C, diabetes, cirrosis, anemia faliciforme
                            enfermedad poliquistica renal, insuficiencia renal o transtornos renales
                            crónicos, infección por VIH, trastornos de la coagulación, trastornos osteoarticulares,
                            trastornos vasculares, malformaciones, enfermedades autoinmunes, enfermedades
                            crónicas, alcoholismo, drogadicción, trastornos pulmonares o hepáticos crónicos,
                            inmunodeficiencias, transplantes previos? ¿Fuma diariamente más de un paquete
                            veinte (20) cigarrillos? ¿Si es mujer, dos (2) miembros del núcleo familiar, –madre,
                            hermanas-, han padecido cáncer de seno?¿Sufre usted y/o su familia el trastorno
                            hereditario dislipidemia familiar o de poliposis vello o adenomatosa familiar.

                        </label>
                        <div class="col-md-12">
                            <select data-explicacion="fuma" name="fuma" class="form-control select2 circle select-salud">
                                <option @if(!$user->fuma) selected @endif disabled value>Seleccione una
                                    opción
                                </option>
                                @foreach($estados_seguro as $estado)
                                    <option @if($user->fuma == $estado) selected @endif value="{{$estado}}">{{$estado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="fuma" class="col-md-12">
                            <div class="control-group">
                                <label class="control-label" for="inputPatient">Explicación:</label>
                                <div class="field desc">

                            <textarea maxlength="500" class="form-control" name="fuma_explicacion">

                            </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-12 control-label label-small">
                            ¿Ha padecido o tiene en la actualidad deficiencias funcionales o pérdidas de organos o
                            miembros, trastornos en ojos u oídos, o ha sido declarado en estado de invalidez o en
                            incapacidad permanente parcial? ¿Esta siendo estudiado o desea ser estudiado por una
                            Junta o comisión médica de estudio de invalidez?
                        </label>
                        <div class="col-md-12">
                            <select data-explicacion="deficiencias" name="deficiencias" class="form-control select2 circle select-salud">
                                <option @if(!$user->deficiencias) selected @endif disabled value>Seleccione una
                                    opción
                                </option>
                                @foreach($estados_seguro as $estado)
                                    <option @if($user->deficiencias == $estado) selected @endif value="{{$estado}}">{{$estado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="deficiencias" class="col-md-12">
                            <div class="control-group">
                                <label class="control-label" for="inputPatient">Explicación:</label>
                                <div class="field desc">

                            <textarea maxlength="120" class="form-control" name="deficiencias_explicacion">

                            </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-12 control-label label-small">
                            ¿Sufre o recientemente se ha detectado o le han visto alguna condición que requiera de
                            estudios, o por los que se encuentre en estudio médico o en espera de estudios y/o
                            tratamientos médicos?
                        </label>
                        <div class="col-md-12">
                            <select data-explicacion="estudios" name="estudios" class="form-control select2 circle select-salud">
                                <option @if(!$user->estudios) selected @endif disabled value>Seleccione una
                                    opción
                                </option>
                                @foreach($estados_seguro as $estado)
                                    <option @if($user->estudios == $estado) selected @endif value="{{$estado}}">{{$estado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="estudios" class="col-md-12">
                            <div class="control-group">
                                <label class="control-label" for="inputPatient">Explicación:</label>
                                <div class="field desc">

                            <textarea maxlength="100" class="form-control" name="estudios_explicacion">

                            </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-md-12 control-label label-small">
                            ¿Sufre de alguna enfermedad o trastorno no preguntado en el presente cuestionario?
                            ¿Practica deportes peligrosos?
                        </label>
                        <div class="col-md-12">
                            <select data-explicacion="deportes" id="idEstadoSalud" name="deportes" class="form-control select2 circle select-salud">
                                <option @if(!$user->deportes) selected @endif disabled value>Seleccione una
                                    opción
                                </option>
                                @foreach($estados_seguro as $estado)
                                    <option @if($user->deportes == $estado) selected @endif value="{{$estado}}">{{$estado}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="deportes" class="col-md-12">
                            <div class="control-group">
                                <label class="control-label" for="inputPatient">Explicación:</label>
                                <div class="field desc">

                            <textarea maxlength="80" class="form-control" name="deportes_explicacion"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <div style="display: none">
                    <fieldset>
                        <legend>Beneficiarios</legend>
                        <fieldset>
                            <legend>Beneficiario 1</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Número de identificación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNumeroIdentificacionBn1"
                                               name="numero_identificacion_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="número de identificación"
                                               data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                               class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Nombres:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNombreBn1" name="nombre_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="nombres." class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Primer apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPrimerApellidoBn1" name="primer_apellido_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="primer apellido" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Segundo apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idSegundoApellidoBn1" name="segundo_apellido_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="Segundo apellido" class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Parentesco:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idParentescoBn1" name="parentesco_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="Parentesco" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Porcentaje de participación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPorcentajeParticipacionBn1"
                                               name="porcentaje_participacion_bn1"
                                               style="text-transform: lowercase;"
                                               placeholder="Porcentaje de participación"
                                               data-inputmask="'numericInput': true, 'mask': '999', 'rightAlignNumerics':false"
                                               class="form-control input-circle participacion">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset style="display: none;" id="beneficiario-template-1">
                            <legend>Beneficiario 2</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Número de identificación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNumeroIdentificacionBn2"
                                               name="numero_identificacion_bn2"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                               placeholder="número de identificación" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Nombres:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNombreBn2" name="nombre_bn2"
                                               style="text-transform: lowercase;"
                                               placeholder="nombres." class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Primer apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPrimerApellidoBn2" name="primer_apellido_bn2"
                                               style="text-transform: lowercase;"
                                               placeholder="primer apellido" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Segundo apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idSegundoApellidoBn2" name="segundo_apellido_bn2"
                                               style="text-transform: lowercase;"
                                               placeholder="Segundo apellido" class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Parentesco:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idParentescoBn2" name="parentesco_bn2"
                                               style="text-transform: lowercase;"
                                               placeholder="Parentesco" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Porcentaje de participación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPorcentajeParticipacionBn2"
                                               name="porcentaje_participacion_bn2"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999', 'rightAlignNumerics':false"
                                               placeholder="Porcentaje de participación"
                                               class="form-control input-circle participacion">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset style="display: none" id="beneficiario-template-2">
                            <legend>Beneficiario 3</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Número de identificación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNumeroIdentificacionBn3"
                                               name="numero_identificacion_bn3"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"

                                               placeholder="número de identificación" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Nombres:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNombreBn3" name="nombre_bn3"
                                               style="text-transform: lowercase;"
                                               placeholder="nombres." class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Primer apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPrimerApellidoBn3" name="primer_apellido_bn3"
                                               style="text-transform: lowercase;"
                                               placeholder="primer apellido" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Segundo apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idSegundoApellidoBn3" name="segundo_apellido_bn3"
                                               style="text-transform: lowercase;"
                                               placeholder="Segundo apellido" class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Parentesco:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idParentescoBn3" name="parentesco_bn3"
                                               style="text-transform: lowercase;"
                                               placeholder="Parentesco" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Porcentaje de participación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPorcentajeParticipacionBn3"
                                               name="porcentaje_participacion_bn3"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999', 'rightAlignNumerics':false"
                                               placeholder="Porcentaje de participación"
                                               class="form-control input-circle participacion">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset style="display: none" id="beneficiario-tempalte-3">
                            <legend>Beneficiario 4</legend>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Número de identificación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNumeroIdentificacionBn4"
                                               name="numero_identificacion_bn4"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999.999.999.999', 'rightAlignNumerics':false"
                                               placeholder="número de identificación" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Nombres:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idNombreBn4" name="nombre_bn4"
                                               style="text-transform: lowercase;"
                                               placeholder="nombres." class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Primer apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPrimerApellidoBn4" name="primer_apellido_bn4"
                                               style="text-transform: lowercase;"
                                               placeholder="primer apellido" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Segundo apellido:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idSegundoApellidoBn4" name="segundo_apellido_bn4"
                                               style="text-transform: lowercase;"
                                               placeholder="Segundo apellido" class="form-control input-circle">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Parentesco:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idParentescoBn4" name="parentesco_bn4"
                                               style="text-transform: lowercase;"
                                               placeholder="Parentesco" class="form-control input-circle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 control-label">
                                        Porcentaje de participación:
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="idPorcentajeParticipacionBn4"
                                               name="porcentaje_participacion_bn4"
                                               style="text-transform: lowercase;"
                                               data-inputmask="'numericInput': true, 'mask': '999', 'rightAlignNumerics':false"
                                               placeholder="Porcentaje de participación"
                                               class="form-control input-circle participacion">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                            <button style="display: none" type="button" id="add_beneficiario" data-template="1"
                                    class="btn green ">Agregar otro
                            </button>
                        </div>
                    </fieldset>
                </div>
                <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Limpiar</button>
                        <button type="button" data-index="4" id="btValidar" name="btValidar"
                                class="btn green save-button">Guardar
                        </button>
                    </div>
                </div>

            </form>
        </section>
    </div>

@endsection