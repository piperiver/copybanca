
function iniciarSwitches() {
    $(".make-switch").bootstrapSwitch({
        size: "mini"
    });
}

function uploadAdjuntosEstudio(input, datos) {
    var html = '<a class="color-negro" title="Visualizar" href="' + window.location.origin + '/visualizar/' + datos.idAdjunto + '" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a>';
    $("#Enlace" + datos.idPadre).parent().html(html);
    $('#modalAdjunto' + datos.idPadre).modal('hide');
}
function tipoCompraCartera() {
    $(document).on("change", ".tipoCompraCartera", function () {
        if ($(this).val() == "0") {
            $("#tCarteraEntidad").val("");
            //$("#tCarteraEntidad").attr("readonly", false);

            $("#tCarteraCuota").val("");
            //$("#tCarteraCuota").attr("readonly", false);
            $(".containerLstObligaciones").fadeOut("slow");
        } else {
            $("#tLstObligaciones").find('option:selected').removeAttr("selected");
            $(".containerLstObligaciones").fadeIn("slow");
        }
//        $(".containerLstObligaciones").slideToggle("slow");
    })

    $(document).on("change", "#tLstObligaciones", function () {
        if ($(this).val() != "-1") {
            var selected = $(this).find('option:selected');
            var entidad = selected.data('entidad');
            var saldo = selected.data('saldo');

            $("#tCarteraEntidad").val(entidad);
            //$("#tCarteraEntidad").attr("readonly", true);

            $("#tCarteraCuota").val(saldo);
            //$("#tCarteraCuota").attr("readonly", true);
        } else {
            $("#tCarteraEntidad").val("");
            //$("#tCarteraEntidad").attr("readonly", false);

            $("#tCarteraCuota").val("");
            //$("#tCarteraCuota").attr("readonly", false);
        }
    })
}
function dspValoracion(){
    $(document).on("click", "#dspValoracion", function(){
        $("#modalValoracion").modal({show:true})        
    })
}

function editModalValoracion(){    
    $("#modalValoracion").on("show.bs.modal", function(e){
        $("#frameValoracion").contents().find("#cabezeraPrincipal").remove();
        $("#frameValoracion").contents().find("#cabezeraSecundaria").remove();        
    })    
}
function agregarCompraCartera() {
    $(document).on("click", "#idAddCompraCartera", function () {
        var entidad = $("#tCarteraEntidad").val();
        var cuota = limiparPuntos($("#tCarteraCuota").val());

        if (entidad != "" && cuota != "") {
            var pertenece = "";
            if ($('.containerLstObligaciones').is(':visible')) {
                pertenece = $("#tLstObligaciones").val();
            } else {
                pertenece = false;
            }

            if (pertenece == "-1") {
                displayMessageMini("Debe seleccionar una Obligación");
            } else {

                var valoracion = $("#tCarteraValoracion").val();
                var url = $(this).data("url");

                $.post(url + "Estudio/compraCartera",
                        {
                            pertenece: pertenece,
                            valoracion: valoracion,
                            entidad: entidad,
                            cuota: cuota,
                            _token: $("input[name=_token]").val()
                        }, function (data) {
                    var resultado = JSON.parse(data);
                    $("#obligacionesCompletas").html(resultado.obligaciones);
                    $("#obligacionesDesprendibles").html(resultado.desprendibles);
                    $("#tLstObligaciones").html(resultado.optionDesprendible);
                    //$("#sumaSaldo").text("TOTAL: "+resultado.sumaSaldo);
                    $("#ModalComprasCartera").modal("hide");

                    iniciarSwitches();
                    iniciarInputEditable();
                    changeSwitches();

                    $(".tipoCompraCartera").find('option:selected').removeAttr("selected");
                    $("#tLstObligaciones").find('option:selected').removeAttr("selected");
                    $("#tCarteraEntidad").val("");
                    //$("#tCarteraEntidad").attr("readonly", false);
                    $("#tCarteraCuota").val("");
                    //$("#tCarteraCuota").attr("readonly", false);
                    $(".containerLstObligaciones").slideToggle("slow");

                });
            }
        } else {
            if (cuota == "") {
                $("#error-tCarteraCuota").slideToggle("slow");
                $("#error-tCarteraCuota").focus();
                setTimeout(function () {
                    $("#error-tCarteraCuota").slideToggle("slow");
                }, 5000);
            }
            if (entidad == "") {
                $("#error-tCarteraEntidad").slideToggle("slow");
                $("#error-tCarteraEntidad").focus();
                setTimeout(function () {
                    $("#error-tCarteraEntidad").slideToggle("slow");
                }, 5000);
            }
        }
    })
}
function sumaObligacionesCuotaFija(){
    var total = 0;
    $(".listObligacionCuotaFija").each(function(){
        var cuota = getNumber($(this).html());
        total+= cuota;
    })
    $("#totalSumaObligacionesCuotaFija").html(format_miles(total));
}

function construirTablaObligacionesCuotaFija(obligacion, newValue){
    var cuota = 0;
    var proyectada = false;
    if(newValue === "DÍA"){
        var oblCuotaVariable = JSON.parse($("#obligacionesCuotaVariable").val());
        if(oblCuotaVariable.indexOf(obligacion.tipoCuenta) < 0){
            if(obligacion.ValorCuota !== null && getNumber(obligacion.ValorCuota) > 0){
                cuota = getNumber(obligacion.ValorCuota);
            }else if(obligacion.CuotasProyectadas !== null && parseInt(obligacion.CuotasProyectadas) > 0){
                proyectada = true;                    
                cuota = obligacion.CuotasProyectadas;
            }
            
            cuota = (cuota > 0)? format_miles(cuota+"") : 0;
            var color = (proyectada)? 'style="color:blue"' : "";
            var htmlCuotaFija= ' <tr id="rowObligacionCuotaFija'+obligacion.id+'">'
                                            +'<td>'+obligacion.Entidad+'</td>'
                                            +'<td '+color+' id="keyCuotaFija'+obligacion.id+'" class="listObligacionCuotaFija">'+cuota+'</td>'
                                        +'</tr>';
            $("#containerCapacidadObligacionesCuotaFija").append(htmlCuotaFija);
            $("#Sumatoria" + obligacion.id).data("tipocuentaobligacion", "CuotaFija");
            sumaObligacionesCuotaFija();                    

        }else{
            delObligacionCuotaFija(obligacion);
        }
    }else{
        delObligacionCuotaFija(obligacion);
    }
}

/*
 * 
 * @returns {undefined}Recontruye la tabla de las obligaciones que se van a comprar, ubicada dentro de la modal de la capacidad
 */
function construirTablaCompras(){
    var obligacionesList = getAllObligaciones();
    
    var filasTabla = "";
    var totalCompras = 0;
    var oblCuotaVariable = JSON.parse($("#obligacionesCuotaVariable").val());
    
    for (let i in obligacionesList) {
        
        if(obligacionesList[i].Compra === "S" && obligacionesList[i].EstadoCuenta === "DÍA"){
            
            var cuota = 0;
            
            if(oblCuotaVariable.indexOf(obligacionesList[i].tipoCuenta) >= 0){
                if(obligacionesList[i].CuotasProyectadas !== null && parseInt(obligacionesList[i].CuotasProyectadas) > 0){
                    cuota = obligacionesList[i].CuotasProyectadas;
                }
            }else if(obligacionesList[i].ValorCuota !== null && getNumber(obligacionesList[i].ValorCuota) > 0){
                cuota = getNumber(obligacionesList[i].ValorCuota);
            }
             
             if(cuota > 0){
                filasTabla += "<tr>"
                                +"<td>"+obligacionesList[i].Entidad+"</td>"
                                +"<td>"+format_miles(cuota)+"</td>"
                            +"</tr>";
                totalCompras += cuota;
             }
        }
    }
    
    if(filasTabla.length === 0){
        filasTabla += "<tr>"
                        +"<td colspan='2'>"+getMensajeHTML("No se han seleccionado obligaciones para comprar", "warning")+"</td>"
                    +"</tr>";        
    }
    $("#totalObligacionesCompradas").html(format_miles(totalCompras));
    $("#cuerpoTablaCompras").html(filasTabla);
    //b070164227f86d
}

function getMensajeHTML(texto, tipo){
    return "<div class='alert alert-"+tipo+"' style='display: block;'>"
                +"<button class='close' data-close='alert'></button>"
                +texto+
            "</div>";
}
function delObligacionCuotaFija(obligacion){
    if($("#rowObligacionCuotaFija"+obligacion.id)){
        $("#Sumatoria" + obligacion.id).data("tipocuentaobligacion", "false");
        $("#rowObligacionCuotaFija"+obligacion.id).remove();
        sumaObligacionesCuotaFija();
    }
}

function totalSumaSaldoCuotaCompras(){
    var obligacionesList = getAllObligaciones();
    var totalSaldo = 0;
    var totalCuotas = 0;
    for (let i in obligacionesList) {
        if(obligacionesList[i].Compra === "S"){
            totalSaldo += (obligacionesList[i].SaldoActual.length > 0)? getNumber(obligacionesList[i].SaldoActual) : 0;
            totalCuotas += (obligacionesList[i].ValorCuota.length > 0)? getNumber(obligacionesList[i].ValorCuota) : 0;
        }
    }
    $("#sumaComprasSaldo").html(format_miles(totalSaldo)); 
    $("#sumaComprasCuotas").html(format_miles(totalCuotas));
}

function iniciarInputEditable() {

    $("#cuotaVisado").editable({
        success: function(response, newValue) {            
            var capacidad = getNumber($("#totalCalculoCapacidad").html());
            var visado = getNumber(newValue);
            
            if(visado <= capacidad){
                $("#cuotaVisado").html(newValue);
                calculoCapacidad();                
            }else{
                displayMessageMini("El valor del visado no puede ser mayor a la capacidad ($"+$("#totalCalculoCapacidad").html()+")");
                return false;
            }
        }
    });
    
    
    $(".editableSaldo").editable({
        type: "text",       
        success: function(response, newValue) {
            var obligacion = getInfoObligacion($(this).parent().parent().data("obligacion"));            
            var saldoOriginal = obligacion.SaldoActualOriginal;
            var valorDigitado = getNumber(newValue);
            if((valorDigitado - saldoOriginal) === 0){
                $(".saldoActual" + obligacion.id).css("color", "black"); 
            }else{
                $(".saldoActual" + obligacion.id).css("color", "red"); 
            }
            
            $("#Sumatoria" + obligacion.id).data("valorsaldo", newValue);
            $.when(actualizarJson(obligacion.id, "SaldoActual", newValue))
                    .then(totalSumaSaldoCuotaCompras())
                        .then(costosDeLaTransformacion())
                            .then(construirTablaCompras())
                                .then(calculoCapacidad());            
            
        }
    });
    
    $(".editableCuota").editable({
        type: "text",       
        success: function(response, newValue) {
            var obligacion = getInfoObligacion($(this).parent().parent().data("obligacion"));            
            
            $("#Sumatoria" + obligacion.id).data("valorcuota", newValue);
            
            if(obligacion.TipoCuotaEstudio == "CuotaFija"){
                $("#keyCuotaFija"+obligacion.id).html(newValue);
                sumaObligacionesCuotaFija();
            }
            
            $.when(actualizarJson(obligacion.id, "ValorCuota", newValue))
                                    .then(totalSumaSaldoCuotaCompras())
                                        .then(construirTablaCompras())
                                            .then(calculoCapacidad());
            
            if(getNumber(newValue) == 0){
                var valorCuotaProyectada = parseInt(obligacion.CuotasProyectadas);
                if(valorCuotaProyectada > 0){
                     $(".valorCuota" + obligacion.id).css("color", "blue");
                     return {newValue: format_miles(valorCuotaProyectada)};
                }                   
             }
        }
    });
    

    $.fn.editable.defaults.mode = 'popup';

    $(".editablePago").editable({
       type: "select",
       showbuttons: false,
       source: [
                {value: "S", text: 'SI'},
                {value: "N", text: 'NO'}
            ],        
        success: function(response, newValue) {
            var obligacion = getInfoObligacion($(this).parent().parent().data("obligacion"));
            
            $("#Sumatoria" + obligacion.id).data("compra", newValue);
            $.when(actualizarJson(obligacion.id, "Compra", newValue))
                                    .then(totalSumaSaldoCuotaCompras())
                                        .then(construirTablaCompras())
                                            .then(calculoCapacidad());
        }
    });

    $(".editablePagoWithParcial").editable({
       type: "select",
       showbuttons: false,
       source: [
                {value: "S", text: 'SI'},
                {value: "P", text: 'PARC'},
                {value: "N", text: 'NO'}
            ],        
        success: function(response, newValue) {
            var obligacion = getInfoObligacion($(this).parent().parent().data("obligacion"));
            
            $("#Sumatoria" + obligacion.id).data("compra", newValue);
            $.when(actualizarJson(obligacion.id, "Compra", newValue))
                                    .then(totalSumaSaldoCuotaCompras())
                                        .then(construirTablaCompras())
                                            .then(calculoCapacidad());
        }
    });
    
    $(".optionEstadoObligacion").editable({
       type: "select",
       showbuttons: false,
       source: [
                {value: "DÍA", text: 'DÍA'},
                {value: "MORA", text: 'MORA'},
                {value: "CAST", text: 'CAST'},
                {value: "PYS", text: 'PYS'}
            ],
        validate: function(value) {            
            if($.trim(value) == 'PYS') {
                return "Acción inhabilitada";
            }
        },
        success: function(response, newValue) {
            var obligacion = getInfoObligacion($(this).parent().parent().data("obligacion"));
            
            $("#Sumatoria" + obligacion.id).data("estadoobligacion", newValue);
            $.when(actualizarJson(obligacion.id, "EstadoCuenta", newValue)).
                                                                            then(construirTablaCompras()).
                                                                                then(construirTablaObligacionesCuotaFija(obligacion, newValue)).
                                                                                    then(calculoCapacidad());            
        }
    });
}
function getInfoObligacion(id){
    var listObligaciones = desencriptar($("#listObligaciones").val());
    
    for(var i = 0; i < listObligaciones.length; i++){
        if(listObligaciones[i].id === id){
            return listObligaciones[i];
        }
    }
    
}
function getAllObligaciones(){
    return desencriptar($("#listObligaciones").val());
}
function actualizarJson(id, columna, valor){
    var listObligaciones = desencriptar($("#listObligaciones").val());
    
    for(var i = 0; i < listObligaciones.length; i++){
        if(listObligaciones[i].id === id){
            listObligaciones[i][columna] = valor;
        }
    }
    $("#listObligaciones").val(encriptar(JSON.stringify(listObligaciones)));    
}

function encriptar(valor){
    return (valor.length > 0)? btoa(valor) : "";    
}

function desencriptar(valor){
    return (valor.length > 0)? JSON.parse(atob(valor)) : "";    
}

function limiparPuntos(numero) {
    return numero.replace(/\./g, "");
}

function iniciarMiles() {
    var cleave = new Cleave('.miles', {
        numeral: true,
        delimiter: ".",
        numeralDecimalScale: 0,
        numeralPositiveOnly: true,
        numeralDecimalMark: ",",
        numeralThousandsGroupStyle: 'thousand'
    });
    var cleave2 = new Cleave('#mcEgreso', {
        numeral: true,
        delimiter: ".",
        numeralDecimalScale: 0,
        numeralPositiveOnly: true,
        numeralDecimalMark: ",",
        numeralThousandsGroupStyle: 'thousand'
    });
    var cleave3 = new Cleave('#mcIngreso', {
        numeral: true,
        delimiter: ".",
        numeralDecimalScale: 0,
        numeralPositiveOnly: true,
        numeralDecimalMark: ",",
        numeralThousandsGroupStyle: 'thousand'
    });
    var cleave4 = new Cleave('#ing_ad_valor', {
        numeral: true,
        delimiter: ".",
        numeralDecimalScale: 0,
        numeralPositiveOnly: true,
        numeralDecimalMark: ",",
        numeralThousandsGroupStyle: 'thousand'
    });
}
function eventoKeyUp() {

//    $(document).on("keyup", ".inputEstudio", function(){
//        if($(this).hasClass("egreso")){
//            $("#estudio_egreso_error").html("");
//            if($("#estudio_ingreso").val() == "" || $("#estudio_ingreso").val() == null || limiparPuntos($("#estudio_ingreso").val()) <= 0){
//                $("#estudio_egreso_error").html("Ingrese un número valido en el campo \"INGRESO\"");
//                $(this).val("");
//                return;
//            }else{
//                var datos = JSON.parse($("#parameters").val());
//                var ingreso = limiparPuntos($("#estudio_ingreso").val());
//                var descuentoLey = (ingreso*datos.leyDocentes);
//                if(limiparPuntos($(this).val()) < descuentoLey){
//                    $("#estudio_egreso_error").html("El valor de egreso no puede ser menor a los descuentos de ley ($"+descuentoLey+")");
//                    return;
//                }
//            }
//        }
//
//        if(limiparPuntos($(this).val()) != "" && limiparPuntos($(this).val()) != null && limiparPuntos($(this).val()) > 0){
//           calcularEstudio();
//        }
//    })
//
//    $(document).on("focusout", ".egreso", function(){
//        $("#estudio_egreso_error").html("");
//    })
}
function format_miles(x) {    
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function inputMilesEditable() {
    $(document).on("focus", ".inputEditableMiles", function () {
        var cleave = new Cleave($(this), {
            numeral: true,
            delimiter: ".",
            numeralDecimalScale: 0,
            numeralPositiveOnly: true,
            numeralDecimalMark: ",",
            numeralThousandsGroupStyle: 'thousand'
        });
    })
}

var obligacionesEliminar = [];
$(document).on("click", "#ModalDefinirObligaciones .listaSelObligaciones .obligacion", function () {
    if ($(this).hasClass("Eliminar")) {
        $(this).removeClass("Eliminar");
    } else {
        $(this).addClass("Eliminar");
    }

    var obligacion = $(this).data("obligacion");
    if ($(".itemEliminarObligacion" + obligacion).val() == "true") {
        $(".itemEliminarObligacion" + obligacion).remove();
    } else {
        $("#formularioObligacionesAEliminar").append('<input class="itemEliminarObligacion' + obligacion + '" value="' + obligacion + '" name="eliminar[]" type="hidden">');
    }

})

function cambiarUpload(input, datos) {
    var rutaPrincipal = $("#dominioPrincipal").val();
    var html = '<a class="color-negro" title="Visualizar" href="' + rutaPrincipal + '/visualizar/' + datos.idAdjunto + '" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a>';
    $("#dsp_modal" + datos.tipoAdjunto + "-" + datos.idPadre).html(html);
    $("#modal" + datos.tipoAdjunto + "-" + datos.idPadre).modal("hide");
    input.filestyle('disabled', true);
    input.filestyle('buttonText', 'Archivo Cargado');

    if (datos.tipoAdjunto == "VIS" || datos.tipoAdjunto == "LBZ") {
        $("#adjunto" + datos.tipoAdjunto + "-" + datos.idPadre).val("1");
        calculoCapacidad();
        seleccionarEstado();
    }
}

function addIngresosAdicionales() {
    $(document).on("click", "#send_form_ing_adicionales", function () {
        var tipo = $("#ing_ad_tipo").val();
        var valor = $("#ing_ad_valor").val();
        var idEstudio = $("#Identificacion_Estudio").val();

        if (valor == "") {
            displayMessageMini("El campo Valor es obligatorio");
            return;
        }

        valor = limiparPuntos(valor);

        url = $("#dominioPrincipal").val();
        $.post(url + "/Estudio/addIngresosAdicionales", {tipo: tipo, valor: valor, idEstudio: idEstudio, _token: $("input[name=_token]").val()}, function (data) {
            var respuesta = JSON.parse(data);
            $("#ing_ad_valor").val("");
            $("#contenedorModalesIngresosAdicionales7").html(respuesta.modales);
            $("#contenedor_lst_ingresos_adicionales").html(respuesta.html);
            $("#ingresosAdicionales").html(respuesta.totalIngresosAdicionales);
            calculoCapacidad();
        });

    })
}

function cerrarModalPadre() {
    $(document).on("click", ".cerrarModalPadre", function () {
        $("#ingresosAdicionalesMDL").modal("hide");
        $(".componenetFile").filestyle({input: false, buttonText: "Cargar Archivo"});
    })

    $(document).on("hidden.bs.modal", ".modalesAdjuntosIngAdicionales", function () {
        $("#ingresosAdicionalesMDL").modal("show");
    })
}

function deleteIngresosAdicionales() {
    $(document).on("click", ".EliminarIngresoAdicional", function () {
        var idIngreso = $(this).data("ing");
        var idEstudio = $(this).data("parent");

        url = $("#dominioPrincipal").val();
        $.post(url + "/Estudio/delIngresosAdicionales", {idIngreso: idIngreso, idEstudio: idEstudio, _token: $("input[name=_token]").val()}, function (data) {
            var respuesta = JSON.parse(data);
            $("#contenedorModalesIngresosAdicionales7").html(respuesta.modales);
            $("#contenedor_lst_ingresos_adicionales").html(respuesta.html);
            $("#ingresosAdicionales").html(respuesta.totalIngresosAdicionales);
            calculoCapacidad();
        });

    })
}

function changeTiposDeContrato() {
    $(document).on("change", ".tiposDeContrato", function () {
        $.when(tiposContratoFuncion()).then(calculoCapacidad()).then(procesoBancos());
    })
}

function tiposContratoFuncion() {
    console.log("Calculando tipo de contrato");
    if ($(".tiposDeContrato").val() == "OTHER" || $(".tiposDeContrato").val() == "") {
        $("#DesicionEstudio").html("Negado");
        $("#plazoMaximo").html("");
        $("#ROplazo").val("");
    } else if ($(".tiposDeContrato").val() == "PROP" || $(".tiposDeContrato").val() == "PRUE" || $(".tiposDeContrato").val() == "DEF") {
        $("#DesicionEstudio").html("Aprobado");
        calcularMesesRetiroForzoso();
    } else {
        $("#plazoMaximo").html(tiposContrato[$(".tiposDeContrato").val()]);
        $("#ROplazo").val(tiposContrato[$(".tiposDeContrato").val()]);
        $("#DesicionEstudio").html("Aprobado");

        

    }
}
function calcularMesesRetiroForzoso() {
    if ($(".tiposDeContrato").val() == "PROP" || $(".tiposDeContrato").val() == "PRUE" || $(".tiposDeContrato").val() == "DEF") {
        var fechaNacimiento = $("#FechaNacimientoPlazo").val();
        if (fechaNacimiento != "") {
            var arrayFInicio = fechaNacimiento.split("-");
            var parametroRetiroForzoso = $("#retiroForzoso").val();
            var cumpleAnnios70 = parseInt(parametroRetiroForzoso) + parseInt(arrayFInicio[0]);

            var meses = monthDiff(cumpleAnnios70, parseInt(arrayFInicio[1]), parseInt(arrayFInicio[2]));
            if(!costos_has_change){
                meses =	parseInt($('#ROplazo').val());
            }
            $("#mesesRetiroForzoso").html(meses + " meses");
            if (meses <= tiposContrato[$(".tiposDeContrato").val()]) {
                if (meses <= 0) {
                    $("#plazoMaximo").html(0);
                    $("#mesesRetiroForzoso").html(0);
                } else {
                    $("#plazoMaximo").html(meses);
                    $("#ROplazo").val(meses);
                    calcularDesembolso();
                }
            } else {
                $("#plazoMaximo").html(tiposContrato[$(".tiposDeContrato").val()]);
                if($("#ROplazo").val() == ""){
                    $("#ROplazo").val(tiposContrato[$(".tiposDeContrato").val()]);
                }
                calcularDesembolso();
            }
        }
    } else {
        $("#mesesRetiroForzoso").html(" ");
    }
}
function calculoEdad() {
    console.log("Calculando Edad");
    if ($(".calculeEdad").val() != "") {
        var fechaNacimiento = $(".calculeEdad").val();
        var fechaActual = new Date();
        
        var edad = getEdad(fechaNacimiento, fechaActual);
       
        $("#EdadUser").html(edad);
        
        calcularMesesRetiroForzoso();
        calcularMesesAsegurado();
        procesoBancos();
    }
}
function getEdad(fechaNacimiento, fecha){        
        
        //Se divide la fecha de nacimiento en tres partes
        var arrayNacimiento = fechaNacimiento.split("-");
        //Restamos el año de la fecha en la que se quiere calcular la edad con el año de nacimiento lo cual nos retornara un valor aproximado
        var edad = fecha.getFullYear() - parseInt(arrayNacimiento[0]);
        
        //Validamos si el mes proyectado es menor a el mes en el que nacio, Si es asi quiere decir que aun no ha cumplido la edad ese año y por eso se le resta un año
        if ((fecha.getMonth() + 1) < parseInt(arrayNacimiento[1])) {
            edad--;
        }
        //Ahora validamos si el mes proyectado es igual a el mes en el que nacio y procedemos a verificar si el dia en el que nacio no ha pasado, para restarle un dia.
        if ((fecha.getMonth() + 1) == parseInt(arrayNacimiento[1]) && fecha.getUTCDate() < parseInt(arrayNacimiento[2])) {
            edad--;
        }
        
        if (edad <= 0) {
            displayMessageMini("La  Fecha de Nacimiento es invalida");
            edad = 0;
        }
        return edad;
}
function generateEdadByFecha() {
    $(document).on("change", ".calculeEdad", function () {
        if ($(this).val() == "") {
            displayMessageMini("Ingrese una fecha de nacimiento válida");
            return;
        }
        calculoEdad();
    })
}

function calculoTiempoContrato() {
    $(document).on("change", "#fecha_inicio_contrato", function () {
        var fechaInicio = $("#fecha_inicio_contrato").val();
        var errores = false;
        if (fechaInicio == "") {
            errores = true;
            var mensaje = "Por favor ingrese un valor valido en la fecha de inicio";
        }

        var arrayFInicio = fechaInicio.split("-");
        var fechaActual = new Date();

        if (parseInt(arrayFInicio[0]) > fechaActual.getFullYear()) {
            errores = true;
            mensaje = "La fecha de inicio de contrato no puede ser mayor a la fecha actual";
        } else if (parseInt(arrayFInicio[0]) == fechaActual.getFullYear() && parseInt(arrayFInicio[1]) > (fechaActual.getMonth() + 1)) {
            errores = true;
            mensaje = "La fecha de inicio de contrato no puede ser mayor a la fecha actual";
        } else if (parseInt(arrayFInicio[0]) == fechaActual.getFullYear() && parseInt(arrayFInicio[1]) == (fechaActual.getMonth() + 1) && parseInt(arrayFInicio[2]) > fechaActual.getUTCDate()) {
            errores = true;
            mensaje = "La fecha de inicio de contrato no puede ser mayor a la fecha actual";
        }
        if (errores) {
            displayMessageMini(mensaje);
            return;
        }

        var tiempo = fechaActual.getFullYear() - parseInt(arrayFInicio[0]);

        if ((fechaActual.getMonth() + 1) < parseInt(arrayFInicio[1])) {
            tiempo--;
        } else if ((fechaActual.getMonth() + 1) == parseInt(arrayFInicio[1]) && fechaActual.getUTCDate() < parseInt(arrayFInicio[2])) {
            tiempo--;
        }
        
         if (tiempo > 0) {
            $("#tiempoTrabajadoAlDia").html(tiempo + " años");
        }
//        if (tiempo <= 0) {
//            
//            if (fechaActual.getFullYear() > parseInt(arrayFInicio[0])) {
//                var mesesDelAnnioPasado = 12 - parseInt(arrayFInicio[1]);
//                tiempo = mesesDelAnnioPasado + (fechaActual.getMonth() + 1);
//                if (parseInt(arrayFInicio[2]) > fechaActual.getUTCDate()) {
//                    tiempo--;
//                }
//            } else {
//                var tiempo = (fechaActual.getMonth() + 1) - parseInt(arrayFInicio[1]);
//                if (fechaActual.getUTCDate() < parseInt(arrayFInicio[2])) {
//                    tiempo--;
//                }
//            }
//
//            $("#tiempoTrabajadoAlDia").html(tiempo + " meses");
//
//        } else {
//            $("#tiempoTrabajadoAlDia").html(tiempo + " años");
//        }
        procesoBancos();
    
    })
}

function funcionalidadParaAsegurados() {
    $(document).on("change", "#asegurado", function () {
        calcularMesesAsegurado();
    });
}
function calcularMesesAsegurado() {
    if ($("#asegurado").val() == "1") {
        $(".containerAdjuntoAsegurado").show();
        if ($("#FechaNacimientoPlazo").val() != "") {
            var fechaNacimiento = $("#FechaNacimientoPlazo").val();
            var arrayFInicio = fechaNacimiento.split("-");
            var parametroEdadMaxSeguro = $("#edadSeguro").val();

            var cumpleAnnios70 = parseInt(parametroEdadMaxSeguro) + parseInt(arrayFInicio[0]);
            var meses = monthDiff(cumpleAnnios70, parseInt(arrayFInicio[1]), parseInt(arrayFInicio[2]))
            $("#resultMesesSeguro").html(meses + " meses");
        }
    } else {
        $(".containerAdjuntoAsegurado").hide();
        $("#resultMesesSeguro").html("");
    }
}
function monthDiff(annio, mes, dia) {
    var Hoy = new Date();
    var Fin = new Date(annio, mes, dia);
    var diffYears = Fin.getFullYear() - Hoy.getFullYear();
    var diffMonths = Fin.getMonth() - (Hoy.getMonth() + 1);
    var diffDays = Fin.getDate() - Hoy.getDate();

    var months = (diffYears * 12 + diffMonths);

    if (diffDays <= 0) {
        months--;
    }
    return months;
}
function calculoCapacidad() {
    var ingreso = parseInt(limiparPuntos($("#EstudioIngresoCapacidad").val()));
    var ingresosAdicionales = parseInt(limiparPuntos($("#ingresosAdicionales").html()));
    var porcentajeGastoFijo = parseInt($("#porcentajeGastoFijo").val()) / 100;

    var totalCuotasFijas = 0;
    var totalCuotasVariables = 0;
    var compras = 0;
    var totalMoraParciales = 0;
    var comprasDesprendible = 0;
    console.log("Calculando Capacidad...");
    //Bucle que recorre las obligaciones
    $(".listaObligacionesFormulaEstudio").each(function (index) {
        var valorCuota = 0;
        var valorCuotaProyectada = 0;
        
        //Se coloca el texto negro
        $(".valorCuota" + $(this).data("obligacion")).css("color", "black");
        
        //Si tiene paz y salvo no se tiene en cuenta la obligacion para el calculo de a capacidad
        if(parseInt($(this).data("pazsalvo")) > 0 && $(this).data("cddrad") == "N"){
            console.log("Pase Por alto en la capacidad "+$(this).data("obligacion"));
            return true;
        }

        //Se verifica si la obligacion es de cuota fija (<> a TCR, Sobregiros y Rotativos && Al dia) o si es de cuota variable (= a TCR, Sobregiros y Rotativos) y se realizan las sumatorias
        if ($(this).data("tipocuentaobligacion") == "CuotaFija") {
            if ($(this).data("valorcuota") != "" && parseInt($(this).data("valorcuota")) > 0) {
                valorCuota = limiparPuntos($(this).data("valorcuota") + "");
                totalCuotasFijas = totalCuotasFijas + parseFloat(valorCuota);
            } else if ($(this).data("cuotasproyectadas") != "" && parseInt($(this).data("cuotasproyectadas")) > 0) {
                valorCuota = $(this).data("cuotasproyectadas");
                //$("#Sumatoria"+$(this).data("obligacion")).data("valorcuota", format_miles(parseInt(valorCuota)));                
                $(".valorCuota" + $(this).data("obligacion")).editable("setValue", format_miles(parseInt(valorCuota)));
                $(".valorCuota" + $(this).data("obligacion")).css("color", "blue");
                totalCuotasFijas = totalCuotasFijas + parseFloat(valorCuota);
            }
        } else if ($(this).data("tipocuentaobligacion") == "CuotaVariable") {
            if ($(this).data("cuotasproyectadas") != "" && parseInt($(this).data("cuotasproyectadas")) > 0) {
                valorCuotaProyectada = $(this).data("cuotasproyectadas");
                totalCuotasVariables = totalCuotasVariables + parseFloat(valorCuotaProyectada);
            }
        } else {
            if ($(this).data("valorcuota") != "" && parseInt($(this).data("valorcuota")) > 0) {
                valorCuota = limiparPuntos($(this).data("valorcuota") + "");
            } else if ($(this).data("cuotasproyectadas") != "" && parseInt($(this).data("cuotasproyectadas")) > 0) {
                valorCuota = $(this).data("cuotasproyectadas");                
                $(".valorCuota" + $(this).data("obligacion")).editable("setValue", format_miles(parseInt(valorCuota)));
                $(".valorCuota" + $(this).data("obligacion")).css("color", "blue");
            }
        }

        //Este proceso es para identificar las cuotas de las obligaciones que se compren parciales las cuales finalmente se restan a la capacidad
        if ($(this).data("estadoobligacion") == "MORA" && ($(this).data("compra") == "P" || $(this).data("compra") == "N")) {
            if ($(this).data("tipocuentaobligacion") == "CuotaVariable") {
                totalMoraParciales = totalMoraParciales + parseFloat(valorCuotaProyectada);
            } else {
                totalMoraParciales = totalMoraParciales + parseFloat(valorCuota);
            }
        }

        //Este proceso es para  calcular las compras del desprendible las cuales solamente de visualizaran en pantalla en el label Compras
        if ($(this).data("desprendible") == "S" && $(this).data("compra") == "S") {
            console.log("Obligacion suma "+$(this).data("obligacion"));
            if ($(this).data("tipocuentaobligacion") == "CuotaVariable") {
                comprasDesprendible = comprasDesprendible + parseFloat(valorCuotaProyectada);
            } else {
                comprasDesprendible = comprasDesprendible + parseFloat(valorCuota);
            }
        }

        //Con  Este proceso identificamos las obligaciones que se compraran las cuales seran usadas en el calculo
        if ($(this).data("compra") == "S" && $(this).data("estadoobligacion") == "DÍA") {            
            if ($(this).data("tipocuentaobligacion") == "CuotaVariable") {
                compras = compras + parseFloat(valorCuotaProyectada);
            } else {
                compras = compras + parseFloat(valorCuota);
            }
        }
    });    
    //Se realiza el calculo de la capacidad con la informacion recolectada anteriormente
    var total = (ingreso + ingresosAdicionales) - ((ingreso + ingresosAdicionales) * porcentajeGastoFijo) - totalCuotasFijas - totalCuotasVariables + compras - totalMoraParciales;
    $("#totalCalculoCapacidad").html(format_miles(parseInt(total)));
    $("#totalCalculoCapacidadModal").html(format_miles(parseInt(total)));

    //Finalmente se muestran las compras
    $("#DisponibleConCompras").html(format_miles(parseInt(comprasDesprendible)));
    $("#DescuentoCalculadora").html(format_miles(parseInt(comprasDesprendible + parseInt(limiparPuntos($("#DisponibleOriginal").html())))));


            //Este proceso es para mostrar la cuota mas baja en pantalla
            var cuotaMaxima = 0;
            if (parseInt(limiparPuntos($("#DescuentoCalculadora").html())) < total) {
                cuotaMaxima = parseInt(limiparPuntos($("#DescuentoCalculadora").html()));
            } else {
                cuotaMaxima = parseInt(total);
            }
            
            var cuota = getNumber($("#ROcuota").val());
            if(cuota <= 0 || cuota > cuotaMaxima){
                $("#ROcuota").val(cuotaMaxima);
            }
            cuotaMaxima = (cuotaMaxima >= 0) ? format_miles(cuotaMaxima) : 0;
            $("#CuotaMaxima").html(cuotaMaxima);
            
            //Se realiza el calculo del valor desembolso
            calcularDesembolso();
}

function calcularDesembolso() {
    console.log("Calcular desembolso");
    var cuota = parseFloat(limiparPuntos($("#ROcuota").val()));
    var tasa = (parseFloat($("#ROtasa").val())) / 100;
    var plazo = parseInt($("#ROplazo").val());

    if (cuota > 0 && tasa > 0 && plazo > 0) {
        var valorXmillon = $("#valorXmillon").val();
        var infoCredito = calcularCreditos(cuota, tasa, plazo);        
        var millones = Math.ceil(infoCredito.valorCreditoReal/1000000);
        var costoSeguro = millones * parseInt(valorXmillon);
        var nuevaCuota = cuota - costoSeguro;
        var creditoReal = calcularCreditos(nuevaCuota, tasa, plazo);        
        var valorCreditoReal = creditoReal.valorCreditoReal;
        $("#costoSeguro").val(costoSeguro);
        
        $("#ROcredito").val(format_miles(Math.round(valorCreditoReal)));
        calcularCostos();
        guardarCostos();
    } else {
        $("#ROcredito").val("0");
        $("#CMcostos").val("0");
        $("#Deselbolso").html("0");
        $("#DeselbolsoCliente").html("0");
        $("#DeselbolsoClienteReal").val("-1");
        $("#costoSeguro").val("0");
        
    }
    seleccionarEstado();
}

/*
 * Eventos que requieren volver a realizar el calculo de la formula
 */
function eventosReiniciarFormula() {
    /*
     * En el momento en que se envia el formulario se toma el ingreso y se actualiza el evento para volver a correr la formula
     */
    $(document).on("click", "#send-form", function () {
        var ingresos = $("#EstudioIngreso").val();
        if (ingresos != "") {
            $("#EstudioIngresoCapacidad").val(ingresos);
            calculoCapacidad();
        }
    });

    $(document).on("change", "#porcentajeGastoFijo", function () {
        calculoCapacidad();
    })
}
var cuotaOriginal;
function keyUpCuotaOperacion() {
    $(document).on("keyup", "#ROcuota", function () {
        var cuotaOriginal = parseInt(limiparPuntos($("#CuotaMaxima").html()));
        var cuota = parseInt(limiparPuntos($(this).val()));

        if (cuota > cuotaOriginal) {
            $("#error-ROcuota").html("El valor de la cuota no puede superar los " + format_miles(cuotaOriginal));
            $("#error-ROcuota").show();
        } else {
            if ($("#error-ROcuota").is(":visible") == true) {
                $("#error-ROcuota").hide();
            }
            calcularDesembolso();
            procesoBancos();
        }
    })
    $(document).on("keyup", "#ROtasa", function () {
        calcularDesembolso();
        procesoBancos();
    })
    $(document).on("keyup", "#ROplazo", function () {
        var plazoOriginal = parseInt($("#plazoMaximo").html());
        var plazo = parseInt($(this).val());

        if (plazo > plazoOriginal) {
            $("#error-ROplazo").html("El valor del plazo no puede superar los " + (plazoOriginal) + " meses");
            $("#error-ROplazo").show();
        } else {
            if ($("#error-ROplazo").is(":visible") == true) {
                $("#error-ROplazo").hide();
            }
            calcularDesembolso();
            procesoBancos();
        }
    })
}

function calcularCreditos(cuota, tasa, plazo) {

    var valorCreditoReal = cuota * (((Math.pow(1 + tasa, plazo)) - 1) / (tasa * (Math.pow(1 + tasa, plazo))));
    var valorDesembolsoReal = valorCreditoReal / 1.2;
    var costos = valorCreditoReal - valorDesembolsoReal;

    return  {
        valorCreditoReal: valorCreditoReal,
        valorDesembolsoReal: valorDesembolsoReal,
        costos: costos
    };
}
function desplegarModalBanco() {

}
function is_empty(valor) {
    if (valor == "" || !valor) {
        return "";
    } else {
        return valor;
    }
}
function negarEstudio(){        
    var url = $("#dominioPrincipal").val();
    var ruta = url + "Estudio/negarEstudio";
    var idEstudio = $("#Identificacion_Estudio").val();
    $.post(ruta,
                {
                    idEstudio: idEstudio,
                    _token: $("input[name=_token]").val()
                }, function (data) {
            var respuesta = JSON.parse(data);
            if(respuesta.STATUS){
                $("#EstadoEstudioLbl").html("NO VIABLE");
                $("#EstadoEstudio").val("NEG");                
                displayMessageMini("El estudio ha sido negado");
            }else{
                displayMessageMini("Ocurrio un problema al intentar negar el estudio. Por favor refresque la pagina e intentelo de nuevo.");
            }
        })
}

function clickGuardarEstudio() {
    $(document).on("change", "#accionEstudio", function () {
        if ($(this).val() == "GUA") {
            guardarEstudio("GUA");
        }else if($(this).val() == "NEG"){
            $("#EstadoEstudioLbl").html("NEGADO");
            $("#EstadoEstudio").val("NEG");
            definicionSelectAccion("NEG");
            guardarEstudio("NEG");
        }else if($(this).val() == "APR"){
            if(validarAprobacion()){
                aprobarEstudio();
            }else{
                displayMessageMini("El estudio no puede ser aprobado, revise nuevamente el estudio");
            }
        }else if($(this).val() == "PEN"){
            $("#EstadoEstudioLbl").html("PENDIENTE");
            $("#EstadoEstudio").val("PEN");
            definicionSelectAccion("PEN");
            guardarEstudio("PEN");
        }else if($(this).val() == "PRE"){
            $("#EstadoEstudioLbl").html("PRE APROBADO");
            $("#EstadoEstudio").val("PRE");
            guardarEstudio("PRE");
        }else if($(this).val() == "DES"){
            $("#EstadoEstudioLbl").html("DESISTIO");
            $("#EstadoEstudio").val("DES");
            guardarEstudio("DES");
        }

    })
}

function validarAprobacion(){
    
    if(isComite()){
        return true; 
    }else{
        return false;
    }
}

function aprobarEstudio(){
    console.log("Entro aprobar estudio");
    var formData = new FormData();

    formData.append("estudio_id", $('#Identificacion_Estudio').val());
    formData.append("user_id", $('#Identificacion_UserLogin').val());
    formData.append("aprobacion", 1);
    formData.append("_token", $("input[name=_token]").val());
    var nameLogin = $("#Nombre_UserLogin").val().substr(0,3);

    var url = $("#dominioPrincipal").val();
    var ruta = url + "Estudio/aprobarEstudio";

    $.ajax({

        url:ruta,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success:function(data){
            var respuesta = JSON.parse(data);
            console.log(respuesta);
            if(respuesta.bandera == "ON") {
                $("#aprobacionLbl").html("APROBADO");
                $("#aprobado").val(1);
                $("#DesicionEstudio").html("APROBADO");
            }else{
                $("#aprobacionLbl").html("APRUEBA: "+nameLogin);
            }

            $("#table_logAprobacion").html(respuesta.table);
            seleccionarEstado();
            displayMessageMini(respuesta.Message);
        },

        error:function(jqHRX,textStatus, errorThrown){
            console.log("Error: "+errorThrown);
        }

    });
}

function guardarEstudio(accion) {
    var tipoContrato = is_empty($(".tiposDeContrato").val());
    var mesesRetiroForzoso = is_empty(parseInt($("#mesesRetiroForzoso").html()));
    var fechaNacimiento = is_empty($("#FechaNacimientoPlazo").val());
    var edad = is_empty(parseInt($("#EdadUser").html()));
    var fechaInicioContrato = is_empty($("#fecha_inicio_contrato").val());
    var antiguedad = is_empty(parseInt($("#tiempoTrabajadoAlDia").html()));        
    var cargo = is_empty($("#cargo").val());
    var seguroVida = is_empty($("#asegurado").val());
    var mesesSeguroVida = is_empty(parseInt($("#resultMesesSeguro").html()));
    var plazoMaximo = is_empty(parseInt($("#plazoMaximo").html()));

    var cuotaMaxima = limiparPuntos(is_empty($("#CuotaMaxima").html()));
    var disponible = limiparPuntos(is_empty($("#DisponibleOriginal").html()));
    var compras = limiparPuntos(is_empty($("#DisponibleConCompras").html()));
    var descuento = limiparPuntos(is_empty($("#DescuentoCalculadora").html()));
    var gastoFijo = is_empty($("#porcentajeGastoFijo").val());
    var capacidad = limiparPuntos(is_empty($("#totalCalculoCapacidad").html()));

    var garantia = limiparPuntos($("#garantia").val());
    var tasa = is_empty($("#ROtasa").val());
    var plazo = is_empty($("#ROplazo").val());
    var cuota = limiparPuntos(is_empty($("#ROcuota").val()));
    var valorCredito = limiparPuntos(is_empty($("#ROcredito").val()));
    var valorCreditoBanco = limiparPuntos(is_empty($("#cifraBanco").val()));

    
    var bancosEncontrados = is_empty($("#BancosEncontrados").val());        
    if(bancosEncontrados != false && bancosEncontrados != ""){
        var bancoSeleccionado = is_empty($("#BancoSeleccionadoEstudio").val());
        var datosBanco = JSON.parse(bancosEncontrados);
        datosBanco.bancoSeleccionado = bancoSeleccionado;         
        datosBanco = JSON.stringify(datosBanco);
    }else{
        var datosBanco = "";
    }



    const ajusteCostos = parseFloat($('#ajusteCostos').val());
    const totalCostosV =  parseFloat($('#valorTotal').html().replace(/[^0-9]/g, ''));
    var datosCostos = {
        ajusteCostos: ajusteCostos,
        totalCostosV: totalCostosV
    };
    var ahorroCarteraCastigadaP = parseFloat($("#porcentajeAhoCarCastigada").html());
    var ahorroCarteraCastigadaV = eliminarCaracteres(limiparPuntos($("#valorAhoCarCastigada").html()));
    var ahorroCarteraEnMoraP = parseFloat($("#porcentajeCarMora").html());
    var ahorroCarteraEnMoraV = eliminarCaracteres(limiparPuntos($("#valorCarMora").html()));
    var ahorroTasaXannioP = parseFloat($("#porcentajeAhoTasaXannio").html());
    var ahorroTasaXannioV = eliminarCaracteres(limiparPuntos($("#valorAhoTasaXannio").html()));
    var ahorroSubtotalV = eliminarCaracteres(limiparPuntos($("#valorSubtotalAhorro").html()));
    var reduccionCuota = eliminarCaracteres(limiparPuntos($("#valorReduccionCuota").html()));
    var desembolsoVtm = eliminarCaracteres(limiparPuntos($("#valorDesembolsoVTM").html()));
    var desembolsoBanco = eliminarCaracteres(limiparPuntos($("#valorDesembolsoBanco").html()));
    var subtotalEfectivoP = parseFloat($("#porcentajeSubtotalEfectivo").html());
    var subtotalEfectivoV = eliminarCaracteres(limiparPuntos($("#valorSubtotalEfectivo").html()));
    var totalBeneficiosV = eliminarCaracteres(limiparPuntos($("#valorTotalBenefcios").html()));
    var datosBeneficios = {
        ahorroCarteraCastigadaP: ahorroCarteraCastigadaP,
        ahorroCarteraCastigadaV: ahorroCarteraCastigadaV,
        ahorroCarteraEnMoraP: ahorroCarteraEnMoraP,
        ahorroCarteraEnMoraV: ahorroCarteraEnMoraV,
        ahorroTasaXannioP: ahorroTasaXannioP,
        ahorroTasaXannioV: ahorroTasaXannioV,
        ahorroSubtotalV: ahorroSubtotalV,
        reduccionCuota: reduccionCuota,
        desembolsoVtm: desembolsoVtm,
        desembolsoBanco: desembolsoBanco,
        subtotalEfectivoP: subtotalEfectivoP,
        subtotalEfectivoV: subtotalEfectivoV,
        totalBeneficiosV: totalBeneficiosV
    };

    var saldo = limiparPuntos(is_empty($("#DeselbolsoCliente").html()));
    var desembolso = limiparPuntos(is_empty($("#Deselbolso").html()));
    var cuotaVisado = limiparPuntos(is_empty($("#cuotaVisado").html()));
    var costoSeguro = getNumber($("#costoSeguro").val());
    var valorXmillon = getNumber($("#valorXmillon").val());

    //var lstobligacionesCompras = [];
    //var lstobligacionesNoCompras = [];
    //var nuevosSaldos = [];

    /*$(".listaObligacionesFormulaEstudio").each(function (index) {
        if ($(this).data("compra") == "S") {
            lstobligacionesCompras.push($(this).data("obligacion"));
        } else {
            lstobligacionesNoCompras.push($(this).data("obligacion"));
        }

        nuevosSaldos.push([$(this).data("obligacion") + "[{-}]" + limiparPuntos($(this).data("valorsaldo") + "")]);
    })*/

    var estado = $("#EstadoEstudio").val();
    var infoObligaciones = atob($("#listObligaciones").val());
    
    
    //informacion calculadora calculo cupo
    var ingreso = ($("#cupo_ingreso").val() != "") ? limiparPuntos($("#cupo_ingreso").val()) : 0;
    var egreso = ($("#cupo_egreso").val() != "") ? limiparPuntos($("#cupo_egreso").val()) : 0;
    var ley1527 = ($("#cupo_ley1527").val() != "") ? limiparPuntos($("#cupo_ley1527").val()) : 0;
    var cupo = ($("#cupo_cupo").val() != "") ? limiparPuntos($("#cupo_cupo").val()) : 0;    
    
    var formData = new FormData();
    //formData.append("nuevosSaldos", nuevosSaldos);
    //formData.append("lstObligacionesComprar", lstobligacionesCompras);
    //formData.append("lstObligacionesNoComprar", lstobligacionesNoCompras);
    formData.append("tipoContrato", tipoContrato);
    formData.append("mesesRetiroForzoso", mesesRetiroForzoso);
    formData.append("fechaNacimiento", fechaNacimiento);
    formData.append("edad", edad);
    formData.append("fechaInicioContrato", fechaInicioContrato);
    formData.append("antiguedad", antiguedad);        
    formData.append("cargo", cargo);        
    formData.append("seguroVida", seguroVida);
    formData.append("mesesSeguroVida", mesesSeguroVida);
    formData.append("plazoMaximo", plazoMaximo);
    formData.append("cuotaMaxima", cuotaMaxima);
    formData.append("disponible", disponible);
    formData.append("compras", compras);
    formData.append("descuento", descuento);
    formData.append("gastoFijo", gastoFijo);
    formData.append("capacidad", capacidad);
    formData.append("tasa", tasa);
    formData.append("plazo", plazo);
    formData.append("cuota", cuota);
    formData.append("valorCredito", valorCredito);
    formData.append("saldo", saldo);
    formData.append("desembolso", desembolso);
    formData.append("cuotaVisado", cuotaVisado);
    formData.append("garantia", garantia);
    formData.append("datosCostos", JSON.stringify(datosCostos));
    formData.append("datosBeneficios", JSON.stringify(datosBeneficios));
    formData.append("accion", accion);
    formData.append("datosBanco", datosBanco);
    formData.append("valorCreditoBanco", valorCreditoBanco);
    formData.append("estado", estado);
    formData.append("costoSeguro", costoSeguro);
    formData.append("valorXmillon", valorXmillon);
    formData.append("_token", $("input[name=_token]").val());
    formData.append("idEstudio", $("#Identificacion_Estudio").val());
    formData.append("aprobado", $("#aprobado").val());
    formData.append("infoObligaciones", infoObligaciones);
    formData.append("ingreso", ingreso);
    formData.append("egreso", egreso);
    formData.append("ley1527", ley1527);
    formData.append("cupo", cupo);

    var url = $("#dominioPrincipal").val();
    var ruta = url + "Estudio/guardarEstudio";
    $.ajax({
        url: ruta,
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            var respuesta = JSON.parse(data);

            if (accion == "VIAB") {
                $("#btn_save_viabilizar").attr("disabled", true);
            }
            
            $("#accionEstudio").val(0);
            
            if(accion == "NEG"){                
                    bootbox.alert({
                        message: "Estudio Negado",
                        size: 'small',
                        callback: function () {
                            $("#accionEstudio").remove();
                            location.href = url+"Radicacion_Estudio";
                        }
                    });                
            }else if(accion == "APR"){
                displayMessageMini("Estudio Aprobado.");
            }else{
                displayMessageMini(respuesta.Message);
            }
        },
        error: function (data) {
            displayMessageMini("Ocurrio un problema al tratar de almacenar el estudio");
        }
    });

}
function getSumatorias() {
    var sumaComprasCastigadas = 0;
    var sumaComprasMora = 0;
    $(".listaObligacionesFormulaEstudio").each(function (index) {
        if ($(this).data("compra") == "S" && $(this).data("estadoobligacion") == "CAST") {
            sumaComprasCastigadas = sumaComprasCastigadas + parseInt(limiparPuntos($(this).data("valorsaldo") + ""));
        }
        if ($(this).data("compra") == "S" && $(this).data("estadoobligacion") == "MORA") {
            sumaComprasMora = sumaComprasMora + parseInt(limiparPuntos($(this).data("valorsaldo") + ""));
        }
    })

    return {
        sumaComprasCastigadas: sumaComprasCastigadas,
        sumaComprasMora: sumaComprasMora
    };
}
function eventoModalCostos() {
    $(document).on("click", "#dspModalCostos", function () {
        $("#modalCostos").modal("show");
    })

}
function costosDeLaTransformacion(){
    var correrAlgoritmo = parseInt($("#correrAlgoritmoCostosAndBeneficios").val());
    if(correrAlgoritmo == 1){ //Si se corre el algoritmo
        algoritmoCalculoCostosAndBeneficios();
    }else{
        calcularDesembolsoAndSaldo();
    }
}
function calcularDesembolsoAndSaldo(){
    var credito = getNumber($("#ROcredito").val());
    if(credito > 0){
        console.log($("#CMcostos").val());
        var valorCostos = parseInt(limiparPuntos($("#CMcostos").val()));
        console.log(credito,valorCostos);
        var desembolso = credito - valorCostos;
        $("#Deselbolso").html((desembolso <= 0) ? 0 : format_miles(parseInt(desembolso)));
                
        var saldosCompras = parseInt(limiparPuntos($("#sumaComprasSaldo").html()));
        var desembolsoCliente = desembolso - saldosCompras;
        console.log(desembolsoCliente);
        if (desembolsoCliente >= 0) {
            $("#DesicionEstudio").html("SI");
        } else {
            $("#DesicionEstudio").html("NO");
        }
        $("#DeselbolsoClienteReal").val(desembolsoCliente);
        $("#DeselbolsoCliente").html((desembolsoCliente > 0) ? format_miles(parseInt(desembolsoCliente)) : 0);        
    }
}
function algoritmoCalculoCostosAndBeneficios() {
    console.log("Calculando costos Completo");
    var tasa = $("#ROtasa").val();
    var credito = $("#ROcredito").val();
    
}
function buttonViabilizar() {
    $(document).on("click", "#btn_viabilizar", function () {
        if ($(this).data("viabilizado")) {
            return;
        }
        var obligacionesAlDia = [];
        var sumaTotalSaldo = 0;
        var sumaTotalCuota = 0;

        $(".listaObligacionesFormulaEstudio").each(function (index) {


            if ($(this).data("valorsaldo") == "" && $(this).data("desprendible") == "S") {
                if ($(this).data("valorcuota") != "") {
                    objCredito = calcularCreditos(parseFloat(limiparPuntos($(this).data("valorcuota") + "")), 0.024, 84);
                    saldoReal = objCredito.valorCreditoReal;
                } else {
                    saldoReal = 0;
                }
            } else {
                saldoReal = parseFloat(limiparPuntos($(this).data("valorsaldo") + ""));
            }

            if ($(this).data("estadoobligacion") == "CAST" && parseInt($(this).data("adjunto")) == 0) {
                saldo = saldoReal * 0.55;
            } else if ($(this).data("estadoobligacion") == "MORA" && parseInt($(this).data("adjunto")) == 0) {
                saldo = saldoReal * 0.90;
            } else {
                saldo = saldoReal;
            }

            $(".saldoActual" + $(this).data("obligacion")).html(format_miles(parseInt(saldo)));
            $("#Sumatoria" + $(this).data("obligacion")).data("valorsaldo", format_miles(parseInt(saldo)));
            
            var saldoOriginal = $("#Sumatoria" + $(this).data("obligacion")).data("valorsaldooriginal");
            if(saldo - parseInt(saldoOriginal) == 0){
               $(".saldoActual" + $(this).data("obligacion")).css("color", "black"); 
            }else{
                $(".saldoActual" + $(this).data("obligacion")).css("color", "red"); 
            }
            

            if ($(this).data("desprendible") == "S") {
                if ($(this).data("valorcuota") != "") {
                    datosCredito = calcularCreditos(parseFloat(limiparPuntos($(this).data("valorcuota") + "")), 0.015, 96);
                } else {
                    datosCredito = false;
                }

                if (datosCredito != false && datosCredito.valorDesembolsoReal > saldo) {
                    calculoCapacidad();
                    desembolso = parseInt(limiparPuntos($("#Deselbolso").html()));
                    if (desembolso > sumaTotalSaldo) {
                        $("#Sumatoria" + $(this).data("obligacion")).data("compra", "S");
                        $(".compras" + $(this).data("obligacion")).html("Si");
                    } else {
                        $("#Sumatoria" + $(this).data("obligacion")).data("compra", "N");
                        $(".compras" + $(this).data("obligacion")).html("No");
                    }

                } else {
                    $("#Sumatoria" + $(this).data("obligacion")).data("compra", "N");
                    $(".compras" + $(this).data("obligacion")).html("No");
                }

            }

            if ($(this).data("estadoobligacion") == "DÍA" && $(this).data("desprendible") == "N") {
                cuota = ($(this).data("valorcuota") != "") ? parseFloat(limiparPuntos($(this).data("valorcuota") + "")) : 0;
                indice = cuota / saldoReal;
                obligacionesAlDia.push([indice, $(this).data("obligacion"), $(this).data("valorcuota"), format_miles(parseInt(saldo))]);
                $("#Sumatoria" + $(this).data("obligacion")).data("compra", "S");
                $(".compras" + $(this).data("obligacion")).html("Si");
            }


            if ($(this).data("compra") == "S") {
                sumaTotalSaldo = sumaTotalSaldo + parseInt(saldo);
                sumaTotalCuota = sumaTotalCuota + parseFloat(limiparPuntos($(this).data("valorcuota") + ""));

            }

        })//each

        //$("#sumaComprasSaldo").html(format_miles(sumaTotalSaldo));
        //$("#sumaComprasCuotas").html(format_miles(sumaTotalCuota));



        obligacionesAlDia.sort(function (a, b) {
            return b[0] - a[0];
        });

        for (i = 0; i < obligacionesAlDia.length; i++) {
            compras = parseFloat(limiparPuntos($("#DisponibleConCompras").html()));
            capacidad = parseFloat(limiparPuntos($("#totalCalculoCapacidad").html()));
            desembolso = parseInt(limiparPuntos($("#Deselbolso").html()));
            if (compras > capacidad || desembolso < sumaTotalSaldo) {
                $("#Sumatoria" + obligacionesAlDia[i][1]).data("compra", "N");
                $(".compras" + obligacionesAlDia[i][1]).html("No");
                sumaTotalSaldo = sumaTotalSaldo - parseFloat(limiparPuntos(obligacionesAlDia[i][3] + ""));
                sumaTotalCuota = sumaTotalCuota - parseFloat(limiparPuntos(obligacionesAlDia[i][2] + ""));
                $("#sumaComprasSaldo").html(format_miles(sumaTotalSaldo));
                $("#sumaComprasCuotas").html(format_miles(sumaTotalCuota));
                calculoCapacidad();
            }
        }

        $("#sumaComprasSaldo").html(format_miles(sumaTotalSaldo));
        $("#sumaComprasCuotas").html(format_miles(sumaTotalCuota));
        desembolso = parseInt(limiparPuntos($("#Deselbolso").html()));
        if (desembolso < sumaTotalSaldo || desembolso == 0) {
            $("#DesicionEstudio").html("NO");
        } else {
            $("#DesicionEstudio").html("SI");
        }

        calculoCapacidad();
        $(this).html("GUARDAR VIABILIZACIÓN");
        $(this).attr("id", "btn_save_viabilizar");

    })

    $(document).on("click", "#btn_save_viabilizar", function () {
        guardarEstudio("VIAB");
    })
}
function miniCalculadora() {
    $(document).on("focusout", "#mcIngreso", function () {
        if($(this).val().length > 5){
            calcularNewCalculadora();
        }
    });
    $(document).on("keyup", "#mcEgreso", function () {
        if($(this).val().length > 5){
            calcularNewCalculadora();
        }
    });

    $(document).on('change', '#regimenEspecial', function () {
        calcularNewCalculadora();
    });

    $(document).on("click", ".dspModalMiniCalculadora", function () {
        console.log($("#cupo_egreso").val(), $("#cupo_ingreso").val());
        $('#mcEgreso').val($("#cupo_egreso").val());
        $('#mcIngreso').val($("#cupo_ingreso").val());
        calcularNewCalculadora();
        $("#modalEstudioMiniCalculadora").modal("show");
    })

    $(document).on("click", "#sendMiniCalculadora", function () {
        $("#cupo_egreso").val($('#mcEgreso').val());
        $("#cupo_ingreso").val($('#mcIngreso').val());
        $("#cupo_ley1527").val($('#mcLey1527').val());
        $("#cupo_cupo").val($('#mcCupo').val());
        
        
        var ingreso = ($("#mcIngreso").val() != "") ? limiparPuntos($("#mcIngreso").val()) : 0;    
        var cupo = ($("#mcCupo").val() != "") ? limiparPuntos($("#mcCupo").val()) : 0;        

        $("#EstimadoIngresoFamiliar").val(format_miles(parseInt(ingreso - (ingreso * 0.40))));
        $("#EstudioIngresoCapacidadModal").val(format_miles(parseInt(ingreso)));
        $("#EstudioIngresoCapacidad").val(ingreso);
        $("#DisponibleOriginal").html(format_miles(cupo));
        
        $.when(calculoCapacidad()).then(function(){
            $("#modalEstudioMiniCalculadora").modal("hide");
        });
    })
}
function showCupoTotal(json){
    $('#loadingCupo').hide();
    $('.cupoDiv').show();

    $('#mcIngreso').prop('disabled', false);
    cupo_real = json.cupo - parseInt(limiparPuntos($('#mcEgreso').val())) + json.salud_pension;
    $("#mcLey1527").val(format_miles(json.cupo));
    $('#mcDescuentosLey').val(format_miles(json.descuentos_ley));
    $('#mcCupo').val(format_miles(cupo_real));
}
function calcularNewCalculadora() {

    if ($("#mcIngreso").val() != "") {
        $('#loadingCupo').show();
        $('.cupoDiv').hide();
        $('#mcIngreso').prop('disabled', true);        
        let estudio =  String(window.location.href).split('/').slice(-1)[0];
        ingreso = $('#mcIngreso').val();
        if(ingreso.length > 5){
            egreso = $('#mcRetencion').val();
            pagaduria = $('#idPagaduria').val();
            if ($('#regimenEspecial').is(":checked")) {
                var regEspecial = "on"
            } else {
                var regEspecial = "off"
            }
            console.log(ingreso,egreso,pagaduria,regEspecial);
            calcularCupoDesprendible(ingreso,egreso,pagaduria,regEspecial,showCupoTotal)
        };
    }
}

function validateEmpty(info){
    return (info != null && info != "")? info : false;
}
function procesoBancos() {
    console.log("Proceso bancos...");
    //Se obtiene la informacion de las variables que se van a utilizar
    var entidadesDondeQuedoEnMora = validateEmpty($("#entidadesDondeQuedoEnMora").val());
    var fechaNacimiento = validateEmpty($(".calculeEdad").val());
    var puntajes= JSON.parse(validateEmpty($("#pv").val()));
    var bancos = validateEmpty($("#BancosEncontrados").val());
    var cuota = getNumber($("#ROcuota").val());
    var tipoContrato = validateEmpty($(".tiposDeContrato").val());
    var cargo = validateEmpty($("#cargo").val());
    var Antiguedad = getNumber($("#tiempoTrabajadoAlDia").html());
    var valorCredito = getNumber($("#ROcredito").val());
    var bancosDisponibles = [];
    var cantidadBancosDisponibles = 0;
    var existeBancoSeleccionado = false;
    var stringMensajeError = "";
    const edad = parseInt($('#EdadUser').html());

    //Se valida que obtenga la informacion basica
    if ( bancos != false && cuota != 0 && tipoContrato != false && valorCredito > 0) {
        //Convertimos los bancos en un objeto
        infoBancos = JSON.parse(bancos);
        //Se obtiene el listado de bancos encontrados
        bancosEncontrados = infoBancos.bancos;
        //Se recorren los bancos
        for (var i = 0; i < bancosEncontrados.length; i++) {
            stringMensajeError += "\n *****************************************************************";
            stringMensajeError += "\n BANCO EN CUESTION "+bancosEncontrados[i].Descripcion;            
            //Si hay puntajes definidos los validamo

            if(puntajes != false){
                if(puntajes.PuntajeData <  bancosEncontrados[i].PuntajeData || puntajes.PuntajeCifin < bancosEncontrados[i].PuntajeCifin ){
                    stringMensajeError+= "\n* No le dieron los puntajes con el banco.[PCifin Requerido "+bancosEncontrados[i].PuntajeCifin+", PCifin User "+puntajes.PuntajeCifin +" | PData Requerido "+bancosEncontrados[i].PuntajeData+", PData User "+puntajes.PuntajeData+"]";
                    continue;
                }
            }
            
            //Procedemos a verificar si No recibe castigos y moras, si existen entidades cerradas en donde el usuario quedo en mora y si el banco tiene parametrizado entidades para descartar cuando se cae en mora
            if(bancosEncontrados[i].CastigoMora == "N" && entidadesDondeQuedoEnMora != false && bancosEncontrados[i].Entidades != null && bancosEncontrados[i].Entidades != ""){
                //Creamos los objetos que vamos a necesitar
                var objetoEntidades = JSON.parse(entidadesDondeQuedoEnMora);
                var entidadesVigiladasBanco = JSON.parse(bancosEncontrados[i].Entidades);
                //Variable para controlar si se sigue o no con el banco en cuestion
                var encontroCoincidencias = false;
                //recorremos las entidades que el banco descarta si se quedo en mora antes                
                for(var c = 0; c < entidadesVigiladasBanco.length; c++){
                    //Buscamos en la lista de entidades en donde el usuario quedo en mora si hay alguna que sea igual a las que el banco descartaria, si es asi activamos la variable para saltar el banco
                     if(objetoEntidades.indexOf(entidadesVigiladasBanco[c])  >= 0){
                        stringMensajeError+= "\n* Quedo en mora con: "+entidadesVigiladasBanco[c];
                        encontroCoincidencias = true;
                    }
                }
                //Si se autoriza el salto del banco, lo saltamos
                if(encontroCoincidencias){
                    continue;
                }
               
            }else{
                    stringMensajeError+= "\n* Si recibe castigos y mora";
            }            
            
            //Si no tiene politicas lo ignoramos
            if (bancosEncontrados[i].Politica == "") {
                stringMensajeError+= "\n* No tiene politicas";                
                continue;
            }
            
            politica = JSON.parse(bancosEncontrados[i].Politica);
            politica.sort(ordenarPorMonto);            
            
            for (var j = 0; j < politica.length; j++) {
                stringMensajeError+= "\n******************* Politica # "+j;
                //Validamos que las politicas tengan la informacion basica para seguir con el proceso de validaciones
                if (    politica[j].Nombramiento == tipoContrato 
                    && politica[j].Cargo == cargo                     
                    && parseFloat(politica[j].Tasa) > 0 
                    && parseInt(politica[j].Plazo) > 0) {
                
                    //Si la antiguedad del cliente es menor a la minima exigida por el banco, entonces lo descartamos
                    if(parseInt(politica[j].Antiguedad) > 0 && Antiguedad < parseInt(politica[j].Antiguedad)){                        
                            stringMensajeError+= "\n* No cuenta con la antiguedad suficiente";                            
                            continue;                        
                    }
                    if(parseInt(politica[j].EdadInclusion) > 0 && parseInt(politica[j].EdadInclusion) < edad){
                        stringMensajeError+= "\n* La edad es mayor a la edad de inclusión";
                        continue;
                    }
                
                                    
                    //Validamos que exista politica de edad
                    if(politica[j].Edad != "" && parseInt(politica[j].Edad) > 0){
                        //Ahora validamos que exista la fecha de nacimiento del cliente, si no es asi lo descartamos
                        if(fechaNacimiento != false ){
                            //dividimos la fecha 0 => annio, 1 => mes, 2 => dia
                            var arrayFechaNacimiento = fechaNacimiento.split("-");
                            //Calculamos en que año el cliente cumplira la edad maxima de la politica
                            var annioCumpleAnnios70 = parseInt(politica[j].Edad) + parseInt(arrayFechaNacimiento[0]);
                            //obtenemos los meses que faltan para que el cliente cumpla esa edad
                            var meses = monthDiff(annioCumpleAnnios70, parseInt(arrayFechaNacimiento[1]), parseInt(arrayFechaNacimiento[2]));
                            //se valida que no pueda pasarse del plazo maximo de la politica
                            var plazoCalculo = (meses <= politica[j].Plazo)? meses : parseInt(politica[j].Plazo);
                            politica[j].Plazo = plazoCalculo;

                            /*//Obtenemos la fecha actual
                            var fechaActual = new Date();
                            //Le sumamos el plazo del banco
                            var fechaProyectada = fechaActual.setMonth(parseInt(politica[j].Plazo));
                            //Obtenemos la edad que tendria el usuario al cumplir con el plazo                            
                            var edadProyectada = getEdad(fechaNacimiento, fechaActual);
                            //Validamos si la edad que tendra el usuario es mayor a la permitida por el banco entonces lo descartamos
                            if(edadProyectada > parseInt(politica[j].Edad)){
                                stringMensajeError+= "\n* No cuenta con la edad proyectada: [Limite:"+politica[j].Edad+", edad: "+edadProyectada+" - plazo: "+politica[j].Plazo+"]";
                                continue;
                            }*/
                        }else{
                            stringMensajeError+= "\n* No tiene fecha de nacimiento";
                            continue;
                        }
                    }else{
                        var plazoCalculo = politica[j].Plazo;
                    }
                
                    if(valorCredito <= politica[j].Monto){                        
                        //informacionCredito = calcularCreditos(cuota, parseFloat(politica[j].Tasa / 100), parseInt(politica[j].Plazo));
                        informacionCredito = calcularCreditos(cuota, parseFloat(politica[j].Tasa / 100), plazoCalculo);
                        
                        //validamos si este banco tiene descuento configurado, si es asi se procede a aplicarlo
                        if(bancosEncontrados[i].DtoInicial != "" && parseInt(bancosEncontrados[i].DtoInicial) > 0){
                            //Se calcula el valor del credito con el descuento aplicado
                            var creditoConDescuento = informacionCredito.valorCreditoReal - (informacionCredito.valorCreditoReal * (bancosEncontrados[i].DtoInicial / 100));
                            informacionCredito.valorCreditoReal = creditoConDescuento;
                        }
                        
                        informacionCredito.valorCreditoReal = (informacionCredito.valorCreditoReal > parseInt(politica[j].Monto)) ? parseInt(politica[j].Monto) : informacionCredito.valorCreditoReal;    
                        if(Math.round(informacionCredito.valorCreditoReal) >= valorCredito){
                            bancosDisponibles.push({
                                Id: bancosEncontrados[i].Id,
                                Descripcion: bancosEncontrados[i].Descripcion,
                                CastigoMora: bancosEncontrados[i].CastigoMora,
                                Politica: politica[j],
                                PazSalvo: bancosEncontrados[i].PazSalvo,
                                Tasa: bancosEncontrados[i].Tasa,
                                InfoCredito: informacionCredito
                            });
                            cantidadBancosDisponibles++;
                            if(parseInt(infoBancos.bancoSeleccionado) == parseInt(bancosEncontrados[i].Id)){
                                existeBancoSeleccionado = true;
                            }
                        }else{
                             stringMensajeError+= "\n* No le alcanzo: [prestado: "+informacionCredito.valorCreditoReal +", valor credito: "+ valorCredito+"]";
                        }
                            break;
                    }
                   
                }else{                    
                    stringMensajeError+= "\n* puede ser nombramiento diferente: [requerida: "+politica[j].Nombramiento +", seleccionada: "+ tipoContrato +"]";
                    stringMensajeError+= "\n* cargo diferente [requerida: "+politica[j].Cargo+", seleccionada: "+ cargo +"]";
                    stringMensajeError+= "\n* no hay tasa ["+politica[j].Tasa+"]";
                    stringMensajeError+= "\n* no hay plazo ["+politica[j].Plazo+"]";                    
                }
            }            
        }
        
        if(stringMensajeError != ""){
                console.log(stringMensajeError);
        }
        
        if (bancosDisponibles.length > 0) {            
            bancosDisponibles.sort(ordenarPorValorCredito);
            var idBancoSeleccionado = (existeBancoSeleccionado)? infoBancos.bancoSeleccionado : bancosDisponibles[0].Id;
            var htmlBancos = '';            
            var valorCreditoBancoSeleccionado = 0;
            for (var k = 0; k < bancosDisponibles.length; k++) {                
                
                        htmlBancos += plantillaBanco(bancosDisponibles[k].Descripcion,
                                format_miles(Math.round(bancosDisponibles[k].InfoCredito.valorCreditoReal) + ""),
                                bancosDisponibles[k].Politica.Tasa,
                                bancosDisponibles[k].Politica.Plazo,
                                format_miles(Math.round(bancosDisponibles[k].Politica.Monto)),
                                bancosDisponibles[k].Id,
                                idBancoSeleccionado);     
                    
                            
                    if(idBancoSeleccionado == bancosDisponibles[k].Id){
                        valorCreditoBancoSeleccionado = format_miles(Math.round(bancosDisponibles[k].InfoCredito.valorCreditoReal));
                        
                    }
            }

            $("#CuotaBancos").html(format_miles(cuota));
            $("#cantidadBancosEncontrados").val(""+cantidadBancosDisponibles);
            $("#BancoSeleccionadoEstudio").val(""+idBancoSeleccionado);                        
            $("#cifraBanco").val(valorCreditoBancoSeleccionado);
            $("#containerInformacionBancos").html(htmlBancos);
            costosDeLaTransformacion();
            seleccionarEstado();
        } else {
            $("#CuotaBancos").html(format_miles(cuota));
            $("#containerInformacionBancos").html(htmlMessage());
            $("#cifraBanco").val("0");
            $("#cantidadBancosEncontrados").val(""+cantidadBancosDisponibles);
            costosDeLaTransformacion();
            seleccionarEstado();
        }
    }

}

function ordenarPorValorCredito(a, b) {
    if (a.InfoCredito.valorCreditoReal === b.InfoCredito.valorCreditoReal) {
        return 0;
    }else {
        return (a.InfoCredito.valorCreditoReal < b.InfoCredito.valorCreditoReal) ? -1 : 1;
    }
}

function ordenarPorMonto(a, b){
    if (a.Monto === b.Monto) {
        return 0;
    }else {
        return (a.Monto > b.Monto) ? -1 : 1;
    }
}

function plantillaBanco(nombreBanco, valorCredito, tasa, plazo, montoMax, idBanco, idSeleccionado) {
    return '<div class="portlet box red bancos">'
            + '<div class="portlet-title text-center">'
            + '<div class="caption">'
            + '<span class="flechaSeleccion flecha' + idBanco + '" ' + ((idBanco == idSeleccionado) ? "" : 'style="display: none"') + '><i class="fa fa-arrow-right"></i></span>'
            + '<span class="pointer nombrebanco" data-id="' + idBanco + '" data-valorcredito="' + valorCredito + '"><i class="fa fa-bank"></i><span> ' + nombreBanco + ' <span>$' + valorCredito + '</span></span></span>'
            + '</div>'
            + '<div class="tools">'
            + '<a href="javascript:;" class="collapse"></a>'
            + '</div>'
            + '</div>'
            + '<div class="portlet-body">'
            + '<div class="row">'
            + '<div class="text-center">'
            + '<span class="bold" style="display: inline-block"><label>Tasa: </label> ' + tasa + '%</span>'
            + '<span class="bold" style="margin-right: 15px;margin-left: 15px;display: inline-block;"><label>Plazo: </label> ' + plazo + '</span>'
            //+ '<span class="bold" style="display: inline-block"><label>Monto Max:</label> $' + montoMax + '</span>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
}

function htmlMessage() {
    return '<div class="alert alert-warning" role="alert">'
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '<strong>Mensaje:</strong>'
            + '<p>No se encontrarón bancos que cumplan con las caracteristicas del estudio</p>'
            + '</div>';
}
function cambioDeBanco() {
    $(document).on("click", ".nombrebanco", function () {
        idBanco = $(this).data("id");
        valorCredito = $(this).data("valorcredito");
        $(".flechaSeleccion").hide();
        $(".flecha" + idBanco).show();
        $("#BancoSeleccionadoEstudio").val(idBanco);
        $("#cifraBanco").val(valorCredito);
        costosDeLaTransformacion();
        seleccionarEstado();

    })
}
function eventosBanco() {
    $(document).on("focusout", "#ROcuota", function () {
//        procesoBancos();
    })
}
function setCalendario(elemento){   
    $(elemento).datepicker({changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd'});
}
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
            setCalendario("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud"+padre);
            setCalendario("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega"+padre);
            
            if($(this).val() == 'PSOL'){
                $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudPYS").show("slow", function(){
                    $("#modalAdjunto" + padre + " .containerSolicitado").show("slow");
                });
            }
            
            if($(this).val() == 'CSOL'){
                $("#modalAdjunto" + padre + " .containerSolicitado .containerAdjuntoSolicitudCDD").show("slow", function(){
                    $("#modalAdjunto" + padre + " .containerSolicitado").show("slow");
                });
            }            

        } else if ($("#modalAdjunto" + padre + " .containerSolicitado").is(":visible")) {
            $("#modalAdjunto" + padre + " .containerSolicitado").hide();
        }

        if ($(this).val() == 'CRAD' || $(this).val() == 'PRAD') {
            $("#modalAdjunto" + padre + " .btnGuardar").hide();        
            
            setCalendario("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion"+padre);
            setCalendario("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento"+padre);
            
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

        $("#fechaSolicitud"+padre).val("");
        $("#fechaEntrega"+padre).val("");
        $("#fechaRadicacion"+padre).val("");
        $("#fechaVencimiento"+padre).val("");
    });
}
function validateFechasSolicitud(){    
    $(document).on("change", ".fechasAdjuntoSolicitud", function () {
        var padre = $(this).data("id");
        var input = $("#modalAdjunto" + padre + " .ComponentArchivo");
        
            if ($("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud"+padre).val() != "" && $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega"+padre).val() != "") {
                if ($("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud"+padre).val() <= $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega"+padre).val()) {
                    input.filestyle('disabled', false);
                }else{
                    input.filestyle('disabled', true);
                    displayMessageMini("La fecha de Solicitud no puede ser mayor a la fecha de Entrega");
                }
            } else{
                input.filestyle('disabled', true);
            }             
    })
}
function ValidateFechasContent() {
    $(document).on("change", ".fechasAdjunto", function () {
        var padre = $(this).data("id");
        var input = $("#modalAdjunto" + padre + " .ComponentArchivo");

        if ($("#modalAdjunto" + padre + " .containerRadicada .containerFechaVencimiento").is(":visible")) {
            if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion"+padre).val() != "" && $("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento"+padre).val() != "") {
                if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion"+padre).val() <= $("#modalAdjunto" + padre + " .containerRadicada #fechaVencimiento"+padre).val()) {
                    input.filestyle('disabled', false);
                } else {
                    input.filestyle('disabled', true);
                    displayMessageMini("La fecha de radicación no puede ser mayor a la fecha de vencimiento");
                }
            } else {
                input.filestyle('disabled', true);
            }
        } else {
            if ($("#modalAdjunto" + padre + " .containerRadicada #fechaRadicacion"+padre).val() == "") {
                input.filestyle('disabled', true);
            } else {
                input.filestyle('disabled', false);
            }
        }
    })
}
function saveFechasSolicitud(input, data, dataPHP) {   
    
    var padre = data.idPadre;
    var fechaSolicitud = $("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud"+padre).val();
    var fechaEntrega = $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega"+padre).val();
    
    var estado = $("#modalAdjunto" + padre + " .sltAccionObligacion").val();    
    var url = $("#dominioPrincipal").val();

    $.post(url + "Estudio/GuardarFechas",
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
                $("#Enlace"+padre+" span").removeClass("fa-arrow-up");
                $("#Enlace"+padre+" span").addClass("fa-paperclip");

                seleccionarEstado();
            }else{
                displayMessageMini(resultado.MENSAJE);
            }
        });

}
function updateInfoAdjuntos(input, data, dataPHP) {
    
    if($("#Sumatoria" + data.idPadre).parent().hasClass("danger")){
        $("#Sumatoria" + data.idPadre).parent().removeClass("danger");
    }
    var fechaRadicacion = $("#modalAdjunto" + data.idPadre + " .containerRadicada #fechaRadicacion"+data.idPadre).val();
    var fechaVencimiento = null;
    if ($("#modalAdjunto" + data.idPadre + " .containerRadicada .containerFechaVencimiento").is(":visible")) {
        fechaVencimiento = $("#modalAdjunto" + data.idPadre + " .containerRadicada #fechaVencimiento"+data.idPadre).val();
    }
    var valorCertificado = null;
    if ($("#modalAdjunto" + data.idPadre + " .containerRadicada .containerValorCertificado").is(":visible")) {
        valorCertificado = $("#modalAdjunto" + data.idPadre + " .containerRadicada #valorCertificado"+data.idPadre).val();
    }

    var estado = $("#modalAdjunto" + data.idPadre + " .sltAccionObligacion").val();
    var url = $("#dominioPrincipal").val();

    $.post(url + "Estudio/guardarFechasRadicacion",
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
            }else if(data.tipoAdjunto == "PYS"){
                if(resultado.tienePazSalvo == false){
                    console.log("Entro a false editable");
                    $(".compras" + data.idPadre).editable("setValue", "N");
                    $(".valorCuota" + data.idPadre).editable("setValue", 0);
                    $(".saldoActual" + data.idPadre).editable("setValue", 0);
                    $(".estadoCuenta" + data.idPadre).editable("setValue", "PYS");
                    $(".estadoCuenta" + data.idPadre).editable("option","disabled", true);
                }

                $("#Sumatoria" + data.idPadre).data("pazsalvo", 1);
            }
            
            if(valorCertificado != null){                
                var totalSuma =($("#sumaComprasSaldo").html() == "")? 0 : parseInt(limiparPuntos($("#sumaComprasSaldo").html()));
                var valorCertificadoAnterior = ($("#Sumatoria" + data.idPadre).data("valorsaldo") == "")? 0 : parseInt(limiparPuntos($("#Sumatoria" + data.idPadre).data("valorsaldo")));
                var diferencia = parseInt(limiparPuntos(valorCertificado)) - valorCertificadoAnterior;
                var nuevoTotal = diferencia + totalSuma;                                
                $("#Sumatoria" + data.idPadre).data("valorsaldo", valorCertificado);                
                $(".saldoActual" + data.idPadre).editable("setValue", valorCertificado);
                var saldoOriginal = $("#Sumatoria" + data.idPadre).data("valorsaldooriginal");
                if(parseInt(limiparPuntos(valorCertificado)) - parseInt(saldoOriginal) == 0){
                   $(".saldoActual" + data.idPadre).css("color", "black"); 
                }else{
                    $(".saldoActual" + data.idPadre).css("color", "red"); 
                }
                
                $("#sumaComprasSaldo").html(format_miles(nuevoTotal));
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
            
            if($("#Enlace"+data.idPadre+" span").hasClass("fa-arrow-up")){
                $("#Enlace"+data.idPadre+" span").removeClass("fa-arrow-up");
                $("#Enlace"+data.idPadre+" span").addClass("fa-paperclip");                
            }
            if(resultado.tienePazSalvo == false){
                calculoCapacidad();
            }

            seleccionarEstado();
        }else{
            displayMessageMini(resultado.MENSAJE);
        }

    });

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
                            if (infoAdjunto.tipoAdjunto == "CDD") {
                                $("#Sumatoria" + infoAdjunto.id_obligacion).data("adjunto", 0);
                            }
                            $(delparent).remove();
                            if (infoAdjunto.tipoAdjunto == "CDD") {                                
                                    $("#modalAdjunto" + infoAdjunto.id_obligacion + " .optionCertificadosDeuda").html('<option value="CSOL">Solicitada</option>');
                                    $("#modalAdjunto" + infoAdjunto.id_obligacion + " .optionCertificadosDeuda").show();                                
                            } else if (infoAdjunto.tipoAdjunto == "PYS") {
                                    $("#modalAdjunto" + infoAdjunto.id_obligacion + " .containerPazYSalvo").html('<option value="PSOL">Solicitada</option>');
                                    $("#modalAdjunto" + infoAdjunto.id_obligacion + " .containerPazYSalvo").show();
                            }
                            
                            if($("#containerListaAdjuntosObligaciones"+infoAdjunto.id_obligacion).html().trim().length == 0){
                                if($("#Enlace"+infoAdjunto.id_obligacion+" span").hasClass("fa-paperclip")){
                                    $("#Enlace"+infoAdjunto.id_obligacion+" span").removeClass("fa-paperclip");
                                    $("#Enlace"+infoAdjunto.id_obligacion+" span").addClass("fa-arrow-up");  
                                }
                            }
                            calculoCapacidad();
                            seleccionarEstado();
                        }
                        
                        displayMessageMini(resultado.Message);

                    });
                }
            }
        });
    })

}

function guardarFechasSolicitado() {
    $(document).on("click", ".btnGuardar", function () {
        var padre = $(this).data("id");
        var fechaSolicitud = $("#modalAdjunto" + padre + " .containerSolicitado #fechaSolicitud"+padre).val();
        var fechaEntrega = $("#modalAdjunto" + padre + " .containerSolicitado #fechaEntrega"+padre).val();
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

        $.post(url + "Estudio/GuardarFechas",
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

            seleccionarEstado();
            displayMessageMini(resultado.MENSAJE);
        });



    })
}
/*definicion de estado del estudio*/


function isNoViable() {
//    var saldo = getNumber($("#DeselbolsoCliente").html());
    var saldo = $("#DeselbolsoClienteReal").val();
    var tipoContrato = $(".tiposDeContrato").val();
    var valorCredito = getNumber($("#ROcredito").val());
    var creditoBanco = getNumber($("#cifraBanco").val());

    if (saldo < 0 || tipoContrato == "OTHER" || valorCredito > creditoBanco) {
//    if (saldo < 0 || tipoContrato == "OTHER") {
        return true;
    } else {
        return false;
    }
}
function isViable() {
    var desembolso = getNumber($("#Deselbolso").html());
    var bancosCompradores = $("#cantidadBancosEncontrados").val();
    var datos = JSON.parse($("#parameters").val());
    if (!isNoViable() && desembolso > 0 && parseInt(bancosCompradores) >= parseInt(datos.bancosDisponibles)) {
        return true;
    } else {
        return false;
    }
}
function isFirmado() {
    var adjuntoLbzFirmada = parseInt($(".adjuntoLbzFirmada").val());
    var adjuntoAutorizacionConsulta = parseInt($("#adjuntoAutorizacionConsulta").val());
    
    if (!isNoViable() && isViable() && adjuntoLbzFirmada > 0 && adjuntoAutorizacionConsulta > 0) {
        return true;
    } else {
        return false;
    }
}

function isComite(){
    if (!isNoViable() && isViable() && isFirmado() && validarCertificacionesDeuda()) {
        return true;
    } else {
        return false;
    }
}
function isVisado() {
    
    var aprobado = parseInt($("#aprobado").val());
    
    if (!isNoViable() && isViable() && isFirmado() && isComite() && aprobado > 0) {
        return true;
    } else {
        return false;
    }
}
function validarCertificacionesDeuda() {
    var todasTienenCertificaciones = true;
    $(".listaObligacionesFormulaEstudio").each(function (index) {
        var compra = $(this).data("compra");
        if (compra == "S") {
            var certificacionDeuda = parseInt($(this).data("adjunto"));
            var tipoObligacion =  $(this).data("estadoobligacion");
            if (certificacionDeuda <= 0 && tipoObligacion !== "PYS") {
                console.log($(this).data("obligacion"));
                todasTienenCertificaciones = false;
            }
        }
    })
    return todasTienenCertificaciones;
}
function isTesoreria() {
    var cuotaVisado = getNumber($("#cuotaVisado").html());
    var adjuntoVisado = parseInt($(".adjuntoVisado").val());
    if (!isNoViable() && isViable() && isFirmado() && isComite() && isVisado() && cuotaVisado > 0 && adjuntoVisado > 0) {
        return true;
    } else {
        return false;
    }
}
function validarSoportesPago() {
    var todasTienenSoporte = true;
    $(".listaObligacionesFormulaEstudio").each(function (index) {
        var compra = $(this).data("compra");
        if (compra == "S") {
            var soporte = parseInt($(this).data("soporte"));
            if (soporte <= 0) {
                todasTienenSoporte = false;
            }
        }
    })
    return todasTienenSoporte;
}
function isCartera() {
    if (!isNoViable() && isViable() && isFirmado() && isComite() && isVisado() && isTesoreria() && validarSoportesPago()) {
        return true;
    } else {
        return false;
    }
}

function seleccionarEstado() {
    console.log("Seleccionando Estado");
    var estadoActual = $("#EstadoEstudio").val();

    if(estadoActual == "CAR" || estadoActual == "PRT" || estadoActual == "BAN" || estadoActual == "DES" || estadoActual == "NEG" || estadoActual == "PRE" || estadoActual == "TES"){
        return true;
    }
    if(estadoActual == "PEN"){

        if (isViable() && estadoActual != "VIA"){
            $("#EstadoEstudioLbl").html("Viable");
            $("#EstadoEstudio").val("VIA");
            //cambiarEstado("VIA");
            definicionSelectAccion("VIA");
        }else {
            console.log("Revisar estudio, no se puede hacer la transición de pendiente a viable");
        }

    }else {

        if (isTesoreria()) {
            if (estadoActual != "TES") {
                $("#EstadoEstudioLbl").html("Tesoreria");
                $("#EstadoEstudio").val("TES");
                //cambiarEstado("TES");
                definicionSelectAccion("TES");
            }
        } else if (isVisado()) {
            if (estadoActual != "VIS") {
                $("#EstadoEstudioLbl").html("Visado");
                $("#EstadoEstudio").val("VIS");
                //cambiarEstado("VIS");
                definicionSelectAccion("VIS");
            }
        } else if (isComite()) {
            if (estadoActual != "COM") {
                $("#EstadoEstudioLbl").html("Comite");
                $("#EstadoEstudio").val("COM");
                //cambiarEstado("COM");
                definicionSelectAccion("COM");
            }
        } else if (isFirmado()) {
            if (estadoActual != "FIR") {
                $("#EstadoEstudioLbl").html("Firmado");
                $("#EstadoEstudio").val("FIR");
                //cambiarEstado("FIR");
                definicionSelectAccion("FIR");
            }
        } else if (isViable()) {
            if (estadoActual != "VIA") {
                $("#EstadoEstudioLbl").html("Viable");
                $("#EstadoEstudio").val("VIA");
                //cambiarEstado("VIA");
                definicionSelectAccion("VIA");
            }
        } else if (isNoViable()) {
            if (estadoActual != "NVI") {
                $("#EstadoEstudioLbl").html("No Viable");
                $("#EstadoEstudio").val("NVI");
                //cambiarEstado("NVI");
                definicionSelectAccion("NVI");
            }
        } else {
            console.log("No cumplio ninguna caracteristica asi que el estado sigue siendo el mismo");
        }
    }
}


function definicionSelectAccion(estado){

    var html = "";
    $("#accionEstudio").html("");
    var dias_creacion= $("#diasCreacion").val();

    if(estado == "COM"){
        html =  "<option value='0'>Sel acción</option>" +
            "<option value='GUA'>Guardar</option>" +
            "<option value='APR'>Aprobar</option>" +
            "<option value='NEG'>Negado</option>" +
            "<option value='PEN'>Pendiente</option>" +
            "<option value='DES'>Desistio</option>";
        $("#accionEstudio").html(html);

    }else if(estado == "NEG" && dias_creacion<=40){

        html =  "<option value='0'>Sel acción</option>" +
            "<option value='GUA'>Guardar</option>" +
            "<option value='PEN'>Pendiente</option>";

        $("#accionEstudio").html(html);

    }else if(estado != "NEG"){

        html =  "<option value='0'>Sel acción</option>" +
            "<option value='GUA'>Guardar</option>" +
            "<option value='NEG'>Negado</option>" +
            "<option value='PEN'>Pendiente</option>" +
            "<option value='DES'>Desistio</option>";

        $("#accionEstudio").html(html);

    }else{
        $('#accionEstudio').hide();
    }
}

function cambiarEstado(estado){
                var idEstudio = $("#Identificacion_Estudio").val();
                var url = $("#dominioPrincipal").val();
                $.post(url + "Estudio/cambiarEstado",
                        {
                            idEstudio: idEstudio,
                            estado: estado,
                            _token: $("input[name=_token]").val()
                        }, function (data) {
                            console.log(data);
                });   
}

function dspModalDetalleCapacidad(){
    $(document).on("click", ".modalDetalleCapacidad", function(){
        var ingreso = getNumber($("#EstudioIngresoCapacidadModal").val());
        var consumoAutonomo = getNumber($("#porcentajeGastoFijo").val());
        
        var resultado = ingreso * (consumoAutonomo/100);
        $("#EstimadoIngresoFamiliar").val(format_miles(Math.round(resultado)));
        $("#modalDetalleCapacidad").modal("show");
    })
}
function bloquearOpciones(){
    var estadoEstudio = $("#EstadoEstudio").val();
    var estadosBloqueados = ["TES", "PRT", "CAR"];
    
    if(estadosBloqueados.indexOf(estadoEstudio)  >= 0){
        
        bloquearElemento(".tiposDeContrato");
        bloquearElemento("#FechaNacimientoPlazo");
        bloquearElemento("#fecha_inicio_contrato");        
        bloquearElemento("#asegurado");
        bloquearElemento("#mcIngreso");
        bloquearElemento("#mcEgreso");
        bloquearElemento("#porcentajeGastoFijo");
        $("#formularioIngresosAdicionales").hide(); //Se oculta el formulario para adicionar ingresos adicionales que esta en la modal
        $(".nombrebanco").removeClass("nombrebanco"); //esto inactiva la posibilidad de seleccionar otro banco, ya que esta clase es la que tiene el evento click cuando se seleccionaba otro banco
        bloquearElemento("#ROtasa");
        bloquearElemento("#ROplazo");
        bloquearElemento("#ROcuota");        
        $('#cuotaVisado').editable('option', 'disabled', true);        
        $('.llaveLock').editable('option', 'disabled', true);
        $('.container-option-CDD-PYS').remove();
        $('.modalCargaCDD-PYS table .fa-remove').remove();
        $('#obligacionesCompletas .desplegarModalCDD-PYS').remove();
        
    }
    
}
function bloquearElemento(selector){
    $(selector).attr("disabled", true);
    $(selector).attr("readonly", true);
    $(selector).addClass("lockedFocus");
    
}
function bloqueando(){
    $(document).on("focus", ".lockedFocus", function(){
        $(this).attr("disabled", true);
        $(this).attr("readonly", true);
    })
}
function desplegarDetalleModalCerradas(){
    $(document).on("click", ".desplegarDetalleCerradas", function(){
        var identificador = $(this).data("target");
        $("#modalObligacionesCerradas").modal("hide");
        $(identificador).modal("show");
    })
    
     $(document).on("hidden.bs.modal", ".grupoModalesDetalleCerradas", function () {
        $("#modalObligacionesCerradas").modal("show");
    })    
}
function desplegarDetalleModalInhabilitadas(){
    $(document).on("click", ".desplegarDetalleInhabilitadas", function(){
        var identificador = $(this).data("target");
        $("#modalObligacionesInhabilitadas").modal("hide");
        $(identificador).modal("show");
    })
    
     $(document).on("hidden.bs.modal", ".grupoModalesDetalleInhabilitadas", function () {
        $("#modalObligacionesInhabilitadas").modal("show");
    })    
}
function reemplazarTablaVisado(input, datos, tabla){
    if(datos.tipoAdjunto == "SVI"){
        $(".solicitudVisado").remove();
        $(".container-Visado-Solicitud").remove();
    }else if(datos.tipoAdjunto == "VIS"){
        $(".container-select-visado").remove();
        $(".container-Visado-Radicacion").remove();
    }
    $(".container-tabla-adjuntos-visado").html(tabla);
    seleccionarEstado();
}
function accionVisado(){
    $(document).on("change", "#optionVisado", function(){
        if($(this).val() == "SOL"){            
            $(".container-Visado-Solicitud").show("slow");
            $(".container-Visado-Radicacion").hide("slow");
        }else if($(this).val() == "RAD"){
            $(".container-Visado-Solicitud").hide("slow");
            $(".container-Visado-Radicacion").show("slow");
        }else{
            $(".container-Visado-Solicitud").hide("slow");
            $(".container-Visado-Radicacion").hide("slow");
        }
        
    })
}
function eventoCargo(){
    $(document).on("change", "#cargo", function(){
        procesoBancos();
    })
}
function temporalCostos(){
    $(document).on("focusout", "#CMcostos", function(){
        $("#valorTotal").html("$"+$(this).val());
        $.when(algoritmoCalculoCostosAndBeneficios()).then(calcularDesembolsoAndSaldo());
    })
}

function refrescarBancos(){
    $(document).on("click", ".refrescarBancos", function(){
        var idEstudio = $(this).data("idestudio");
        $.post(window.origin + "/Estudio/actualizarBancos", {idEstudio: idEstudio, _token: $("input[name=_token]").val()}, function (data) {
            $("#BancosEncontrados").val(JSON.parse(data));
            procesoBancos();
        });
    })
}
function datajuridico() {
    $(document).on("click", ".getProcesosJuridicos", function(){
        var options = $(this).data("options");
        
        bootbox.confirm({
            message: "\u00BFRealmente desea consultar el Data Juridico de este usuario?",
            buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-danger'},
                cancel: {
                    label: 'No',
                    className: 'btn-default'}},
            callback: function (resultado) {
                if (resultado) {
                    $.post(options.url, {idValoracion: options.idValoracion, cedula: options.cedula, _token: $("input[name=_token]").val()}, function (data) {
                        $("#container_procesosJuridicos").html(data);
                        displayMessageMini("Consulta Exitosa");
                    });
                }
            }
        });
        
    });
}

$(function () {
    $(function () {
        $('[data-toggle="popover"]').popover()
    });

    iniciarMiles();
    inputMilesEditable();
    iniciarSwitches();
    tipoCompraCartera();
    agregarCompraCartera();
    iniciarInputEditable();
//    eventoKeyUp();
    addIngresosAdicionales();
    deleteIngresosAdicionales();
    cerrarModalPadre();
    changeTiposDeContrato();
    generateEdadByFecha();
    calculoTiempoContrato();
    funcionalidadParaAsegurados();
    eventosReiniciarFormula();
    keyUpCuotaOperacion();
    clickGuardarEstudio();
    eventoModalCostos();
    buttonViabilizar();
    miniCalculadora();
    modalAdjuntos();
    deleteAdjuntos();
    ValidateFechasContent();
    validateFechasSolicitud();
    guardarFechasSolicitado();
    refrescarBancos();
    desplegarDetalleModalCerradas();
    desplegarDetalleModalInhabilitadas();
//    agregarDatePicker();
    dspModalDetalleCapacidad();
    dspValoracion();
    editModalValoracion();
    accionVisado();
    eventoCargo();

    var estadoEstudio = $("#EstadoEstudio").val();
    if (costos_data.hasOwnProperty('totalCostosV')){
        start = parseFloat(costos_data.ajusteCostos);
    }else{

    }
    //Si el estudio esta radicado debe calcularse la capacidad para hallar la cuota y luego si calcular los bancos. Pero si ya el estudio esta en otro estado solamente se calculan los bancos porque se supone que la cuota ya fue hallada antes
    if (estadoEstudio == "RAD"){       
        tiposContratoFuncion();
        calcularMesesAsegurado();        
        //$.when(calculoCapacidad()).then(procesoBancos());
    }else{
        //procesoBancos();
    }
    
    //siempre se va a calcular la edad
     if ($("#FechaNacimientoPlazo").val() != "" || $("#FechaNacimientoPlazo").val() != "0") {
            var fechaNacimiento = new Date($("#FechaNacimientoPlazo").val());
            if (fechaNacimiento.getFullYear() < new Date().getFullYear()) {
                calculoEdad();
            }
        }
    
    
    cambioDeBanco();
    eventosBanco();    
    
    bloqueando();
    bloquearOpciones();
    temporalCostos();
    datajuridico();
});
