<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title">Detalle del comercial</h4>
        </div>
        <div class="modal-body">
            <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">

                @if($user->tipo_de_persona == 'natural')
                    <h3>Nombre: {{$user->nombres()}}</h3>
                    <h3>Persona natural</h3>
                    <h4>Número de documento: {{number_format($user->cedula)}}</h4>
                @else
                    <h3>Razón Social: {{$user->nombres()}}</h3>
                    <h3>Persona juridica</h3>
                    <h3>Representante: {{$user->representante_legal}}</h3>
                    <h4>N.I.T: {{number_format($user->cedula)}}</h4>
                @endif
                    <h4>Teléfono: <a href="tel:{{$user->telefono}}"> {{$user->telefono}}</a></h4>
                    <h4><a href='https://mail.google.com/mail/?view=cm&fs=1&to={{$user->email}}'> Email: {{$user->email}}</a></h4>
                    <h4>Fecha de ingreso: {{$user->created_at->format('Y-m-d')}}</h4>
            <div class="col-md-12">
                <h3>Solicitudes</h3>
                <div class="col-md-12">
                    <h4>Solicitudes puestas: <small>{{$solicitudes_puestas}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Solicitudes aprobadas:  <small>{{$solicitudes_aprobadas}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Solicitudes rechazadas: <small>{{$solicitudes_rechazadas}}</small></h4>
                </div>
            </div>

            <div class="col-md-12">
                <h3>Creditos</h3>
                <div class="col-md-12">
                    <h4>Creditos negados: <small>{{$negados}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Creditos aprobados: <small>{{number_format($creditos_aprobados)}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Promedio de dinero desembolsado por crédito: <small>{{number_format($promedio_desembolso)}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Total de dinero desembolsado: <small>{{number_format($dinero_desembolsado)}}</small></h4>
                </div>
                <div class="col-md-12">
                    <h4>Total de comisión obtenida: <small>{{number_format($promedio_comision)}}</small></h4>
                </div>
            </div>

            </div>
        </div>
    </div>
</div>
