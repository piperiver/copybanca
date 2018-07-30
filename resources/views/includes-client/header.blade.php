
<div class="container-fluid menu center-block"> 
	<span class="glyphicon glyphicon-menu-hamburger pull-left boton1"></span>
	<img src="{{ asset('/assets/layouts/layout5/img/logosistema.png') }}" alt=""/>
	<a class="pointer pull-right login" title="Salir" onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
		<span class="glyphicon glyphicon-log-out"></span>
	</a> 
	<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
		{{ csrf_field() }}
	</form>
</div>
<div class="menu-vtm text-left" style="color: #fff; padding-left: 10px;">{{Auth::user()->nombre}}</div>