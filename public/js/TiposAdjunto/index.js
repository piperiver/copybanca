/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#txCodigo').val("");
    $('#txDescripcion').val("");
    $('#hnAccion').val("");
    $("#txCodigo").prop('disabled', false);
    $('#btGuardar').addClass('green');
    $('#btGuardar').removeClass('yellow-gold');
    $('#btGuardar').text("Guardar");
    $('#ventana').modal('show');
});
/****************
    Evento del link en fila para dirigirse a Actualizar
*****************/
$(document).on('click', '#lkEdit', function()
{
    $('#txCodigo').val($(this).data('codigo'));
    $('#txDescripcion').val($(this).data('descripcion'));
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar

    //Se ajusta la modal "ventana" para la actualización.
    $("#txCodigo").prop('disabled', true);
    $('#btGuardar').addClass('yellow-gold');
    $('#btGuardar').removeClass('green');
    $('#btGuardar').text("Actualizar");
    $('#ventana').modal('show');
});
/****************
    en este evento se toma los valores de los campos dados a la modal
    y se valida que accion se ha indicado(Guardar o Actualizar), segun
    sea el caso toma la ruta y hace la petición ajax.
*****************/
$('#btGuardar').click(function()
{
    //var ModalC = modalCarga("Por Favor espere...");//funcion llamada desde el archivo public/js/global.js
    var Codigo = $('#txCodigo').val();
    var Descripcion =  $('#txDescripcion').val();
    var ruta = "addTipoAdjunto";

    if($('#hnAccion').val())
    {
        ruta = "editTipoAdjunto";
    }
    
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Codigo': Codigo,
            'Descripcion': Descripcion
        },
        success: function(data)
        {
            resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
            //ModalC.modal('hide');
        }
    });
});
/****************
    Evento del link en fila para Eliminar
*****************/
$(document).on('click', '#lkDelete', function()
{
    var Codigo = $(this).data('codigo');
    bootbox.confirm
    ({
        message: "\u00BFSeguro que desea eliminar el registro de Código: [" + Codigo + "]?",
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
                var ruta = "deleteTipoAdjunto";

                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Codigo': Codigo
                    },
                    success: function(data) {
                        bootbox.alert({
                            message: data.Mensaje,
                            size: 'small'
                        });

                        $('#contenido').html(data.tabla);
                        tabla();//funcion llamada desde el archivo public/js/global.js
                        //ModalC.modal('hide');
                    }
                });
            }
        }
    });
});
/****************
    Al mostrar la modal se procede a posicionar  el cursor segun el caso (Guardar o Actualizar)
    en su respectivo campo.
*****************/
$('#ventana').on('shown.bs.modal', function () {
    if($('#hnAccion').val())
    {
        $('#txDescripcion').focus();
    }
    else
    {
        $('#txCodigo').focus();
    }
});
