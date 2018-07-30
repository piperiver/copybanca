@extends('layout.default')
@section('content')
@include('flash::message')

                    <!-- BEGIN BREADCRUMBS -->
                    <div class="breadcrumbs">
                        <h1>101 Error general</h1>                        
                    </div>
                    <!-- END BREADCRUMBS -->
                    <!-- BEGIN PAGE BASE CONTENT -->
                    <div class="row">
                        <div class="col-md-12 page-404">
                            <div class="number font-green"> 401 </div>
                            <div class="details">
                                <h3>Oops!</h3>
                                <p>{{ $mensaje }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE BASE CONTENT -->

@endsection