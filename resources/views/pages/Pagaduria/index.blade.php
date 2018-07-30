@extends('layout.default')

@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet box main-color">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Pagadurias
                    </div>
                    <div class="actions">
                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Insertar"))
                            <a href="" id="lkSave" name="lkSave" class="btn btn-default btn-sm" data-toggle="modal">
                                <i class="fa fa-plus"></i> Crear
                            </a>
                        @endif
                    </div>
                </div>
                <div id="contenido" class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable order-column text-center"
                           id="tabla">
                        <thead>
                        <tr>
                            <th> Código</th>
                            <th> Nombre</th>
                            <th> Tipo</th>
                            <th> Fecha Creaci&oacute;n</th>
                            @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                <th> Acción</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagadurias as $pagaduria)
                            <tr id="{{$pagaduria->id}}" class="item{{$pagaduria->id}}">
                                <td>{{ $pagaduria->codigo }}</td>
                                <td>{{ $pagaduria->nombre }}</td>
                                <td>{{ $pagaduria->tipo }}</td>
                                <td>{{$pagaduria->created_at->format('d/m/Y')}}</td>
                                @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar") || App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                    <td>
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Actualizar"))
                                            <a class='btn btn-icon-only yellow-gold update'
                                               data-update_url="{{ url('pagadurias', ['id'=>$pagaduria->id, 'edit'=>'edit'] ) }}">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        @endif
                                        @if(App\Librerias\UtilidadesClass::ValidarAcceso($forma,"Eliminar"))
                                            <a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red'
                                               data-toggle='modal' data-delete-url="{{url('pagadurias', ['id'=>$pagaduria->id])}}" data-id='{{$pagaduria->id}}'>
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
    <div class="modal fade" id="ventana" tabindex="-1" role="basic" aria-hidden="true">
        @include('pages.Pagaduria.form')
    </div>{{--  FIN DE MODAL  --}}

    <div class="modal fade" id="ventana_actualizar" tabindex="-1" role="basic" aria-hidden="true">

    </div>

    <input type="hidden" id="hnAccion" name="hnAccion" value="">
    <script src="{{ asset('js/Pagadurias/index.js') }}" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).on('submit', '.save-model-form', function (e) {
            e.preventDefault();
            ruta = "";
            selector = $(this);
            $.ajax({
                type: selector.data('method'),
                url: selector.data('create_url'),
                data: formToJson($(this)[0]),
                success: function(data)
                {
                    resultadoEvento(data);
                    $('#ventana_actualizar').modal('hide');
                }
            });
        });

        $(document).on('change', '.tipo_pagaduria', function () {
            const value =$(this).val();
            console.log($('.empleadoDiv'));
            if(value === "Activos"){
                $('.empleadoDiv').show();
            }else if(value === "Pensionados"){
                $('.empleadoDiv').hide();
            };
        });

        $(document).on('change', '#idSecretariaEducacion', function () {
            const value =$(this).val();
            if(value === "1"){
                $('#normalDiv').show();
            }else if(value === "0"){
                $('#normalDiv').hide();
            };
        });
        $(document).on('change', '#idNormal', function () {
            const value =$(this).val();
            if(value === "0"){
                $('#protegeDiv').show();
            }else if(value === "1"){
                $('#protegeDiv').hide();
            };
        });

        $(document).on('click', '.update', function (e) {
            e.preventDefault();
            $('#ventana_actualizar').html(`
            <div class="modal-dialog">
                <div class="modal-content">
                   <div class="modal-body">
                   <br>
                    <i class="fa fa-spinner fa-spin" style="font-size:48px; text-align: center"></i>
                    <br>
                   </div>
                </div>
            </div>
            `);
            $('#ventana_actualizar').modal();
            fetch(($(this).data('update_url')), {
                credentials: "same-origin"
            }).then(response => response.text()).then(html => {
                $('#ventana_actualizar').html(html);
                $('.scroller').slimScroll({
                    height: '300px'
                });
            });
        });
    </script>
@endsection