/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#slBanco').val("");
    $('#slDesembolso').val("");
    $('#slTipoCuenta').val("");
    $('#txCuenta').val("");
    $('#hnAccion').val("");

    $("#slBanco").prop('disabled', false);
    $("#slDesembolso").prop('disabled', false);
    $("#slTipoCuenta").prop('disabled', false);
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
    $('#slBanco').val($(this).data('banco'));
    $('#slDesembolso').val($(this).data('entidaddesembolso'));
    $('#slTipoCuenta').val($(this).data('tipocuenta'));
    $('#txCuenta').val($(this).data('cuenta'));
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar
    
    //Se ajusta la modal "ventana" para la actualización.
    $("#slBanco").prop('disabled', true);
    $("#slDesembolso").prop('disabled', true);
    $("#slTipoCuenta").prop('disabled', true);
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
    var Banco = $('#slBanco').val();
    var EntidadDesembolso =  $('#slDesembolso').val();
    var TipoCuenta = $('#slTipoCuenta').val();
    var Cuenta =  $('#txCuenta').val();
    var ruta = "addCuentaBancaria";
    
    if($('#hnAccion').val())
    {
        ruta = "editCuentaBancaria";
    }

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Banco': Banco,
            'EntidadDesembolso': EntidadDesembolso,
            'TipoCuenta': TipoCuenta,
            'Cuenta': Cuenta
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
    var Banco = $(this).data('banco');
    var EntidadDesembolso = $(this).data('entidaddesembolso');
    var TipoCuenta = $(this).data('tipocuenta');

    bootbox.confirm(
    {
        message: "\u00BFSeguro que desea eliminar el registro de Código: [" + Banco + '-' + EntidadDesembolso + '-' + TipoCuenta + "]?",
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
                var ruta = "deleteCuentaBancaria";
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Banco': Banco,
                        'EntidadDesembolso': EntidadDesembolso,
                        'TipoCuenta': TipoCuenta
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
    Al mostrar la modal se procede a posicionar  el cursor segun el caso (Guardar o Actualizar)
    en su respectivo campo.
*****************/
$('#ventana').on('shown.bs.modal', function () {
    $('#txCuenta').focus();
});
