function setMiles(selector){
    var cleave = new Cleave(selector, {
            numeral: true,
            delimiter: ".",
            numeralDecimalScale: 0,           
            numeralDecimalMark: ",",
            numeralThousandsGroupStyle: 'thousand'
        });
}
function initMiles(){
    $(document).on("focus", ".miles", function(){        
        setMiles($(this));
    })    
}



$(document).ready(function(){    
    $('#ModalDefinirObligaciones').modal({ backdrop: 'static', keyboard: false },'show');
    initMiles();     
})


$(document).on("click", "#ModalDefinirObligaciones .listaSelObligaciones .obligacion", function(){
    var obligacion = $(this).data("obligacion"); 
    if($(this).hasClass("Eliminar")){
        $(".itemEliminarObligacion"+obligacion).remove();
        $(this).removeClass("Eliminar");
    }else{
        $("#formularioObligacionesAEliminar").append('<input class="itemObli itemEliminarObligacion'+obligacion+'" value="'+obligacion+'" name="obligaciones[]" type="hidden">');        
        $(this).addClass("Eliminar");
    }
})

$(document).on("click", "#ModalDefinirObligaciones .listaSelObligaciones .obligacionProc", function(){
    var obligacion = $(this).data("obligacion"); 
    if($(this).hasClass("Actualizar")){
        $(".itemEliminarObligacion"+obligacion).remove();
        $(this).removeClass("Actualizar");
    }else{
        $("#formularioObligacionesAEliminar").append('<input class="itemObli itemEliminarObligacion'+obligacion+'" value="'+obligacion+'" name="obligaciones[]" type="hidden">');        
        $(this).addClass("Actualizar");
    }
})

//inicio logica obligaciones

$(document).on("click", "#sgtProceso", function(){
    
    var formData = new FormData($("#formularioObligacionesAEliminar")[0]);
    var url = $(this).data("url");
    var redireccion = $(this).data("redireccion");
    
    if($('.itemObli').length >= 0){
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
                success: function(data){
                    var respuesta = JSON.parse(data);
                    if(respuesta.STATUS){
                        displayMessageMini("Espere un momento mientras es enviado a la valoraci&oacute;n");
                        location.href =redireccion;
                    }else{
                        displayMessageMini(respuesta.MENSAJE);
                    }
                },
                error: function(data){
                    console.log("error",data);
                }
        });
    }else{
        location.href =redireccion;
    }
})

//Inicio logica obligaciones desprendibles

$(document).on("click", "#Procesar", function(){
    
    var formData = new FormData($("#formularioObligacionesAEliminar")[0]);
    var url = $(this).data("url");
    
    
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
                success: function(data){
                    datos = JSON.parse(data);                    
                    //window.location.href = datos.url;
                    $(".modal-header").html(datos.htmlHeader);
                    $(".modal-body").html(datos.htmlBody);     
                    $("#divFooter").html(datos.htmlFooter);
                    $(".componenetFile").filestyle({input: false, buttonText: "Cargar Archivo"});
                    jsonPagadurias();
                },
                error: function(data){
                    console.log("error",data);
                }
        });
    
})

$(document).on("click","#Adicionar",function(){
        var Entidad = $("#pagaduria").val();
        var Cuota = $("#Cuota").val();
        var Valoracion = $("#idValoracion").val();
        var Estudio = $("#idEstudio").val();
        var url = $(this).data("url")
   
            $.ajax({
               type: 'POST',
               url: url,
               data: {'_token': $('input[name=_token]').val(),'Entidad': Entidad, 'Cuota': Cuota,'Valoracion': Valoracion, 'Estudio': Estudio},
                    success: function(data){
                        datos = JSON.parse(data);
                        $("#pagaduria").val("");
                        $("#Cuota").val("");
                        $("#divTables").html(datos.htmlTable);
                    },
                    error: function(data){
                        console.log("error", data)
                    }
            }); 
})

$(document).on("click", "#fnsProcess", function(){
    var Ingresos = $("#ingresos").val();
    var Egresos = $("#egresos").val();
    var Estudio = $("#idEstudio").val();
    var uri = $(this).data("url");
            
            $.ajax({
                type: 'POST',
                url: uri,
                data: {_token: $('input[name=_token]').val(),Ingresos: Ingresos, Egresos: Egresos,Estudio: Estudio},                
                    success: function(data){
                        datos = JSON.parse(data);
                        displayMessageMini("El sistema esta creando el estudio, por favor espere");
                        window.location.href = datos.uri;
                    },
                    error: function(data){
                        console.log("error", data);
                    }
            })    
})

$(document).on("click", "#Anterior", function(){
    window.location.href = $(this).data("url");
})

function resize(){
    var mediaquery = window.matchMedia("(max-width: 330px)");
        if (mediaquery.matches) {                
            $("p").addClass('font-size11');                        
        }  
}

/*
 * Funcion para inicializar la funcionalidad de autocomplete en el campo pagaduria. La idea es realizar una sola peticion ajax al cargar la pagina y traer todos los datos, para que cuando el usuario
 * empiece a teclear en el input los datos que se muestren sean tomados de la respuesta inicial y asi no se generarian peticiones innecesarias cada vez que tecleara el usuario
 */
function jsonPagadurias(){
        var url = $("#base").data("url");
        $.post( url+"searchEntidad",{ _token: $("#_token").val() },  function( data ) {            
            var dataRespuesta = JSON.parse(data);
            
            $( "#pagaduria" ).autocomplete({
                source: dataRespuesta,
                minLength: 1,
                select: function( event, ui ) {

                }
            });
            $( "#pagaduria" ).autocomplete( "option", "appendTo", ".eventInsForm" );
            
        }); 
}


function removeItemsForm(){
    $(".itemObli").remove();
}

function miles(x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

//fin logica obligaciones