@if(count($errors))
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			&times;
		</button>
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

@if(session()->has('warning'))
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">
			&times;
		</button>
		<ul>
			{{session('warning')}}
		</ul>
	</div>
@endif
