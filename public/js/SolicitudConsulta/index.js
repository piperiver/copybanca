
$(document).on('change', '#departamento', function(){
           
            var url = `${window.location.origin}/municipios?departamento_id=${$(this).find(':selected').data('id')}`;
            fetch(url, {credentials: "same-origin"})
            .then(response => response.json())
            .then(data => setMunicipios(data))
            .catch(error => console.error('Hay un error'));
});

function setMunicipios(data){
    
    var option = "<option selected disabled value>Seleccione una opción</option>";
    
    for(var i = 0; i < data.length; i++){
        option += "<option value="+data[i].municipio+" data-id="+data[i].id_municipio+">"+data[i].municipio+"</option>";
    }
    
    $("#municipio").html(option);
}

$(document).on('click', '#enviar_descripcion', function(){

        var id = $(this).data('id');
        console.log(id);

        var url = $("#dominioPrincipal_"+id).val();
        var ruta = url + "solicitudes/devuelta";
        
        var formData = new FormData();
        formData.append("id", id);
        formData.append("_token", $("#token_"+id).val());
        formData.append("descripcion_devolucion", $("#descripcion_devolucion_"+id).val());
        
        $.ajax({
                
            url:ruta,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            success:function(data){  
                var respuesta = JSON.parse(data);
                $('#descripcion_mensaje').html(respuesta.mensajes);
                $('#mensaje').modal('show');
                $('#ajaxModal').modal('hide');
                
                //Actualizacion de contenido en tablas 
                $('#contenido_completas').html(respuesta.tabla_completas);
                $('#completas').html(respuesta.countCompletas);

                $('#contenido_pendientes').html(respuesta.tabla_pendientes);
                $('#pendientes').html(respuesta.contPendientes);

                $('#contenido_devueltas').html(respuesta.tabla_devueltas);
                $('#devueltas').html(respuesta.contDevueltas);
                   iniciarTabla();
                $('#mensaje').modal('hide');
                
            },

            error:function(jqHRX,textStatus, errorThrown){
                console.log("Error: "+errorThrown);
            }
                
        });
        
});

function cleanInput(){
    $('input[name="foto_documento"]').val("");
    $('input[name="autorizacion"]').val("");
}

function guardarBanco(){

    console.log("Entro a guardar banco");

    var banco = $("#banco").val();
    console.log("Nombre del banco "+banco);
    var id = $("#id_estudioBanco").data('id');
    console.log(id);

    var url = $("#dominioPrincipal_"+id).val();
    var ruta = url + "solicitudes/guardarBanco";

    var formData = new FormData();
    formData.append("id", id);
    formData.append("_token", $("#token_"+id).val());
    formData.append("banco", banco);

    $.ajax({

        url:ruta,
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,

        success:function(data){
            var respuesta = JSON.parse(data);

            //Actualizacion de contenido en tablas
            $('#contenido_completas').html(respuesta.tabla_completas);

            $('#contenido_pendientes').html(respuesta.tabla_pendientes);

            $('#contenido_devueltas').html(respuesta.tabla_devueltas);
            iniciarTabla();

            console.log("Banco guardado");

            displayMessageMini("Banco guardado");

        },

        error:function(jqHRX,textStatus, errorThrown){
            console.log("Error: "+errorThrown);

            displayMessageMini("Banco no guardado UPPS!");
        }

    });

}
