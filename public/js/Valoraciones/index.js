function libreriaAdjunto(){
    $("#adjuntoAutorizacion").filestyle({buttonBefore: true, buttonText: "Cargar Autorización"});
    
}
function validarFormulario(){
    $(document).on("submit", "#formulario-valoracion", function(){        
        console.log("Entreees");
        if($("#txEmail").val() == ""){
            displayMessageMini("Debe ingresar el Correo para continuar");
            return false;
        }
        if($("#txCelular").val() == ""){
            displayMessageMini("Debe ingresar el Celular para continuar");
            return false;
        }
        if($("#txPagaduria").val() == ""){
            displayMessageMini("Debe ingresar la Pagaduría para continuar");
            return false;
        }
        if($("#txCedula").val() == ""){
            displayMessageMini("Debe ingresar la Cédula para continuar");
            return false;
        }
        if($("#txPrimerApellido").val() == ""){
            displayMessageMini("Debe ingresar el Primar Apellido para continuar");
            return false;
        }
        if($("#adjuntoAutorizacion").val() == ""){
            displayMessageMini("Debe cargar la autorización de consulta para continuar");
            return false;
        }
        $("#botonEnviarFormulario").attr("disabled", true);
        return true;
    })
}
$(function(){
    libreriaAdjunto();  
    validarFormulario();
    jsonPagadurias();
})

function jsonPagadurias(){
        var url = $("#txPagaduria").data("url");
        $.post( url+"searchEntidad",{ _token: $("#token").val() },  function( data ) {            
            var dataRespuesta = JSON.parse(data);
            
            $( "#txPagaduria" ).autocomplete({
                source: dataRespuesta,
                minLength: 1,                
                select: function( event, ui ) {
                }
            });                    
        }); 
}


