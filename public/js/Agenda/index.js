
   


function renderFilter(){
    var user = $('#slUserAg').val();   
    $("#calendar").fullCalendar('removeEvents');
    $.ajax({
        url: '/getUser',
        data: {'_token': $('input[name=_token]').val(), user},
        type: "POST",
        success: function(json) {
            if(json.errores == true){
                resultadoEvento(json);//funcion llamada desde el archivo public/js/global.js
            }else{ 
                
                $("#calendar").fullCalendar('renderEvents', JSON.parse(json), true);
            }
        }
    });
}

$(document).ready(function(){
        var calendar = $('#calendar').fullCalendar({  // Se instancia el formulario
            header:{
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            locale: 'es',
            editable: true,
            selectable: true,
            allDaySlot: false,
            
            events: JSON.parse($('#eventos').val()),  //Se llaman los eventos y se parse a Json para ser aceptado por la libreria
   
            
            eventClick:  function(event, jsEvent, view) {  // Se dispara con el evento click sobre el calentario
                endtime = $.fullCalendar.moment(event.end).format('h:mm');
                starttime = $.fullCalendar.moment(event.start).format('dddd, MMMM Do YYYY, h:mm');
                var mywhen = starttime + ' - ' + endtime;
                $('#modalTitle').val(event.title);
                $('#modalLugar').val(event.lugar);
                $('#modalDescripcion').text(event.descripcion);
                $('#modalUsuario').val(event.nombre);
                $('#modalWhen').text(mywhen);
                $('#eventID').val(event.id);
                $('#calendarModal').modal();
            },
            
            select: function(start, end, jsEvent) {  // Se dispara si se da click en un campo vacio para agregar uno nuevo
                endtime = $.fullCalendar.moment(end).format('h:mm');
                starttime = $.fullCalendar.moment(start).format('dddd, MMMM Do YYYY, h:mm');
                var mywhen = starttime + ' - ' + endtime;
                start = moment(start).format();
                end = moment(end).format();
                $('#createEventModal #startTime').val(start);
                $('#createEventModal #endTime').val(end);
                $('#createEventModal #diaHora').val(mywhen);
                $('#createEventModal').modal('toggle');
           },
           eventDrop: function(event, delta){ // Evento cuando se arrastra con el mouse
               $.ajax({
                   url: '/editAgenda',
                   data: {'_token': $('input[name=_token]').val(), titulo: event.title, inicio: moment(event.start).format(), fin: moment(event.end).format(), id: event.id},
                   type: "POST",
                   success: function(json) {
                   //alert(json);
                   }
               });
           },
           eventResize: function(event) {  // Evento cuando se redimensiona con el mouse
               $.ajax({
                   url: '/editAgenda',
                   data: {'_token': $('input[name=_token]').val(), titulo: event.title, inicio: moment(event.start).format(), fin: moment(event.end).format(), id: event.id},
                   type: "POST",
                   success: function(json) {
                       //alert(json);
                   }
               });
           }
        });
               
       $('#submitButton').on('click', function(e){ // Evento sobre el
           // boton submit
           e.preventDefault();
           doSubmit(); // Envia el formulario a la funcion
       });
       
       $('#deleteButton').on('click', function(e){ // Evento sobre el
           // boton delete
           e.preventDefault();
           doDelete(); //Envia la data a la funcion para ser borrada
       });
       
       function doDelete(){  // Evento para borrar 
           $("#calendarModal").modal('hide');
           var eventID = $('#eventID').val();
           $.ajax({
               url: '/dltAgenda',
               data: {'_token': $('input[name=_token]').val(), eventID},
               type: "POST",
               success: function(json) {  
                 $("#calendar").fullCalendar('removeEvents',eventID); 
               }
           });
       }
       function doSubmit(){ // Evento para crear
           var titulo = $('#titulo').val();
           var inicio = $('#startTime').val();
           var fin = $('#endTime').val();
           var lugar = $('#lugar').val();
           var descripcion = $('#descripcion').val();
           var usuario = $('#usuario').val();
           
           $.ajax({
               url: '/addAgenda',
               data: {'_token': $('input[name=_token]').val(), titulo, inicio, fin, lugar, descripcion, usuario},
               type: "POST",
               success: function(json) {
                   if(json.errores == true){
                       resultadoEvento(json);//funcion llamada desde el archivo public/js/global.js
                   }else{
                       $("#createEventModal").modal('hide');
                       resultadoEvento(json);//funcion llamada desde el archivo public/js/global.js
                        $('#titulo').val("");
                        $('#startTime').val("");
                        $('#endTime').val("");
                        $('#lugar').val("");
                        $('#descripcion').val("");
                        
                   $("#calendar").fullCalendar('renderEvent',
                        {
                            id: json.id,
                            title: titulo,
                            start: inicio,
                            end: fin,
                            lugar: lugar,
                            descripcion: descripcion,
                            usuario: usuario
                        },
                   true);
                   }
                   
                   
               }
           });
           
       }
       
    });
    
  




























































