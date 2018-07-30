$("#fAdjCliente").filestyle({input: false, buttonText: "Cargar Archivo"});


//if(screen.width<=768){
//}


$(document).on("click", '#btAdicionar', function(){
    
    var Estudio = $("#hnEstudio").val();
    var Valor = ($('#txValor').val() != "")? parseInt(limiparPuntos($('#txValor').val())) : 0;
    var TipoGiro =  $('#slTipoGiro').val();
    var restanteGiros = $("#ValorPorGirarCopy").val();
    var adjunto = $('#fAdjCliente').val();
    
    if(adjunto == ""){
        displayMessageMini("Debes Adjuntar el soporte para adicionar el giro.");
        return;
    }
    if(Valor > restanteGiros){
        displayMessageMini("No es posible ingresar el giro, porque el valor ingresado es mayor a el saldo restante.");
        return;
    }
    
    if(Valor == ""){
        displayMessageMini("El campo Valor es obligatorio");
        return;
    }
    if(TipoGiro == ""){
        displayMessageMini("El campo Tipo Giro es obligatorio");
        return;
    }

    var formData = new FormData($("#frmAdjCliente")[0]);
    formData.append('Estudio',Estudio);
    formData.append('Valor',Valor);
    formData.append('TipoGiro',TipoGiro);
    formData.append('_token',$('input[name=_token]').val());
    var ruta = window.location.origin+"/addGiroCliente";

    $.ajax({
        type: 'post',
        url: ruta,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta){
            
            if(respuesta.STATUS){
                /*
                //Limipiamos los campos de la modal
                $("#slTipoGiro").val("Desembolso");
                $("#txValor").val("");
                
                //Actualizamos la tabla
                $("#ListadoGirosRealizados").html(respuesta.htmlGiros); 
                
                //actualizamos el valor que se ha girado
                $("#ValorGirado").html(" $"+format_miles(respuesta.totalGirado));
                $("#ValorGiradoCopy").val(respuesta.totalGirado);
                
                //obtenemos el saldo del cliente
                var saldoCliente = ($("#saldoCliente").text() != "")? limiparPuntos($("#saldoCliente").text()) : 0;
                saldoCliente = saldoCliente.replace("$","");                
                //Actualizamos el saldo
                var restanteGiro =  saldoCliente - parseInt(limiparPuntos(respuesta.totalGirado));
                $("#ValorPorGirar").html(" $"+format_miles(restanteGiro));
                $("#ValorPorGirarCopy").val(restanteGiro);
                */
               location.reload(true);
            }
            
            $("#fAdjCliente").filestyle('clear');
            bootbox.alert({
                      message: respuesta.Mensaje,
                      size: 'small',
                      callback: function(){                            
                          $('#vtnGiroCliente').modal('hide');            
                      }
            });            
        }
    });
});

$(document).on("click", ".iconEliminar", function(){
    
    var id = $(this).data("id");
    var url = $(this).data("url");
    var valor = $(this).data("valor");
    var elemento = $(this);
    
    bootbox.confirm("Realmente desea eliminar el Giro", function(result){ 
        if(result){
                $.post( url+"Giros/Eliminar",
                            {
                                id: id,
                                _token: $("input[name=_token]").val()
                            }, function(data){
                                var resultado = JSON.parse(data);
                                if(resultado.STATUS){
                                    var girado = $("#ValorGiradoCopy").val();
                                    var newValueGirado = ((girado-valor) > 0)? format_miles(girado-valor) : 0;
                                    $("#ValorGirado").html(" $"+newValueGirado);
                                    $("#ValorGiradoCopy").val(girado-valor);

                                    var restante = $("#ValorPorGirarCopy").val();
                                    $("#ValorPorGirar").html(" $"+format_miles(parseInt(restante)+parseInt(valor)));
                                    $("#ValorPorGirarCopy").val(parseInt(restante)+parseInt(valor));                        
                                    
                                    elemento.parent().parent().remove();
                                }
                                    displayMessageMini(resultado.MENSAJE);                        
                            });
        }
    });
    
})
