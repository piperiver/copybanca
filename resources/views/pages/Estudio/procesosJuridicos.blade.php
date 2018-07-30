@inject('UtilidadesClass', 'App\Librerias\UtilidadesClass')
@foreach($listProcesosJuridicos as $proceso)
@php 
$juicios = $proceso->juicios;
@endphp
<div class="portlet box red pMaximo sinMarginBottom" style="margin-bottom: 0">
    <div class="portlet-title">
        <div class="caption">
            <strong>Consultado por: {{ $UtilidadesClass->getInfoUser($proceso->usuario)->nombre ." el d&iacute;a: ". date("d-m-Y" ,strtotime($proceso->fechaConsulta)) }} ({{ count($juicios) }} Procesos)</strong>
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand"></a>
        </div>                                    
    </div>
    <div class="portlet-body" style="display: none;">
        @if($proceso->status === "0" || $proceso->status === "2")
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Mensaje!</strong> {{ $proceso->mensajeError }}
            </div>
        @endif
        @if(count($juicios) > 0)
        <table class="table table-striped table-hover text-center todasObligaciones">
            <thead>
                <tr>
                    <th class="text-center">{{ config('constantes.EST_CONT1_DEMANDANTE') }}</th>
                    <th class="text-center">Tipo de Causa</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($juicios as $juicio)
                    <tr class="pointer" data-toggle="modal" data-target="#modalJuicio{{ $juicio->id }}">
                        <td>{{ $juicio->nombresActor }}</td>
                        <td>{{ $juicio->tipoDeCausa }}</td>
                        <td>{{ $juicio->estadoProceso }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>


@foreach($juicios as $juicio)
  <div class="modal fade modalEstudio" id="modalJuicio{{ $juicio->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="uppercase">Detalle del proceso</h4>
            </div>
            <div class="modal-body row">
                <div class="col-md-6">   
                    <label class="bold">CIUDAD</label>
                    <p>{{ $juicio->ciudad }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">N&Uacute;MERO EXPEDIENTE</label>
                    <p>{{ $juicio->expediente }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">FECHA INICIO</label>
                    <p>{{ date("d-m-Y", strtotime($juicio->fechaInicioProceso)) }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">FECHA &Uacute;LTIMO MOVIMIENTO</label>
                    <p>{{ date("d-m-Y", strtotime($juicio->fechaUltimoMovimiento)) }}</p>
                </div>
                <div class="col-md-6">  
                    <label class="bold">N&Uacute;MERO PROCESO</label>
                    <p>{{ $juicio->idJuicio }}</p>
                </div>
                <div class="col-md-6">  
                    <label class="bold">N&Uacute;MERO JUZGADO</label>
                    <p>{{ $juicio->numeroJuzgado }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">TIPO DE JUZGADO</label>
                    <p>{{ $juicio->tipoDeJuzgado }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">RANGO PRETENCIONES</label>
                    <p>{{ $juicio->rangoPretenciones }}</p>
                </div>
                <div class="col-md-6">      
                    <label class="bold">GARANTIAS</label>
                    <p>{{ $juicio->tieneGarantias }}</p>
                </div>
                
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>        
            </div>
        </div>
    </div>
</div> 
@endforeach


@endforeach

