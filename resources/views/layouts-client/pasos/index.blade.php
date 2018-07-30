@extends('layout-client.default')
@section('content')
<div class="container">
        <div class="row container-contenido">
            <div class="col-md-6 lateral borderVertical">
                <!-- INICIO CAROUSEL -->
                <div id="carouselDesc" class="carousel slide" data-ride="carousel">
                  <!-- Indicators -->
                  <ol class="carousel-indicators">
                    <li data-target="#carouselDesc" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselDesc" data-slide-to="1"></li>
                    <li data-target="#carouselDesc" data-slide-to="2"></li>
                    <li data-target="#carouselDesc" data-slide-to="3"></li>
                  </ol>

                  <!-- Wrapper for slides -->
                  <div class="carousel-inner" role="listbox">                    
                    <div class="item active">
                        <h2>CENTRALES DE RIESGO</h2>
                        <img src="{{ asset('img/tendencia.png') }}" alt="imagen" >
                        <p>La valoración consulta la información sobre tu hábito de pago en las centrales de riesgo, datacrédito y transúnion (conocido como CIFIN)</p>
                    </div>
                    <div class="item">
                        <h2>CENTRALES DE RIESGO</h2>
                        <img src="{{ asset('img/checklist.png') }}" alt="imagen" >
                        <p>La valoración consulta la información sobre tu hábito de pago en las centrales de riesgo, datacrédito y transúnion (conocido como CIFIN)</p>
                    </div>
                    <div class="item">
                        <h2>CENTRALES DE RIESGO</h2>
                        <img src="{{ asset('img/calculatorpng.png') }}" alt="imagen" >
                        <p>La valoración consulta la información sobre tu hábito de pago en las centrales de riesgo, datacrédito y transúnion (conocido como CIFIN)</p>
                    </div>
                    <div class="item">
                        <h2>CENTRALES DE RIESGO</h2>
                        <img src="{{ asset('img/banco.png') }}" alt="imagen" >
                        <p>La valoración consulta la información sobre tu hábito de pago en las centrales de riesgo, datacrédito y transúnion (conocido como CIFIN)</p>
                    </div>                    
                  </div>                  
                </div>
                <!-- FIN CAROUSEL -->
            </div>
            <div class="col-md-6 lateral">
                <h2 class="text-center title-pasos">PASOS PARA VALORARSE</h2>
                <ol class="rounded-list">
                    <li class="item-pasos">
                        <p class="title" data-desc="Debes ingresar tus datos reales (Nombre, cédula, celular y correo)" data-title="DATOS PERSONALES">DATOS PERSONALES</p>
                    </li>
                    <li class="item-pasos">
                        <p class="title" data-title="FORMAS DE PAGO" data-desc="Contamos con diferentes formas de pago, tales como: Efecty, Baloto, tarjeta debito o credito">FORMAS DE PAGO</p>
                    </li>
                    <li class="item-pasos">
                        <p class="title" data-title="VALIDACIÓN" data-desc="En este proceso pasaras por un sistema de verificacion de identidad, para brindar seguridad a los usuarios">VALIDACIÓN</p>
                    </li>
                    <li class="item-pasos">
                        <p class="title" data-title="VALORARSE" data-desc="En este momento ya tendras los resultados de tu valoracion y podras ser transformado">VALORARSE</p>
                    </li>
                </ol>
                <a class="btnValorate" href="Datos">Valórate</a>
            </div>
        </div><!-- row -->      
      </div><!-- container -->
@endsection