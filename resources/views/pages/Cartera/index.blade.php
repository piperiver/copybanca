@extends('layout.default')
@section('content')
    <div class="row">      
        <div class="col-md-12">
         <div class="portlet box main-color">
             <div class="portlet-title text-center" style="padding: 3px; min-height: 0;">                 
                    <strong>INFORMACIÓN DEL USUARIO</strong>                                                      
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3 col-sm-3 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">NOMBRE</label></div>
                                <span class="uppercase">{{ $infoEstudio[0]->nombre }} {{ $infoEstudio[0]->apellido }}</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">PAGADURIA:</label></div>
                                <span class="uppercase">{{ $infoEstudio[0]->pagaduria }}</span>                      
                            </div>
                        </div>        

                        <div class="col-md-2 col-sm-2 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">VALOR CREDITO:</label></div>
                                <span>${{ number_format($vlrCredito, 0, ",", ".") }}</span>                                
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-2 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">CUOTA</label></div>
                                <span>${{ number_format($cuota, 0, ",", ".") }}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-1 col-sm-1 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">TASA:</label></div>
                                <span>{{ $tasa*100 }}%</span>                                
                            </div>
                        </div>

                        <div class="col-md-1 col-sm-1 col-xs-6 text-center">
                            <div class="form-group">
                                <div><label for="" class="bold">PLAZO</label></div>
                                <span>{{ $plazo }} meses</span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
         </div>        
      </div>
    </div>    
    <div class="row">
        <div class="col-md-12">
             <div class="portlet box main-color">
                <div class="portlet-title text-center" style="padding: 6px; min-height: 0;">                 
                       <strong>PLAN DE AMORTIZACIÓN</strong>                                                      
                       <a target="_blank" href="{{ config("constantes.RUTA")."Generar_Pdf/".$infoEstudio[0]->idEstudio."/".$idVal }}" class="pointer pull-right" style="color: #fff; position: absolute; right: 22px;top: 8px;"><span class="fa fa-print fa-2x hover"></span></a>
               </div>
               <div class="portlet-body">
                   <div class="row">
                       <div class="col-md-12">
                                <table class="table table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">SEGURO</th>
                                        <th class="text-center">INTERES</th>
                                        <th class="text-center">CAPITAL</th>
                                        <th class="text-center">CUOTA</th>
                                        <th class="text-center">CREDITO</th>                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $vlrCreditoBucle = $vlrCredito;
                                    @endphp
                                    <tr>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>{{ number_format($vlrCredito, 0, ",", ".") }}</td>                                
                                    </tr>      
                                    @for($i = 1; $i <= $plazo; $i++)                                        
                                            @php
                                                $vlrCreditoBucle = round($vlrCreditoBucle, 3);
                                                $interes = round($vlrCreditoBucle*($tasa), 3);
                                                $capital = round($cuota - $interes - $seguro, 3);
                                                $cuotaCalculada = round($seguro+$interes+$capital, 3);
                                                $vlrCreditoBucle = round($vlrCreditoBucle - $capital, 3);                                
                                            @endphp
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ number_format(round($seguro, 0), 0, ",", ".") }}</td>                                
                                                <td>{{ number_format(round($interes, 0), 0, ",", ".") }}</td>                                
                                                <td>{{ number_format(round($capital, 0), 0, ",", ".") }}</td>                                
                                                <td>{{ number_format(round($cuotaCalculada, 0), 0, ",", ".") }}</td>                                
                                                <td>{{ number_format(round($vlrCreditoBucle, 0), 0, ",", ".") }}</td>                                
                                            </tr>                                          
                                    @endfor
                                </tbody>
                            </table>
                       </div>
                   </div>
               </div>
             </div>
            
        </div>
    </div>
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">    
@endsection