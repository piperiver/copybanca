
function modalCarga(texto)
{
    var modal = bootbox.dialog(
    {
        title: texto,
        message: '<p><i class="fa fa-spin fa-spinner"></i> Cargando...</p>',
        closeButton: false
    });

    return modal;
}

$(function() {
    $('.list-icons').iconpicker();
    $( ".list-icons" ).prop( "readonly", true );
});

function tabla()
{
    $('#tabla').DataTable({
                            responsive: true, //Indica que al cambiar el tamaño del navegador los registros se deben adaptar.
                            "order" : [], //Deshabilita el orden que da el DataTable
                            "columnDefs": [{ className: "dt-head-left", "targets": [ 0 ] }]
                           });
}

function iniciarTabla(){
    $('.iniciarDatatable').DataTable({
                            responsive: true, //Indica que al cambiar el tamaÃ±o del navegador los registros se deben adaptar.
                            scrollX: false,
                            "order" : [], //Deshabilita el orden que da el DataTable
                            "columnDefs": [{ className: "dt-head-left", "targets": [ 0 ] }]
                           });

}

function iniciarTablaEstudio(){
    $('.iniciarDatatableEstudio').DataTable({
                            responsive: true, //Indica que al cambiar el tamaÃ±o del navegador los registros se deben adaptar.
                            "order" : [], //Deshabilita el orden que da el DataTable
                            "columnDefs": [{ className: "dt-head-left", "targets": [ 0 ] }],
                            "bPaginate": false,
                            "paging": false
                           });

}

function resultadoEvento(data)
{
    /*bootbox.alert('Danger!!' ).
    find('.modal-content').
    css({
        'background-color': '#EF4836',
        'font-weight' : 'bold',
        color: '#F00',
        'font-size': '2em'
        });*/
    if(data.errores)
    {
        var Mensajes = "";
        for (var i=0; i<data.Mensaje.length; i++)
        {
            Mensajes += "<i class='fa fa-check-square'></i>&nbsp;&nbsp;" + data.Mensaje[i] + "<br/>";
        }
        bootbox.alert({
        message: Mensajes,
        buttons:
        {
            ok:
            {
                label: 'Entendido',
                className: 'btn-default'
            }
        }}).find('.modal-content').css({color: '#E07572'} );
    }
    else
    {
        $('#contenido').html(data.tabla);
        tabla();
        $('#ventana').modal('hide');
    }
}

function agregarDatePicker(){
    //if (navigator.userAgent.toLowerCase().indexOf('chrome') != 87){
        $('[type="date"]').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        
        $('.desplegarCalendario').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
    //}
}

function puntosMiles(){
    $(document).on("focus",  ".puntosMiles", function(){
        var cleave = new Cleave($(this), {
            numeral: true,
            delimiter: ".",
            numeralDecimalScale: 0,
            numeralPositiveOnly: true,
            numeralDecimalMark: ",",
            numeralThousandsGroupStyle: 'thousand'
        });
    })
    
    $(document).on("focus",  ".puntosMilesDecimales", function(){        
        new Cleave($(this), {
            numeral: true,
            numeralDecimalScale: 3,
            numeralDecimalMark: ',',
            delimiter: '.'
        });    
    })

}

function format_miles(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function limiparPuntos(numero){
    return numero.replace(/\./g, "");
}

function eliminarCaracteres(valor) {
    return valor.replace("$", "");
}
function getNumber(numero) {
    if (numero != "" && numero != null) {
        numero = limiparPuntos(numero + "");
        numero = parseInt(numero);
        return numero;
    } else {
        return 0;
    }
}

function SearchDatatable(){
    $('input[type="search"]').keyup(function () {
        $('.SearchDatatable').DataTable().search($(this).val()).draw();
    });
}

function ajustesModales(){
    $(".modal .modal-header h4").each(function(data, pos){
      var tituloModal = $(this).html();
      if(tituloModal != ""){
        $(this).parent().parent().find(".modal-body").prepend("<h4 class='text-center bold'>"+tituloModal+"</h4>");
        $(this).remove();
      }
    })
}

function cargadorAjax(){
     $(document).ajaxComplete(function () {
        $(".wrapper").css("opacity", "1");
        $("#cargadorAjax").remove();
    });

    $(document).ajaxStart(function () {        
        $(".wrapper").css("opacity", "0.2");
        $("body").append('<div id="cargadorAjax" style="position: fixed;z-index: 10000000000000;left: 50%;margin-left: -100px;top: 50%;margin-top: -100px;width: 200px;height: 200px;"><style type="text/css">.base{color: #060062}#cargadorAjax h1{position:absolute;font-family:"sans-serif";font-weight:600;font-size:12px;text-transform:uppercase;left:50%;top:58%;margin-left:-20px}#cargadorAjax .body{position:absolute;top:50%;margin-left:-50px;left:50%;animation:speeder .4s linear infinite}#cargadorAjax .body > span{height:5px;width:35px;background:#060062;position:absolute;top:-19px;left:60px;border-radius:2px 10px 1px 0}#cargadorAjax .base span{position:absolute;width:0;height:0;border-top:6px solid transparent;border-right:100px solid #060062;border-bottom:6px solid transparent}#cargadorAjax .base span:after{content:"";height:22px;width:22px;border-radius:50%;background:#060062;position:absolute;right:-110px;top:-16px}#cargadorAjax .base span:before{content:"";position:absolute;width:0;height:0;border-top:0 solid transparent;border-right:55px solid #060062;border-bottom:16px solid transparent;top:-16px;right:-98px}#cargadorAjax .face{position:absolute;height:12px;width:20px;background:#060062;border-radius:20px 20px 0 0;transform:rotate(-40deg);right:-125px;top:-15px}#cargadorAjax .face:after{content:"";height:12px;width:12px;background:#060062;right:4px;top:7px;position:absolute;transform:rotate(40deg);transform-origin:50% 50%;border-radius:0 0 0 2px}#cargadorAjax .body > span > span:nth-child(1),.body > span > span:nth-child(2),.body > span > span:nth-child(3),.body > span > span:nth-child(4){width:30px;height:1px;background:#060062;position:absolute;animation:fazer1 .2s linear infinite}#cargadorAjax .body > span > span:nth-child(2){top:3px;animation:fazer2 .4s linear infinite}#cargadorAjax .body > span > span:nth-child(3){top:1px;animation:fazer3 .4s linear infinite;animation-delay:-1s}#cargadorAjax .body > span > span:nth-child(4){top:4px;animation:fazer4 1s linear infinite;animation-delay:-1s}@keyframes fazer1{0%{left:0}100%{left:-80px;opacity:0}}@keyframes fazer2{0%{left:0}100%{left:-100px;opacity:0}}@keyframes fazer3{0%{left:0}100%{left:-50px;opacity:0}}@keyframes fazer4{0%{left:0}100%{left:-150px;opacity:0}}@keyframes speeder{0%{transform:translate(2px,1px) rotate(0deg)}10%{transform:translate(-1px,-3px) rotate(-1deg)}20%{transform:translate(-2px,0px) rotate(1deg)}30%{transform:translate(1px,2px) rotate(0deg)}40%{transform:translate(1px,-1px) rotate(1deg)}50%{transform:translate(-1px,3px) rotate(-1deg)}60%{transform:translate(-1px,1px) rotate(0deg)}70%{transform:translate(3px,1px) rotate(-1deg)}80%{transform:translate(-2px,-1px) rotate(1deg)}90%{transform:translate(2px,1px) rotate(0deg)}100%{transform:translate(1px,-2px) rotate(-1deg)}}#cargadorAjax .longfazers{position:absolute;width:100%;height:100%}#cargadorAjax .longfazers span{position:absolute;height:2px;width:20%;background:#060062}#cargadorAjax .longfazers span:nth-child(1){top:20%;animation:lf .6s linear infinite;animation-delay:-5s}#cargadorAjax .longfazers span:nth-child(2){top:40%;animation:lf2 .8s linear infinite;animation-delay:-1s}#cargadorAjax .longfazers span:nth-child(3){top:60%;animation:lf3 .6s linear infinite}#cargadorAjax .longfazers span:nth-child(4){top:80%;animation:lf4 .5s linear infinite;animation-delay:-3s}@keyframes lf{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf2{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf3{0%{left:200%}100%{left:-100%;opacity:0}}@keyframes lf4{0%{left:200%}100%{left:-100%;opacity:0}}</style><div class="body"><span><span></span><span></span><span></span><span></span></span><div class="base"><span></span><div class="face"></div></div></div><div class="longfazers"><span></span><span></span><span></span><span></span></div><h1 style="color: #060062; font-weight: bold">PROCESANDO</h1></div>');
//        $("body").append('<div id="cargadorAjax" style="position: fixed;z-index: 10000000000000;left: 50%;margin-left: -100px;top: 50%;margin-top: -100px;width: 200px;height: 200px;"><style type="text/css">.base{color: #b41820}body{background-color:transparent;overflow:hidden}h1.title{position:absolute;font-family:"Open Sans";font-weight:600;font-size:12px;text-transform:uppercase;left:50%;top:58%;margin-left:-20px}.body{position:absolute;top:50%;margin-left:-50px;left:50%;animation:speeder .4s linear infinite}.body > span{height:5px;width:35px;background:#b41820;position:absolute;top:-19px;left:60px;border-radius:2px 10px 1px 0}.base span{position:absolute;width:0;height:0;border-top:6px solid transparent;border-right:100px solid #b41820;border-bottom:6px solid transparent}.base span:after{content:"";height:22px;width:22px;border-radius:50%;background:#b41820;position:absolute;right:-110px;top:-16px}.base span:before{content:"";position:absolute;width:0;height:0;border-top:0 solid transparent;border-right:55px solid #b41820;border-bottom:16px solid transparent;top:-16px;right:-98px}.face{position:absolute;height:12px;width:20px;background:#b41820;border-radius:20px 20px 0 0;transform:rotate(-40deg);right:-125px;top:-15px}.face:after{content:"";height:12px;width:12px;background:#b41820;right:4px;top:7px;position:absolute;transform:rotate(40deg);transform-origin:50% 50%;border-radius:0 0 0 2px}.body > span > span:nth-child(1),.body > span > span:nth-child(2),.body > span > span:nth-child(3),.body > span > span:nth-child(4){width:30px;height:1px;background:#b41820;position:absolute;animation:fazer1 .2s linear infinite}.body > span > span:nth-child(2){top:3px;animation:fazer2 .4s linear infinite}.body > span > span:nth-child(3){top:1px;animation:fazer3 .4s linear infinite;animation-delay:-1s}.body > span > span:nth-child(4){top:4px;animation:fazer4 1s linear infinite;animation-delay:-1s}@keyframes fazer1{0%{left:0}100%{left:-80px;opacity:0}}@keyframes fazer2{0%{left:0}100%{left:-100px;opacity:0}}@keyframes fazer3{0%{left:0}100%{left:-50px;opacity:0}}@keyframes fazer4{0%{left:0}100%{left:-150px;opacity:0}}@keyframes speeder{0%{transform:translate(2px,1px) rotate(0deg)}10%{transform:translate(-1px,-3px) rotate(-1deg)}20%{transform:translate(-2px,0px) rotate(1deg)}30%{transform:translate(1px,2px) rotate(0deg)}40%{transform:translate(1px,-1px) rotate(1deg)}50%{transform:translate(-1px,3px) rotate(-1deg)}60%{transform:translate(-1px,1px) rotate(0deg)}70%{transform:translate(3px,1px) rotate(-1deg)}80%{transform:translate(-2px,-1px) rotate(1deg)}90%{transform:translate(2px,1px) rotate(0deg)}100%{transform:translate(1px,-2px) rotate(-1deg)}}.longfazers{position:absolute;width:100%;height:100%}.longfazers span{position:absolute;height:2px;width:20%;background:#b41820}.longfazers span:nth-child(1){top:20%;animation:lf .6s linear infinite;animation-delay:-5s}.longfazers span:nth-child(2){top:40%;animation:lf2 .8s linear infinite;animation-delay:-1s}.longfazers span:nth-child(3){top:60%;animation:lf3 .6s linear infinite}.longfazers span:nth-child(4){top:80%;animation:lf4 .5s linear infinite;animation-delay:-3s}@keyframes lf{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf2{0%{left:200%}100%{left:-200%;opacity:0}}@keyframes lf3{0%{left:200%}100%{left:-100%;opacity:0}}@keyframes lf4{0%{left:200%}100%{left:-100%;opacity:0}}</style><div class="body"><span><span></span><span></span><span></span><span></span></span><div class="base"><span></span><div class="face"></div></div></div><div class="longfazers"><span></span><span></span><span></span><span></span></div><h1 class="title" style="color: #b41820; font-weight: bold">PROCESANDO</h1></div>');
    });
}

function lockButtons(){
    $(document).on("click", "button.lockClick", function(){
        $(this).prop( "disabled", true );
    })
}

$(document).ready(function ()
{
    ajustesModales();
    agregarDatePicker();
    tabla();
    iniciarTabla();
    iniciarTablaEstudio();
    puntosMiles();
    $(".ocultarTabla").hide();
    SearchDatatable();
    cargadorAjax();
    lockButtons();
});

$(document).on('click', '.cargarModalAjax', function () {
    const url = `${window.location.origin}`+$(this).data('url');
    console.log(url);
    $.get(url, function (data) {
        $('#ajaxModal').html(data);
        $(".componenetFile").filestyle('buttonText', 'Cargar Archivo');
        $('#ajaxModal').modal()
    });
});



$(document).on('submit', '#comment-form', function (e) {
    e.preventDefault();
    const url = $(this).data('url');
    const fd = new FormData($(this)[0]);
    $.ajax({
        url: url,
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(data){
            $('#ajaxModal').html(data);
            $('#ajaxModal').modal()
        }
    })
});

$(document).on('submit', '.create-form', function (e) {
    e.preventDefault();
    const url = $(this).data('url');
    const fd = new FormData($(this)[0]);
    $.ajax({
        url: url,
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(data){
            $('#contenido').html(data);
            tabla();
            $('#ajaxModal').modal('hide');
        }
    })
});


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function format_miles(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function displayMessageMini(message){
    bootbox.alert({
        message: message,
        size: 'small',
    });
}

/*
 * Funcion para desplegar una modal con opciones de confirmacion para el usuario antes de alguna redireccion
 * para su funcionamiento correcto requiere de dos cosas:
 * 1. el texto que se le preguntara al usuario => este texto se recibe como parametro directamente en la funcion
 * 2. la url para la redireccion en caso de que confirme el usuario
 */
function confirmar(texto,url){
    
    bootbox.confirm({
            message: "\u00BF"+texto+"?",
            buttons:{
                        confirm:{
                                    label: 'Si',
                                    className: 'btn-danger'},
                        cancel:{
                                    label: 'No',
                                    className: 'btn-default'}},
            callback: function (resultado) {
                if(resultado){
                    window.location.href = url;
                }
                
                return true;//Se retorna verdadero para que cierre la ventana
                
            }
        });
}

function calcularCupoDesprendible(ingreso, retenciones, pagaduria, regimenEspecial, callback) {
    const url = `${window.location.origin}/calcularCupo?ingreso=${ingreso}&egreso=${retenciones}&pagaduria=${pagaduria}&regimen_especial=${regimenEspecial}`;
    //const url = `http://localhost:8888/bancarizate/public/calcularCupo?ingreso=${ingreso}&egreso=${retenciones}&pagaduria=${pagaduria}&regimen_especial=${regimenEspecial}`;
    fetch(url, {credentials: "same-origin"}).then(response => {
        return response.json()}).then(json => callback(json));
}
