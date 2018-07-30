$( document ).ready(function() {
       
    $('#generarReporte').click(function(){
        
        $("#formulario").validate({
            errorClass: "field-error",
            validClass: "field-success",
            rules: {
                tipo_reporte: { required: true},
                anno: { required: true},
                mes: { required: true}
            },
            messages: {
                tipo_reporte: {
                            required: "El campo tipo de reporte es requerido"
                },
                anno: { 
                            required: "El campo año es requerido"
                },
                mes: { 
                            required: "El campo mes es requerido"
                }
            },
            submitHandler: function(form){
                
                var url = $("#dominioPrincipal").val();
                var ruta = url + "reportes/ajaxLstTable";
                $('#tabla_reporte').html("");
                $.ajax({

                    url:ruta,
                    type: "POST",
                    data: $("#formulario").serialize(),
                    dataType: "json",

                    success:function(respuesta){
                       
                        if(respuesta.msg.length > 0){
                            $('#descripcion_mensaje').html(respuesta.msg);
                            $('#mensaje').modal('show');
                        }else{
                            //Actualizacion de contenido en tablas 
                            $('#title_reporte').html(respuesta.title);
                            $('#tabla_reporte').html(respuesta.tabla_reporte);
                            $('#tabla_reporte').css('display','block');

                            $('html,body').animate({
                                 scrollTop: $("#tabla_reporte").offset().top
                            }, 2000);
                            
                            $('.iniciarDatatable').DataTable({
                                scrollX: false,
                                "order" : [], //Deshabilita el orden que da el DataTable
                                "columnDefs": [{ className: "dt-head-left", "targets": [ 0 ] }]
                           });


                        }
                    },

                    error:function(jqHRX,textStatus, errorThrown){
                        console.log("Error: "+errorThrown);
                    }

                });

            }
        });
        
    });
    
   $("#anno").change(function(){
        
       var meses = [
                    'Enero',
                    'Febrero',
                    'Marzo',
                    'Abril',
                    'Mayo',
                    'Junio',
                    'Julio',
                    'Agosto',
                    'Septiembre',
                    'Octubre',
                    'Noviembre',
                    'Diciembre'
                    ];
        
        $('#mes').html("");

        $( "#mes" ).append( "<option value=''>Seleccione el mes</option>" );

        var ano = (new Date).getFullYear();
        
        if(ano == $(this).val()){
            
            var mesActual = (new Date).getMonth() + 1;
            var count = 0;            
           for(var time = 1; time<=mesActual; time++ ){
                $( "#mes" ).append( "<option value='"+time+"'>"+meses[count]+"</option>" );
                count++;
            }
            
        }else{
            
            var count = 0;
            for(var time = 1; time<=meses.length; time++ ){
                $( "#mes" ).append( "<option value='"+time+"'>"+meses[count]+"</option>" );
                count++;
            }            
        }
    });
    
    
    $('.copyToClipboard').click(function(e) {

        e.preventDefault();		
        copyToClipboard($(this).data('source'));
    });

});

  
function copyToClipboard(tableId){ 
    var textRange = document.body.createTextRange(); 
    textRange.moveToElementText(document.getElementById(tableId)); 
    textRange.execCommand("Copy");
}

