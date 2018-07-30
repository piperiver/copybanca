$(document).ready(function(){
   
    $(document).on("click", ".boton1", function(){       
        $(".menu-vtm").slideToggle("slow");
    })
    $("document").on("click","#btnPlay",function(){
	    $('.Mvideo').get(0).play();
    })
    
     /**
     * Evento para iniciar la funcionalidad de desplegar el carousel con el dedo $
     */
    $(".carousel.slide").carousel({
        swipe: 30 // percent-per-second, default is 50. Pass false to disable swipe
    });     
    
    
    
    $(".btnTransition").click(function(){   


        if($(this).data("event") == "validationForm"){
            var banderaSeguir = true;
            
            $("#pagaduria").css("box-shadow", "1px 1px 4px 1px #cccccc");
            $(".cPagaduria").html("");

            $("#fecha").css("box-shadow", "1px 1px 4px 1px #cccccc");
            $(".cFecha").html("");

            $("#cedula").css("box-shadow", "1px 1px 4px 1px #cccccc");
            $(".cCedula").html("");

            $("#pApellido").css("box-shadow", "1px 1px 4px 1px #cccccc");
            $(".cApellido").html("");

            $("#nombre").css("box-shadow", "1px 1px 4px 1px #cccccc");
            $(".cNombre").html("");

            if($("#pagaduria").val().length <= 0){
                banderaSeguir = false;
                $(".cPagaduria").html("<span style='font-weight: bold;color: #911b1d'>Completa este campo</span>");
                $("#pagaduria").css("box-shadow", "1px 1px 4px 1px #911b1d");
                $("#pagaduria").focus();
            }
            if($("#fecha").val().length <= 0){
                banderaSeguir = false;
                $(".cFecha").html("<span style='font-weight: bold;color: #911b1d'>Completa este campo</span>");
                $("#fecha").css("box-shadow", "1px 1px 4px 1px #911b1d");
                $("#fecha").focus();
            }
            if($("#cedula").val().length <= 6){
                banderaSeguir = false;
                $(".cCedula").html("<span style='font-weight: bold;color: #911b1d'>El campo cedula debe tener almenos 6 números</span>");
                $("#cedula").css("box-shadow", "1px 1px 4px 1px #911b1d");
                $("#cedula").focus();
            }
            if($("#pApellido").val().length <= 0){
                banderaSeguir = false;
                $(".cApellido").html("<span style='font-weight: bold;color: #911b1d'>Completa este campo</span>");
                $("#pApellido").css("box-shadow", "1px 1px 4px 1px #911b1d");
                $("#pApellido").focus();
            }
            if($("#nombre").val().length <= 0){
                banderaSeguir = false;
                $(".cNombre").html("<span style='font-weight: bold;color: #911b1d'>Completa este campo</span>");
                $("#nombre").css("box-shadow", "1px 1px 4px 1px #911b1d");
                $("#nombre").focus();
            }                   
            

            if(banderaSeguir){
                var url = $("#pagaduria").data("url");
                
                $.post( 
                    url+"updateUser",
                    { 
                        _token: $("#token").val(),
                        nombre: $("#nombre").val(),
                        pApellido: $("#pApellido").val(),
                        cedula: $("#cedula").val(),
                        fecha: $("#fecha").val(),
                        pagaduria: $("#pagaduria").val()
                        
                        },  
                         function(data) {
                             var response = JSON.parse(data);
                            if(response.STATUS){                                
                                $('#pasoApaso').carousel('next');    
                            }else{
                                alert(response.mensaje);
                            }                    
                });
            }

        }else{
            $('#pasoApaso').carousel('next');
        }
        
        
    })//.btnTransicion


 jsonPagadurias();   
    
})//ready function

/*
 * Funcion para inicializar la funcionalidad de autocomplete en el campo pagaduria. La idea es realizar una sola peticion ajax al cargar la pagina y traer todos los datos, para que cuando el usuario
 * empiece a teclear en el input los datos que se muestren sean tomados de la respuesta inicial y asi no se generarian peticiones innecesarias cada vez que tecleara el usuario
 */
function jsonPagadurias(){
        var url = $("#pagaduria").data("url");
        $.post( url+"searchEntidad",{ _token: $("#token").val() },  function( data ) {            
            var dataRespuesta = JSON.parse(data);
            
            $( "#pagaduria" ).autocomplete({
                source: dataRespuesta,
                minLength: 1,
                select: function( event, ui ) {

                }
            });        
            
        }); 
}


$(document).on("click", ".title", function(){
        
    if($(this).hasClass("active")){
        $(this).text($(this).data("title"));
        $(this).removeClass("active");
    }else{
        $(".title").removeClass("active");
        $(".title").each(function(numero, elemento){
            $(this).text($(this).data("title"));
        })
        $(this).text($(this).data("desc"));   
        $(this).addClass("active");
    }

    
});

$('#lkCodigoPromocional').click(function(event)
{
    $('#ventana').modal('show');
});

function preguntas(){
    var ruta = $("#urlPrincipal").val();
    
    $.ajax({
        type: 'post',
        url: ruta+"getPreguntas",
        data: {
            '_token': $('input[name=_token]').val()            
        },
        success: function(data)
        {
            
        }
    });
}


$('#btValidar').click(function(){
    //var ModalC = modalCarga("Por Favor espere...");//funcion llamada desde el archivo public/js/global.js
    var CodigoPromocional = $('#txCodigoPromocional').val().replace(/ /g, "");
    var Cedula = $('#cedula').val();
    var PrimerApellido = $('#pApellido').val();
    
    if(CodigoPromocional == "")
    {
        alert('Error: El Campo Código esta Vacio.');
        return;
    }
    var ruta = "consumirCodigo";
    
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'CodigoPromocional': CodigoPromocional,
            'Cedula': Cedula,
            'PrimerApellido': PrimerApellido
        },
        success: function(data)
        {
            if(!data.valido)
            {
                alert('El Código Digitado no es Valido.');
            }
            else
            {
                //window.location.href='valoracion1';
                preguntas();
            }
        }
    });
});