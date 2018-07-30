<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use App\EntidadDesembolso;

class EntidadesDesembolsoController extends Controller
{
    protected $forma = "ENDES";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $EntidadesDesembolso = EntidadDesembolso::orderBy('Nit', 'ASC')->get();
        $EntidadesDesembolso = $Utilidad->Ordenar($EntidadesDesembolso);
        return view('pages.EntidadesDesembolso.index')->with('EntidadesDesembolso', $EntidadesDesembolso)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Nit' => 'required|max:50|unique:EntidadesDesembolso',
                      'Descripcion' => 'required|max:1000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Nit esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $EntidadesDesembolso = new EntidadDesembolso($request->all());
        $EntidadesDesembolso->save();

        $Utilidad = new UtilidadesClass();
        $EntidadesDesembolso = EntidadDesembolso::orderBy('Nit', 'ASC')->get();
        $EntidadesDesembolso = $Utilidad->Ordenar($EntidadesDesembolso);
        $tabla = $this->tabla($EntidadesDesembolso);

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
        
        $EntidadDesembolso = EntidadDesembolso::find($request->input('Nit'));
        $EntidadDesembolso->fill($request->all());
        $EntidadDesembolso->save();

        $Utilidad = new UtilidadesClass();
        $EntidadesDesembolso = EntidadDesembolso::orderBy('Nit', 'ASC')->get();
        $EntidadesDesembolso = $Utilidad->Ordenar($EntidadesDesembolso);
        $tabla = $this->tabla($EntidadesDesembolso);
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $EntidadDesembolso = EntidadDesembolso::find($request->input('Nit'));
        $EntidadDesembolso->delete();

        $Utilidad = new UtilidadesClass();
        $EntidadesDesembolso = EntidadDesembolso::orderBy('Nit', 'ASC')->get();
        $EntidadesDesembolso = $Utilidad->Ordenar($EntidadesDesembolso);
        $tabla = $this->tabla($EntidadesDesembolso);

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla]);
    }

  public function listarDesembolsos()
  {
      $EntidadesDesembolso = EntidadDesembolso::orderBy('Nit', 'ASC')->get();

      return $EntidadesDesembolso;
  }

    public function tabla($EntidadesDesembolso)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                          <th> Nit </th>
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
                foreach($EntidadesDesembolso as $EntidadDesembolso)
                {
                    $tabla .=
                    "<tr id='".$EntidadDesembolso->Nit."'>
                        <td>". $EntidadDesembolso->Nit ."</td>
                        <td>". $EntidadDesembolso->Descripcion ."</td>
                        <td>". $EntidadDesembolso->updated_at ."</td>
                        <td>". $EntidadDesembolso->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-nit='".$EntidadDesembolso->Nit."' data-descripcion='".$EntidadDesembolso->Descripcion."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-nit='".$EntidadDesembolso->Nit."'>
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
