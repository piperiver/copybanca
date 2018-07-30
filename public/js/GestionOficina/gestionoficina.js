
$(document).on("click", "#btnCancelar", function(){
    $("#modalPlus").modal('hide');
});

/*$(document).on("change", "#sltCliente", function(){       
       
    var Id = $(this).val();          
    var url = $('#dom').val();  
    var concepto = $("sltConcepto").val();
    
    $.post(url+"dsAdjunto",
        {
            id: Id, 
            Concepto: concepto,
            _token: $("input[name=_token]").val()
        }, function(data){                                        
            $("#adjGesAdjunto").html(data);
            styComponent();
            $("#modalPlus").modal({ backdrop: 'static', keyboard: false },'show');                              
        }
    )                                             
});*/

$(document).on("click", ".actTareas", function(){   
    
    var url = $('#dom').val();
    var id = $(this).data('id');
    var nombre = $(this).data('nombre');
    var concepto = $(this).data('concepto');
    
    $.post(url+"dsAdjunto",
        {
            Id: id,
            Concepto: concepto,
            _token: $("input[name=_token]").val()
        }, function(data){
            var respuestas = JSON.parse(data);
            $("#clienteMod").val(nombre);
            $("#conceptoMod").val(concepto);
            $("#adjGesLib").html(respuestas.carga);
            $("#container_cargaTablaAdjuntosCargados").html(respuestas.tabla);
            $("#actModal").modal('show');
            styComponent();
        }                
    )
});

function reload(){
    window.location.reload();
}

$(document).on("click", "#btnGuardar", function(){
    var url = $('#dom').val();
    var Id = $("#sltCliente-id").val();
    var concepto = $("#sltConcepto").val();
    
    $.post(url+"addTarea",
        {
            id: Id, 
            Concepto: concepto,
            _token: $("input[name=_token]").val()
        }, function(data){                                        
            bootbox.alert({
                        message: data.Mensaje,
                        size: 'small',
                        callback: function(){
                            $("#modalPlus").modal("hide"); 
                            reload();
                            
                        }
                    });                              
        }
    )
});

function styComponent(){
    $(".componenetFile").filestyle({input: false, buttonText: "Cargar Archivo"});
}

function jsonClientes(){
        var url = $('#dom').val();
        $.post( url+"searchClientes",{ _token: $("#_token").val() },  function( data ) { 
         
            var dataRespuesta = JSON.parse(data);            
            $( "#sltCliente" ).autocomplete({
                source: dataRespuesta,
                minLength: 0,
                select: function( event, ui ) {
                    $("#sltCliente").val(ui.item.label);
                    $("#sltCliente-id").val(ui.item.value);
                    return false;
                }
            });
            
            
        }); 
}

function jsonConcepto(){
        var url = $('#dom').val();
        $.post( url+"searchConceptos",{ _token: $("#_token").val() },  function( data ) {             
            var dataRespuesta = JSON.parse(data);        
            console.log(dataRespuesta);
            $( "#sltConcepto" ).autocomplete({
                source: dataRespuesta,
                minLength: 0                        
            });
    })
}

$(document).ready(function(){
    jsonClientes();
    jsonConcepto();
});