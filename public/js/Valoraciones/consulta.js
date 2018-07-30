$('#lkGenerarCodigo').click(function(event)
{
    var ruta = "GenerarCodigo";

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val()
        },
        success: function(data)
        {
            $('#lblCodigo').text(data.Codigo);
            $('#ventana').modal('show');
        }
    });
});

/****************
 Evento del link en fila para Eliminar
 *****************/
$(document).on('click', '#lkDelete', function()
{
    var id = $(this).data('id');
    bootbox.confirm
    ({
        message: "\u00BFSeguro que desea eliminar la Valoración?",
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

                var ruta = "deleteValoracion";

                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Valoracion': id
                    },
                    success: function(data) {
                        var respuesta = JSON.parse(data);

                        $("#contenido").html(respuesta.tabla);
                        iniciarTabla();

                        if(respuesta.errores.length > 0){

                           var errores = respuesta.errores;
                           $html = "<ul style='list-style-type:disc'>"

                            for(var i = 0; i < errores.length; i++){
                                $html += "<li>"+errores[i]+"</li>";
                            }

                            $html += "</ul>";

                            displayMessageMini(respuesta.mensaje);
                           $("#descripcion_mensaje").html($html);
                           $('#mensaje').modal('show');
                        }else {
                            displayMessageMini(respuesta.mensaje);
                        }

                    },
                    error:function(jqHRX,textStatus, errorThrown){
                        displayMessageMini("Ocorrió un error comuníquese con soporte");
                        console.log("Error: "+errorThrown);
                    }
                });
            }
        }
    });
});
