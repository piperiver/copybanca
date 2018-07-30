<table class="table-striped table-hover text-center iniciarDatatable">
    <thead>
    <tr>
        <th><center> Nº</center></th>
        <th>Tipo de identificacion</th>
        <th>Nº identificación</th>
        <th>Nombre tercero</th>
        <th>Fecha límite de pago</th>
        <th>Numero obligación</th>
        <th>Código sucursal</th>
        <th>Calidad</th>
        <th>Estado de obligación</th>
        <th>Edad de mora</th>
        <th>Años en mora</th>
        <th>Fecha de corte</th>
        <th>Fecha inicio</th>
        <th>Fecha terminación</th>
        <th>Fecha de exigibilidad</th>
        <th>Fecha de prescripción</th>
        <th>Fecha de pago</th>
        <th>Modo extinción</th>
        <th>Tipo de pago</th>
        <th>Periodicidad</th>
        <th>Número de cuotas pagadas</th>
        <th>Número de cuotas pactadas</th>
        <th>Cuotas en mora</th>
        <th>Valor inicial</th>
        <th>Valor de mora</th>
        <th>Valor del saldo</th>
        <th>Valor de la cuota</th>-
        <th>Valor De Cargo Fijo</th>
        <th>Línea de crédito</th>
        <th>Tipo de contrato</th>
        <th>Estado de contrato</th>
        <th>Termino O Vigencia Del Contrato</th>-
        <th>Numero De Meses Del Contrato</th>-
        <th>Obligación reestructurada</th>
        <th>Naturaleza de la reestructuración</th>
        <th>Número de reestructuraciones</th>
        <th>No De Cheques Devueltos</th>
        <th>Plazo</th>
        <th>Días De Cartera</th>
        <th>Dirección casa del tercero</th>
        <th>Teléfono casa del tercero</th>
        <th>Código ciudad casa del tercero</th>
        <th>Ciudad casa cel tercero</th>
        <th>Código departamento del tercero</th>
        <th>Departamento casa del tercero</th>
        <th>Nombre empresa</th>
        <th>Dirección de la empresa</th>
        <th>Teléfono de la empresa</th>
        <th>Código ciudad empresa del tercero</th>
        <th>Ciudad empresa del tercero</th>
        <th>Código departamento empresa del tercero</th>
        <th>Departamento empresa del tercero</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reporteData as $registro)
        <tr class="item#">
            <td>{{$loop->iteration}}</td>
            <td>{{$registro->tipoIdentificacion}}</td>
            <td>{{$registro->Cedula}}</td>
            <td>{{$registro->nombre}}</td>
            <td>{{$registro->fechalimitePago}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$registro->estadoCuenta}}</td>
            <td>{{$registro->edadMora}}</td>
            <td>{{$registro->edadMora}}</td>
            <td>{{$registro->fechaPago}}</td>
            <td>{{$registro->fechaInicioContrato}}</td>
            <td>{{$registro->fechaFinDelContrato}}</td>
            <td></td>
            <td></td>
            <td>{{$registro->fechaPago}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$registro->cuotasPagadas}}</td>
            <td>{{$registro->plazo}}</td>
            <td>{{$registro->mesesEnMora}}</td>
            <td>{{$registro->saldoDeuda}}</td>
            <td></td>
            <td>{{$registro->cuotaMensual}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$registro->fechaClausulaPermanencia}}</td>
            <td>{{$registro->plazo}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$registro->plazo}}</td>
            <td></td>
            <td>{{$registro->direccionCorrespondencia}}</td>
            <td>{{$registro->celuCorrespondencia}}</td>
            <td>{{$registro->codigoDaneCorrespondencia}}</td>
            <td>{{$registro->ciudadCorrespondencia}}</td>
            <td>{{$registro->codigoDaneCorrespondencia}}</td>
            <td>{{$registro->departamenteCorrespondencia}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
