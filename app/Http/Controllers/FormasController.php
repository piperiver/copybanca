<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Forma;
use App\Permiso;
use App\Estado;
use App\Librerias\UtilidadesClass;
use DB;

class FormasController extends Controller
{
    protected $forma = "FORMA";
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Formas = DB::select("SELECT FORMAS.Codigo,FORMAS.Descripcion,MODULOS.Codigo CodigoModulo,MODULOS.Descripcion Modulo,Ruta,
                                     Visible,FORMAS.Icono,FORMAS.created_at,FORMAS.updated_at
                                FROM FORMAS
                                JOIN MODULOS
                                  ON FORMAS.Modulo = MODULOS.Codigo
                               ORDER BY FORMAS.Codigo");
        $Utilidad = new UtilidadesClass();
        $Formas = $Utilidad->Ordenar($Formas);

        return view('pages.Formas.index')->with('Formas', $Formas)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:5|unique:Formas',
                        'Descripcion' => 'required|max:1000',
                        'Modulo' => 'required|max:4',
                        'Ruta' => 'required|max:200',
                        'Icono' => 'required|max:4000',
                        'Visible' => 'required|max:1'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Forma = new Forma($request->all());
        $Forma->Codigo = strtoupper($request->Codigo);
        $Forma->save();
        $tabla = $this->tabla();
        
        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:1000',
                        'Modulo' => 'required|max:4',
                        'Ruta' => 'required|max:200',
                        'Icono' => 'required|max:4000',
                        'Visible' => 'required|max:1'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Forma = Forma::find($request->input('Codigo'));
        $Forma->fill($request->all());
        $Forma->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        $FormasInPermiso = Permiso::where("Forma",$request->input('Codigo'))->first();
        $Estado = Estado::where("Forma",$request->Codigo)->first();
        
        if(!is_null($FormasInPermiso) || !is_null($Estado)){
            $mensaje = $Utilidad->getMessage(1, "danger" , [$request->input('Codigo')], basename( __FILE__ ), __LINE__);
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }else{
            $Forma = Forma::find($request->input('Codigo'));
            $Forma->delete();
        }
        
        $tabla = $this->tabla();
        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function listarFormas()
    {
        $Formas = Forma::orderBy('Codigo', 'ASC')->get();

        return $Formas->toArray();
    }

    public function tabla()
    {
        $Formas = DB::select("SELECT FORMAS.Codigo,FORMAS.Descripcion,MODULOS.Codigo CodigoModulo,MODULOS.Descripcion Modulo,Ruta,
                                     Visible,FORMAS.Icono,FORMAS.created_at,FORMAS.updated_at
                                FROM FORMAS
                                JOIN MODULOS
                                  ON FORMAS.Modulo = MODULOS.Codigo
                               ORDER BY FORMAS.Codigo");
        
        $Utilidad = new UtilidadesClass();
        $Formas = $Utilidad->Ordenar($Formas);
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Código </th>
                            <th> Descripción </th>
                            <th> Módulo </th>
                            <th> Ruta </th>
                            <th> Visible </th>
                            <th> Icono </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Formas as $Forma)
                    {
                        $tabla .=
                        "<tr id='".$Forma->Codigo."'>
                            <td>". $Forma->Codigo ."</td>
                            <td>". $Forma->Descripcion ."</td>
                            <td>". $Forma->Modulo ."</td>
                            <td>". $Forma->Ruta ."</td>
                            <td>". $Forma->Visible ."</td>
                            <td class='text-center'><span title='" . $Forma->Icono . "' class='fa ". $Forma->Icono . "'></span></td>
                            <td>". $Forma->updated_at ."</td>
                            <td>". $Forma->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Forma->Codigo."' data-descripcion='".$Forma->Descripcion."' data-modulo='".$Forma->CodigoModulo."' data-ruta='".$Forma->Ruta."' data-visible='".$Forma->Visible."' data-icono='".$Forma->Icono."'>
                                        <i class='fa fa-edit'></i>
                                    </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$Forma->Codigo."'>
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