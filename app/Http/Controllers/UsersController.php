<?php

namespace App\Http\Controllers;

use App\Municipio;
use App\Pagaduria;
use App\Valoracion;
use Illuminate\Http\Request;
use App\User;
use App\BancoEntidades;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Hash;
use App\Adjunto;
use DB;

class UsersController extends Controller
{
    protected $forma = 'USUAR';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }
        if(Auth::user()->perfil == config('constantes.PERFIL_COORDINADOR')){
            $Users = User::where('Perfil',config('constantes.PERFIL_COMERCIAL'))->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))->orderBy("nombre", "ASC")->get();
        }else{
            $Users = DB::select("SELECT USERS.id,USERS.nombre,USERS.apellido,USERS.cedula,USERS.fecha_expedicion,
                                    USERS.sexo,USERS.fecha_nacimiento,USERS.telefono,USERS.email,USERS.password,
                                    USERS.estado CodigoEstado,ESTADOS.Descripcion estado,USERS.perfil CodigoPerfil,
                                    PERFILES.Descripcion perfil,USERS.created_at,USERS.updated_at
                               FROM USERS,PERFILES,ESTADOS
                              WHERE USERS.perfil = PERFILES.Codigo
                                AND USERS.estado = ESTADOS.Codigo
                                AND USERS.id != :usuario
                                AND perfil != :perfil
                              ORDER BY nombre",['perfil' => config('constantes.ID_PERFIL_CLIENTE'),
                'usuario' => Auth::user()->id]);
        }
        return view('pages.Usuarios.index')->with('Usuarios', $Users)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Apellido' => 'required|max:255',
                        'Cedula' => 'required|max:11|unique:users',
                        'Sexo' => 'required|max:1',
                        'FechaNacimiento' => 'required|max:11|date',
                        'Telefono' => 'required|max:100',
                        'Email' => 'required|email|unique:users',
                        'Password' => 'required|max:50|same:Confirmacion',
                        'Estado' => 'required',
                        'Perfil' => 'required'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'Email o Identificación digitados ya estan registrados.',
                     'same' => 'Confirmación de Contraseña Incorrecta'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $User = new User();
        $User->nombre = $request->Nombre;
        $User->apellido = $request->Apellido;
        $User->cedula = $request->Cedula;
        $User->sexo = $request->Sexo;
        $User->fecha_nacimiento = $request->FechaNacimiento;
        $User->telefono = $request->Telefono;
        $User->email = $request->Email;
        $User->password = bcrypt($request->Password);
        $User->estado = $request->Estado;
        $User->perfil = $request->Perfil;
        $User->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Apellido' => 'required|max:255',
                        'Cedula' => 'required|max:11',
                        'Sexo' => 'required|max:1',
                        'FechaNacimiento' => 'required|max:11|date',
                        'Telefono' => 'required|max:100',
                        'Email' => 'required|email',
                        'Estado' => 'required',
                        'Perfil' => 'required'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $User = User::find($request->input('id'));
        
        if($User->email != $request->Email)
        {
            $Validar = User::where('email',$request->Email)->first();
            if(isset($Validar->email))
            {
                return response()->json(['errores' => true, 'Mensaje' => ['El Correo digitado No está Disponible.']]);
            }
            else
            {
                $User->email = $request->Email;
            }
        }
        if($User->cedula != $request->Cedula)
        {
            $Validar = User::where('cedula',$request->Cedula)->first();
            if(isset($Validar->cedula))
            {
                return response()->json(['errores' => true, 'Mensaje' => ['La Cedula digitada ya se encuentra registrada en el Sistema.']]);
            }
            else
            {
                $User->cedula = $request->Cedula;
            }
        }
        $User->nombre = $request->Nombre;
        $User->apellido = $request->Apellido;
        $User->sexo = $request->Sexo;
        $User->fecha_nacimiento = $request->FechaNacimiento;
        $User->telefono = $request->Telefono;
        $User->estado = $request->Estado;
        $User->perfil = $request->Perfil;
        
        $User->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $User = User::find($request->input('id'));
        $User->delete();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla]);
    }

    public function cambioPassword(Request $request)
    {
        $condiciones = ['Password1' => 'required|max:50|same:Password2'];
        
        $mensajes = ['required' => 'Campo Contraseña es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'same' => 'Confirmación de Contraseña Incorrecta.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $User = User::find($request->id);
        $User->password = bcrypt($request->Password1);
        $User->save();
        
        if(isset($request->Cliente))
        {
            $Users = User::where('Perfil',config('constantes.ID_PERFIL_CLIENTE'))->orderBy("nombre", "ASC")->get();
            $tabla = $this->tablaClientes($Users);
        }
        else
        {
            $Users = User::where('Perfil','<>', config('constantes.ID_PERFIL_CLIENTE'))->orderBy("nombre", "ASC")->get();
            $tabla = $this->tabla($Users);
        }
        

        return response()->json(['Mensaje' => 'Contraseña Actualizada.',
                                 'tabla' => $tabla]);
    }
    
    public function tabla()
    {
        $Users = DB::select("SELECT USERS.id,USERS.nombre,USERS.apellido,USERS.cedula,USERS.fecha_expedicion,
                                    USERS.sexo,USERS.fecha_nacimiento,USERS.telefono,USERS.email,USERS.password,
                                    USERS.estado CodigoEstado,ESTADOS.Descripcion estado,USERS.perfil CodigoPerfil,
                                    PERFILES.Descripcion perfil,USERS.created_at,USERS.updated_at
                               FROM USERS,PERFILES,ESTADOS
                              WHERE USERS.perfil = PERFILES.Codigo
                                AND USERS.estado = ESTADOS.Codigo
                                AND USERS.id != :usuario
                                AND perfil != :perfil
                              ORDER BY nombre",['perfil' => config('constantes.ID_PERFIL_CLIENTE'),
                                                'usuario' => Auth::user()->id]);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                        <th> Nombre </th>
                        <th> Apellido </th>
                        <th> Cedula </th>
                        <th> Sexo </th>
                        <th> Fecha Nacimiento </th>
                        <th> Telefono </th>
                        <th> Email </th>
                        <th> Estado </th>
                        <th> Perfil </th>
                        <th> Ultima Actualización </th>
                        <th> Fecha Creación </th>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<th> Acción </th>";
                        }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Users as $User)
                    {
                        $tabla .=
                        "<tr id='".$User->id."'>
                            <td>". $User->nombre ."</td>
                            <td>". $User->apellido ."</td>
                            <td>". $User->cedula ."</td>
                            <td>". $User->sexo ."</td>
                            <td>". $User->fecha_nacimiento ."</td>
                            <td>". $User->telefono ."</td>
                            <td>". $User->email ."</td>
                            <td>". $User->estado ."</td>
                            <td>". $User->perfil ."</td>
                            <td>". $User->updated_at ."</td>
                            <td>". $User->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='".$User->id."' data-nombre='".$User->nombre."' data-apellido='".$User->apellido."' data-cedula='".$User->cedula."' data-sexo='".$User->sexo."' data-fechanacimiento='".$User->fecha_nacimiento."' data-telefono='".$User->telefono."' data-email='".$User->email."' data-password='".$User->password."' data-estado='".$User->CodigoEstado."' data-perfil='".$User->CodigoPerfil."'>
                                        <i class='fa fa-edit'></i>
                                    </a>";
                                }
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='".$User->id."' data-nombre='".$User->nombre."'>
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

    function updateDatosUsuario(Request $request){       
        
        $entidad = BancoEntidades::where("nombre", strtoupper($request->pagaduria))->first();        
        if(!isset($entidad->id)){
            $bancoEntidad = new BancoEntidades;
            $bancoEntidad->nombre = strtoupper($request->pagaduria);
            $bancoEntidad->save();
        }

        $fechaExpedicion = str_replace("/", "-", $request->fecha);

        $user = User::find(Auth::user()->id);
            $user->nombre = $request->nombre;
            $user->cedula = $request->cedula;
            $user->apellido = $request->pApellido;
            $user->primerApellido = $request->pApellido;
            $user->fecha_expedicion = strtotime($fechaExpedicion);
            $user->pagaduria = strtoupper($request->pagaduria);
        echo $user->save();
    }

    /*
        Metodos de la vista de perfil.blade.php
    */

    public function miPerfil()
    {
        return view('pages.Usuarios.perfil');
    }

    public function actualizarPerfil(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Apellido' => 'required|max:255',
                        'Telefono' => 'required|max:100'];
        
        $validacion = \Validator::make($request->all(),$condiciones);

        if ($validacion->fails())
        {
            return back()->withInput()->withErrors(['Todos los campos Marcados con el asterisco (*) Son Obligatorios.']);
        }

        $User = User::find(Auth::user()->id);
        $User->nombre = $request->Nombre;
        $User->apellido = $request->Apellido;
        $User->fecha_nacimiento = $request->FechaNacimiento;
        $User->telefono = $request->Telefono;
        $actualizado = $User->save();

        return redirect('MiPerfil')->with('msg', "Los Datos han sido Actualizados.");
    }

    public function cambiarFoto(Request $request)
    {
        $archivo = $request->file('fFotoPerfil');
        $NombreOriginal = $archivo->getClientOriginalName();
	    $extension = $archivo->getClientOriginalExtension();

        $extensionesPermitidas = [
            "jpg",
            "jpeg",
            "png",
            "bmp"
        ];

        if(!in_array(strtolower($extension), $extensionesPermitidas))
        {
            return back()->withInput()->withErrors(['Imagen No Valida.']);
        }

        $Adjunto = Adjunto::where('idPadre',Auth::user()->id)
                          ->where('Tabla','Users')
                          ->where('TipoAdjunto','FPE')->first();
        if(is_null($Adjunto))
        {
            $Adjunto = new Adjunto();
            $Adjunto->NombreArchivo = $NombreOriginal;
            $Adjunto->Extension = $extension;
            $Adjunto->idPadre = Auth::user()->id;
            $Adjunto->Tabla = 'Users';
            $Adjunto->TipoAdjunto = 'FPE';
            $Adjunto->Modulo = 'VALO';
            $Adjunto->save();
        }
        else
        {
            $Adjunto->NombreArchivo = $NombreOriginal;
            $Adjunto->Extension = $extension;
            $Adjunto->save();
        }
        
        \Storage::disk('fotosperfiles')->put($Adjunto->id.".".$extension, \File::get($archivo));
        
        return back();
    }

    public function cambioClave(Request $request)
    {
        $condiciones = ['Clave' => 'required|max:255',
                        'NuevaClave' => 'required|max:255',
                        'Confirmacion' => 'required|max:255'];
        
        $validacion = \Validator::make($request->all(),$condiciones);

        if ($validacion->fails())
        {
            return back()->withInput()->withErrors(['Todos los campos Marcados con el asterisco (*) Son Obligatorios.']);
        }

        $User = User::find(Auth::user()->id);
        
        if(Hash::check($request->Clave, $User->password) && $request->NuevaClave == $request->Confirmacion)
        {
            $User->password = bcrypt($request->NuevaClave);
            $User->save();
            $mensaje = "La Contraseña se ha Cambiado Satisfactoriamente.";
        }
        else
        {
            $mensaje = "Contraseña Incorrecta o Confirmación no coinciden, Por Favor Verifique los Datos he intente Nuevamente.";
        }

        return redirect('MiPerfil')->with('msg', $mensaje);
    }

    public function fotoPerfil()
    {
        $Adjunto = Adjunto::where('idPadre',Auth::user()->id)
                          ->where('Tabla','Users')
                          ->where('TipoAdjunto','FPE')->first();
        if(!is_null($Adjunto))
        {
            return $Adjunto->id.".".$Adjunto->Extension;
        }
        else
        {
            return "profile-default.svg";
        }
    }

    /*
        Metodos de la vista de clientes.blade.php
    */

    public function clientes()
    {
        $this->forma = 'CLIEN';
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }
        $Clientes = DB::select("SELECT USERS.id,USERS.nombre,USERS.apellido,USERS.cedula,
                                       USERS.fecha_nacimiento,USERS.telefono,USERS.email,USERS.password,
                                       USERS.pagaduria,USERS.estado CodigoEstado,ESTADOS.Descripcion estado,
                                       USERS.created_at,USERS.updated_at
                                  FROM USERS,ESTADOS
                                 WHERE USERS.estado = ESTADOS.Codigo
                                   AND perfil = :perfil
                                 ORDER BY nombre",['perfil' => config('constantes.ID_PERFIL_CLIENTE')]);

        return view('pages.Usuarios.clientes')->with('Clientes', $Clientes)->with('forma', $this->forma);
    }

    public function createCliente(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Apellido' => 'required|max:255',
                        'Cedula' => 'required|max:11|unique:users',
                        'Telefono' => 'required|max:100',
                        'Pagaduria' => 'required|max:255',
                        'Email' => 'required|email|unique:users',
                        'Password' => 'required|max:50|same:Confirmacion',
                        'Estado' => 'required'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'Email o Identificación digitados ya estan registrados.',
                     'same' => 'Confirmación de Contraseña Incorrecta'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Cliente = new User();
        $Cliente->nombre = $request->Nombre;
        $Cliente->apellido = $request->Apellido;
        $Cliente->cedula = $request->Cedula;
        $Cliente->fecha_nacimiento = $request->fecha_nacimiento;
        $Cliente->telefono = $request->Telefono;
        $Cliente->email = $request->Email;
        $Cliente->pagaduria = $request->Pagaduria;
        $Cliente->password = bcrypt($request->Password);
        $Cliente->perfil = config('constantes.ID_PERFIL_CLIENTE');
        $Cliente->estado = $request->Estado;
        $Cliente->save();
        
        $tabla = $this->tablaClientes();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                            'tabla' => $tabla]);
    }

    public function updateCliente(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Apellido' => 'required|max:255',
                        'Cedula' => 'required|max:11',
                        'Telefono' => 'required|max:100',
                        'Pagaduria' => 'required|max:255',
                        'Email' => 'required|email',
                        'Estado' => 'required'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'Email o Identificación digitados ya estan registrados.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $Cliente = User::find($request->input('id'));
        if($Cliente->email != $request->Email)
        {
            $Validar = User::where('email',$request->Email)->first();
            if(isset($Validar->email))
            {
                return response()->json(['errores' => true, 'Mensaje' => ['El Correo digitado No está Disponible.']]);
            }
            else
            {
                $Cliente->email = $request->Email;
            }
        }
        if($Cliente->cedula != $request->Cedula)
        {
            $Validar = User::where('cedula',$request->Cedula)->first();
            if(isset($Validar->cedula))
            {
                return response()->json(['errores' => true, 'Mensaje' => ['La Cedula digitada ya se encuentra registrada en el Sistema.']]);
            }
            else
            {
                $Cliente->cedula = $request->Cedula;
            }
        }
        $Cliente->nombre = $request->Nombre;
        $Cliente->apellido = $request->Apellido;
        $Cliente->fecha_nacimiento = $request->fecha_nacimiento;
        $Cliente->telefono = $request->Telefono;
        $Cliente->pagaduria = $request->Pagaduria;
        $Cliente->estado = $request->Estado;
        $Cliente->save();
        
        $tabla = $this->tablaClientes();

        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }
    
    public function clavesDesprendibles(Request $request)
    {
        $Desprendibles = DB::select("SELECT ESTUDIOS.Pagaduria,ClaveDesprendible
                                       FROM ESTUDIOS,VALORACIONES,USERS
                                      WHERE USERS.id = :id
                                        AND Usuario = USERS.id
                                        AND VALORACIONES.id = Valoracion",['id' => $request->id]);
        $filas = "<table class='table table-striped table-bordered table-hover'>
                    <thead>
                        <tr>
                            <th>Pagaduria</th>
                            <th>Clave</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($Desprendibles as $Desprendible)
        {
            $filas .=
           "<tr>
                <td>".$Desprendible->Pagaduria."</td>
                <td>".$Desprendible->ClaveDesprendible."</td>
            </tr>";
        }
        $filas .= "</tbody></table>";
        
        return response()->json([$filas]);
    }

    public function tablaClientes()
    {
        $Clientes = DB::select("SELECT USERS.id,USERS.nombre,USERS.apellido,USERS.cedula,
                                       USERS.fecha_nacimiento,USERS.telefono,USERS.email,USERS.password,
                                       USERS.pagaduria,USERS.estado CodigoEstado,ESTADOS.Descripcion estado,
                                       USERS.created_at,USERS.updated_at
                                  FROM USERS,ESTADOS
                                 WHERE USERS.estado = ESTADOS.Codigo
                                   AND perfil = :perfil
                                 ORDER BY nombre",['perfil' => config('constantes.ID_PERFIL_CLIENTE')]);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                        <th> Nombres </th>
                        <th> Apellidos </th>
                        <th> Cedula </th>
                        <th> Fecha Nacimiento </th>
                        <th> Email </th>
                        <th> Telefono </th>
                        <th> Pagaduria </th>
                        <th> Estado </th>
                        <th> Ultima Actualización </th>
                        <th> Fecha Creación </th>
                        <th> Acción </th>
                      </tr>
                    </thead>
                    <tbody>";
                    foreach($Clientes as $Cliente)
                    {
                        $tabla .=
                        "<tr id='".$Cliente->id."'>
                            <td>". $Cliente->nombre ."</td>
                            <td>". $Cliente->apellido ."</td>
                            <td>". $Cliente->cedula ."</td>
                            <td>". $Cliente->fecha_nacimiento ."</td>
                            <td>". $Cliente->email ."</td>
                            <td>". $Cliente->telefono ."</td>
                            <td>". $Cliente->pagaduria ."</td>
                            <td>". $Cliente->estado ."</td>
                            <td>". $Cliente->updated_at ."</td>
                            <td>". $Cliente->created_at ."</td>
                            <td>
                                <a href='' id='lkDesprendible' name='lkDesprendible' class='btn btn-icon-only green' data-toggle='modal' data-id='".$Cliente->id."'>
                                    <i class='fa fa-plus'></i>
                                </a>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .=
                                "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='".$Cliente->id."' data-nombre='".$Cliente->nombre."' data-apellido='".$Cliente->apellido."' data-cedula='".$Cliente->cedula."'data-telefono='".$Cliente->telefono."' data-email='".$Cliente->email."' data-pagaduria='".$Cliente->pagaduria."' data-password='".$Cliente->password."' data-estado='".$Cliente->CodigoEstado."' data-fechanacimiento='".$Cliente->fecha_nacimiento."'>
                                        <i class='fa fa-edit'></i>
                                </a>";
                            }
                     $tabla .= "</td></tr>";
                }
        $tabla .= "</tbody></table>";
        return $tabla;
    }
    
    public function listarUsers()
    {
        $Utilidad = new UtilidadesClass();
        $Users = User::where('perfil', '!=', config('constantes.ID_PERFIL_CLIENTE'))->get();
        
        return $Users->toArray();
    }

    public function getFormularioSolicitud($id){
        $valoracion = Valoracion::find($id);
        $user = User::find($valoracion->Usuario);
        $municipios = Municipio::all();
        $tipos_de_vivienda = ['propia','arrendada', 'familiar'];
        $estratos = ['uno','dos', 'tres', 'cuatro', 'cinco', 'seis'];
        $estados_civiles = ['soltero','casado', 'union libre','viudo'];
        $niveles_de_estudio = ['primaria','bachilletaro', 'tecnico', 'tecnologico', 'universitario', 'especialista'];
        $direccion_de_correspondencia = ['residencia','oficina','email'];
        $parentescos = ['padre', 'madre', 'sobrino', 'hij@', 'abuelo','Primo', 'tío', 'hermano', 'abuela'];
        $estados_seguro = ['si', 'no'];
        $json_user = json_encode($user->toJsonData());
        if(isset($user->id)){
            return view('layouts-client.formulario_datos.index')->with(
                ['user'=> $user, 'ciudades'=> $municipios, 'json_user'=>$json_user,
                    'tipos_de_vivienda'=>$tipos_de_vivienda, 'estratos'=>$estratos, 'estados_civiles'=>$estados_civiles,
                    'niveles_de_estudio'=>$niveles_de_estudio, 'direcciones_de_correspondencia'=>$direccion_de_correspondencia,
                    'parentescos'=>$parentescos, 'estados_seguro'=>$estados_seguro
                ]
            );
        }
        return view('errors.404');
    }

    public function updateFormularioData(Request $request, $id) {
        $data = $request->all();
        $user = User::find($id);
        $user->fill($data)->save();
        return response()->json($user->toJsonData());
    }


    public function indexVendedores(){
        $users = User::where('Perfil',config('constantes.PERFIL_COMERCIAL'))->orderBy("nombre", "ASC")->get();
        return view('pages.Usuarios.Comerciales.index')->with('usuarios', $users)->with('forma', $this->forma);;
    }

    public function createVendedor(Request $request){

    }
}