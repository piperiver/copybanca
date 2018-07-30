<div class="modal-dialog">
    <form @if(isset($pagaduria_object->nombre)) data-method="PUT" @else data-method="POST"
          @endif data-create_url="@if(isset($pagaduria_object->nombre)) {{ url('pagadurias', ['id'=>$pagaduria_object->id] ) }}@else {{ url('pagadurias') }} @endif"
          class="save-model-form">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Pagaduria</h4>
            </div>
            <div class="modal-body">

                <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Codigo:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_codigo" value="{{$pagaduria_object->codigo}}"
                                           name="codigo" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-10 ">
                                    Nombre:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre" value="{{$pagaduria_object->nombre}}"
                                           name="nombre" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-10">
                                    Nit:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nit" value="{{$pagaduria_object->nit}}" name="nit"
                                           maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Nomina {{$pagaduria_object->tipo}}:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <select name="tipo" class="form-control tipo_pagaduria select2 circle">
                                        <option @if(!isset($pagaduria_object->tipo)) selected @endif disabled value>
                                            Seleccione una opción
                                        </option>
                                        @foreach($nomina as $value)
                                            <option @if($value == $pagaduria_object->tipo) selected
                                                    @endif value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div style="display: none" class="empleadoDiv" id="empleadoDiv">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-6 ">
                                        Tipo
                                    </label>
                                    <div class="col-sm-8 col-md-12">
                                        <select id="idTipoDeDescuento" name="tipo_de_descuento"
                                                class="form-control select2 circle">

                                            <option @if(!isset($pagaduria_object->tipo_de_descuento)) selected
                                                    @endif disabled value>Seleccione una opción
                                            </option>
                                            @foreach($tipos_de_descuentos as $value)
                                                <option @if($value == $pagaduria_object->tipo_de_descuento) selected
                                                        @endif value="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Tipo de empresa:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <select name="tipo_empresa" class="form-control select2 circle">
                                        <option @if(!isset($pagaduria_object->tipo_empresa)) selected @endif disabled
                                                value>Seleccione una opción
                                        </option>
                                        @foreach($tipo_empresa as $value)
                                            <option @if($value == $pagaduria_object->tipo_empresa) selected
                                                    @endif value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Descuentos permitidos:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <select name="descuentos_permitidos"
                                            class="form-control select2 circle">
                                        <option @if(!isset($pagaduria_object->descuentos_permitidos)) selected
                                                @endif disabled value>Seleccione una opción
                                        </option>
                                        @foreach($descuentos_permitidos as $value)
                                            <option @if($value == $pagaduria_object->descuento_permitidos) selected
                                                    @endif value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <label class="col-sm-2 col-md-6 ">
                                Aplica provectus
                            </label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input radio-provectus" type="radio"
                                       onchange="" name="provectus" id="inlineRadio1"
                                       @if(isset($pagaduria_object->provectus) and $pagaduria_object->provectus == 1) checked @endif
                                       value="1">
                                <label class="form-check-label" for="inlineRadio1">Si</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input radio-provectus" type="radio" @if(isset($pagaduria_object->provectus) and $pagaduria_object->provectus == 0) checked @endif name="provectus" id="inlineRadio2"
                                       value="0">
                                <label class="form-check-label" for="inlineRadio2">No</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Direccion:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre" value="{{$pagaduria_object->direccion}}"
                                           name="direccion" maxlength="30"
                                           class="form-control input-circle address">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Ciudad:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_ciudad" value="{{$pagaduria_object->ciudad}}"
                                           name="ciudad"
                                           maxlength="30"
                                           class="form-control input-circle address">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Telefono:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_nombre" value="{{$pagaduria_object->telefono}}"
                                           name="telefono" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Contacto unico:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="text" id="id_contacto" value="{{$pagaduria_object->contacto}}"
                                           name="contacto" maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Email:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <input type="email" id="id_contacto" value="{{$pagaduria_object->email}}"
                                           name="email"
                                           maxlength="30"
                                           class="form-control input-circle">
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Día de reporte:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <select name="dia_reporte"
                                            class="form-control select2 circle">
                                        <option @if(!isset($pagaduria_object->dia_reporte)) selected @endif disabled
                                                value>
                                            Seleccione una opción
                                        </option>
                                        @foreach($dia_reporte as $value)
                                            <option @if($value == $pagaduria_object->dia_reporte) selected
                                                    @endif value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-6 ">
                                    Tipo de visación:
                                </label>
                                <div class="col-sm-8 col-md-12">
                                    <select name="tipo_visacion"
                                            class="form-control select2 circle">
                                        <option @if(!isset($pagaduria_object->tipo_visacion)) selected @endif disabled
                                                value>Seleccione una opción
                                        </option>
                                        @foreach($tipo_visacion as $value)
                                            <option @if($value == $pagaduria_object->tipo_visacion) selected
                                                    @endif value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btGuardar" name="btGuardar" class="btn green">Guardar</button>
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </form>
</div>