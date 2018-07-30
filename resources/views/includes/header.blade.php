<!-- En este archivo se encuentra todo el HEADER del menu-->
@inject('Menu', 'App\Http\Controllers\PermisosController')
@inject('fotoPerfil', 'App\Http\Controllers\UsersController')


<nav class="navbar mega-menu" role="navigation">
    <div class="container-fluid container-no-padding">
        <div class="clearfix navbar-fixed-top">
            <!-- Brand and toggle get grouped for better mobile display -->
            <button type="button" class="navbar-toggle" id="btnMenuMovil">
                <span class="sr-only">Toggle navigation</span>
                <span class="toggle-icon">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </span>
            </button>
            <!-- End Toggle Button -->
            <!-- BEGIN LOGO -->
            <a id="index" class="page-logo" href="/">
                <img src="{{ asset('assets/layouts/layout5/img/logosistema.png') }}" alt="Logo">
            </a>
             <div class="topbar-actions">

                <!-- BEGIN USER PROFILE -->
                <div class="btn-group-img btn-group">
                    @if(isset(Auth::user()->id))
                        <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span>{{Auth::user()->nombre}}</span>
                                <img src="{{asset('fotosperfiles')}}/{{$fotoPerfil->fotoPerfil()}}" alt="">
                        </button>
                        <ul class="dropdown-menu-v2" role="menu">
                            <li>
                                <a href="{{config('constantes.RUTA')}}MiPerfil">
                                    <i class="icon-user"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a href="{{config('constantes.RUTA')}}Agenda/{{Auth::user()->id}}">
                                    <i class="icon-calendar"></i> Mi Calendario </a>
                            </li>
                            <li>
                                <a href="{{ url('/logout') }}" onclick="deleteCookies(); event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                    <i class="icon-key"></i> Cerrar Sesion
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    @endif
                </div>
                <!-- END USER PROFILE -->
               
            </div>
            <!-- END TOPBAR ACTIONS -->
        </div>
            <!-- END LOGO -->


    @if(isset(Auth::user()->id))

        <!-- BEGIN TOPBAR ACTIONS -->

        <!-- BEGIN HEADER MENU -->
        <div id="container-menu-desktop" class="nav-collapse collapse navbar-collapse navbar-responsive-collapse">
            <ul class="nav navbar-nav">
              @php($Modulos = $Menu->CargarModulos(Auth::user()->perfil))              
              @foreach($Modulos as $Modulo)
                  @if(isset($_COOKIE['Modulo']) && $_COOKIE['Modulo'] == $Modulo->Modulo)
                  <li class="dropdown dropdown-fw active selected">
                  @else
                  <li class="dropdown dropdown-fw">
                  @endif
                      <a href="javascript:;" class="text-uppercase title-modulo">
                          <i class="fa {{$Modulo->Icono}}"></i> {{$Modulo->Modulo}}
                      </a>
                      <ul class="dropdown-menu dropdown-menu-fw">
                        @php($Vistas = $Menu->CargarVistas($Modulo->Codigo,Auth::user()->perfil))
                        @foreach($Vistas as $Vista)                            
                            <li class="gestiones">
                                <a href="{{config('constantes.RUTA').$Vista->Ruta}}" class="{{ (isset($_COOKIE['Gestion']) && $_COOKIE['Gestion'] == $Vista->Forma)? 'active-gestion' : '' }}">
                                    <i class="fa {{$Vista->Icono}}"></i> {{$Vista->Forma}}
                                </a>
                            </li>
                        @endforeach
                      </ul>
                  </li>                  
              @endforeach
            </ul>
        </div>
        <!-- END HEADER MENU -->
        <!-- INICIO MENU VERSION MOVIL -->
        <div class="container-menu-movil nav-collapse">
            <ul class="nav navbar-nav">
              @php($Modulos = $Menu->CargarModulos(Auth::user()->perfil))
              @php($seleccion = true)
              @foreach($Modulos as $Modulo)
                  @if($seleccion)
                  <li class="dropdown dropdown-fw">
                      <a href="javascript:;" class="text-uppercase">
                          <i class="fa {{$Modulo->Icono}}"></i> {{$Modulo->Modulo}}
                      </a>
                      <ul class="dropdown-menu dropdown-menu-fw" style="padding-top: 10px">
                        @php($Vistas = $Menu->CargarVistas($Modulo->Codigo,Auth::user()->perfil))
                        @foreach($Vistas as $Vista)
                            <li>
                                <a href="{{config('constantes.RUTA').$Vista->Ruta}}">
                                    <i class="fa {{$Vista->Icono}}"></i> {{$Vista->Forma}}
                                </a>
                            </li>
                        @endforeach
                      </ul>
                  </li>
                  @php($seleccion = false)
                  @else
                  <li class="dropdown dropdown-fw">
                      <a href="javascript:;" class="text-uppercase">
                          <i class="fa {{$Modulo->Icono}}"></i> {{$Modulo->Modulo}}
                      </a>
                      <ul class="dropdown-menu dropdown-menu-fw" style="padding-top: 10px">
                        @php($Vistas = $Menu->CargarVistas($Modulo->Codigo,Auth::user()->perfil))
                        @foreach($Vistas as $Vista)
                            <li>
                                <a href="{{config('constantes.RUTA').$Vista->Ruta}}">
                                    <i class="fa {{$Vista->Icono}}"></i> {{$Vista->Forma}}
                                </a>
                            </li>
                        @endforeach
                      </ul>
                  </li>
                  @endif
              @endforeach
            </ul>
        </div>
        <!-- FIN MENU VERSION MOVIL -->
        @endif

    </div>
    <!--/container-->
</nav>
