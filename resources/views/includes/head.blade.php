
<!--Archivo que contiene el contenido del HEAD del html en el cual se adicionan
las librerias externas e internas para la construccion de las vistas del sitio
web en cada uno de sus modulos. Se crea asi con el fin de que si se necesita
agregar algun plugin se pueda agregar en este solo archivo y asi afectar todas
las vistas que extiendan de este -->

<meta charset="utf-8" />
<title>Bancarizate</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta content="Plataforma de gestion de creditos" name="description" />
<meta content="Juan David Osorio" name="author" />
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

<!-- INICIA EL LAYOUT -->
<link href="//fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
<!-- TERMINA EL LAYOUT -->
<!-- INICIAN LOS ESTILOS GLOBALES PRINCIPALES -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- FINALIZAN LOS ESTILOS GLOBALES PRINCIPALES -->
<!-- INICIA EL NIVEL DE PLUGINS NECESARIOS -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet" type="text/css" />
<!-- FINALIZA EL NIVEL DE PLUGINS NECESARIOS -->
  <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/clockface/css/clockface.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap3-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css" />
@yield('styleSelect')
        <!-- END PAGE LEVEL PLUGINS -->
<!-- INICIA EL THEME GLOBAL -->
<link href="{{ asset('assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
<link href="{{ asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
<!-- GINALIZA EL THEME GLOBAL -->
<!-- INICIA EL LAYOUT THEME -->
<link href="{{ asset('assets/layouts/layout5/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/layouts/layout5/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
<!-- FINALIZA EL LAYOUT THEME -->
<!-- BEGIN PAGE LEVEL STYLES-->
<!-- Plugin para los iconos -->
<link href="{{ asset('assets/global/plugins/fontawesome-iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" />
<link href="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Estilos para las paginas de errores -->
<link href="{{ asset('assets/pages/css/error.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/global.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/cleave.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('formulario_solicitud/inputmask/inputmask/bindings/inputmask.binding.js') }}"
        type="text/javascript"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsTrWZ63UJA29sWi0h2KN7P-PgvkU5d-U&libraries=places"></script>
<!-- FINALIZA EL HEAD -->

<script type="text/javascript">
    $(document).ready(function () {
        var options = {
            types: ['address'],
            componentRestrictions: {country: 'co'}
        };
        input = document.getElementsByClassName('address');
        console.log(input);
        for (i = 0; i < input.length; i++) {
            autocomplete = new google.maps.places.Autocomplete(input[i], options);
            console.log(autocomplete);
        }
    });

</script>