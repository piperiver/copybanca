/****************
    Evento del link "Crear"
*****************/
$('#lkSave').click(function(event)
{
    $('#slPerfil').val("");
    $('#slForma').val("");
    $('#ckInsertar').val("");
    $('#ckInsertar').parent().removeClass('checked');
    $('#ckActualizar').val("");
    $('#ckActualizar').parent().removeClass('checked');
    $('#ckEliminar').val("");
    $('#ckEliminar').parent().removeClass('checked');
    $('#hnAccion').val("");

    $("#slPerfil").prop('disabled', false);
    $("#slForma").prop('disabled', false);
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
    $('#slPerfil').val($(this).data('perfil'));
    $('#slForma').val($(this).data('forma'));

    if($(this).data('insert') == 'S')
    {
        $('#ckInsertar').parent().addClass('checked');
    }
    else
    {
        $('#ckInsertar').parent().removeClass('checked');
    }

    if($(this).data('update') == 'S')
    {
        $('#ckActualizar').parent().addClass('checked');
    }
    else
    {
        $('#ckActualizar').parent().removeClass('checked');
    }

    if($(this).data('delete') == 'S')
    {
        $('#ckEliminar').parent().addClass('checked');
    }
    else
    {
        $('#ckEliminar').parent().removeClass('checked');
    }
    
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar
    
    //Se ajusta la modal "ventana" para la actualización.
    $("#slPerfil").prop('disabled', true);
    $("#slForma").prop('disabled', true);
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
    var Perfil = $('#slPerfil').val();
    var Forma =  $('#slForma').val();
    var Insertar = "";
    var Actualizar =  "";
    var Eliminar =  "";
    var ruta = "addPermiso";

    if($('#ckInsertar').parent().hasClass('checked'))
    {
        Insertar = "S";
    }
    else
    {
        Insertar = "N";
    }

    if($('#ckActualizar').parent().hasClass('checked'))
    {
        Actualizar =  "S";
    }
    else
    {
        Actualizar =  "N";
    }

    if($('#ckEliminar').parent().hasClass('checked'))
    {
        Eliminar =  "S";
    }
    else
    {
        Eliminar =  "N";
    }

    if($('#hnAccion').val())
    {
        ruta = "editPermiso";
    }

    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Perfil': Perfil,
            'Forma': Forma,
            'Insertar': Insertar,
            'Actualizar': Actualizar,
            'Eliminar': Eliminar
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
    var Perfil = $(this).data('perfil');
    var Forma = $(this).data('forma');

    bootbox.confirm(
    {
        message: "\u00BFSeguro que desea eliminar el registro de Código: [" + Perfil + '-' + Forma + "]?",
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
                var ruta = "deletePermiso";
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Perfil': Perfil,
                        'Forma': Forma
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
    $('#ckInsertar').focus();
});
