$("#Email").change(function(){
    $.ajax({
        type: 'post',
        url: 'BusquedaRegistro',
        data: {
            '_token': $('input[name=_token]').val(),
            'Email': $(this).val()
        },
        success: function(data) {
            $('#Celular').val(data.Celular);
            $('#Nombre').val(data.Nombre);
        }
    });
});