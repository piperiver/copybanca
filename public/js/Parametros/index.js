/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#divCalcularIva').hide();
    $('#txCodigo').val("");
    $('#txDescripcion').val("");
    $('#txValor').val("");
    $('#txTipo').val("");
    $('#txModulo').val("");
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
    if($(this).data('tipo') == 'DESCUENTO')
    {
        $('#divCalcularIva').show();
    }
    else
    {
        $('#divCalcularIva').hide();
    }
    $('#txCodigo').val($(this).data('codigo'));
    $('#txDescripcion').val($(this).data('descripcion'));
    $('#txValor').val($(this).data('valor'));
    $('#txTipo').val($(this).data('tipo'));
    $('#txModulo').val($(this).data('modulo'));
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
    var Valor =  $('#txValor').val();
    var Tipo =  $('#txTipo').val();
    var Modulo =  $('#slModulo').val();
    var ruta = "addParametro";

    if($('#hnAccion').val())
    {
        ruta = "editParametro";
    }

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Codigo': Codigo,
            'Descripcion': Descripcion,
            'Valor': Valor,
            'Tipo': Tipo,
            'Modulo': Modulo
        },
        success: function(data)
        {
            resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
            //ModalC.modal('hide');
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
$('#btCalcularIva').click(function()
{
    //var ModalC = modalCarga("Por Favor espere...");//funcion llamada desde el archivo public/js/global.js
    var Valor = $('#txValor').val();
    var Codigo = $('#txCodigo').val();

    if(Valor == '' || Valor == null)
    {
        alert('El campo valor no tiene un dato valido');
        return;
    }

    var ruta = 'calcularIvaCredito';

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Codigo': Codigo,
            'Valor': Valor
        },
        success: function(data)
        {
            resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
            //ModalC.modal('hide');
        }
    });
});