/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#txNombre').prop('disabled', false);
    $('#txEntidad').prop('disabled', false);
    $('#txCargo').prop('disabled', false);
    $('#txTelefono').prop('disabled', false);
    $('#txCorreo').prop('disabled', false);
    $('#txArea').prop('disabled', false);
    
    $('#hnId').val("");
    $('#txNombre').val("");
    $('#txEntidad').val("");
    $('#txCargo').val("");
    $('#txTelefono').val("");
    $('#txCorreo').val("");
    $('#txArea').val("");
    $('#hnAccion').val("");
    
    $('#btGuardar').addClass('green');
    $('#btGuardar').removeClass('yellow-gold');
    $('#btGuardar').text("Guardar");
    $('#btGuardar').show();
    $('#ventana').modal('show');
});
/****************
    Evento del link en fila para dirigirse a Actualizar
*****************/
$(document).on('click', '#lkEdit', function()
{
    $('#txNombre').prop('disabled', false);
    $('#txEntidad').prop('disabled', false);
    $('#txCargo').prop('disabled', false);
    $('#txTelefono').prop('disabled', false);
    $('#txCorreo').prop('disabled', false);
    $('#txArea').prop('disabled', false);
    
    $('#hnId').val($(this).data('id'));
    $('#txNombre').val($(this).data('nombre'));
    $('#txEntidad').val($(this).data('entidad'));
    $('#txCargo').val($(this).data('cargo'));
    $('#txTelefono').val($(this).data('telefono'));
    $('#txCorreo').val($(this).data('correo'));
    $('#txArea').val($(this).data('area'));
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar

    //Se ajusta la modal "ventana" para la actualización.
    
    $('#btGuardar').addClass('yellow-gold');
    $('#btGuardar').removeClass('green');
    $('#btGuardar').text("Actualizar");
    $('#btGuardar').show();
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
    var id = $('#hnId').val();
    var Nombre = $('#txNombre').val();
    var Entidad =  $('#txEntidad').val();
    var Cargo =  $('#txCargo').val();
    var Telefono =  $('#txTelefono').val();
    var Correo =  $('#txCorreo').val();
    var Area =  $('#txArea').val();
    var ruta = "addContacto";
    
    if($('#hnAccion').val())
    {
        ruta = "editContacto";
    }

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
            'Nombre': Nombre,
            'Entidad': Entidad,
            'Cargo': Cargo,
            'Telefono': Telefono,
            'Correo': Correo,
            'Area': Area
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
/****************
    Al mostrar la modal se procede a posicionar  el cursor segun el caso (Guardar o Actualizar)
    en su respectivo campo.
*****************/
$('#ventana').on('shown.bs.modal', function () {
    $('#txNombre').focus();
});
/****************
    Detalle del Registro.
*****************/
$(document).on('click', '#lkVer', function()
{
    $('#txNombre').prop('disabled', true);
    $('#txEntidad').prop('disabled', true);
    $('#txCargo').prop('disabled', true);
    $('#txTelefono').prop('disabled', true);
    $('#txCorreo').prop('disabled', true);
    $('#txArea').prop('disabled', true);
    
    
    $('#txNombre').val($(this).data('nombre'));
    $('#txEntidad').val($(this).data('entidad'));
    $('#txCargo').val($(this).data('cargo'));
    $('#txTelefono').val($(this).data('telefono'));
    $('#txCorreo').val($(this).data('correo'));
    $('#txArea').val($(this).data('area'));

    $('#btGuardar').hide();
    $('#ventana').modal('show');
});