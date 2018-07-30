@extends('layout.default')

@section('encabezado')
    <link href="{{ asset('css/Cartera/detalle.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('js/Cartera/cartera.js') }}" type="text/javascript"></script>

@endsection

@section('content')
<h1 class="text-center bold">ESTADO CUENTA</h1>
<div class="text-right">
    <button onClick="imprimirElemento('areaImprimir')" class="bold btn btn-danger"><span class="fa fa-print"></span> IMPRIMIR</button>
</div>

<div id="areaImprimir">
    <img src="{{asset("assets/layouts/layout5/img/logovtm.png")}}" width="90" height="50" align="top" class="viewPrint">
    <h2 style="text-align: center" class="viewPrint">ESTADO CUENTA</h2>
    
<p style="width: 24%; text-align: center; display: inline-block"><strong>Tasa: </strong>{{ $infoEstudio->Tasa }}</p>
<p style="width: 25%; text-align: center; display: inline-block"><strong>Plazo: </strong>{{ $infoEstudio->Plazo }}</p>
<p style="width: 24%; text-align: center; display: inline-block"><strong>Cuota: </strong>{{ number_format($infoEstudio->Cuota, 0, ",", ".") }}</p>
<p style="width: 25%; text-align: center; display: inline-block"><strong>Valor Credito: </strong>{{ number_format($infoEstudio->ValorCredito, 0, ",", ".") }}</p>
<hr style="margin-top: 0">



<table align="center" class="tablaEstadoCuenta" cellpadding="5" cellspacing="0" width="100%" style="text-align: center; border: 1px solid #000;">
    <thead>
        <tr>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" colspan="5" class="text-center">
                CAUSACION
            </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" colspan="3" class="text-center fondo">
                PAGO
            </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" colspan="4" class="text-center">
                DEDUCCI&Oacute;N PAGO
            </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" colspan="6" class="text-center fondo">
                BALANCE
            </th>
        </tr>            
        <tr>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">fecha</th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">seguro</th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Interes Mora</th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Interes Corriente  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Abono Capital  </th>

            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">F.Pago</th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">valor</th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Saldo a Favor</th>

            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Seguro  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Interes Mora  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Interes Corriente  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center" class="text-center">Abono Capital  </th>

            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Seguro  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Interes Mora  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Interes Corriente  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Abono Capital  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Saldo Capital  </th>
            <th style="padding: 0 2px;border: 1px solid #000;text-align: center;background: #e5e5e5;" class="text-center fondo">Saldo Total</th>
        </tr>
    </thead>
    <tbody>        

        @foreach ($data as $item)
            <tr>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ date("m-d", strtotime($item["infoData"][0]["fechaCausacion"])) }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["causadoSeguro"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["causadoInteresMora"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["causadoInteresCorriente"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["causadoAbonoCapital"], 2, ",", ".") }}</td>
                
                <td style="border-right: 1px solid #000;padding: 1px 2px;border-bottom: 1px solid #000;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="border-bottom fondo" rowspan="{{ ((count($item["infoData"])) <= 0)? "false" : count($item["infoData"]) }}">{{ $item["infoPago"]["fechaPago"] }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;border-bottom: 1px solid #000;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="border-bottom fondo" rowspan="{{ ((count($item["infoData"])) <= 0)? "false" : count($item["infoData"]) }}">{{ $item["infoPago"]["valor"] }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;border-bottom: 1px solid #000;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="border-bottom fondo" rowspan="{{ ((count($item["infoData"])) <= 0)? "false" : count($item["infoData"]) }}">{{ $item["infoPago"]["saldoFavor"] }}</td>
                
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["pagadoSeguro"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["pagadoInteresMora"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["pagadoInteresCorriente"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}">{{ number_format($item["infoData"][0]["pagadoAbonoCapital"], 2, ",", ".") }}</td>
                
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][0]["balanceSeguro"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][0]["balanceInteresMora"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][0]["balanceInteresCorriente"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][0]["balanceAbonoCapital"], 2, ",", ".") }}</td>
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][0]["balanceSaldoCapital"], 2, ",", ".") }}</td>               
                <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ (count($item["infoData"]) == 1)? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format(round($item["infoData"][0]["balanceValorTotal"], 2), 2, ",", ".") }}</td>               
            </tr>
        
            @for($i = 1; $i < count($item["infoData"]); $i++)
                <tr>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ date("m-d", strtotime($item["infoData"][$i]["fechaCausacion"])) }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["causadoSeguro"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["causadoInteresMora"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["causadoInteresCorriente"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["causadoAbonoCapital"], 2, ",", ".") }}</td>
                        
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["pagadoSeguro"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["pagadoInteresMora"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["pagadoInteresCorriente"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" >{{ number_format($item["infoData"][$i]["pagadoAbonoCapital"], 2, ",", ".") }}</td>

                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][$i]["balanceSeguro"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][$i]["balanceInteresMora"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][$i]["balanceInteresCorriente"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][$i]["balanceAbonoCapital"], 2, ",", ".") }}</td>
                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format($item["infoData"][$i]["balanceSaldoCapital"], 2, ",", ".") }}</td>               
                    <td style="border-right: 1px solid #000;padding: 1px 2px;background: #e5e5e5;{{ ($i == (count($item["infoData"])-1))? "border-bottom: 1px solid #000;" : "" }}" class="fondo">{{ number_format(round($item["infoData"][$i]["balanceValorTotal"], 2), 2, ",", ".") }}</td>               
                </tr>
            @endfor            
        @endforeach    

        </tbody>
</table>
</div>
@endsection