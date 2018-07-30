<?php

namespace App\Http\Controllers;

use App\Estudio;
use App\Librerias\UtilidadesClass;
use App\Obligacion;
use App\Pagaduria;
use App\Comercial;
use App\User;
use App\SolicitudConsulta;
use App\Departamento;
use App\Municipio;
use App\Librerias\ComponentAdjuntos;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use DB;

class SolicitudConsultaController extends Controller
{
    protected $forma = 'SOLIC';

    /**
     * Muestra el listado de las solicitudes según el estado
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function index()
    {
        if (!UtilidadesClass::ValidarAcceso($this->forma)) {
            return view('errors.401');
        }

        $solicitudes = $this->getSolicitudes(config("constantes.SOLICITUD_COMPLETA"));

        $solicitudesPendientes = $this->getSolicitudes(config("constantes.SOLICITUD_PENDIENTE"));

        $solicitudesDevueltas = $this->getSolicitudes(config("constantes.SOLICITUD_DEVUELTA"));

        $pagadurias = Pagaduria::all();

        $user = Auth::user();
        $comerciales = array();

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {

            $comerciales = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))
                ->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))
                ->orderBy("nombre", "ASC")
                ->get();

        }

        return view('pages.SolicitudesConsulta.index')
            ->with('solicitudes', $solicitudes)
            ->with('contSolicitudes', count($solicitudes))
            ->with('solicitudesPendientes', $solicitudesPendientes)
            ->with('contSolicitudesPendientes', count($solicitudesPendientes))
            ->with('solicitudesDevueltas', $solicitudesDevueltas)
            ->with('contSolicitudesDevueltas', count($solicitudesDevueltas))
            ->with('user', $user);
    }

    /**
     * Despliega la vista para crear una solicitud
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function create()
    {
        $pagadurias = Pagaduria::all();
        $user = Auth::user();
        $comerciales = array();
        $departamentos = Departamento::orderBy('departamento', 'ASC')->get();
        $municipios = array();

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $comerciales = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))
                ->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))
                ->orderBy("nombre", "ASC")->get();
        }

        return view('pages.SolicitudesConsulta.create')
            ->with('departamentos', $departamentos)
            ->with('municipios', $municipios)
            ->with('comerciales', $comerciales)
            ->with('pagadurias', $pagadurias);
    }

    /**
     * Crear una solicitud
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // var_dump(\Request::file('foto_documento')->getMimeType());

        //Validar tipo de documento
        $condiciones = ['foto_documento' => 'mimes:jpg,jpeg,png,pdf',
            'nombre' => 'required',
            'apellido' => 'required',
            'telefono' => 'required',
            'departamento' => 'required',
            'municipio' => 'required',
            'pagaduria_id' => 'required',
            'autorizacion' => 'mimes:jpg,jpeg,png,pdf'
        ];

        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
            'mimes' => 'Por favor solo adjunt&eacute; archivos con extension .jpg .jpeg, .png, .pdf'];

        $validacion = \Validator::make($request->all(), $condiciones, $mensajes);

        if ($validacion->fails()) {

            $errors = $validacion->errors()->all();

            return redirect('solicitudes/create')
                ->with('warning', $errors)
                ->withInput();
        }

        //Validar correo de la solicitud

        $usuarios = User::select('email')
            ->where('estado', config('constantes.ACTIVO'))
            ->where('perfil', config('constantes.ID_PERFIL_CLIENTE'))
            ->get();

        foreach ($usuarios as $usuario) {

            if (trim(strtolower($usuario->email)) == trim(strtolower($data['email']))) {
                return redirect('solicitudes/create')
                    ->with('warning', 'Correo ya registrado, porfavor ingrese otro correo')
                    ->withInput();
            }
        }

        $data['cedula'] = str_replace('_', '', $data['cedula']);
        $data['cedula'] = str_replace('.', '', $data['cedula']);

        if (isset($data['foto_documento'])) {
            $data['foto_documento_oculta'] = 0;
        }

        if (isset($data['autorizacion'])) {
            $data['autorizacion_oculta'] = 0;
        }

        $data['estado'] = $this->validarSolicitud($data);


        $user = Auth::user();
        if ($user->perfil != config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $data['user_id'] = $user->id;
        }

        $solicitud = new SolicitudConsulta($data);

        $solicitud->save();

        $utilidad = new UtilidadesClass();

        if (!is_null($request->file('foto_documento'))) {
            $archivo = $request->file('foto_documento');
            $extension = $archivo->getClientOriginalExtension();
            $NombreOriginal = $archivo->getClientOriginalName();

            $id = $utilidad->registroAdjunto($solicitud->id, 'SolicitudConsulta', $NombreOriginal, $extension, config("constantes.CEDULA_DE_CIUDADANIA"), 'VALO');
            \Storage::disk('adjuntos')->put($id, \File::get($archivo));
        }

        if (!is_null($request->file('autorizacion'))) {
            $archivo = $request->file('autorizacion');
            $extension = $archivo->getClientOriginalExtension();
            $NombreOriginal = $archivo->getClientOriginalName();

            $id = $utilidad->registroAdjunto($solicitud->id, 'SolicitudConsulta', $NombreOriginal, $extension, config("constantes.AUTORIZACION_DE_CONSULTA"), 'VALO');
            \Storage::disk('adjuntos')->put($id, \File::get($archivo));
        }
        return redirect('solicitudes');
    }

    /**
     * Valida la solicitud definiendo el estado en que debe de quedar
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function validarSolicitud($data)
    {

        //0 - > Pendiente
        //1 - > Completa

        $estado = 1;

        if (!isset($data['apellido'])) {
            $estado = 0;
        }

        if (!isset($data['telefono'])) {
            $estado = 0;
        }

        if (!isset($data['pagaduria_id'])) {
            $estado = 0;
        }

        if (!isset($data['clave_desprendible'])) {
            $estado = 0;
        }

        if (!isset($data['email'])) {
            $estado = 0;
        }

        if (!isset($data['foto_documento']) && !isset($data['foto_documento_oculta'])) {
            $estado = 0;
        }

        /*
         if(!isset($data['autorizacion']) &&  !isset($data['autorizacion_oculta']) ){
          $estado = 0;
         }
         */
        return $estado;
    }

    /**
     * Muestra el detalle de la solicitud de consulta
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function show($id)
    {
        $solicitud = SolicitudConsulta::select('*')
            ->with(array('pagaduria' => function ($query) {
                $query->select('*');
            }))
            ->where('id', $id)->get()->first();

        return view('pages.SolicitudesConsulta.detail')->with('solicitud', $solicitud);
    }

    /**
     * Muestra la vista para modificar la solicitud de consulta
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function edit($id)
    {
        $solicitud = SolicitudConsulta::find($id);
        $pagadurias = Pagaduria::all();

        $ComponentAdjuntos = new ComponentAdjuntos();
        $autorizacion = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"));
        $cedula = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"));

        $departamentos = Departamento::all();
        $municipios = Municipio::all();
        $user = Auth::user();
        $comerciales = array();

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {

            $comerciales = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))
                ->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))
                ->orderBy("nombre", "ASC")->get();

        }

        return view('pages.SolicitudesConsulta.edit')
            ->with('pagadurias', $pagadurias)
            ->with('autorizacion', $autorizacion)
            ->with('cedula', $cedula)
            ->with('comerciales', $comerciales)
            ->with('departamentos', $departamentos)
            ->with('municipios', $municipios)
            ->with('solicitud', $solicitud);
    }

    /**
     * Actualiza la solicitud de consulta
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function update(Request $request)
    {
        //
        $data = $request->all();

        $solicitud = SolicitudConsulta::find($data["solicitud_id"]);

        $user = Auth::user();
        if ($user->perfil != config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $data['user_id'] = $user->id;
        }

        $data['cedula'] = str_replace('_', '', $data['cedula']);
        $data['cedula'] = str_replace('.', '', $data['cedula']);

        if (isset($data['foto_documento_oculta'])) {
            $data['foto_documento'] = 0;
        }

        if (isset($data['autorizacion_oculta'])) {
            $data['autorizacion'] = 0;
        }
        //----//
        if (isset($data['foto_documento'])) {
            $data['foto_documento_oculta'] = 0;
        }

        if (isset($data['autorizacion'])) {
            $data['autorizacion_oculta'] = 0;
        }


        //Validar tipo de documento
        $condiciones = ['foto_documento' => 'mimes:jpg,jpeg,png,pdf',
            'nombre' => 'required',
            'apellido' => 'required',
            'telefono' => 'required',
            'departamento' => 'required',
            'municipio' => 'required',
            'pagaduria_id' => 'required',
            'autorizacion' => 'mimes:jpg,jpeg,png,pdf'
        ];

        $mensajes = ['mimes' => 'Por favor solo adjunt&eacute; archivos con extension .jpg .jpeg, .png, .pdf'];

        $validacion = \Validator::make($request->all(), $condiciones, $mensajes);

        if ($validacion->fails()) {
            $error = $validacion->errors()->all();
            return redirect('solicitudes/' . $data["solicitud_id"] . '/edit')
                ->with('warning', $error)
                ->withInput();
        }

        $data['estado'] = $this->validarSolicitud($data);
        $solicitud->fill($data);

        $solicitud->save();

        $utilidad = new UtilidadesClass();


        if (!is_null($request->file('foto_documento'))) {

            $archivo = $request->file('foto_documento');
            $extension = $archivo->getClientOriginalExtension();
            $NombreOriginal = $archivo->getClientOriginalName();

            $id = $utilidad->registroAdjunto($solicitud->id, config("constantes.KEY_SOLICITUD"), $NombreOriginal, $extension, config("constantes.CEDULA_DE_CIUDADANIA"), 'VALO');
            \Storage::disk('adjuntos')->put($id, \File::get($archivo));

        }

        if (!is_null($request->file('autorizacion'))) {

            $archivo = $request->file('autorizacion');
            $extension = $archivo->getClientOriginalExtension();
            $NombreOriginal = $archivo->getClientOriginalName();

            $id = $utilidad->registroAdjunto($solicitud->id, config("constantes.KEY_SOLICITUD"), $NombreOriginal, $extension, config("constantes.AUTORIZACION_DE_CONSULTA"), 'VALO');
            \Storage::disk('adjuntos')->put($id, \File::get($archivo));

        }

        return redirect('solicitudes');

        /* $solicitudes = $this->getSolicitudes(config("constantes.SOLICITUD_COMPLETA"));

         $tabla_completas = $this->tablaSolicitudesCompletas($solicitudes);

         $solicitudesPendintes =  $this->getSolicitudes(config("constantes.SOLICITUD_PENDIENTE"));

         $tabla_pendientes = $this->tablaSolicitudesPendientes($solicitudesPendintes);

         $solicitudesDevueltas =  $this->getSolicitudes(config("constantes.SOLICITUD_DEVUELTA"));

         $tabla_devueltas = $this->tablaSolicitudesDevueltas($solicitudesDevueltas);

         echo json_encode([
              "mensajes" => "La solicitud ha sido Modificada con exito",
              "tabla_devueltas" => $tabla_devueltas,
              "tabla_pendientes" => $tabla_pendientes,
              "tabla_completas" => $tabla_completas,
          ]);*/
    }

    /**
     * Define la solicitud de consulta
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     */
    public function solicitudDevuelta(Request $request)
    {

        $data = $request->all();

        $solicitud = SolicitudConsulta::find($data["id"]);
        $solicitud->estado = 2;
        $solicitud->descripcion_devolucion = $data['descripcion_devolucion'];
        $solicitud->save();

        $solicitudes = $this->getSolicitudes(config("constantes.SOLICITUD_COMPLETA"));

        $tabla_completas = $this->tablaSolicitudesCompletas($solicitudes);

        $solicitudesPendintes = $this->getSolicitudes(config("constantes.SOLICITUD_PENDIENTE"));

        $tabla_pendientes = $this->tablaSolicitudesPendientes($solicitudesPendintes);

        $solicitudesDevueltas = $this->getSolicitudes(config("constantes.SOLICITUD_DEVUELTA"));

        $tabla_devueltas = $this->tablaSolicitudesDevueltas($solicitudesDevueltas);

        echo json_encode([
            "mensajes" => "La solicitud ha sido devuelta con exito",
            "tabla_devueltas" => $tabla_devueltas,
            "contDevueltas" => count($solicitudesDevueltas),
            "tabla_pendientes" => $tabla_pendientes,
            "contPendientes" => count($solicitudesPendintes),
            "tabla_completas" => $tabla_completas,
            "countCompletas" => count($solicitudes),
        ]);
    }

    /**
     * Html de la tabla de solicitudes completas
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     **/
    public function tablaSolicitudesCompletas($solicitudes)
    {
        $ComponentAdjuntos = new ComponentAdjuntos();
        $count = 0;
        $user = Auth::user();

        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable' id='tabla'>
                    <thead>
                      <tr>
                            <th><center> Nº</center></th>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estado</th>";

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $tabla .= " <th> Comercial</th>";
        }

        $tabla .= " <th style='text-align: right !important;'> Acción</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($solicitudes as $solicitud) {
            $cedula = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"));
            $autorizacion = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"));
            $banco = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD"));

            $tabla .=
                "<tr id='" . $solicitud->id . "' class='item" . $solicitud->id . "'>
                        <td>" . $count++ . "</td>
                        <td>" . $solicitud->nombre . " " . $solicitud->apellido . "</td>
                        <td>" . $solicitud->cedula . "</td>
                        <td>" . $solicitud->pagaduriaNombre . "</td>
                        <td>" . substr($solicitud->created_at, 0, 10) . "</td>";
            $tabla .= "<td>";

            if (count($cedula) > 0) {
                $tabla .= "<label>Foto documento</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $cedula[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Foto documento</label><br/>";
            }

            if (count($autorizacion) > 0) {
                $tabla .= "<label>Autorizaci&oacute;n</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $autorizacion[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Autorizaci&oacute;n</label><br/>";
            }

            if (count($banco) > 0) {
                $tabla .= "<label>Formato Banco</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $banco[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Formato Banco</label><br/>";
            }

            $tabla .= "</td><td>".$solicitud->estadoEstudio."</td>";

            if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
                $tabla .= "<td>" . $solicitud->usuarioNombre . " " . $solicitud->usuarioPrimerApellido . " " . $solicitud->usuarioApellido . "</td>";
            }

            $tabla .= "<td><div class='btn-group btn-group-sm' role='group' aria-label=''...'>";

            if (!empty($solicitud->valoracion_id)) {
                if ($solicitud->estadoEstudio == "NEG") {
                    $tabla .= "<a class='btn btn-icon-only red'><i class='fa fa-file fa-xs' title='Llenar solicitud'></i></a><br/>";
                } else {
                    $tabla .= "<a href='" . url('formulario-registro', $solicitud->user_id) . "' class='btn btn-icon-only blue'><i class='fa fa-file fa-xs' title='Llenar solicitud'></i></a><br/>";
                }
            } elseif ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
                $tabla .="<a href='" . url('solicitudes/' . $solicitud->id) . "' class='btn btn-icon-only yellow-gold'>
                            <i class='fa fa-dollar fa-xs' title='Realizar valoraci&oacute;n'></i>
                          </a><br/>";
            }

            $tabla .= "<a data-url='/solicitudes/ver-detalle/".$solicitud->id."'  class='btn btm-sm yellow-gold cargarModalAjax'>
                            <i class='fa fa-eye fa-xs' title='Revisar'></i>
                        </a><br/>";

            $tabla .="<a data-url='/solicitudes/ver-bancos/'".$solicitud->id." class='btn btn-icon-only green cargarModalAjax'> <i class='fa fa-university fa-xs'  title='Actualizar banco'></i></a></div></td>";

            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    /**
     * Html de la tabla de solicitudes pendientes
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     **/
    public function tablaSolicitudesPendientes($solicitudes)
    {
        $ComponentAdjuntos = new ComponentAdjuntos();
        $count = 0;
        $user = Auth::user();
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable' id='tabla'>
                    <thead>
                      <tr>
                            <th><center> Nº</center></th>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estado</th>";

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $tabla .= " <th> Comercial</th>";
        }
        $tabla .= "     <th style='text-align: right !important;'> Acción</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($solicitudes as $solicitud) {
            $cedula = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"));
            $autorizacion = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"));
            $banco = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD"));

            $tabla .=
                "<tr id='" . $solicitud->id . "' class='item" . $solicitud->id . "'>
                        <td>" . $count++ . "</td>
                        <td>" . $solicitud->nombre . " " . $solicitud->apellido . "</td>
                        <td>" . $solicitud->cedula . "</td>
                        <td>" . $solicitud->pagaduriaNombre . "</td>
                        <td>" . substr($solicitud->created_at, '0', '1') . "</td>";
            $tabla .= "<td>";
            if (count($cedula) > 0) {
                $tabla .= "<label>Foto documento</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $cedula[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Foto documento</label><br/>";
            }

            if (count($autorizacion) > 0) {
                $tabla .= "<label>Autorizaci&oacute;n</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $autorizacion[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Autorizaci&oacute;n</label><br/>";
            }

            if (count($banco) > 0) {
                $tabla .= "<label>Formato Banco</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $banco[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Formato Banco</label><br/>";
            }

            $tabla .= "</td><td>" . $solicitud->estadoEstudio . "</td>";

            if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
                $tabla .= "<td>" . $solicitud->usuarioNombre . " " . $solicitud->usuarioPrimerApellido . " " . $solicitud->usuarioApellido . "</td>";
            }

            $tabla .= "<td><a href='" . url('solicitudes/' . $solicitud->id . '/edit') . "' class='btn btn-icon-only yellow-gold'>
                            <i class='fa fa-edit'></i></a></td>";

            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    /**
     * Html de la tabla de solicitudes devueltas
     **/
    public function tablaSolicitudesDevueltas($solicitudes)
    {
        $ComponentAdjuntos = new ComponentAdjuntos();
        $count = 0;
        $user = Auth::user();
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center iniciarDatatable SearchDatatable' id='tabla'>
                    <thead>
                      <tr>
                            <th><center> Nº</center></th>
                            <th> Nombre</th>
                            <th> Cedula</th>
                            <th> Pagaduria</th>
                            <th> Fecha</th>
                            <th> Adjuntos</th>
                            <th> Estado</th>
                            <th> Descripci&oacute;n</th>";


        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
            $tabla .= " <th> Comercial</th>";
        }
        $tabla .= " <th style='text-align: right !important;'> Acción</th>
                        </tr>
                        </thead>
                        <tbody>";

        foreach ($solicitudes as $solicitud) {
            $cedula = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.CEDULA_DE_CIUDADANIA"));
            $autorizacion = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.AUTORIZACION_DE_CONSULTA"));
            $banco = $ComponentAdjuntos->adjunto_exist($solicitud->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_SOLICITUD"), config("constantes.FORMATO_BANCO_SOLICITUD"));

            $tabla .="<tr id='" . $solicitud->id . "' class='item" . $solicitud->id . "'>
                        <td>" . $count++ . "</td>
                        <td>" . $solicitud->nombre . " " . $solicitud->apellido . "</td>
                        <td>" . $solicitud->cedula . "</td>
                        <td>" . $solicitud->pagaduriaNombre . "</td>
                        <td>" . substr($solicitud->created_at, '0', '1') . "</td>";

            $tabla .= "<td>";

            if (count($cedula) > 0) {
                $tabla .= "<label>Foto documento</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $cedula[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Foto documento</label><br/>";
            }

            if (count($autorizacion) > 0) {
                $tabla .= "<label>Autorizaci&oacute;n</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $autorizacion[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Autorizaci&oacute;n</label><br/>";
            }

            if (count($banco) > 0) {
                $tabla .= "<label>Formato Banco</label><a class='color-negro' title='Visualizar' href='" . config('constantes.RUTA') . '/visualizar/' . $banco[0]->id . "' target='_blank'><span class='fa fa-paperclip  color-negro' style='font-size:15px'></span></a><br/>";
            } else {
                $tabla .= "<label>Formato Banco</label><br/>";
            }

            $tabla .= "</td><td>".$solicitud->estadoEstudio."</td>";

            if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {
                $tabla .= "<td>" . $solicitud->descripcion_devolucion . "</td>
                                <td>" . $solicitud->usuarioNombre . " " . $solicitud->usuarioPrimerApellido . " " . $solicitud->usuarioApellido . "</td>
                            <td>";
            }

            $tabla .= "<a href='".url('solicitudes/' . $solicitud->id . '/edit')."' class='btn btn-icon-only yellow-gold'>
                            <i class='fa fa-edit'></i>
                       </a>";

            $tabla .= "</td>";

            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    /**
     * Obtiene las solicitudes segun el estado y las solicitudes
     * segùn si es administrador o comercial
     *
     * @author Vanessa Torres <programdorcc@laborfinanciera.com>
     **/
    public function getSolicitudes($estado)
    {

        $user = Auth::user();

        $solicitudes = array();

        $query = DB::table('solicitudes_consulta')
            ->leftJoin('estudios', 'solicitudes_consulta.valoracion_id', '=', 'estudios.Valoracion')
            ->leftJoin('users', 'solicitudes_consulta.user_id', '=', 'users.id')
            ->join('pagadurias', 'solicitudes_consulta.pagaduria_id', '=', 'pagadurias.id')
            ->where('solicitudes_consulta.estado', $estado)
            ->orderBy('solicitudes_consulta.updated_at', 'DESC')
            ->select('solicitudes_consulta.*',
                'estudios.Tasa as estudioTasa',
                'estudios.Plazo as estudioPlazo',
                'estudios.Cuota as estudioCuota',
                'estudios.ValorCredito as estudioValorCredito',
                'estudios.Desembolso as estudioDesembolso',
                'estudios.Estado as estadoEstudio',
                'users.nombre as usuarioNombre',
                'users.primerApellido as usuarioPrimerApellido',
                'users.apellido as usuarioApellido',
                'users.cedula as usuarioCedula',
                'users.telefono as usuarioTelefono',
                'users.email as usuarioEmail',
                'users.perfil as usuarioPerfil',
                'pagadurias.id as pagaduriaId',
                'pagadurias.nombre as pagaduriaNombre'
            );

        if ($user->perfil == config("constantes.PERFIL_ROOT") || $user->perfil == config("constantes.PERFIL_ADMIN")) {

            $solicitudes = $query->get();

        } else {

            $solicitudes = $query->where('solicitudes_consulta.user_id', $user->id)
                ->get();
        }

        return $solicitudes;

    }

    public function getSolicitudById($id)
    {

        $user = Auth::user();

        $solicitudes = array();

        $query = DB::table('solicitudes_consulta')
            ->leftJoin('estudios', 'solicitudes_consulta.valoracion_id', '=', 'estudios.Valoracion')
            ->leftJoin('users', 'solicitudes_consulta.user_id', '=', 'users.id')
            ->join('pagadurias', 'solicitudes_consulta.pagaduria_id', '=', 'pagadurias.id')
            ->where('solicitudes_consulta.id', $id)
            ->orderBy('solicitudes_consulta.updated_at', 'DESC')
            ->select('solicitudes_consulta.*',
                'estudios.Tasa as estudioTasa',
                'estudios.id as estudioId',
                'estudios.Plazo as estudioPlazo',
                'estudios.Cuota as estudioCuota',
                'estudios.ValorCredito as estudioValorCredito',
                'estudios.Desembolso as estudioDesembolso',
                'estudios.Estado as estadoEstudio',
                'users.nombre as usuarioNombre',
                'users.primerApellido as usuarioPrimerApellido',
                'users.apellido as usuarioApellido',
                'users.cedula as usuarioCedula',
                'users.telefono as usuarioTelefono',
                'users.email as usuarioEmail',
                'users.perfil as usuarioPerfil',
                'pagadurias.id as pagaduriaId',
                'pagadurias.nombre as pagaduriaNombre'
            );
        $solicitud = $query->get()[0];
        if(isset($solicitud->estudioId)){
            $estudio = Estudio::find($solicitud->estudioId);
            $solicitud->obligaciones = Obligacion::where("Valoracion", $estudio->Valoracion)->where("Estado", "Activo")->where('EstadoCuenta', "<>", "Cerrada")->orderBy('Compra', 'desc')->orderBy('EstadoCuenta', 'desc')->get();
        }
        return $solicitud;

    }

    public function getMunicipio(Request $request)
    {

        //$municipios = Municipio::where('departamento_id',$request->departamento_id);
        return response()->json(Municipio::where('departamento_id', $request->departamento_id)->orderBy('municipio','ASC')->get());
    }

    public function mostrarBancos($id)
    {
        $solicitud = SolicitudConsulta::find($id);
        return view('pages.SolicitudesConsulta.Fragmento.bancos')->with('solicitud', $solicitud);
    }

    public function guardarBanco(Request $request)
    {

        $data = $request->all();

        $solicitud = SolicitudConsulta::find($data["id"]);
        $solicitud->banco = $data["banco"];
        $solicitud->save();

        $solicitudes = $this->getSolicitudes(config("constantes.SOLICITUD_COMPLETA"));

        $tabla_completas = $this->tablaSolicitudesCompletas($solicitudes);

        $solicitudesPendintes = $this->getSolicitudes(config("constantes.SOLICITUD_PENDIENTE"));

        $tabla_pendientes = $this->tablaSolicitudesPendientes($solicitudesPendintes);

        $solicitudesDevueltas = $this->getSolicitudes(config("constantes.SOLICITUD_DEVUELTA"));

        $tabla_devueltas = $this->tablaSolicitudesDevueltas($solicitudesDevueltas);

        echo json_encode([
            "tabla_devueltas" => $tabla_devueltas,
            "tabla_pendientes" => $tabla_pendientes,
            "tabla_completas" => $tabla_completas,
        ]);

    }

    public function detalleSolicitud($id)
    {
        $solicitud = $this->getSolicitudById($id);
        return view('pages.SolicitudesConsulta.Fragmento.solicitud_detail')->with('solicitud', $solicitud);
    }
}
