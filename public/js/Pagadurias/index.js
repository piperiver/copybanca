/****************
 Evento del link "Crear"
 *****************/
$('#lkSave').click(function(event)
{
    $('#id_nombre').val("");
    $('#id_tipo').val("");
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
    $('#id_nombre').val($(this).data('nombre'));
    $('#id_tipo').val($(this).data('tipo'));
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
function formToJson(form){
    var data = new FormData(form);
    var object = {};
    data.forEach(function(value, key){
        object[key] = value;
    });
    return object;
}



/****************
 Evento del link en fila para Eliminar
 *****************/
$(document).on('click', '#lkDelete', function()
{
    var ruta = $(this).data('delete-url');
    bootbox.confirm
    ({
        message: "\u00BFSeguro que desea eliminar el registro?",
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

                $.ajax({
                    type: 'delete',
                    data: {
                        '_token': $('input[name=_token]').val(),
                    },
                    url: ruta,
                    success: function(data) {
                        resultadoEvento(data);
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
