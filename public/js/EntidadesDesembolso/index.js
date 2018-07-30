/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#txNit').val("");
    $('#txDescripcion').val("");
    $('#hnAccion').val("");
    $("#txNit").prop('disabled', false);
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
    $('#txNit').val($(this).data('nit'));
    $('#txDescripcion').val($(this).data('descripcion'));
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar

    //Se ajusta la modal "ventana" para la actualización.
    $("#txNit").prop('disabled', true);
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
    var Nit = $('#txNit').val();
    var Descripcion =  $('#txDescripcion').val();
    var ruta = "addEntidadDesembolso";

    if($('#hnAccion').val())
    {
        ruta = "editEntidadDesembolso";
    }
    
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Nit': Nit,
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
    var Nit = $(this).data('nit');
    bootbox.confirm
    ({
        message: "\u00BFSeguro que desea eliminar el registro de Código: [" + Nit + "]?",
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
                var ruta = "deleteEntidadDesembolso";

                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Nit': Nit
                    },
                    success: function(data) {
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
        $('#txNit').focus();
    }
});