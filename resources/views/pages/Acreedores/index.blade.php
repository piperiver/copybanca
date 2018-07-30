@extends('layout.default')

@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Acreedores
                    </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a data-url="/acreedores/create" name="lkSave" class="btn btn-default btn-sm cargarModalAjax">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        @endif
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    @include('pages.Acreedores.__list')
                </div>
            </div>
        </div>
    </div>
@endsection