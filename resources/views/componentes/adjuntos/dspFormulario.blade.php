<div class="form-Adjuntos">
    <form class="form-horizontal formularioArchivos" enctype="multipart/form-data" id="formularioAdjuntos{{$idObjectAdjunto}}">
        {{ csrf_field() }}
        <!-- labels -->
        <div class="row">      
        @if($resultAdjunto != false)
            <div class="col-md-12 col-xs-12 col-sd-12 col-lg-12 text-center">
                <strong style="text-transform: uppercase">Adjuntar: {{ $resultAdjunto->Descripcion }}</strong>
            </div>
        @endif
        @if(is_array($tiposAdjunto))
            <div class="{{($nombreAdjunto == false)? "col-md-4 col-xs-12 col-sd-4 col-lg-4 col-lg-offset-1 col-md-offset-1 col-sd-offset-1" : "col-md-8 col-xs-12 col-sd-8 col-lg-8" }} ">
                <label for="tipoAdjunto">Tipo Adjunto</label>                
                <select id="tipoAdjunto" name="tipoAdjunto" class="form-control circle">
                            @foreach($tiposAdjunto as $tipo)                            
                                @if(is_array($tipoAdjuntoIgnore) && in_array($tipo["Codigo"], $tipoAdjuntoIgnore))
                                    @continue
                                @endif    
                                    <option value="{{$tipo['Codigo']}}">{{$tipo['Descripcion']}}</option>
                            @endforeach
                </select>                
            </div>    
        @endif
        
        @if($nombreAdjunto == false)
            <div class="marginX10 {{ ($resultAdjunto != false)? "col-md-7 col-xs-12 col-sd-7 col-lg-7" : "col-md-4 col-xs-12 col-sd-4 col-lg-4" }}">
                <label>Nombre</label>
                <input type="text" class="form-control circle nombreAdjunto" name="NombreArchivo" id="NombreArchivo{{$idObjectAdjunto}}" >
            </div>
        @endif
            <div class="{{ ($resultAdjunto != false)? (($nombreAdjunto == false)? "col-md-5 col-xs-12 col-sd-5 col-lg-5" : "col-md-12 col-xs-12 col-sd-12 col-lg-12") : "col-md-3 col-xs-12 col-sd-3 col-lg-3" }}">        
                <input type="file" class="filestyle componenetFile form-control ComponentArchivo" data-action="{{ config('constantes.RUTA') }}uploadAdjuntos" data-input="false" data-idelements="{{$idObjectAdjunto}}" id="ComponentArchivo{{$idObjectAdjunto}}" name="ComponentArchivo">
            </div>        
        </div>
        
        <input type="hidden" value="{{$otrosDatos}}" name="otrosDatos">        
    </form>
</div>