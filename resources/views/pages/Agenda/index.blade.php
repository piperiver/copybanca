@extends('layout.default')
@section('content')
<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<link href="{{ asset('assets/global/plugins/calendar-full/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/calendar-full/fullcalendar.print.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/moment.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/calendar-full/fullcalendar.min.js') }}" type="text/javascript" ></script>
<script src="{{ asset('assets/global/plugins/calendar-full/locale-all.js') }}" type="text/javascript" ></script>
<script src="{{ asset('js/Agenda/index.js') }}" type="text/javascript" ></script>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet box main-color">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-calendar"></i>
                    Mi Agenda
                </div>
            </div>
            <div id="contenido" class="portlet-body">     
                <div id="calendar"></div>
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="eventos" id="eventos" value="{{$usuario}}"/>
<input type="hidden" name="usuario" id="usuario" value="{{Auth::user()->id}}"/>
<div id="createEventModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

    <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title">Crear Evento</h4>
        </div>
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label" for="inputPatient">Titulo:</label>
                <div class="field desc">
                    <input class="form-control" id="titulo" name="titulo" placeholder="Titulo" type="text" value="">
                </div>
            </div>

            <input type="hidden" id="startTime"/>
            <input type="hidden" id="endTime"/>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">


            <div class="control-group">
                <label class="control-label" for="when">Dia/Hora:</label>
                <input type="text" class="form-control" id="diaHora" name="diaHora"/>
            </div>

            <div class="control-group">
               <label class="control-label" for="inputPatient">Lugar:</label>
               <div class="field desc">
                    <input class="form-control" id="lugar" name="lugar" placeholder="Lugar" type="text" value=""/>
               </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPatient">Descripcion:</label>
                <div class="field desc">
                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Observaciones"></textarea>
                </div>
            </div>
        </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="submitButton">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal to Event Details -->
<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Detalle:</h4>
            </div>
            <div class="modal-body">
                <div class="control-group">
                    <label class="control-label" for="inputPatient">Titulo:</label>
                    <div class="field desc">
                        <input class="form-control" type="text" value="" id="modalTitle" name="modalTitle"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputPatient">Lugar:</label>
                    <div class="field desc">
                        <input class="form-control" type="text" value="" id="modalLugar" name="modalLugar"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputPatient">Descripcion:</label>
                    <div class="field desc">
                        <textarea class="form-control" id="modalDescripcion" name="modalDescripcion" placeholder="Observaciones"></textarea>
                    </div>
                </div>
            </div>
            <input type="hidden" id="eventID"/>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                <button type="submit" class="btn btn-danger" id="deleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>
<!--Modal-->

@endsection


