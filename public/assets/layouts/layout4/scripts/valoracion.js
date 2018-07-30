function abrirModal() {
    $(document).on("click", ".abrirModal", function () {
        var selector = $(this).attr("data-nameModal");
        $("#" + selector).modal('show');
    })
}

function tabla() {
    $('.tablaDatatable').DataTable({
        responsive: true, //Indica que al cambiar el tama単o del navegador los registros se deben adaptar.
        "order": [], //Deshabilita el orden que da el DataTable
        "columnDefs": [{className: "dt-head-left", "targets": [0]}]
    });
}

function iniciarPopover() {
    $('.popoverAbajo').popover({
        animation: true,
        placement: "bottom",
        trigger: "focus"
    })
}

function visitaGuiada() {
    $(".demo_stopOn").click(function () {
        alert('Clicking on the backdrop or Esc will NOT stop the show')
        bootstro.start('.bootstro', {stopOnBackdropClick: false, stopOnEsc: false});
    });
    $(".demo_size1").click(function () {
        bootstro.start('.bootstro_size1');
    });
    $(".demo_nonav").click(function () {
        bootstro.start('.bootstro', {
            nextButton: 'Siguiente',
            prevButton: 'Anterior',
            finishButton: 'Finalizar'
        });
    });
    $(".visitaGuiada").click(function () {
        bootstro.start();
    })
}

var eventoKey = false;

function EstudioPress() {
    $(document).on("focusout", ".inputEstudio", function () {
        calcular();
    })
}

function calcularPrestamo(json) {
    var compras = parseInt(limiparPuntos($("#EstudioCompras").val()));
    const {cupo, ingreso, egreso} = json;
    calculoInfo(cupo + compras);
    $("#vlrCupo").val(parseInt(cupo + compras));
    $("#vlrCuota").val(parseInt(cupo + compras));
    setMiles("#vlrCupo");
    setMiles("#vlrCuota");
    if (eventoKey == false) {
        eventChangeCuota();
        eventoKey = true;
    }

}

function calcular() {
    if($('#EstudioIngreso').val().length > 5){
        var ingreso = parseInt(limiparPuntos($("#EstudioIngreso").val()));
        var egreso = parseInt(limiparPuntos($("#EstudioEgreso").val()));
        if ($('#regimenEspecial').is(":checked")) {
            var regEspecial = "on"
        } else {
            var regEspecial = "off"
        }
        var pagaduria = $('#pagaduriaId').val();
        calcularCupoDesprendible(ingreso, 0, pagaduria, regEspecial, calcularPrestamo);
    };
}


function eventChangeCuota() {
    $("#vlrCuota").keyup(function () {
        $("#vlrCuotaText").hide();
        if (parseInt(limiparPuntos($(this).val())) <= parseInt(limiparPuntos($("#vlrCupo").val()))) {
            calculoInfo(parseInt(limiparPuntos($(this).val())));
        } else {
            $("#vlrCuotaText #cifra").html($("#vlrCupo").val());
            $("#vlrCuotaText").show();
        }
    })
}

function enviarFormulario() {
    $("#send-form").click(function () {

        var ingreso = $("#EstudioIngreso").val();
        var egreso = $("#EstudioEgreso").val();
        var fechaNacimiento = $("#FechaNacimiento").val();
        var puerta = true;
        var localizacion = $(this).data("localizacion");
        var idestudio = $(this).data("idestudio");

        if (ingreso == "" || ingreso == null || ingreso <= 0) {
            puerta = false;
            bootbox.alert("El campo Ingreso es obligatorio");
            $("#EstudioIngreso").focus();
            return;
        } else {
            ingreso = parseInt(limiparPuntos($("#EstudioIngreso").val()));
        }

        if (egreso == "" || egreso == null || egreso <= 0) {
            puerta = false;
            bootbox.alert("El campo Egreso es obligatorio");
            $("#EstudioEgreso").focus();
            return;
        } else {
            egreso = parseInt(limiparPuntos($("#EstudioEgreso").val()));
        }


        if (fechaNacimiento == "" || fechaNacimiento == null || fechaNacimiento <= 0) {
            puerta = false;
            bootbox.alert("La Fecha de Nacimiento es obligatoria");
            $("#FechaNacimiento").focus();
            return;
        }

        var arrayNacimiento = fechaNacimiento.split("-");
        var fechaActual = new Date();

        var edad = fechaActual.getFullYear() - parseInt(arrayNacimiento[0]);
        if ((fechaActual.getMonth() + 1) < parseInt(arrayNacimiento[1])) {
            edad--;
        } else if ((fechaActual.getMonth() + 1) == parseInt(arrayNacimiento[1]) && fechaActual.getUTCDate() < parseInt(arrayNacimiento[2])) {
            edad--;
        }
        if (edad <= 0) {
            displayMessageMini("La  Fecha de Nacimiento es invalida");
            $("#FechaNacimiento").focus();
            return;
        }


        if (puerta) {
            $("#modalEstudio").fadeOut("slow");
            var formData = new FormData($("#form-estudio")[0]);
            formData.append('comercialAsignado', $("#comercialAsignado").val());
            formData.append('localizacion', localizacion);
            formData.append('idestudio', idestudio);
            var ruta = $(this).data("url");
            var rutaEstudio = $(this).data("urlestudio") + "Estudio/" + $("#idValoracion").val();

            $.ajax({
                url: ruta,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    var respuesta = JSON.parse(data);

                    bootbox.alert({
                        message: respuesta.MENSAJE,
                        size: 'small',
                        callback: function () {
                            if (localizacion == "Estudio") {
                                var disponible = parseInt(limiparPuntos($("#vlrCupo").val())) - parseInt(limiparPuntos($("#EstudioCompras").val()));
                                disponible = (disponible >= 0) ? disponible : 0;
                                $("#DisponibleOriginal").html(format_miles(disponible));
                                calculoCapacidad();
                                $('#modalEstudioCalculadora').modal('hide');
                            } else {
                                if (respuesta.URI !== "") {
                                    displayMessageMini("Espere un momento mientras es enviado al siguiente paso.");
                                    location.href = respuesta.URI;
                                } else {
                                    $('#modalEstudio').modal('hide');
                                }
                            }
                            $(".ModalConfirmBootbox").children().addClass("modal-sm");
                        }
                    });
                },
                error: function (data) {
                    bootbox.alert({
                        message: "error Ocurrio un problema inesperado, por favor recargue la pagina e intentelo de nuevo, si el problema persiste comuniquese con soporte [Error ajax debido a error php]",
                        size: 'small'
                    });

                }
            });
        }
    })

}

function calculoInfo(cupo) {

    datos = JSON.parse($("#parameters").val());
    porcentajeTotalDescuentos = parseFloat(datos.descuento1) + parseFloat(datos.descuento2) + parseFloat(datos.descuento3) + parseFloat(datos.descuento4);

    valorCreditoReal = cupo * (((Math.pow(1 + parseFloat(datos.tasaCredito), parseInt($('#Plazo').val()))) - 1) / (parseFloat(datos.tasaCredito) * (Math.pow(1 + parseFloat(datos.tasaCredito), parseInt($('#Plazo').val())))));
    valorDesembolsoReal = valorCreditoReal / 1.2;


    $("#vlrCredito").val(Math.round(valorCreditoReal));
    var valorDesembolso = (Math.round(valorDesembolsoReal) <= 0) ? 0 : Math.round(valorDesembolsoReal);
    $("#vlrDesembolso").val(valorDesembolso);
    setMiles("#vlrCredito");
    setMiles("#vlrDesembolso");

}

function setMiles(selector) {
    var cleave = new Cleave(selector, {
        numeral: true,
        delimiter: ".",
        numeralDecimalScale: 0,
        numeralDecimalMark: ",",
        numeralThousandsGroupStyle: 'thousand'
    });
}

function initMiles() {
    $(document).on("focus", ".miles", function () {
        setMiles($(this));
    })
}

function limiparPuntos(numero) {
    return numero.replace(/\./g, "");
}

function blockEditInput() {
    $(document).on("focus", ".readonly", function () {
        $(this).attr("readonly", true);
    })
}

function agregarDatePicker() {
    if (navigator.userAgent.toLowerCase().indexOf('chrome') != 87) {
        $('[type="date"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
    }
}

function dspInfoObligacion() {
    $(document).on("click", ".dspInfoObligacion", function () {
        var idObligacion = $(this).data("obligacion");
        var padre = $(this).data("padre");
        $("#" + padre).modal("hide");
        $("#infoObligacion" + idObligacion).data("padre", padre);
        $("#infoObligacion" + idObligacion).modal("show");
    })

    $(document).on("hidden.bs.modal", ".informacionDeLasObligaciones", function () {
        var padre = $(this).data("padre");
        $("#" + padre).modal("show");
    })
}

$(document).ready(function () {
    iniciarPopover();
    abrirModal();
    tabla();
    visitaGuiada();
    initMiles();
    EstudioPress();
    blockEditInput();
    enviarFormulario();
    agregarDatePicker();
    //calcular();
    dspInfoObligacion();


    var sliderFormat = document.getElementById('slider-format');

    noUiSlider.create(sliderFormat, {
        start: [96],
        connect: [true, false],
        step: 12,
        range: {
            'min': [12],
            'max': [108]
        },
    });

    var inputFormat = document.getElementById('Plazo');

    sliderFormat.noUiSlider.on('update', function (values, handle) {
        inputFormat.value = values[handle];
        calculoInfo(parseInt(limiparPuntos($('#vlrCuota').val())));
    });

    inputFormat.addEventListener('change', function () {
        sliderFormat.noUiSlider.set(this.value);
        calculoInfo(parseInt(limiparPuntos($('#vlrCuota').val())));

    });
}); //ready function   
           
    
    