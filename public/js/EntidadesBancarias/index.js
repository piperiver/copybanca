/****************
    Evento del link "Crear"
*****************/

$('#lkSave').click(function(event)
{
    $('#Descripcion').val("");    
    $('#hnAccion').val("");
    $('#CastigoMora').val("S");
    $('#PazSalvo').val("S");
    
    $('#Entidades').val(null).trigger('change');
    $('#Descuento').val("0");
    $('#PData').val("0");
    $('#PCifin').val("0");
    $('#Politicas').html("");
    $('#btGuardar').addClass('green');
    $('#btGuardar').removeClass('yellow-gold');
    $('#btGuardar').text("Guardar");
    politicas = [];
    reEdit();
    $('#ventana').modal('show');
});
/****************
    Evento del link en fila para dirigirse a Actualizar
*****************/
$(document).on('click', '#lkEdit', function()
{       
    var propiedades = $(this).data('entidades');
    $("#idReg").val($(this).data('codigo'));
    $('#Descripcion').val($(this).data('descripcion'));    
    $('#CastigoMora').val($(this).data('castigo'));
    $('#PazSalvo').val($(this).data('paz'));    
    $('#Descuento').val($(this).data('dcto'));
    $('#PData').val($(this).data('pdata'));
    $('#PCifin').val($(this).data('pcifin'));
    $('#PazSalvo').val($(this).data('paz'));    
    $('#hnAccion').val("update"); // este campo oculto se utiliza para validar si el registro es para actualizar o Guardar
    $('#Politicas').html("");
    $('#Entidades').val(propiedades).trigger("change");
    console.log($(this).data('politicas'));
    politicas = $(this).data('politicas');
    reEdit();
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

/********** Funcion que Toma los arrays del formulario dinamico y retorna un Json ******/
function procesarJson(){
    var pagadurias = $("select[name='Pagaduria[]']").serializeArray();
    var nombramientos = $("select[name='Nombramiento[]']").serializeArray();
    var cargos = $("select[name='Cargo[]']").serializeArray();
    var montos = $("input[name='Monto[]']").serializeArray();
    var tasas = $("input[name='Tasa[]']").serializeArray();
    var plazos = $("input[name='Plazo[]']").serializeArray();
    var antiguedades = $("input[name='Antiguedad[]']").serializeArray();
    var edades = $("input[name='EdadPension[]']").serializeArray();
    
    var json = [];
    for(var i = 0;i < pagadurias.length;i++){
        json.push({'Pagaduria': pagadurias[i].value, 
                   'Nombramiento': nombramientos[i].value, 
                   'Monto': limiparPuntos(montos[i].value), 
                   'Tasa': tasas[i].value,
                   'Plazo': plazos[i].value,
                   'Antiguedad': antiguedades[i].value,
                   'Edad': edades[i].value, 
                   'Cargo':cargos[i].value });
    }
    return JSON.stringify(json);
}
/********************
Funcion para procesar las entidades seleccionadas con la libreria select2
**********/ 
function procesarEntidades(){
    var entidades = $("#Entidades").select2('data');
    var arrEntidades = [];
    for(var i=0;i <= entidades.length-1;i++){
        arrEntidades.push(entidades[i].text);
    }
    return JSON.stringify(arrEntidades);
}

/********************
 Evento para adicionar politicas
**********/ 
$(document).on('click', '#addPolitica', function(){
    var addHtml = "<div id='row'><div><div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                                        +"<div class='form-group'>"
                                            +"<label for='PazSalvo'>Pagaduria</label>"                                    
                                            +"<select class='form-control' name='Pagaduria[]'>"
                                                +"<option value='SEM CALI'>SEM CALI</option>"                                                            
                                                +"<option value='FODE VALLE'>FODE VALLE</option>"
                                            +"</select>"                                                                
                                        +"</div>"
                                    +"</div>"
                                    +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                                        +"<div class='form-group'>"
                                            +"<label for='PazSalvo'>Nombramiento</label>"                                    
                                            +"<select class='form-control' name='Nombramiento[]'>"
                                                +"<option value='PROP'>PROPIEDAD</option>"                                                            
                                                +"<option value='PRUE'>P. PRUEBA</option>"
                                                +"<option value='DEF'>P. V. DEF</option>"
                                                +"<option value='FIJO'>T. FIJO</option>"
                                                +"<option value='INDEF'>T. INDEFIN</option>"
                                                +"<option value='OTHER'>OTRO</option>"
                                            +"</select>"                                                                
                                        +"</div>"
                                    +"</div>"
                                    +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                                        +"<div class='form-group'>"
                                            +"<label for='Cargo'>Cargo</label>"                                    
                                            +"<select class='form-control' name='Cargo[]'>"
                                                +"<option value='ADM'>ADMINISTRATIVO</option>"                                                            
                                                +"<option value='DOC'>DOCENTE</option>"
                                                +"<option value='PEN'>PENSIONADO</option>"                                                
                                            +"</select>"                                                                
                                        +"</div>"
                                    +"</div>"
                                    +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                                        +"<div class='form-group'>"
                                            +"<label for='Tasa'>Monto/Limite</label>"                                                                            
                                            +"<input class='form-control puntosMiles' name='Monto[]' value=''>"                                    
                                        +"</div>"
                                    +"</div>"                                                                  
                    +"</div>"
                    +"<div>"
                        +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                            +"<div class='form-group'>"
                                +"<label for='Tasa'>Tasa</label>"                                                                            
                                +"<input class='form-control' name='Tasa[]' value=''>"                                    
                            +"</div>"
                        +"</div>"      
                        +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                            +"<div class='form-group'>"
                                +"<label for='Plazo'>Plazo</label>"
                                +"<input class='form-control' name='Plazo[]' value=''>"                                    
                            +"</div>"
                        +"</div>"
                        +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                            +"<div class='form-group'>"
                                +"<label for='Antiguedad'>Antiguedad</label>"                                                                            
                                +"<input class='form-control' name='Antiguedad[]' value=''>"                                    
                            +"</div>"
                        +"</div>"
                        +"<div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>"
                            +"<div class='form-group'>"                                
                                +"<label for='EPension'>Edad Retiro</label><span id='rmvPolitica'><i class='fa fa-minus-circle' aria-hidden='true' style='position: absolute;right: 15px;top: 0;'></i></span>"                                                                            
                                +"<input class='form-control' name='EdadPension[]' value=''>"                                    
                            +"</div>"
                        +"</div>"
                    +"</div></div>";
    $("#Politicas").append(addHtml);    
});
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
/********************
 Evento para adicionar politicas
**********/ 
$(document).on('click', '#rmvPolitica', function(){
    $(this).parent().parent().parent().parent().remove();
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
                var ruta = "deleteBanco";

                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Id': Codigo
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
        $('#txCodigo').focus();
    }
});

$(document).ready(function(){
   $('#ventana').on('hide.bs.modal', function (e) {       
       $('#Entidades').val(null).trigger("change");
    });     
});



function langSelect(){
    $("#Entidades").select2({
      language: "es"
    });
}

