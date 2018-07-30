<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <style>
table {
	border-collapse: collapse;
}
table, td, th {
	padding: 6px;
}
TD {
	font-family: Cambria,Georgia,serif; 
	font-size: 14px;
}
TH {
	font: bold 14px Helvetica, sans-serif;
	background: #006AA1;
	text-align :center;
	font-weight: bold;
	color: white;
}
body {
	font-family: Cambria,Georgia,serif; 
	font-size: 14px;
        background-color: #FFFFFF;	
        background: transparent url("http://vtmdev:8080/assets/layouts/layout5/img/fondocartas.png");        
	background-repeat: no-repeat;
        background-size: 90%;
}
</style>
</head>
<body>
<table border="0">
    <tr>
        <td colspan="2">
            <img src="{{asset("assets/layouts/layout5/img/logovtm.png")}}" width="90" height="50" align="right">
        </td>
    </tr>
    <tr>

	<td width="25" rowspan="2">&nbsp;
            <br>
            <br>
        </td>

	<td style="text-align: justify;">
                <br>                               
		<br>
		<br>                
                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{config('constantes.GBL_RAZON_SOCIAL')}} <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CERTIFICA</b>
		<br>				
                <br>		
                Que el cliente <b>{{utf8_decode($data["nombre"])}}</b> identificado(a) con documento n&uacute;mero {{number_format($data["cedula"],0,",",".")}},adeuda la suma de {{ strtoupper($data["valorLetras"]) }} M/CTE<b>(${{number_format($data["valorProyectado"],0,",",".")}})</b> por concepto de la obligaci&oacute;n No.{{$data["id_estudio"]}}, descontada bajo la modalidad de libranza con una cuota mensual de {{strtoupper($data["cuotaLetras"])}} PESOS M/CTE(${{number_format($data["valorCuota"],0,",",".")}}).
		<br>
		<br>                
		En concordancia con lo estipulado en el Art. 880 del C&oacute;digo del Comercio, nos reservamos la posibilidad de efectuar el cobro de cualquier transacci&oacute;n realizada con posterioridad a la presente fecha.
		<br>
		<br>
                El saldo adeudado puede ser pagado en efectivo, cheque de Gerencia o transferencia, en la <b>cuenta corriente, del {{config('constantes.GBL_BANCO')}} No. {{config('constantes.GBL_CUENTA_BANCO')}} a nombre de {{config('constantes.GBL_RAZON_SOCIAL')}}, NIT {{config('constantes.GBL_NIT_EMPRESA')}}.</b>		
		<br>		
		<br>
		Los pagos que se realicen a otro tipo de cuentas distintas a la mencionada en esta certificaci&oacute;n, no ser&aacute;n tenidos en cuenta ni se realizar&aacute; devoluci&oacute;n de dicho dinero.
		<br>
		<br>		
		Se debe enviar el comprobante de pago al correo {{config('constantes.GBL_EMAIL')}}				
		<br>
		<br>
		Para la certificaci&oacute;n vencida y no enviar el soporte de pago de manera oportuna generar&aacute; el cobro del descuento en el mes siguiente.
		<br>
		<br>
		Esta constancia se expide por solicitud del cliente y se firma en {{config('constantes.GBL_CIUDAD')}} a los {{date("d")}} d&iacute;as del mes de {{date("F")}} de {{date("Y")}}, tiene vigencia hasta el <b>quince({{config('constantes.CAR_FECHA_CORTE')}}) de {{$data["mesVigencia"]}} de {{$data["anioVigencia"]}}</b>.		
		<br>
		<br>
                Atentamente,
                <br>
                <br>                
                <img src="{{asset("/assets/layouts/layout5/img/cedula.jpg")}}" height="120" width="120"><br>                
                _______________________________________________<br>
		<b>Usuario Cartera</b><br>
		<b>Director Operaciones</b><br>
		<b>{{config('constantes.GBL_RAZON_SOCIAL')}}</b>
		<br>
		<br>
		<br>
		<br>
		<br>		              
                <br>
                <br>                              
		<p align="center">Telefono: {{config('constantes.GBL_TELEFONO')}} - Celular: {{config('constantes.GBL_CELULAR')}} - E-Mail: {{config('constantes.GBL_EMAIL')}}<br>
		Direcci&oacute;n: {{config('constantes.GBL_DIRECCION')}} - {{config('constantes.GBL_CIUDAD')}} {{config('constantes.GBL_PAIS')}}<br>	
		{{config('constantes.EMAIL')}}
		</p>
	</td>
</tr>

</table>
</body>
</html>        


