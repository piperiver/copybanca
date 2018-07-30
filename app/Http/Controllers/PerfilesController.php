<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Auth;
use App\Perfil;
use App\Permiso;
use App\User;
use App\Forma;
use DB;

class PerfilesController extends Controller
{
    protected $forma = 'PERFI';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }
        $Perfiles = DB::select("SELECT PERFILES.Codigo,PERFILES.Descripcion,ESTADOS.Codigo CodigoEstado,
                                       ESTADOS.Descripcion Estado,FORMAS.Descripcion url_redireccionamiento,
                                       FORMAS.Ruta,PERFILES.created_at,PERFILES.updated_at
                                  FROM PERFILES,ESTADOS,FORMAS
                                 WHERE PERFILES.Estado = ESTADOS.Codigo
                                   AND PERFILES.url_redireccionamiento = FORMAS.Ruta
                                 ORDER BY PERFILES.Codigo");
        $Utilidad = new UtilidadesClass();
        $Perfiles = $Utilidad->Ordenar($Perfiles);

        $direcciones = DB::table('formas')->select('Ruta','Descripcion')->where("Visible","S")->get();
        
        return view('pages.Perfiles.index')->with('Perfiles', $Perfiles)
                                           ->with('forma', $this->forma)
                                           ->with("rutas", $direcciones);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:3|unique:Perfiles',
                        'Descripcion' => 'required|max:100',
                        'Estado' => 'required|max:3',
                        'url_redireccionamiento' => 'required|max:250'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Perfil = new Perfil($request->all());
        $Perfil->Codigo = strtoupper($request->Codigo);
        $Perfil->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:100',
                        'Estado' => 'required|max:3',
                        'url_redireccionamiento' => 'required|max:250'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $Perfil = Perfil::find($request->input('Codigo'));
        $Perfil->fill($request->all());
        $Perfil->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        $PerfilInPermiso = Permiso::where("Perfil",$request->input('Codigo'))->first();
        $PerfilInUser = User::where("Perfil",$request->input('Codigo'))->first();
        
        if(isset($PerfilInPermiso->Perfil) || isset($PerfilInUser->perfil))
        {
            $mensaje = $Utilidad->getMessage(4, "danger" , [$request->input('Codigo')], basename( __FILE__ ), __LINE__);
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }
        else
        {      
            $Perfil = Perfil::find($request->input('Codigo'));
            $Perfil->delete();
        }
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                 'tabla' => $tabla]);
    }

    public function listarPerfiles($perfil = false)
    {
        $Utilidad = new UtilidadesClass();
        if(!$perfil)
        {
            $Perfiles = Perfil::where('Estado', '=', $Utilidad->obtenerValorParametro("ESTAACTI"))->get();
        }
        else
        {
            $Perfiles = Perfil::where('Estado', '=', $Utilidad->obtenerValorParametro("ESTAACTI"))
                                ->where('Codigo','<>',Auth::user()->perfil)->get();
        }
        
        return $Perfiles->toArray();
    }

    public function tabla()
    {
        $Perfiles = DB::select("SELECT PERFILES.Codigo,PERFILES.Descripcion,ESTADOS.Codigo CodigoEstado,ESTADOS.Descripcion Estado,
                                       FORMAS.Descripcion url_redireccionamiento,FORMAS.Ruta,
                                       PERFILES.created_at,PERFILES.updated_at
                                  FROM PERFILES,ESTADOS,FORMAS
                                 WHERE PERFILES.Estado = ESTADOS.Codigo
                                   AND PERFILES.url_redireccionamiento = FORMAS.Ruta
                                 ORDER BY PERFILES.Codigo");
        $Utilidad = new UtilidadesClass();
        $Perfiles = $Utilidad->Ordenar($Perfiles);
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Código </th>
                            <th> Descripción </th>
                            <th> Estado </th>
                            <th> Dirección </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Perfiles as $Perfil)
                    {
                        $tabla .=
                        "<tr id='".$Perfil->Codigo."'>
                            <td>". $Perfil->Codigo ."</td>
                            <td>". $Perfil->Descripcion ."</td>
                            <td>". $Perfil->Estado ."</td>
                            <td>". $Perfil->url_redireccionamiento ."</td>
                            <td>". $Perfil->updated_at ."</td>
                            <td>". $Perfil->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Perfil->Codigo."' data-descripcion='".$Perfil->Descripcion."' data-estado='".$Perfil->CodigoEstado."' data-redireccionamiento='".$Perfil->Ruta."'>
                                    <i class='fa fa-edit'></i>
                                </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$Perfil->Codigo."'>
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
}