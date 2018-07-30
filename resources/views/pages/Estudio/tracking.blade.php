<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div style="text-align: center !important;" class="modal-body">
            <div class="portlet box main-color sinMarginBottom">
                <div style="text-align: center; padding: 0px !important; min-height: 0px!important;" class="portlet-title" >
                    <strong style="font-size: 1.4em">SEGUIMIENTO DE ESTADOS</strong>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div style="max-height: 500px; overflow-y: auto" class="col-md-12">
                            <table class="table">
                                <thead>
                                <tr style="text-align: center !important;">
                                    <th style="text-align: center !important;" scope="col">USUARIO</th>
                                    <th class="hidden-xs" style="text-align: center !important;" scope="col">ANTERIOR</th>
                                    <th style="text-align: center !important;" scope="col">ESTADO</th>
                                    <th style="text-align: center !important;" scope="col">FECHA</th>
                                </tr>
                                </thead>
                                <tbody >
                                @foreach($Tracking as $step)
                                    @foreach($step->getModified() as $field => $value)
                                        <tr>
                                            <th style="text-align: center !important;">{{ isset($step->user) ? ucfirst($step->user->nombre) : "" }}</th>
                                            <td
                                                class="hidden-xs">{{ array_key_exists('old',$value) ? $value['old'] : ""  }}
                                            </td>
                                            <td class="">{{ $value["new"] }}</td>
                                            <td>{{ ucfirst($step->created_at->format('d/m/y H:i:s'))}}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
        </div>
    </div>
</div>