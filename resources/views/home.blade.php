
@inject('ComponentAdjuntos', 'App\Librerias\ComponentAdjuntos')
@extends('layout.default')

@section('content')
<h1>Esta es la vista principal</h1>
<div>{{$ComponentAdjuntos->dspFormulario(1,"pailas","SIS", "SIST", "login")}}</div>
@endsection

