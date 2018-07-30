<table class="table-striped table-hover text-center iniciarDatatable">
    <thead>
    <tr>
        <th><center> Nº</center></th>
        <th>Tipo de Identificaci&oacute;n</th>  
        <th> N&uacute;mero de Identificaci&oacute;n</th>
        <th> N&uacute;mero de la  Cuenta u obligaci&oacute;n</th>
        <th> Nombre Completo </th>
        <th> Situaci&oacute;n del titular</th>
        <th> Fecha de apertura</th>
        <th> Fecha de vencimiento</th>
        <th> Responsable</th>
        <th> Forma de pago</th>
        <th> Novedad</th>
        <th> Estado origen de la cuenta</th>
        <th> Fecha estado origen</th>
        <th> Estado de la cuenta </th>
        <th> Fecha estado de la cuenta </th>
        <th> Adjetivo</th>
        <th> Fecha de adjetivo</th>
        <th> Calificaci&oacute;n</th>
        <th> Edad de Mora</th>
        <th> Valor Inicial</th>
        <th> Valor Saldo Deuda</th>
        <th> Valor Disponible</th>
        <th> Valor Cuota Mensual</th>
        <th> Valor Saldo Mora</th>
        <th> Total Cuotas</th>
        <th> Cuotas Canceladas</th>
        <th> Cuotas en Mora</th>
        <th> Clausula de permanencia </th>
        <th> Fecha Clausula de permanencia </th>
        <th> Fecha Limite de pago</th>
        <th> Fecha de pago</th>
        <th> Ciudad de Correspondencia</th>
        <th> Codigo Dane Ciudad de Correspondencia</th>
        <th> Departamente de Correspondencia</th>
        <th> Dirección de Correspondencia</th>
        <th> Correo Electronico </th>
        <th> Celular </th>
        
    </tr>
    </thead>
    <tbody>
        @foreach($reporteData as $registro)
        <tr class="item#">
            
            <td>{{$loop->iteration}}</td>
            <td>{{$registro->tipoIdentificacion}}</td>
            <td>{{$registro->Cedula}}</td>
            <td>{{$registro->numObligacion}}</td>
            <td>{{$registro->nombre}}</td>
            <td>{{$registro->situaTitular}}</td>
            <td>{{$registro->fechaInicioContrato}}</td>
            <td>{{$registro->fechaFinDelContrato}}</td>
            <td>{{$registro->responsable}}</td>
            <td>{{$registro->formaPago}}</td>
            <td>{{$registro->novedad}}</td>
            <td>{{$registro->estadoOrigenCuenta}}</td>
            <td>{{$registro->fechaEstadoOrigen}}</td>
            <td>{{$registro->estadoCuenta}}</td>
            <td>{{$registro->fechaEstadoCuenta}}</td>
            <td>{{$registro->adjetivo}}</td>
            <td>{{$registro->fechaAdjetivo}}</td>
            <td>{{$registro->calificacion}}</td>
            <td>{{$registro->edadMora}}</td>
            <td>{{$registro->desembolso}}</td>
            <td>{{$registro->saldoDeuda}}</td>
            <td>{{$registro->valorDisponible}}</td>
            <td>{{$registro->cuotaMensual}}</td>
            <td>{{$registro->valorSaldoMora}}</td>
            <td>{{$registro->plazo}}</td>
            <td>{{$registro->cuotasPagadas}}</td>
            <td>{{$registro->mesesEnMora}}</td>
            <td>{{$registro->clausulaPermanencia}}</td>
            <td>{{$registro->fechaClausulaPermanencia}}</td>
            <td>{{$registro->fechalimitePago}}</td>
            <td>{{$registro->fechaPago}}</td>
            <td>{{$registro->ciudadCorrespondencia}}</td>
            <td>{{$registro->codigoDaneCorrespondencia}}</td>
            <td>{{$registro->departamenteCorrespondencia}}</td>
            <td>{{$registro->direccionCorrespondencia}}</td>
            <td>{{$registro->correoCorrespondencia}}</td>
            <td>{{$registro->celuCorrespondencia}}</td>
            
        </tr>
        @endforeach
    </tbody>
</table>
