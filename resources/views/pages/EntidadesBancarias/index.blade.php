@extends('layout.default')
@section('encabezado')
    <script type="text/javascript">

    </script>
@endsection
@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Entidades Bancarias </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a href="" id="lkSave" name="lkSave" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        @endif
                      <!--
                        <div class="btn-group">
                            <a class="btn btn-default btn-sm" href="javascript:;" data-toggle="dropdown">
                                <span class="hidden-xs"> Herramientas </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" id="sample_3_tools">
                                <li>
                                    <a href="javascript:;" data-action="0" class="tool-action">
                                        <i class="icon-printer"></i> Imprimir</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="1" class="tool-action">
                                        <i class="icon-check"></i> Copiar</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="2" class="tool-action">
                                        <i class="icon-doc"></i> PDF</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-action="3" class="tool-action">
                                        <i class="icon-paper-clip"></i> Excel</a>
                                </li>
                            </ul>
                        </div>-->
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center"
                           id="tabla">
                        <thead>
                        <tr>
                            <th class="editable"> Código</th>
                            <th> Descripción</th>
                            <th> Ultima Actualización</th>
                            <th> Fecha Creación</th>
                            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                <th> Acción</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($Bancos as $Banco)
                            <tr id="{{$Banco->Id}}" class="item{{$Banco->Id}}">
                                <td>{{ $Banco->Id }}</td>
                                <td>{{ $Banco->Descripcion }}</td>
                                <td>{{ $Banco->updated_at }}</td>
                                <td>{{ $Banco->created_at }}</td>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold'
                                               data-toggle='modal' data-codigo='{{$Banco->Id}}'
                                               data-descripcion='{{$Banco->Descripcion}}' data-tasa='{{$Banco->Tasa}}'
                                               data-castigo='{{$Banco->CastigoMora}}' data-paz='{{$Banco->PazSalvo}}'
                                               data-politicas='{{$Banco->Politica}}' data-dcto='{{$Banco->DtoInicial}}'
                                               data-pdata='{{$Banco->PuntajeData}}'
                                               data-pcifin='{{$Banco->PuntajeCifin}}'
                                               data-entidades='{{$Banco->Entidades}}'>
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red'
                                               data-toggle='modal' data-codigo='{{$Banco->Id}}'>
                                                <i class='fa fa-close'></i>
                                            </a>
                                        @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventanas modales-->
    <div class="modal fade modalEstudio in" id="ventana" role="basic" aria-hidden="true" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Entidad Bancaria</h4>
                </div>
                <div class="modal-body">
                    <form id="formEntidades" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 column-campos borde-lateral">
                                <div class="form-group">
                                    <label for="Descripcion">Descripcion</label>                                                                            
                                        <input class="form-control" id="Descripcion" name="Descripcion" value="">                                                                 
                                </div>                                
                            </div>                            
                        </div>
                        <div class="row" lang="es">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                        <label for="multi-append" class="control-label">Relacionar Entidades:</label>
                                        <div class="input-group select2-bootstrap-append">
                                            <select id="Entidades" name="Entidades[]" class="form-control select2" multiple="multiple" data-placeholder="Seleccionar Entidad" style="width:100%;">                                                
                                                @foreach($Entidades as $Entidad)
                                                <option value="{{$Entidad->Entidad}}">{{$Entidad->Entidad}}</option>                                                
                                                @endforeach
                                            </select>                                                                                      
                                        </div>
                                </div>                                
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 column-campos">
                                <div class="form-group">
                                    <label for="CastigoMora">Castigo/Mora</label>                                    
                                        <select class="form-control" id="CastigoMora" name="CastigoMora">
                                            <option value="S">SI</option>                                                            
                                            <option value="N">NO</option>
                                        </select>                                    
                                </div>                                
                            </div>
                            <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 column-campos">
                                <div class="form-group">
                                    <label for="PazSalvo">Paz y Salvo</label>                                    
                                        <select class="form-control" id="PazSalvo" name="PazSalvo">
                                            <option value="S">SI</option>                                                            
                                            <option value="N">NO</option>
                                        </select>                                                                
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 column-campos borde-lateral">
                                <div class="form-group">
                                    <label for="Descripcion">% Dcto</label>                                                                            
                                        <input type="number" class="form-control" id="Descuento" name="Descuento">                                                                 
                                </div>                                
                            </div>
                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 column-campos borde-lateral">
                                <div class="form-group">
                                    <label for="Descripcion">P.Data</label>                                                                            
                                    <input type="number" class="form-control" id="PData" name="PData">                                                                 
                                </div>                                
                            </div>
                            <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 column-campos borde-lateral">
                                <div class="form-group">
                                    <label for="Descripcion">P.Cifin</label>                                                                            
                                    <input type="number" class="form-control" id="PCifin" name="PCifin">                                                                 
                                </div>                                
                            </div>                                                    
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <table id="example" class="display" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Pagaduria</th>
                                        <th>Nombramiento</th>
                                        <th>Cargo</th>
                                        <th>Monto limite</th>
                                        <th>Tasa</th>
                                        <th>Plazo</th>
                                        <th>Antiguedad</th>
                                        <th>Edad de retiro</th>
                                        <th>Edad de inclusión</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body-politicas">
                                    </tbody>
                                </table>
                                <button type="button" id="add-politica">Agregar politica</button>
                            </div>
                        </div>
                    </form>
                    <input type="hidden" id="idReg"/>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>{{--  FIN DE MODAL  --}}
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="hnAccion" name="hnAccion" value="">        
@endsection

@section('scripts')
    <script src="{{ asset('assets/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/EntidadesBancarias/index.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/EntidadesBancarias/table-edits.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        let politicas = [];
        function reEdit() {
            let html_template = ``;
            politicas.map((e, index) => {
                html_template += `<tr data-index="${index}">
                                        <td data-field="Pagaduria" class="editable">${e.Pagaduria}</td>
                                        <td data-field="Nombramiento" class="editable">${e.Nombramiento}</td>
                                        <td data-field="Cargo" class="editable">${e.Cargo}</td>
                                        <td data-field="Monto" class="editable">${format_miles(e.Monto)}</td>
                                        <td data-field="Tasa" class="editable">${e.Tasa}</td>
                                        <td data-field="Plazo" class="editable">${e.Plazo}</td>
                                        <td data-field="Antiguedad" class="editable">${e.Antiguedad}</td>
                                        <td data-field="Edad" class="editable">${e.Edad}</td>
                                        <td data-field="EdadInclusion" class="editable">${e.EdadInclusion}</td>
                                        <td><a data-index="${index}" class="delete-politica"><i class="fa fa-minus-circle" aria-hidden="true"></i></a></td>
                                    </tr>`
            });
            $('#example').DataTable().destroy();
            $('#table-body-politicas').html(html_template);
            $('#example').DataTable().draw();
            $("table tr").editable({
                extraattr: {
                    Monto: "puntosMiles"
                },
                dropdowns: {
                    Pagaduria: {@foreach($pagadurias as $pagaduria)"{{$pagaduria->nombre}}":"{{$pagaduria->nombre}}",@endforeach},
                    Nombramiento: {@foreach($nombramientos as $key => $nombramiento)"{{$key}}":"{{$nombramiento}}",@endforeach},
                    Cargo: {@foreach($cargos as $key => $cargo)"{{$key}}":"{{$cargo}}",@endforeach},
                },
                values:{
                    Pagaduria: [@foreach($pagadurias as $pagaduria)"{{$pagaduria->nombre}}",@endforeach],
                    Nombramiento: [@foreach($nombramientos as $key => $nombramiento)'{{$key}}',@endforeach],
                    Cargo: [@foreach($cargos as $key => $cargo)'{{$key}}',@endforeach]
                },
                save: function (values) {
                    index = $(this).data('index');
                    console.log(values);
                    values.Monto = parseInt(values.Monto.replace(/[^0-9]/g, ''));
                    politicas[index] = values;
                    reEdit();
                }
            });
        }
        $(document).ready(function () {
            let table = $('#example').DataTable({
                responsive: true, //Indica que al cambiar el tamaÃ±o del navegador los registros se deben adaptar.
                scrollX: true,
                "order": [], //Deshabilita el orden que da el DataTable
            });

            $('#add-politica').click(function (e) {
                e.preventDefault();
                politicas.push({
                    Pagaduria: "",
                    Nombramiento: "",
                    Cargo: "",
                    Monto: "0",
                    Tasa: "0",
                    Plazo: "0",
                    Antiguedad: "0",
                    Edad: "0",
                    EdadInclusion: "0"
                });
                reEdit();
            });

        });

        $(document).on('click', '.delete-politica', function () {
            politicas.splice($(this).data('index'), 1);
            reEdit();
        });
        $('#btGuardar').click(function()
        {
            //var ModalC = modalCarga("Por Favor espere...");//funcion llamada desde el archivo public/js/global.js
            var nombre = $("#Descripcion").val();
            var castigo = $("#CastigoMora").val();
            var paz = $("#PazSalvo").val();
            var politicas_array = JSON.stringify(politicas);
            var ruta = "addBanco";
            var id = $('#idReg').val();
            var dto = $('#Descuento').val();
            var data = $('#PData').val();
            var cifin = $('#PCifin').val();
            var entidades = procesarEntidades();
            if($('#hnAccion').val()) {
                ruta = "editBanco";
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Id': id,
                        'Descripcion': nombre,
                        'CastigoMora': castigo,
                        'PazSalvo': paz ,
                        'Politica': politicas_array,
                        'DtoInicial': dto,
                        'PuntajeData': data,
                        'PuntajeCifin': cifin,
                        'Entidades': entidades
                    },
                    success: function(data)
                    {
                        resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
                        while (politicas.length > 0) {
                            politicas.pop();
                        }
                        reEdit();
                    }
                });
            }
            else{
                $.ajax({
                    type: 'post',
                    url: ruta,
                    data: {
                        '_token': $('input[name=_token]').val(),
                        'Descripcion': nombre,
                        'CastigoMora': castigo,
                        'PazSalvo': paz ,
                        'Politica': politicas_array,
                        'DtoInicial': dto,
                        'PuntajeData': data,
                        'PuntajeCifin': cifin,
                        'Entidades': entidades
                    },
                    success: function(data)
                    {
                        resultadoEvento(data);//funcion llamada desde el archivo public/js/global.js
                        while (politicas.length > 0) {
                            politicas.pop();
                        }
                        reEdit();
                    }

                });
            }
        });
    </script>
@endsection

@section('styleSelect')
<link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
