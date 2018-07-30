
function modalAdjuntos() {
    $(document).on("change", ".sltAccionObligacion", function () {

        var padre = $(this).data("padre");
        var input = $("#modalAdjunto" + padre + " .ComponentArchivo");
        input.filestyle('clear');
        input.filestyle('buttonText', 'Cargar Archivo');
        input.filestyle('disabled', true);

        $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudCDD").hide();
        $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudPYS").hide();
        $("#modalAdjunto" + padre + " .containerRadicada .containerComponenteCertificacion").hide();
        $("#modalAdjunto" + padre + " .containerRadicada .containerComponentePazySalvo").hide();

        if ($(this).val() == 'PSOL' || $(this).val() == 'CSOL') {
            setCalendario("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud" + padre);
            setCalendario("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega" + padre);

            if ($(this).val() == 'PSOL') {
                $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudPYS").show("slow", function () {
                    $("#modalAdjunto" + padre + " .containerSolicitado").show("slow");
                });
            }

            if ($(this).val() == 'CSOL') {
                $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudCDD").show("slow", function () {
                    $("#modalAdjunto" + padre + " .containerSolicitado").show("slow");
                });
            }

        } else if ($("#modalAdjunto" + padre + " .containerSolicitado").is(":visible")) {
            $("#modalAdjunto" + padre + " .containerSolicitado").hide();
        }

        if ($(this).val() == 'CRAD' || $(this).val() == 'PRAD') {
            $("#modalAdjunto" + padre + " .btnGuardar").hide();

            setCalendario("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion" + padre);
            setCalendario("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento" + padre);

            if ($(this).val() == 'CRAD') {
                $("#modalAdjunto" + padre + " .containerRadicada .containerValorCertificado").show("slow");
                $("#modalAdjunto" + padre + " .containerRadicada .containerFechaVencimiento").show("slow");
                $("#modalAdjunto" + padre + " .containerRadicada .containerComponenteCertificacion").show();
            }
            if ($(this).val() == 'PRAD') {
                $("#modalAdjunto" + padre + " .containerRadicada .containerValorCertificado").hide("slow");
                $("#modalAdjunto" + padre + " .containerRadicada .containerFechaVencimiento").hide("slow");
                $("#modalAdjunto" + padre + " .containerRadicada .containerComponentePazySalvo").show();
            }
            $("#modalAdjunto" + padre + " .containerRadicada").show("slow");
        } else if ($("#modalAdjunto" + padre + " .containerRadicada").is(":visible")) {
            $("#modalAdjunto" + padre + " .containerRadicada").hide();
        }

        $("#fechaSolicitud" + padre).val("");
        $("#fechaEntrega" + padre).val("");
        $("#fechaRadicacion" + padre).val("");
        $("#fechaVencimiento" + padre).val("");
    });
}

function saveFechasSolicitud(input, data, dataPHP) {

    var padre = data.idPadre;
    var fechaSolicitud = $("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud" + padre).val();
    var fechaEntrega = $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega" + padre).val();

    var estado = $("#modalAdjunto" + padre + " .sltAccionObligacion").val();
    var url = $("#dominioPrincipal").val();

    $.post(url + "Cartera/GuardarFechas",
            {
                idAdjunto: data.idAdjunto,
                idObligacion: padre,
                fechaSolicitud: fechaSolicitud,
                fechaEntrega: fechaEntrega,
                estado: estado,
                _token: $("input[name=_token]").val()
            }, function (data) {
        var resultado = JSON.parse(data);
        if (resultado.STATUS) {
            $("#modalAdjunto" + padre + " .containerSolicitado").hide();
            if (estado == "CSOL") {
                $("#modalAdjunto" + padre + " .optionCertificadosDeuda").html('<option value="CRAD">Radicada</option>');
            } else if (estado == "PSOL") {
                $("#modalAdjunto" + padre + " .containerPazYSalvo").html('<option value="PRAD">Radicada</option>');
            }

            $("#modalAdjunto" + padre + " #AdjuntosCargados" + padre).html(resultado.TABLA);
            $("#Enlace" + padre + " span").removeClass("fa-arrow-up");
            $("#Enlace" + padre + " span").addClass("fa-paperclip");
        } else {
            displayMessageMini(resultado.MENSAJE);
        }
    });
}

function updateInfoAdjuntos(input, data, dataPHP) {

    if ($("#Sumatoria" + data.idPadre).parent().hasClass("danger")) {
        $("#Sumatoria" + data.idPadre).parent().removeClass("danger");
    }
    var fechaRadicacion = $("#modalAdjunto" + data.idPadre + " .containerRadicada #fechaRadicacion" + data.idPadre).val();
    var fechaVencimiento = null;
    if ($("#modalAdjunto" + data.idPadre + " .containerRadicada .containerFechaVencimiento").is(":visible")) {
        fechaVencimiento = $("#modalAdjunto" + data.idPadre + " .containerRadicada #fechaVencimiento" + data.idPadre).val();
    }
    var valorCertificado = null;
    if ($("#modalAdjunto" + data.idPadre + " .containerRadicada .containerValorCertificado").is(":visible")) {
        valorCertificado = $("#modalAdjunto" + data.idPadre + " .containerRadicada #valorCertificado" + data.idPadre).val();
    }

    var estado = $("#modalAdjunto" + data.idPadre + " .sltAccionObligacion").val();
    var url = $("#dominioPrincipal").val();

    $.post(url + "Cartera/guardarFechasRadicacion",
            {
                idAdjunto: data.idAdjunto,
                fechaRadicacion: fechaRadicacion,
                fechaVencimiento: fechaVencimiento,
                valorCertificado: valorCertificado,
                estado: estado,
                idObligacion: data.idPadre,
                _token: $("input[name=_token]").val()
            }, function (res) {
        var resultado = JSON.parse(res);
        if (resultado.STATUS) {
            if (data.tipoAdjunto == "CDD" || data.tipoAdjunto == "ACD") {
                $("#Sumatoria" + data.idPadre).data("adjunto", 1);
            } else if (data.tipoAdjunto == "PYS") {
                if (resultado.tienePazSalvo == false) {
                    $(".compras" + data.idPadre).editable("setValue", "N");
                    $(".estadoCuenta" + data.idPadre).editable("setValue", "PYS");
                    $(".estadoCuenta" + data.idPadre).editable("option", "disabled", true);
                }
                $("#Sumatoria" + data.idPadre).data("pazsalvo", 1);
            }

            if (valorCertificado != null) {
                $("#Sumatoria" + data.idPadre).data("valorsaldo", valorCertificado);
                $(".saldoActual" + data.idPadre).editable("setValue", valorCertificado);
            }

            $("#modalAdjunto" + data.idPadre + " #AdjuntosCargados" + data.idPadre).html(resultado.TABLA);

            $("#modalAdjunto" + data.idPadre + " .containerRadicada").hide("slow");
            //input.filestyle('disabled', true);

            if (estado == "CRAD") {
                $("#modalAdjunto" + data.idPadre + " .optionCertificadosDeuda").hide();
                $("#modalAdjunto" + data.idPadre + " .optionCertificadosDeuda option").attr("disabled", true);
            } else if (estado == "PRAD") {
                $("#modalAdjunto" + data.idPadre + " .containerPazYSalvo").hide();
                $("#modalAdjunto" + data.idPadre + " .containerPazYSalvo option").attr("disabled", true);

            }

            if ($("#Enlace" + data.idPadre + " span").hasClass("fa-arrow-up")) {
                $("#Enlace" + data.idPadre + " span").removeClass("fa-arrow-up");
                $("#Enlace" + data.idPadre + " span").addClass("fa-paperclip");
            }
        } else {
            displayMessageMini(resultado.MENSAJE);
        }

    });

}

function guardarFechasSolicitado() {
    $(document).on("click", ".btnGuardar", function () {
        var padre = $(this).data("id");
        var fechaSolicitud = $("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud" + padre).val();
        var fechaEntrega = $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega" + padre).val();
        if (fechaSolicitud == "" || fechaEntrega == "") {
            displayMessageMini("Ingrese la fecha de solicitud y la fecha de entrega");
            return;
        }
        if (fechaSolicitud > fechaEntrega) {
            displayMessageMini("La fecha de solicitud no puede ser mayor a la fecha de entrega");
            return;
        }
        var estado = $("#modalAdjunto" + padre + " .sltAccionObligacion").val();
        var url = $("#dominioPrincipal").val();

        $.post(url + "Cartera/GuardarFechas",
                {
                    idObligacion: padre,
                    fechaSolicitud: fechaSolicitud,
                    fechaEntrega: fechaEntrega,
                    estado: estado,
                    _token: $("input[name=_token]").val()
                }, function (data) {
            var resultado = JSON.parse(data);
            if (resultado.STATUS) {
                $("#modalAdjunto" + padre + " .containerSolicitado").hide();
                if (estado == "CSOL") {
                    $("#modalAdjunto" + padre + " .optionCertificadosDeuda").html('<option value="CRAD">Radicada</option>');
                } else if (estado == "PSOL") {
                    $("#modalAdjunto" + padre + " .containerPazYSalvo").html('<option value="PRAD">Radicada</option>');
                }

                $("#modalAdjunto" + padre + " #AdjuntosCargados" + padre).html(resultado.TABLA);
                $("#modalAdjunto" + padre + " .btnGuardar").hide();
            }
            displayMessageMini(resultado.MENSAJE);
        });



    })
}
function setCalendario(elemento) {
    $(elemento).datepicker({changeMonth: true, changeYear: true, dateFormat: 'yy-mm-dd'});
}

function validateFechasSolicitud() {
    $(document).on("change", ".fechasAdjuntoSolicitud", function () {
        var padre = $(this).data("id");
        var input = $("#modalAdjunto" + padre + " .ComponentArchivo");

        if ($("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud" + padre).val() != "" && $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega" + padre).val() != "") {
            if ($("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud" + padre).val() <= $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega" + padre).val()) {
                input.filestyle('disabled', false);
            } else {
                input.filestyle('disabled', true);
                displayMessageMini("La fecha de Solicitud no puede ser mayor a la fecha de Entrega");
            }
        } else {
            input.filestyle('disabled', true);
        }
    })
}

function ValidateFechasContent() {
    $(document).on("change", ".fechasAdjunto", function () {
        var padre = $(this).data("id");
        var input = $("#modalAdjunto" + padre + " .ComponentArchivo");

        if ($("#modalAdjunto" + padre + " .containerRadicada .containerFechaVencimiento").is(":visible")) {
            if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion" + padre).val() != "" && $("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento" + padre).val() != "") {
                if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion" + padre).val() <= $("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento" + padre).val()) {
                    input.filestyle('disabled', false);
                } else {
                    input.filestyle('disabled', true);
                    displayMessageMini("La fecha de radicación no puede ser mayor a la fecha de vencimiento");
                }
            } else {
                input.filestyle('disabled', true);
            }
        } else {
            if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion" + padre).val() == "") {
                input.filestyle('disabled', true);
            } else {
                input.filestyle('disabled', false);
            }
        }
    })
}
function iniciarCalendario() {
    $(".desplegarFile").filestyle({
        buttonText: "Cargar Soporte",
        input: false,
        badge: true,
        badgeName: "badge-danger"
    });
}

function sendFormPagoMasivo() {
    $(document).on("click", "#formPagoMasivo #sendForm", function () {
        
        var archivo = $("#formPagoMasivo #archivo").val();

        if (archivo != null && archivo != "") {
            tablaPagoMasivo.rows().remove().draw();
            $(".tableUsuariosEncontrados").hide();
            $("#pasoFinal").hide();
            $("#contentPagoMasivo #dspAlertas").hide();  
            $("#Pagaduria").html("");
            
            var formData = new FormData($("#formPagoMasivo")[0]);

            var url = $("#formPagoMasivo").data("url");

            var boton = $(this);
            boton.html("<span class='fa fa-spinner fa-pulse'></span> PROCESANDO");

            $.ajax({
                url: url + "PagoMasivo/getData",
                type: "POST",
                data: formData,
                processData: false, // Important!
                contentType: false,
                cache: false,
                success: function (data) {
                    var response = JSON.parse(data);
                    
                    boton.html("PROCESAR <span class='fa fa-upload'></span>");
                    
                    if (response.STATUS.VALUE == false) {
                        $("#contentPagoMasivo #dspAlertas").html('<div class="alert alert-warning alert-dismissible">'
                                + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
                                + response.STATUS.MENSAJE
                                + '</div>');
                        $("#contentPagoMasivo #dspAlertas").slideDown("slow");
                        return false;
                    }

                    for (i = 0; i < response.USUARIOS.length; i++) {
                        tablaPagoMasivo.row.add(["", response.USUARIOS[i].cedula, response.USUARIOS[i].nombre, response.USUARIOS[i].valor]).draw();
                    }
                    
                    $("#totalUsuariosEncontrados").html(response.TOTAL);
                    $("#Pagaduria").html(response.PAGADURIA);
                    $(".tableUsuariosEncontrados").slideDown("slow");
                    
                },
                error: function (data) {
                    boton.html("PROCESAR <span class='fa fa-upload'></span>");
                }
            })
        }else{
            displayMessageMini("Seleccione un archivo para continuar con el proceso");
        }
    })
}

var tablaPagoMasivo;

function iniciarTablaExtraccionUsuarios(){
    tablaPagoMasivo = $('#tablaUsuarios').DataTable({
                            pageLength: 50,
                            columnDefs: [{
                                    orderable: false,
                                    className: 'select-checkbox',
                                    targets: 0
                                }],
                            select: {
                                style: 'multi'
                            }                            
                        });
                        
    $(document).on("click", "#SeleccionarTodo", function(){
        var seleccionados = $('#tablaUsuarios #containerUsuariosEncontrados tr.selected').length;
        if(seleccionados > 0){
            tablaPagoMasivo.rows().deselect();
        }else{
            tablaPagoMasivo.rows().select();
        }        
    })
    
    $(document).on("click", "#botonSiguientePaso", function(){        
        var arrayUsuarios = tablaPagoMasivo.rows( { selected: true } ).data().toArray();        
        if(arrayUsuarios.length > 0){
            $(".tableUsuariosEncontrados").hide();
            
            var total = 0;
            var items = "";
            for (i = 0; i < arrayUsuarios.length; i++) {
                total += getNumber(eliminarCaracteres(arrayUsuarios[i][3]));
                items+= '<tr>'
                            +'<td>'+arrayUsuarios[i][1]+'</td>'
                            +'<td>'+arrayUsuarios[i][2]+'</td>'
                            +'<td>'+arrayUsuarios[i][3]+'</td>'
                        +'</tr>';                
            }
            var object = {
                arrayUsuarios
            };
         
            $("#containerUsuariosFiltrados").html(items);
            $("#dataUsuariosFiltrados").val(JSON.stringify(object));
            $("#totalUsuariosFiltrados").html("$"+format_miles(total));
            $(".tableUsuariosFiltrados").slideDown("slow");
        }else{
            displayMessageMini("Para continuar con el proceso, debe seleccionar uno o varios clientes del listado");
        }        
    })
    
    $(document).on("click", "#botonVolver", function(){
        $(".tableUsuariosFiltrados").hide();
        $(".tableUsuariosEncontrados").slideDown("slow");
    })
    
    $(document).on("click", "#botonPagar", function(){
        $("#botonPagar").attr("disabled", true);
        var data = $("#dataUsuariosFiltrados").val();
                
        var urlPrincipal = $("#urlPrincipal").val();
        var formData = new FormData($("#formPagoMasivo")[0]);
        formData.append("data", data);

        $.ajax({
                url: urlPrincipal+"PagoMasivo/pagar",
                type: "POST",
                data: formData,
                processData: false, // Important!
                contentType: false,
                cache: false,
                success: function (data) {
                   $("#botonPagar").attr("disabled", false);
                   var response = JSON.parse(data);
                   if(response.STATUS){
                       $(".tableUsuariosFiltrados").hide();
                       $(".tableUsuariosEncontrados").hide();
                       displayMessageMini("El pago masivo se ejecuto de manera Correcta");
                       $("#pasoFinal").html(response.CONTENIDO);
                       $("#pasoFinal").slideDown("slow");
                       
                   }else{
                       displayMessageMini(response.MENSAJE);
                   } 
                    
                },
                error: function (data) {
                    $("#botonPagar").attr("disabled", false);
                }
            })   
    })

}

function  deleteAdjuntos() {
    $(document).on("click", ".deleteAdjuntoObligaciones", function () {
        var infoAdjunto = $(this).data("infoadjunto");
        var delparent = $(this).data("delparent");
        var url = $(this).data("url");

        bootbox.confirm({
            message: "\u00BFRealmente desea eliminar el Archivo?",
            buttons:{
                        confirm:{
                                    label: 'Si',
                                    className: 'btn-danger'},
                        cancel:{
                                    label: 'No',
                                    className: 'btn-default'}},
            callback: function (resultado) {
                if (resultado) {
                    $.post(url,
                            {
                                infoAdjunto: infoAdjunto,
                                _token: $("input[name=_token]").val()
                            }, function (data) {
                        var resultado = JSON.parse(data);
                        if (resultado.STATUS) {                            
                            $(delparent).remove();
                            if (infoAdjunto.tipoAdjunto == "PSC") {
                                $("#modalAdjunto" + infoAdjunto.id_obligacion + " .containerPazYSalvo").html('<option value="PSOL">Solicitada</option><option value="PRAD>Radicada</option>"');
                                $("#modalAdjunto" + infoAdjunto.id_obligacion + " .containerPazYSalvo").show();
                            }
                            
                            if($("#containerListaAdjuntosObligaciones"+infoAdjunto.id_obligacion).html().trim().length == 0){
                                if($("#Enlace"+infoAdjunto.id_obligacion+" span").hasClass("fa-paperclip")){
                                    $("#Enlace"+infoAdjunto.id_obligacion+" span").removeClass("fa-paperclip");
                                    $("#Enlace"+infoAdjunto.id_obligacion+" span").addClass("fa-arrow-up");  
                                }
                            }                                                        
                        }                        
                        displayMessageMini(resultado.Message);

                    });
                }
            }
        });
    })

}

function generarCertificacion(){
    $(document).on("click", "#genCertificacion", function () {
        $("#genCertificacion").attr("disabled", true);
        $("#vlrCertificacion").attr("disabled", true);
        var urlPrincipal = $("#dominioPrincipal").val();
        var vlrCertificado = limiparPuntos($('#vlrCertificacion').val());        
        var id = $('#idEstudio').val();
        $.ajax({            
            type: "post",
            url: urlPrincipal+"GenerarCertificacion",
            data: { '_token': $('input[name=_token]').val(),'vlrCertificado': vlrCertificado, 'id': id },                                        
                success: function (data) {                   
                    var response = data;
                    console.log(response[0]);
                    $("#seccionCertificaciones").html(
                            "<div class='col-md-12'>"+                    
                                "<table class='table table-striped table-hover'>"+
                                    "<thead>"+
                                        "<tr>"+
                                            "<th class='text-center'>Solicitud</th>"+
                                            "<th class='text-center'>Usuario</th>"+
                                            "<th class='text-center'>Valor</th>"+                            
                                            "<th class='text-center'>Vigencia</th>"+    
                                            "<th class='text-center'></th>"+    
                                            "<th class='text-center'></th>"+
                                        "</tr>"+
                                    "</thead>"+                                
                                    "<tbody id='containerListaAdjuntosObligaciones107'>"+                                        
                                        "<tr id='"+response[0].id+"'>"+
                                            "<td class='text-center uppercase'>"+response[0].created_at.substr(0,10).split('-').reverse().join('-')+"</td>"+
                                            "<td class='text-center uppercase'>"+response[0].comercial+"</td>"+
                                            "<td class='text-center uppercase'>"+format_miles(response[0].valorProyectado)+"</td>"+
                                            "<td class='text-center uppercase'>"+response[0].diaCorte+"-"+response[0].mesVigencia+"-"+response[0].anioVigencia+"</td>"+
                                            "<td class='text-center uppercase'>"+
                                                "<a class='color-negro' title='Visualizar' href='"+urlPrincipal+"VerCertificacion/"+response[0].id_estudio+"' target='_blank'><span class='fa fa-paperclip fa-1x color-negro'></span></a>"+
                                            "</td>"+                                        
                                            "<td class='text-center'>"+
                                                "<a title='Eliminar' style='cursor: pointer' class='deleteAdjuntoCertificaciones color-redA' data-adjunto='"+response[0].id+"' data-url='"+urlPrincipal+"Cartera/EliminarCertificacion'><span class='fa fa-remove'></span></a>"+                                            
                                            "</td>"+
                                        "</tr>"+                                        
                                    "</tbody>"+
                                "</table>"+                
                            "</div>"                                                                                                                                                              
                    );
                    location.reload(true);
                },
                error: function (data) {
                    $("#genCertificacion").attr("disabled", true);
                    $("#vlrCertificacion").attr("disabled", true);
                }
        })
    })
}

function  deleteAdjuntoCertificaciones() {
    $(document).on("click", ".deleteAdjuntoCertificaciones", function () {
        var infoAdjunto = $(this).data("adjunto"); 
        var id = $('#idEstudio').val();
        var url = $(this).data("url");

        bootbox.confirm({
            message: "\u00BFRealmente desea eliminar el Archivo?",
            buttons:{
                        confirm:{
                                    label: 'Si',
                                    className: 'btn-danger'},
                        cancel:{
                                    label: 'No',
                                    className: 'btn-default'}},
            callback: function (resultado) {
                if (resultado) {
                    $.post(url,
                            {
                                infoAdjunto: infoAdjunto,
                                id: id,
                                _token: $("input[name=_token]").val()
                            }, function (data) {
                                var resultado = JSON.parse(data);
                                if (resultado.STATUS) {                            
                                    $('#seccionCertificaciones').remove();
                                    $('#salto').remove();
                                    $('#vlrCertificacion').prop('disabled', false);
                                    $('#vlrCertificacion').val(resultado.valorCertificado);
                                    $('#genCertificacion').prop('disabled', false);
                                }                        
                        displayMessageMini(resultado.Mensaje);
                    });
                }
            }
        });
    })
}

function opcionesPago(){
    $(document).on("click", ".optionPago", function(){
        $(".optionPago").removeClass("optionSelect");
        $(this).addClass("optionSelect");
        
        var opcion = $(this).data("opcion");
        
        if(opcion == "1"){
            $("#formAddPago #ValorPago").val("");
            $("#formAddPago #tipoPago").val("Individual");
            $("#formAddPago #ValorPago").attr("readonly", false);
        }else if(opcion == "2"){
            var valorCertificacion = 0;
            var infoCertificacion = $(this).data("infocertificacion");
            if(infoCertificacion !== false){
                var valorCertificacion = format_miles(infoCertificacion.valorProyectado);
            }
            $("#formAddPago #tipoPago").val("Certificacion");
            $("#formAddPago #ValorPago").attr("readonly", true);
            $("#formAddPago #ValorPago").val(valorCertificacion);
            
        }
    })
}

function iniciarMiles() {
    if($(".miles").length > 0){
        var cleave = new Cleave('.miles', {
            numeral: true,
            delimiter: ".",
            numeralDecimalScale: 0,
            numeralPositiveOnly: true,
            numeralDecimalMark: ",",
            numeralThousandsGroupStyle: 'thousand'
        });
    }
}

function limiparPuntos(numero) {
    return numero.replace(/\./g, "");
}

function iniciarInputEditable(){
    /*
     * Objeto para el listado de comerciales
     */
    $(".iniciarInputEditableComercial").editable({
        params: {_token: $("input[name=_token]").val()},            
        success: function (response, newValue) {  
            result = JSON.parse(response);            
            displayMessageMini(result.MENSAJE);
        }
    });
        
    /*
     * Objeto para el listado de bancos
     */
    $(".iniciarInputEditableBancos").editable({
        params: {_token: $("input[name=_token]").val()},            
        success: function (response, newValue) {  
            result = JSON.parse(response);            
            displayMessageMini(result.MENSAJE);
        }
    });  
    
    /*
     * Objeto para guardar el valor aprobado por el banco para el cliente
     */
    $(".iniciarInputEditableValorAprobado").editable({
        params: {_token: $("input[name=_token]").val()},            
        success: function (response, newValue) {  
            result = JSON.parse(response);            
            displayMessageMini(result.MENSAJE);
        }
    });
    
    /*
     * Objeto para guardar el estado seleccionado de la cartera
     */
    $(".iniciarInputEditableEstado").editable({
        params: {_token: $("input[name=_token]").val()},            
        success: function (response, newValue) {  
            result = JSON.parse(response);            
            displayMessageMini(result.MENSAJE);
        }
    });
    
    
}

function imprimirElemento(elemento){
  var element = document.getElementById(elemento);
  var urlLogo = window.location.origin+"/assets/layouts/layout5/img/logovtm.png";
  
  var ventana = window.open('', 'PRINT', 'height=auto,width=auto');
  ventana.document.write('<style>@page{size:landscape;}</style><html><head><title></title>');
  ventana.document.write('</head><body >');
  ventana.document.write(element.innerHTML);
  ventana.document.write('</body></html>');
  //ventana.document.close();
  //ventana.focus();
  ventana.print();
  ventana.close();
  return true;
}

$(function () {
    iniciarMiles();
    modalAdjuntos();
    validateFechasSolicitud();
    guardarFechasSolicitado();
    ValidateFechasContent();
    iniciarCalendario();
    deleteAdjuntos();
    //funciones pago masivo
    iniciarTablaExtraccionUsuarios();
    sendFormPagoMasivo();
    generarCertificacion();
    opcionesPago();
    deleteAdjuntoCertificaciones();    
    iniciarInputEditable();
})
