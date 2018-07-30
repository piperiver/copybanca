<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Permiso;
use DB;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Auth;

class PermisosController extends Controller
{
    protected $forma = 'PERMI';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Permisos = DB::select("SELECT PERFILES.Codigo CodigoPerfil,PERFILES.Descripcion Perfil,FORMAS.Codigo CodigoForma,
                                       FORMAS.Descripcion Forma,Insertar,Actualizar,Eliminar,PERMISOS.created_at,
                                       PERMISOS.updated_at
                                  FROM PERFILES,PERMISOS,FORMAS
                                 WHERE Perfil = PERFILES.Codigo
                                   AND Forma = FORMAS.Codigo
                                   AND Perfil != :Perfil
                                 ORDER BY Perfil", ['Perfil' => Auth::user()->perfil]);

        return view('pages.Permisos.index')->with('Permisos', $Permisos)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Perfil' => 'required|max:3',
                        'Forma' => 'required|max:5'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $Permiso = Permiso::where('Perfil', '=', $request->Perfil)->where('Forma', '=', $request->Forma)->first();
        
        if(!empty($Permiso))
        {
            $mensaje = ['Combinación: '.$request->Perfil.'-'.$request->Forma.' Ya Existe.'];
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }

        $Permiso = new Permiso($request->all());
        $Permiso->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $Permiso = Permiso::where('Perfil',$request->Perfil)->where('Forma', $request->Forma)
        ->update(['Insertar' => $request->Insertar,
                'Actualizar' => $request->Actualizar,
                'Eliminar' => $request->Eliminar]);
        
        $tabla = $this->tabla();
        
        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Permiso = Permiso::where('Perfil',$request->Perfil)->where('Forma', $request->Forma)
                   ->delete();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function CargarModulos($Perfil)
    {
        if(Auth::user()->perfil == config("constantes.PERFIL_ROOT")){
            $Modulos = DB::select('SELECT DISTINCT modulos.Codigo, modulos.Icono, modulos.Descripcion Modulo, modulos.Orden 
                                                    FROM `modulos` 
                                                    JOIN formas ON 
                                                    modulos.Codigo = formas.Modulo 
                                                    WHERE 
                                                    formas.Visible = "S" 
                                                    order by Orden');
        }else{
            $Modulos = DB::select("SELECT DISTINCT M.Codigo Codigo, M.Icono Icono, M.Descripcion Modulo, M.Orden Orden
                                 FROM PERMISOS P ,FORMAS F, MODULOS M
                                WHERE P.Forma = F.Codigo
                                  AND M.Codigo = F.Modulo
                                  AND P.Perfil = :Perfil
                                  AND F.Visible = 'S'
                                ORDER BY M.Orden", ['Perfil' => $Perfil]);
        }
       return $Modulos;
    }

    public function CargarVistas($Modulo,$Perfil)
    {
        if(Auth::user()->perfil == config("constantes.PERFIL_ROOT")){
            $Vistas = DB::select("SELECT Icono ,Ruta, Descripcion Forma                                         
                                    FROM FORMAS
                                    WHERE                                      
                                    Modulo = '$Modulo'
                                     AND Visible = 'S'");
        }else{
            $Vistas = DB::select("SELECT F.Icono Icono,F.Ruta Ruta, F.Descripcion Forma,
                                         P.Insertar Insertar, P.Actualizar Actualizar, P.Eliminar Eliminar
                                    FROM FORMAS F,MODULOS M, PERMISOS P, PERFILES PE
                                   WHERE F.Modulo = M.Codigo
                                     AND P.Perfil = PE.Codigo
                                     AND P.Forma = F.Codigo
                                     AND PE.Codigo = :Perfil
                                     AND M.Codigo = :Modulo
                                     AND F.Visible = 'S'", ['Perfil' => $Perfil,'Modulo' => $Modulo]);            
        }
        return $Vistas;
    }

    public function tabla()
    {
        $Permisos = DB::select("SELECT PERFILES.Codigo CodigoPerfil,PERFILES.Descripcion Perfil,FORMAS.Codigo CodigoForma,
                                       FORMAS.Descripcion Forma,Insertar,Actualizar,Eliminar,PERMISOS.created_at,
                                       PERMISOS.updated_at
                                  FROM PERFILES,PERMISOS,FORMAS
                                 WHERE Perfil = PERFILES.Codigo
                                   AND Forma = FORMAS.Codigo
                                   AND Perfil != :Perfil
                                 ORDER BY Perfil", ['Perfil' => Auth::user()->perfil]);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Perfil </th>
                            <th> Forma </th>
                            <th> Insertar </th>
                            <th> Actualizar </th>
                            <th> Eliminar </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Permisos as $Permiso)
                    {
                        $tabla .=
                        "<tr id='".$Permiso->CodigoPerfil."-".$Permiso->CodigoForma."'>
                        <td>". $Permiso->Perfil ." (".$Permiso->CodigoPerfil.")</td>
                        <td>". $Permiso->Forma ." (".$Permiso->CodigoForma.")</td>
                        <td>". $Permiso->Insertar ."</td>
                        <td>". $Permiso->Actualizar ."</td>
                        <td>". $Permiso->Eliminar ."</td>
                        <td>". $Permiso->updated_at ."</td>
                        <td>". $Permiso->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-perfil='".$Permiso->CodigoPerfil."' data-forma='".$Permiso->CodigoForma."' data-insert='".$Permiso->Insertar."' data-update='".$Permiso->Actualizar."' data-delete='".$Permiso->Eliminar."'>
                                <i class='fa fa-edit'></i>
                            </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-perfil='".$Permiso->CodigoPerfil."' data-forma='".$Permiso->CodigoForma."'>
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
