<div class="modal-dialog">
    <div class="modal-content">
        <form method="PUT" data-create-url="{{url('comerciales_vtm', $user->id)}}" class="save-form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Usuario</h4>
            </div>
            <div class="modal-body">
                <div class="scroller" style="height:300px" data-always-visible="1" data-rail-visible1="1">
                    <div class="col-md-6">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="Nombre" class="control-label">Nombre:</label>
                            <input required type="text" name="nombre" value="{{$user->nombre}}" maxlength="255"
                                   class="form-control input-circle" placeholder="Nombre de Usuario.">
                        </div>
                        @if($user->tipo_de_persona == 'juridica')
                            <div class="form-group">
                                <label for="Apellido" class="control-label">Apellido:</label>
                                <input required type="text" name="representante_legal" value="{{$user->representante_legal}}" maxlength="255"
                                       class="form-control input-circle" placeholder="Representante legal.">
                            </div>
                        @else
                            <div class="form-group">
                                <label for="Apellido" class="control-label">Apellido:</label>
                                <input required type="text" name="apellido" value="{{$user->apellido}}" maxlength="255"
                                       class="form-control input-circle" placeholder="Apellido de Usuario.">
                            </div>
                            <div class="form-group">
                                <label for="Cedula" class="control-label">Cedula:</label>
                                <input required name="cedula" type="text" value="{{$user->cedula}}" maxlength="11"
                                       class="form-control input-circle" placeholder="Cedula de Usuario.">
                            </div>
                            <div class="form-group">
                                <label for="txFechaNacimiento" class="control-label">Fecha de
                                    Nacimiento:</label>
                                <input required name="fecha_nacimiento" value="{{$user->fecha_nacimiento}}" type="text"
                                       class=" desplegarCalendario form-control input-circle"
                                       placeholder="Fecha de Nacimiento de Usuario.">
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="txTelefono" class="control-label">Telefono:</label>
                            <input required type="text" value="{{$user->telefono}}" maxlength="100" name="telefono"
                                   class="form-control input-circle" placeholder="Telefono de Usuario.">
                        </div>
                        <div class="form-group">
                            <label for="Email" class="control-label">Email:</label>
                            <input required type="email" name="email" value="{{$user->email}}" maxlength="255"
                                   class="form-control input-circle"
                                   placeholder="Correo Electronico de Usuario.">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Email" class="control-label">Número de cuenta:</label>
                            <input type="text" value="{{$user->numero_de_cuenta}}" name="numero_de_cuenta"
                                   maxlength="255"
                                   class="form-control input-circle" placeholder="Número de cuenta.">
                        </div>
                        <div class="form-group">
                            <label for="Sexo" class="control-label">Tipo de cuenta:</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="tipo_cuenta" value="ahorros"
                                       checked>Ahorros
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="tipo_cuenta" value="">Corriente
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Banco:</label>
                            <input type="text" value="{{$user->banco}}" name="banco" maxlength="255"
                                   class="form-control input-circle" placeholder="Banco.">
                        </div>
                        <div class="form-group">
                            <label for="Estado" class="control-label">Estado:</label>
                            <select name="estado" class="form-control select2 circle">
                                <option value="act">Activo</option>
                                <option value="ina">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Estado" class="control-label">Tipo de comercial:</label>
                            <select id="slPerfil" required name="perfil" class="form-control select2 circle">
                                @foreach($perfiles as $perfil)
                                    <option @if($perfil->codigo==$user->perfil) selected
                                            @endif value="{{$perfil->Codigo}}">{{$perfil->Descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="control-group">
                            <label class="control-label" for="inputPatient">Comentario:</label>
                            <div class="field desc">
                                <textarea class="form-control" name="comentaro"
                                          placeholder="Comentario"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btGuardar" name="btGuardar" class="btn green">Actualizar</button>
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
            </div>
        </form>
    </div>
</div>
