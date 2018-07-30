<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
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

	<td width="28" rowspan="2">&nbsp;
            <br>
            <br>
        </td>

	<td style="text-align: justify;">
                <br>
                <br>                
		<br>
		<br>                
		<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PAZ Y SALVO</b>
		<br>
		<br>
		<br>		
                <br>		
                Por medio de la presente certificamos que el se&ntilde;or(a) <b>{{utf8_decode($data["nombre"])." ".utf8_decode($data["apellido"])}}</b> identificado(a) con la c&eacute;dula de ciudadan&iacute;a n&uacute;mero {{number_format($data["cedula"],0,",",".")}} vinculado a {{$data["pagaduria"]}}, se encuentra a <b>PAZ Y SALVO</b> por concepto de cr&eacute;dito identificado con pagar&eacute; No.{{$idEstudio}} con {{config('constantes.GBL_RAZON_SOCIAL')}}
		<br>
		<br>
		
		El presente PAZ Y SALVO se expide a los {{date("d")}} d&iacute;as del mes de {{date("F")}} de {{date("Y")}}
		<br>
		<br>
		
		Nota:  {{config('constantes.GBL_RAZON_SOCIAL')}} se reserva el derecho de efectuar el cobro de cualquier transacci&oacute;n realizada y no cobrada con anterioridad y que se encuentre debidamente documentada y contabilizada con posterioridad a la presente fecha (art&iacute;culo 880 del c&oacute;digo de comercio).		
		<br>		
		<br>
		<br>
		<br>
		<br>		
		<br>
		Atentamente,		
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>		
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


