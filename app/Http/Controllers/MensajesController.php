<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mensaje;
use App\Librerias\UtilidadesClass;

class MensajesController extends Controller
{
  protected $forma = 'MENSA';
  
  public function index()
  {
      if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
       }
      $Mensaje = Mensaje::orderBy('id', 'ASC')->get();

      return view('pages.Mensajes.index')->with('Mensajes', $Mensaje)->with('forma', $this->forma);
  }

  public function create(Request $request)
  {
      $condiciones = ['id' => 'required|numeric|unique:Mensajes',
                      'Mensaje' => 'required|max:4000',
                      'Causa' => 'max:4000',
                      'Solucion' => 'max:4000'];
        
      $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
      $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

      if ($validacion->fails())
      {
          return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
      }
      
      $Mensaje = new Mensaje();
      $Mensaje->id = $request->id;
      $Mensaje->Mensaje = $request->Mensaje;
      $Mensaje->Causa = $request->Causa;
      $Mensaje->Solucion = $request->Solucion;
      $Mensaje->save();

      $Mensajes = Mensaje::orderBy('id', 'ASC')->get();
      $tabla = $this->tabla($Mensajes);

      return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                              'tabla' => $tabla]);
  }

  public function update(Request $request)
  {
      $condiciones = ['Mensaje' => 'required|max:4000',
                      'Causa' => 'max:4000',
                      'Solucion' => 'max:4000'];
        
      $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
      $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

      if ($validacion->fails())
      {
          return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
      }

      $Mensaje = Mensaje::find($request->input('id'));
      $Mensaje->fill($request->all());
      $Mensaje->save();

      $Mensajes = Mensaje::orderBy('id', 'ASC')->get();
      $tabla = $this->tabla($Mensajes);

      return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                              'tabla' => $tabla]);
  }

  public function destroy(Request $request)
  {
      $Mensaje = Mensaje::find($request->input('id'));
      $Mensaje->delete();

      $Mensajes = Mensaje::orderBy('id', 'ASC')->get();
      $tabla = $this->tabla($Mensajes);

      return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                              'tabla' => $tabla]);
  }
    public function tabla($Mensajes)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> ID </th>
                            <th> Mensaje </th>
                            <th> Causa </th>
                            <th> Solución </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Mensajes as $Mensaje)
                    {
                        $tabla .=
                        "<tr id='".$Mensaje->id."'>
                          <td>". $Mensaje->id ."</td>
                          <td>". $Mensaje->Mensaje ."</td>
                          <td>". $Mensaje->Causa ."</td>
                          <td>". $Mensaje->Solucion ."</td>
                          <td>". $Mensaje->updated_at ."</td>
                          <td>". $Mensaje->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-id='".$Mensaje->id."' data-mensaje='".$Mensaje->Mensaje."' data-causa='".$Mensaje->Causa."' data-solucion='".$Mensaje->Solucion."'>
                                        <i class='fa fa-edit'></i>
                                    </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='".$Mensaje->id."'>
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
