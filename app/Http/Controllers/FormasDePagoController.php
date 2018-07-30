<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FormaDePago;
use App\Librerias\UtilidadesClass;

class FormasDePagoController extends Controller
{
    protected $forma = "FORDP";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $FormasDePago = FormaDePago::orderBy('Codigo', 'ASC')->get();
        $FormasDePago = $Utilidad->Ordenar($FormasDePago);
        return view('pages.FormasDePago.index')->with('FormasDePago', $FormasDePago)->with('forma', $this->forma); 
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:3|unique:FormasDePago',
                      'Descripcion' => 'required|max:1000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $FormaDePago = new FormaDePago($request->all());
        $FormaDePago->save();

        $Utilidad = new UtilidadesClass();
        $FormasDePago = FormaDePago::orderBy('Codigo', 'ASC')->get();
        $FormasDePago = $Utilidad->Ordenar($FormasDePago);
        $tabla = $this->tabla($FormasDePago);

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:1000'];
      
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
     
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $FormaDePago = FormaDePago::find($request->input('Codigo'));
        $FormaDePago->fill($request->all());
        $FormaDePago->save();

        $Utilidad = new UtilidadesClass();
        $FormasDePago = FormaDePago::orderBy('Codigo', 'ASC')->get();
        $FormasDePago = $Utilidad->Ordenar($FormasDePago);
        $tabla = $this->tabla($FormasDePago);
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        //$FormaPagoInGiro = Perfil::where("Estado",$request->input('Codigo'))->get()->toArray();

        /*if(isset($EstadoInPerfil[0]["Estado"])){
            $mensaje = $Utilidad->getMessage(2, "danger" , [$request->input('Codigo')], basename( __FILE__ ), __LINE__);
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }else{            
            $Estado = Estado::find($request->input('Codigo'));
            $Estado->delete();
        }*/

        $FormaDePago = FormaDePago::find($request->input('Codigo'));
        $FormaDePago->delete();

        $FormasDePago = FormaDePago::orderBy('Codigo', 'ASC')->get();
        $FormasDePago = $Utilidad->Ordenar($FormasDePago);
        $tabla = $this->tabla($FormasDePago);

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla]);
    }

  public function listarFormasDePago()
  {
      $FormasDePago = FormaDePago::orderBy('Codigo', 'ASC')->get();

      return $FormasDePago;
  }

    public function tabla($FormasDePago)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                          <th> Código </th>
                          <th> Descripción </th>
                          <th> Ultima Actualización </th>
                          <th> Fecha Creación </th>";
                          if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                          {
                            $tabla .= "<th> Acción </th>";
                          }
                      $tabla .= "</tr>
                    </thead>
                    <tbody>";
                foreach($FormasDePago as $FormaDePago)
                {
                    $tabla .=
                    "<tr id='".$FormaDePago->Codigo."'>
                        <td>". $FormaDePago->Codigo ."</td>
                        <td>". $FormaDePago->Descripcion ."</td>
                        <td>". $FormaDePago->updated_at ."</td>
                        <td>". $FormaDePago->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$FormaDePago->Codigo."' data-descripcion='".$FormaDePago->Descripcion."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$FormaDePago->Codigo."'>
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