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
            font-family: Arial;
            font-size: 14px;
        }
        TH {
            font: bold 14px Helvetica, sans-serif;
            background: #006AA1;
            text-align :center;
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
    <br><br><br>
    Señores
    <br>
    <b>FIDUPREVISORA S.A</b><br><br><br><br>
    Solicito por favor, sean realizados los siguientes pagos desde el encargo Fiduciario No. 148394 de LABOR FINANCIERA S.A identificada con Nit. 805.021.676-9 según como se relaciona a continuación:
</p>
<br><br><br><br>
<table border="1">
    <tr style="font-weight: bold">
        <td>
            BENEFICIARIO
        </td>
        <td>
            CEDULA O NIT
        </td>
        <td>
            TIPO CUENTA
        </td>
        <td>
            NO. CUENTA
        </td>
        <td>
            ENTIDAD
        </td>
        <td>
            CONCEPTO
        </td>
        <td>
            VALOR A PAGAR
        </td>
    </tr>
    <tr>
        <td><br><br>
            {{$data['beneficiario']}}<br><br>
        </td>
        <td><br><br>
            {{$data['documento']}}
            <br><br>

        </td>
        <td><br><br>
            {{$data['tipocuenta']}}
            <br><br>
        </td>
        <td><br><br>
            {{$data['numero_cuenta']}}
            <br><br>
        </td>
        <td><br><br>
            {{$data['entidad']}}
            <br><br>
        </td>
        <td><br><br>
            {{$data['concepto']}}
            <br><br>
        </td>
        <td><br><br>
            {{$data['valorpago']}}
            <br><br>
        </td>
    </tr>
</table>


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
<p style="font-weight: bold">
    Generado por {{Auth::user()->nombres()}}
</p>
</body>
</html>


