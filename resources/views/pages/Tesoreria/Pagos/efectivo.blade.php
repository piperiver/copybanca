<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">

    <title></title>
    <style>
        table {
            border-collapse: collapse;
        }

        table, td, th {
            padding: 6px;
        }

        TD {
            font-family: Arial;
            font-size: 14px;
        }

        TH {
            font: bold 14px Helvetica, sans-serif;
            background: #006AA1;
            text-align: center;
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body>

<p><br>
    <img src="{{asset('logolabor.jpg')}}" style="width: 250px; height: 60px"><br><br>
    <b>Santiago de Cali</b>
    <br>
    {{$data['today']}}<br>
</p>
<br><br><br><br>
<p>Solicito la generaci贸n de un cheque de gerencia con los siguientes datos: </p>

<b>Nombre</b>: {{$data['beneficiario']}}<br><br>

<b>Nit</b>: {{$data['documento']}}<br><br>

<b>Valor</b>: {{$data['valorpago']}}<br><br>
<b>Concepto</b>: {{$data['concepto']}}<br><br>
<b>Nombre de la persona que lo va a reclamara</b>: {{$data['persona_a_reclamar']}}<br><br>

<br>
<br>
<br>
<br>
<br>
<br>

_______________________________________
<p style="font-weight: bold">
    Hector Aguiar
</p>

<br>
<br>
<br>
<br>
<br>
<br>

_______________________________________
<p style="font-weight: bold;">
    Generado por {{Auth::user()->nombres()}}
</p>

</body>
</html>