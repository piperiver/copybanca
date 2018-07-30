$(".componenetFile").filestyle({input: false, buttonText: "Cargar Archivo"});

    $(document).ready(function(){        
        
        $(".componenetFile").filestyle('buttonText', 'Cargar Archivo');  
        
        $(document).on("keyup", ".nombreAdjunto", function(){            
            var newText = $(this).val();
            newText = newText.replace("*", "");
            newText = newText.replace("/", "");
            newText = newText.replace(":", "");
            newText = newText.replace("?", "");
            newText = newText.replace("\"", "");
            newText = newText.replace("<", "");
            newText = newText.replace(">", "");
            newText = newText.replace("|", "");
            newText = newText.replace(".", "");
            newText = newText.replace(",", "");

            $(this).val(newText);
        })
        
        

        $(document).on("change", ".componenetFile", function(){
            var idElements = $(this).data("idelements");            
            
            if($("#NombreArchivo"+idElements).val() == ""){
                displayMessageMini("El nombre del archivo es obligatorio");
                $(this).filestyle('clear'); 
                return;
            }else{
                var tamannioArchivo = $(this)[0].files[0].size;
                console.log(tamannioArchivo);
                if(tamannioArchivo > 21965364){ // 30 MB
                    displayMessageMini("El archivo no puede superar los 30 MB.");
                    return;
                }
                console.log("paso");
                var ruta = $(this).data("action");                
                var formData = new FormData($("#formularioAdjuntos"+idElements)[0]);
                
                var input = $(this);
                bootbox.confirm(
                {
                    message: "\u00BFDesea adjuntar el Archivo?",
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
                    callback: function (resultado)
                    {
                        
                        
                        if(resultado)
                        { 
                            input.filestyle('buttonText', 'Cargando...');                   
                            $.ajax({
                                url: ruta,
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data){
                                    var respuesta = JSON.parse(data); 
                                    bootbox.alert({
                                        message: respuesta.MENSAJE,
                                        size: 'small',
                                        callback: function(){
                                            if(respuesta.STATUS){
                                                switch(respuesta.datos.accionAlFinalizar){
                                                    case "refresh":
                                                        location.reload(true);
                                                    break;
                                                    case "locked":
                                                        input.filestyle('disabled',true);
                                                        input.filestyle('buttonText', 'Archivo Cargado');
                                                    break;
                                                    case "function":            
                                                        
                                                        eval(respuesta.datos.function + "(input,respuesta.datos, respuesta.returnPHP)");
                                                    break;
                                                    case "clear":
                                                        input.filestyle('buttonText', 'Cargar Archivo');    
                                                        input.filestyle('clear');
                                                        $("#NombreArchivo"+idElements).val("");
                                                    break;
                                                    default:
                                                        input.filestyle('disabled',true);
                                                        input.filestyle('buttonText', 'Archivo Cargado');   
                                                    break;
                                                }
                                                
                                                if(respuesta.datos.dspTabla){
                                                    $("#"+respuesta.datos.contenedorListAdjuntos).html(respuesta.itemsTabla);
                                                }
                                                
                                            }else{
                                                input.filestyle('clear');
                                            }
                                        }
                                    });
                                },
                                error: function(data){
                                    input.filestyle('buttonText', 'Cargar Archivo');
                                    input.filestyle('clear'); 
                                    bootbox.alert({
                                        message: "error Ocurrio un problema inesperado, por favor recargue la pagina e intentelo de nuevo, si el problema persiste comuniquese con soporte [Error ajax debido a error php]",
                                        size: 'small'
                                    });                               
                                    
                                }
                            });
                        }                    
                    }
                });
            }
        })
        
        /*
         * Eliminar Adjuntos
         */
        
        $(document).on("click", ".deleteAdjunto", function(){
        var idA = $(this).data("adjunto");        
        var funcionphp = $(this).data("funcionphp");        
        var url = $(this).data("url");
        var idElementParent = ($(this).data("delparent") && $(this).data("delparent") != null && $(this).data("delparent") != false)? $(this).data("delparent") : false;
        var token = $("input[name=_token]").val();
        var nombreFuncion = $(this).data("nombrefuncion");
        var infoAdjunto = $(this).data("infoadjunto");        
        var yo = $(this);
        bootbox.confirm(
                {
                    message: "\u00BFRealmente desea eliminar el Archivo?",
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
                    callback: function (resultado)
                    {
                        
                        if(resultado)
                        { 
                            $.post( url, {idAdjunto: idA, funcionphp: funcionphp, infoAdjunto: infoAdjunto, _token: token}, function(data){
                                var respuesta = JSON.parse(data);
                                    bootbox.alert({
                                        message: respuesta.MENSAJE,
                                        size: 'small',
                                        callback: function(){
                                            if(respuesta.STATUS){   
                                                                                                
                                                if(nombreFuncion == "delete_refresh"){
                                                    location.reload(true);                                                    
                                                }else if(nombreFuncion != false && nombreFuncion != "false" && nombreFuncion != "0" && nombreFuncion != 0){
                                                    try {                                                        
                                                        eval(nombreFuncion+ "(infoAdjunto)");
                                                    } catch(e) {
                                                        console.log("No existe la funcion");
                                                    }                                                    
                                                }
                                                
                                                if(idElementParent != false){
                                                    $(idElementParent).remove();
                                                }else{
                                                    location.reload(true);
                                                }
                                            }
                                        }
                                    });
                                    
                            });
                        }
                    }
                });

    })
    })
    
    
    