<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubEstado;
use App\Librerias\UtilidadesClass;

class SubEstadosController extends Controller
{
    protected $forma = 'SUBES';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $SubEstados = SubEstado::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $SubEstados = $Utilidad->Ordenar($SubEstados);
        
        return view('pages.SubEstados.index')->with('SubEstados', $SubEstados)->with('forma', $this->forma);
    }

  public function create(Request $request)
  {
      $condiciones = ['Codigo' => 'required|max:3|unique:SubEstados',
                      'Descripcion' => 'required|max:1000',
                      'Decision' => 'required|max:1'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $SubEstado = new SubEstado($request->all());
        $SubEstado->save();

        $SubEstados = SubEstado::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $SubEstados = $Utilidad->Ordenar($SubEstados);
        $tabla = $this->tabla($SubEstados);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
  }

  public function update(Request $request)
  {
      $condiciones = ['Descripcion' => 'required|max:1000',
                      'Decision' => 'required|max:1'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $SubEstado = SubEstado::find($request->input('Codigo'));
        $SubEstado->fill($request->all());
        $SubEstado->save();

        $SubEstados = SubEstado::orderBy('Codigo', 'ASC')->get();
        $Utilidad = new UtilidadesClass();
        $SubEstados = $Utilidad->Ordenar($SubEstados);
        $tabla = $this->tabla($SubEstados);

        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
  }

  public function destroy(Request $request)
  {
      $SubEstados = SubEstado::find($request->input('Codigo'));
      $SubEstados->delete();

      $SubEstados = SubEstado::orderBy('Codigo', 'ASC')->get();
      $Utilidad = new UtilidadesClass();
      $SubEstados = $Utilidad->Ordenar($SubEstados);
      $tabla = $this->tabla($SubEstados);

      return response()->json(['Mensaje' => 'El registro se ha eliminado.',
                              'tabla' => $tabla]);
  }
    public function tabla($SubEstados)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Código </th>
                            <th> Descripción </th>
                            <th> Decisión </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($SubEstados as $SubEstado)
                    {
                        $tabla .=
                         "<tr id='".$SubEstado->Codigo."'>
                          <td>". $SubEstado->Codigo ."</td>
                          <td>". $SubEstado->Descripcion ."</td>
                          <td>". $SubEstado->Decision ."</td>
                          <td>". $SubEstado->updated_at ."</td>
                          <td>". $SubEstado->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$SubEstado->Codigo."' data-descripcion='".$SubEstado->Descripcion."' data-decision='".$SubEstado->Decision."'>
                                  <i class='fa fa-edit'></i>
                              </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$SubEstado->Codigo."'>
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
