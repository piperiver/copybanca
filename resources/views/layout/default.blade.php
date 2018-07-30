<!-- Este archivo con tiene la estructura general de la pagina el cual contiene
los include de cada uno y los Yield con el contenido de la pagina-->

@inject('Utilidad', 'App\Librerias\UtilidadesClass')
        <!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

@include('includes.head')

@yield('encabezado')
<!-- Componenetes -->
    <link href="{{ asset('componentes/css/Adjuntos.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('componentes/js/Adjuntos.js') }}" type="text/javascript"></script>

</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo">
<div class="wrapper">
    <header class="page-header">
        @include('includes.header')
    </header>

    <div class="container-fluid container-no-padding">
        @yield('banner')
        <div class="page-content">
            @yield('content')
        </div>
        @include('includes.footer')
    </div>
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="ajaxModal">
        
    </div>
</div>
@include('includes.scripts')
</body>
</html>
