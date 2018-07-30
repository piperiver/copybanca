function inicializarCirculos(){
    
    $('#pendiente').knob({            
            "width": 110,
            "height": 113,
            "fgColor":"#ec1c24",
            "skin":"tron",
            "angleOffset": 0,
            "thickness ": .2,
            "displayPrevious": true,
            "cursor":true
        });     
        $(".dial").knob({
            "width": 110,
            "height": 113,
            "thickness ": 1,
            "fgColor":"#ec1c24"
        });
}
$(document).ready(function ()
{
    inicializarCirculos();
    $('#resultado').DataTable
    ({
        responsive: true, //Indica que al cambiar el tamaño del navegador los registros se deben adaptar.
        "order" : [] //Deshabilita el orden que da el DataTable
    });
});
/****************
    Al momento de Consultar
*****************/
$('#lkConsultar').click(function()
{
    //var ModalC = modalCarga("Por Favor espere...");//funcion llamada desde el archivo public/js/global.js
    
    var Cedula = $('#txCedula').val();
    var Nombre =  $('#txNombre').val();
    var FechaValoracion =  $('#txFechaValoracion').val();
    var Pagaduria =  $('#txPagaduria').val();
    var Estado =  $('#slEstado').val();
    var ValorCredito =  $('#txValorCredito').val();
    var ruta = "Consulta";
    
    $.ajax({
        type: 'post',
        url: ruta,
        data: {
            '_token': $('input[name=_token]').val(),
            'Cedula': Cedula,
            'Nombre': Nombre,
            'FechaValoracion': FechaValoracion,
            'Pagaduria': Pagaduria,
            'Estado': Estado,
            'ValorCredito': ValorCredito
        },
        success: function(data)
        {
            $('#contenido').html(data.tabla);
            $('#resultado').DataTable
            ({
                responsive: true, //Indica que al cambiar el tamaño del navegador los registros se deben adaptar.
                "order" : [] //Deshabilita el orden que da el DataTable
            });
            //ModalC.modal('hide');
        }
    });
});