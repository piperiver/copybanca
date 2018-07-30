/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#txId').val("");
    $('#txMensaje').val("");
    $('#txCausa').val("");
    $('#txSolucion').val("");
    $('#hnAccion').val("");
    $("#txId").prop('disabled', false);
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
    $('#txId').val($(this).data('id'));
    $('#txMensaje').val($(this).data('mensaje'));
    $('#txCausa').val($(this).data('causa'));
    $('#txSolucion').val($(this).data('solucion'));
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar

    //Se ajusta la modal "ventana" para la actualización.
    $("#txId").prop('disabled', true);
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
    var Id = $('#txId').val();
    var Mensaje =  $('#txMensaje').val();
    var Causa =  $('#txCausa').val();
    var Solucion =  $('#txSolucion').val();
    var ruta = "addMensaje";

    if($('#hnAccion').val())
    {
        ruta = "editMensaje";
    }

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'id': Id,
            'Mensaje': Mensaje,
            'Causa': Causa,
            'Solucion': Solucion
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
  var Id = $(this).data('id');

  bootbox.confirm(
        {
            message: "\u00BFSeguro que desea eliminar el registro de Código: [" + Id + "]?",
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
                    var ruta = "deleteMensaje";
                    $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                    '_token': $('input[name=_token]').val(),
                    'id': Id
                    },
                    success: function(data){
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
    Al mostrar la modal se procede a posicionar  el cursor.
*****************/
$('#ventana').on('shown.bs.modal', function () {
    if($('#hnAccion').val())
    {
        $('#txMensaje').focus();
    }
    else
    {
        $('#txId').focus();
    }
});
