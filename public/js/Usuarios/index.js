/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#divTxConfirmacion').show();
    $('#divTxPassword').show();
    $('#divBtActualizarPass').hide();
    $('#hnId').val("");
    $('#txNombre').val("");
    $('#txApellido').val("");
    $('#txCedula').val("");
    $('#rdSexo').val("");
    $('#txFechaNacimiento').val("");
    $('#txTelefono').val("");
    $('#txEmail').val("");
    $('#txPassword').val("");
    $('#txConfirmacion').val("");
    $('#slEstado').val("");
    $('#slPerfil').val("");
    $('#hnAccion').val("");
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
    $('#divTxConfirmacion').hide();
    $('#divTxPassword').hide();
    $('#divBtActualizarPass').show();
    $('#hnId').val($(this).data('id'));
    $('#txNombre').val($(this).data('nombre'));
    $('#txApellido').val($(this).data('apellido'));
    $('#txCedula').val($(this).data('cedula'));
    $('#rdSexo').val($(this).data('sexo'));
    if($(this).data('sexo') == 'M')
    {
        $('#rdMasculino').parent().addClass('checked');
        $('#rdFemenino').parent().removeClass('checked');
    }
    else
    {
        $('#rdFemenino').parent().addClass('checked');
        $('#rdMasculino').parent().removeClass('checked');
    }
    $('#txFechaNacimiento').val($(this).data('fechanacimiento'));
    $('#txTelefono').val($(this).data('telefono'));
    $('#txEmail').val($(this).data('email'));
    $('#slEstado').val($(this).data('estado'));
    $('#slPerfil').val($(this).data('perfil'));
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
    var id = $('#hnId').val();
    var Nombre =  $('#txNombre').val();
    var Apellido =  $('#txApellido').val();
    var Cedula =  $('#txCedula').val();
    if($('#rdMasculino').parent().hasClass('checked'))
    {
        Sexo = 'M';
    }
    else
    {
        Sexo = 'F';
    }
    var FechaNacimiento =  $('#txFechaNacimiento').val();
    var Telefono =  $('#txTelefono').val();
    var Email =  $('#txEmail').val();
    var Password =  $('#txPassword').val();
    var Confirmacion =  $('#txConfirmacion').val();
    var Estado =  $('#slEstado').val();
    var Perfil =  $('#slPerfil').val();
    var ruta = "addUsuario";
    
    if($('#hnAccion').val())
    {
        ruta = "editUsuario";
    }
    
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
            'Nombre': Nombre,
            'Apellido': Apellido,
            'Cedula': Cedula,
            'Sexo': Sexo,
            'FechaNacimiento': FechaNacimiento,
            'Telefono': Telefono,
            'Email': Email,
            'Password': Password,
            'Confirmacion': Confirmacion,
            'Estado': Estado,
            'Perfil': Perfil
        },
        success: function(data)
        {
            resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
        }
    });
});
/****************
    Evento del link en fila para Eliminar
*****************/
$(document).on('click', '#lkDelete', function()
{
    var id = $(this).data('id');
    var Nombre = $(this).data('nombre');

    bootbox.confirm(
    {
        message: "\u00BFSeguro que desea eliminar al Usuario: [" + Nombre + "]?",
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
                var ruta = "deleteUsuario";
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'id': id
                    },
                    success: function(data){
                        $('#contenido').html(data.tabla);
                        tabla();//funcion llamada desde el archivo public/js/global.js
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
    Evento Cambio de Contraseña.
*****************/
$(document).on('click', '#lkCambioPassword', function()
{
    $('#txPassword1').val("");
    $('#txPassword2').val("");
    $('#ventana').modal('hide');
    $('#vtnPassword').modal('show');
});
/****************
    Evento Actualizar Contraseña.
*****************/
$(document).on('click', '#btActualizarPass', function()
{
    var id = $('#hnId').val();
    var Password1 = $('#txPassword1').val();
    var Password2 = $('#txPassword2').val();
    
    bootbox.confirm(
    {
        message: "\u00BFSeguro que desea Cambiar la Contraseña?",
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
                var ruta = "editPassword";
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'id': id,
                        'Password1': Password1,
                        'Password2': Password2
                    },
                    success: function(data){
                        resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
                        $('#vtnPassword').modal('hide');
                    }
                });
            }
        }
    });
});
$(document).on('click', '#btAtras', function()
{
    $('#ventana').modal('show');
    $('#vtnPassword').modal('hide');
});
/****************
    Al mostrar la modal de cambio de contraseña se procede a posicionar  el cursor en el primer campo
*****************/
$('#vtnPassword').on('shown.bs.modal', function () {
    $('#txPassword1').focus();
});