<?php

namespace App\Http\Controllers;

use App\BancoEntidades;
use App\Estado;

use App\Estudio;
use App\Perfil;
use App\SolicitudConsulta;
use App\User;
use App\Valoracion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use App\Comercial;
use DB;


class ComercialesController extends Controller
{
    protected $forma = "COMER";

    public function index()
    {
        $bancos = BancoEntidades::all();
        $users = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))->orderBy("nombre", "ASC")->get();
        $estados = Estado::all();
        return view('pages.Usuarios.Comerciales.index')->with('usuarios', $users)->with('forma', $this->forma)->with('estados', $estados)->with('bancos', $bancos
        );
    }

    public function store(Request $request)
    {
        if ($request->tipo_de_persona == 'natural') {
            $condiciones = ['nombre' => 'required|max:255',
                'apellido' => 'required|max:255',
                'cedula' => 'required|max:11|unique:users',
                'sexo' => 'required|max:1',
                'fecha_nacimiento' => 'required|max:11|date',
                'telefono' => 'required|max:100',
                'email' => 'required|email|unique:users',
                'password' => 'required|max:50|same:password_confirm',
                'perfil' => 'required'];
        } else {
            $condiciones = ['nombre' => 'required|max:255',
                'cedula' => 'required|max:11|unique:users',
                'direccion' => 'required|max:200',
                'representante_legal' => 'required|max:200',
                'documento_representante_legal' => 'required|max:200',
                'telefono' => 'required|max:100',
                'email' => 'required|email|unique:users',
                'password' => 'required|max:50|same:password_confirm',
                'perfil' => 'required'];
        }

        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
            'max' => 'Campo :attribute no permite un numero mayor a  :max',
            'unique' => ':attribute ya estan registrado.',
            'same' => 'Confirmación de Contraseña Incorrecta'];

        $validacion = \Validator::make($request->all(), $condiciones, $mensajes);

        if ($validacion->fails()) {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $user = new User($request->all());
        $user->password = bcrypt($user->password);
        $user->estado = "act";
        $user->save();
        return response()->json(['Mensaje' => 'Comercial guardado',
            'tabla' => $this->getTable()]);
    }

    public function show($id)
    {
        $user = User::find($id);
        $solicitudes_puestas = SolicitudConsulta::where('user_id', $id)->count();
        $solicitudes_aprobadas = SolicitudConsulta::where('user_id', $id)->where('estado', 3)->count();
        $solicitudes_rechazadas = SolicitudConsulta::where('user_id', $id)->where('estado', 2)->count();
        $prestamos = DB::select("SELECT SUM(Estudios.Desembolso) as 'dinero_desembolsado', COUNT(Estudios.id) as 'creditos_aprobados', AVG(Estudios.Desembolso) as 'promedio_desembolso' FROM Valoraciones INNER JOIN Estudios ON Valoraciones.id = Estudios.Valoracion
                                  WHERE estudios.estado NOT IN ('SAV', 'NEG','NVI', 'ING') AND Valoraciones.Comercial = $id
                                  ");
        $negados = DB::select("SELECT COUNT(Estudios.id) as 'creditos_negados' FROM Valoraciones INNER JOIN Estudios ON Valoraciones.id = Estudios.Valoracion
                                  WHERE estudios.estado IN ('NEG','NVI') AND Valoraciones.Comercial = $id");

        $prestamos = $prestamos[0];
        $negados = $negados[0];
        $data = [
            'user'=>$user,
            'solicitudes_aprobadas'=>$solicitudes_aprobadas,
            'solicitudes_puestas' => $solicitudes_puestas,
            'solicitudes_rechazadas' => $solicitudes_rechazadas,
            'promedio_comision'=>$prestamos->dinero_desembolsado * 0.020,
            'negados'=>$negados->creditos_negados
        ];
        $view = view('pages.Usuarios.Comerciales.detail')->with($data)->with((array)$prestamos);
        return $view;
    }

    public function edit($id)
    {
        $perfiles = Perfil::where('Codigo', config('constantes.PERFIL_LIDER_COMERCIAL'))->orWhere('Codigo', config('constantes.PERFIL_COMERCIAL'))->get();
        $view = view('pages.Usuarios.Comerciales.update_form')->with('user', User::find($id))->with('perfiles', $perfiles)->render();
        return $view;
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        $tabla = $this->getTable();
        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
            'tabla' => $tabla]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->fill($request->all());
        $user->save();
        $tabla = $this->getTable();
        return response()->json(['Mensaje' => 'El registro se ha editado.',
            'tabla' => $tabla]);
    }

    public function getTable()
    {
        $users = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))->orderBy("nombre", "ASC")->get();
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                        <th> Nombres </th>
                        <th> Documento </th>
                        <th> Tipo </th>
                        <th> Fecha de inscripción </th>
                        <th> Telefono </th>
                        <th> Email </th>
                        <th> Estado </th>";
        if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar") || UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
            $tabla .= "<th> Acción </th>";
        }
        $tabla .= "</tr>
                    </thead>
                    <tbody>";
        foreach ($users as $user) {
            $tabla .=
                "<tr id='" . $user->id . "'>
                            <td><a data-show-url='".url('comerciales_vtm', $user->id)."' class='comercial-detail'>" . $user->nombres() . "</td>
                            <td><a href='" . url('GestionOficina', $user->id) . "'>" . number_format($user->cedula) . "</a></td>
                            <td>" . $user->perfil . "</td>
                            <td>" . $user->created_at->format('Y-m-d') . "</td>
                            <td>" . $user->telefono . "</td>
                            <td><a href='https://mail.google.com/mail/?view=cm&fs=1&to=" . $user->email . "'>" . $user->email . "</a></td>
                            <td>" . $user->estado . "</td>";
            if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar") || UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
                $tabla .= "<td>";
                if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar")) {
                    $tabla .=
                        "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='" . $user->id . "' data-nombre='" . $user->nombre . "' data-apellido='" . $user->apellido . "' data-cedula='" . $user->cedula . "' data-sexo='" . $user->sexo . "' data-fechanacimiento='" . $user->fecha_nacimiento . "' data-telefono='" . $user->telefono . "' data-email='" . $user->email . "' data-password='" . $user->password . "' data-estado='" . $user->CodigoEstado . "' data-perfil='" . $user->CodigoPerfil . "'>
                                        <i class='fa fa-edit'></i>
                                    </a>";
                }
                if (UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
                    $tabla .=
                        "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='" . $user->id . "' data-nombre='" . $user->nombre . "'>
                                        <i class='fa fa-close'></i>
                                    </a>";
                }
                $tabla .= "</td>";
            }
            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    public function busquedaRegistro(Request $request)
    {
        $datos = Comercial::where('Email', $request->Email)->first();

        return response()->json(['Celular' => isset($datos->Telefono) ? $datos->Telefono : "",
            'Nombre' => isset($datos->Nombre) ? $datos->Nombre : ""]);
    }

    public function actualizarEstadoUsuario(Request $request)
    {
        $this->forma = "COMER";
        $Comercial = Comercial::find($request->id);
        $Comercial->estado = "1";
        $Comercial->save();

        $Usuarios = DB::select("SELECT id, nombre, email, telefono, created_at
                                  FROM USERS
                                 WHERE Estado = :estado", ['estado' => '2']);

        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                        <tr>
                            <th> id </th>
                            <th> Nombre </th>
                            <th> Correo </th>
                            <th> Telefono </th>
                            <th> Fecha de Registro </th>";
        if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar")) {
            $tabla .= "<th> Acción </th>";
        }
        $tabla .= "</tr>
                    </thead>
                    <tbody>";
        foreach ($Usuarios as $User) {
            $tabla .=
                "<tr id='" . $User->id . "'>
                            <td>" . $User->id . "</td>
                            <td>" . $User->nombre . "</td>
                            <td>" . $User->email . "</td>
                            <td>" . $User->telefono . "</td>
                            <td>" . $User->created_at . "</td>";
            if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar")) {
                $tabla .= "<td>
                                    <a href='' id='lkCheck' name='lkCheck' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='" . $User->id . "'>
                                        <i class='fa fa-check'></i>
                                    </a>
                                    </td>";
            }
            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";

        return response()->json(['tabla' => $tabla]);
    }
}
