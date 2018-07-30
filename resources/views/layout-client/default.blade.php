<!-- Este archivo con tiene la estructura general de la pagina el cual contiene
los include de cada uno y los Yield con el contenido de la pagina-->

<!DOCTYPE html>
<html lang="es-AR">
  <head>
      @include('includes-client.head')
      @yield('head')
  </head>
  <body>
    @include('includes-client.header')
    @yield('content')
    @include('includes-client.footer')
    @include('includes-client.scripts')
    @yield('scripts')
  </body>
</html>