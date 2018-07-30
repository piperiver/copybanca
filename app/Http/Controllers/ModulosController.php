<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modulo;
use App\Parametro;
use App\Forma;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Auth;
use Session;

class ModulosController extends Controller
{    
    
    protected $forma = 'MODUL';
    

    public function index()
    { 
        
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Modulos = Modulo::orderBy('Codigo', 'ASC')->get();        
        $Utilidad = new UtilidadesClass();
        $Modulos = $Utilidad->Ordenar($Modulos);
        return view('pages.Modulos.index')->with('Modulos', $Modulos)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:4|unique:Modulos',
                        'Descripcion' => 'required|max:100',
                        'Orden' => 'required|max:99|numeric',
                        'Icono' => 'required|max:4000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $Modulo = new Modulo($request->all());
        $Modulo->Codigo = strtoupper($request->Codigo);
        $Modulo->save();

        $Modulos = Modulo::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $Modulos = $Utilidad->Ordenar($Modulos);
        $tabla = $this->tabla($Modulos);

      return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                              'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:100',
                        'Orden' => 'required|max:99|numeric',
                        'Icono' => 'required|max:4000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $Modulo = Modulo::find($request->input('Codigo'));
        $Modulo->fill($request->all());
        $Modulo->save();

        $Modulos = Modulo::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $Modulos = $Utilidad->Ordenar($Modulos);
        $tabla = $this->tabla($Modulos);

        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        $ModuloInParametro = Parametro::where("Modulo",$request->input('Codigo'))->get()->toArray();
        $ModuloInForma = Forma::where("Modulo",$request->input('Codigo'))->get()->toArray();

        if(isset($ModuloInParametro[0]["Modulo"]) || isset($ModuloInForma[0]["Modulo"])){
            $mensaje = $Utilidad->getMessage(3, "danger" , [$request->input('Codigo')], basename( __FILE__ ), __LINE__);
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }else{
            $Modulos = Modulo::find($request->input('Codigo'));
            $Modulos->delete();
        }

        $Modulos = Modulo::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $Modulos = $Utilidad->Ordenar($Modulos);
        $tabla = $this->tabla($Modulos);

        return response()->json(['Mensaje' => 'El registro se ha eliminado.',
                                'tabla' => $tabla]);
    }

    public function listarModulos()
    {
        $Modulos = Modulo::orderBy('Orden', 'ASC')->get();

        return $Modulos->toArray();
    }

    public function tabla($Modulos)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Código </th>
                            <th> Descripción </th>
                            <th> Orden </th>
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
                    foreach($Modulos as $Modulo)
                    {
                        $tabla .=
                        "<tr id='".$Modulo->Codigo."'>
                            <td>". $Modulo->Codigo ."</td>
                            <td>". $Modulo->Descripcion ."</td>
                            <td>". $Modulo->Orden ."</td>
                            <td class='text-center'><span title='" . $Modulo->Icono . "' class='fa ". $Modulo->Icono . "'></span></td>
                            <td>". $Modulo->updated_at ."</td>
                            <td>". $Modulo->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Modulo->Codigo."' data-descripcion='".$Modulo->Descripcion."' data-orden='".$Modulo->Orden."' data-icono='".$Modulo->Icono."'>
                                         <i class='fa fa-edit'></i>
                                     </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$Modulo->Codigo."'>
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
