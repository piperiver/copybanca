/****************
    Evento del link en fila para Eliminar
*****************/
$(document).on('click', '#lkDelete', function()
{
    var id = $(this).data('id');
    bootbox.confirm
    ({
        message: "\u00BFSeguro que desea eliminar el Contacto?",
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
                //var ModalC = modalCarga("Por Favor espere...");
                var ruta = "deleteContacto";

                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'id': id
                    },
                    success: function(data) {
                        resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
                        $('#slIdPadre').html(data.opciones);
                        //ModalC.modal('hide');
                    }
                });
            }
        }
    });
});



