<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use App\TipoCuenta;

class TiposCuentaController extends Controller
{
    protected $forma = "TICUE";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $TiposCuenta = TipoCuenta::orderBy('Codigo', 'ASC')->get();
        $TiposCuenta = $Utilidad->Ordenar($TiposCuenta);
        return view('pages.TiposCuenta.index')->with('TiposCuenta', $TiposCuenta)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:3|unique:TiposCuenta',
                      'Descripcion' => 'required|max:1000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a :max Caracteres',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Banco = new TipoCuenta($request->all());
        $Banco->save();

        $Utilidad = new UtilidadesClass();
        $TiposCuenta = TipoCuenta::orderBy('Codigo', 'ASC')->get();
        $TiposCuenta = $Utilidad->Ordenar($TiposCuenta);
        $tabla = $this->tabla($TiposCuenta);

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
        
        $TipoCuenta = TipoCuenta::find($request->input('Codigo'));
        $TipoCuenta->fill($request->all());
        $TipoCuenta->save();

        $Utilidad = new UtilidadesClass();
        $TiposCuenta = TipoCuenta::orderBy('Codigo', 'ASC')->get();
        $TiposCuenta = $Utilidad->Ordenar($TiposCuenta);
        $tabla = $this->tabla($TiposCuenta);
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $TipoCuenta = TipoCuenta::find($request->input('Codigo'));
        $TipoCuenta->delete();

        $Utilidad = new UtilidadesClass();
        $TiposCuenta = TipoCuenta::orderBy('Codigo', 'ASC')->get();
        $TiposCuenta = $Utilidad->Ordenar($TiposCuenta);
        $tabla = $this->tabla($TiposCuenta);

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla]);
    }

  public function listarTiposCuenta()
  {
      $TiposCuenta = TipoCuenta::orderBy('Codigo', 'ASC')->get();

      return $TiposCuenta;
  }

    public function tabla($TiposCuenta)
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
                foreach($TiposCuenta as $TipoCuenta)
                {
                    $tabla .=
                    "<tr id='".$TipoCuenta->Codigo."'>
                        <td>". $TipoCuenta->Codigo ."</td>
                        <td>". $TipoCuenta->Descripcion ."</td>
                        <td>". $TipoCuenta->updated_at ."</td>
                        <td>". $TipoCuenta->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$TipoCuenta->Codigo."' data-descripcion='".$TipoCuenta->Descripcion."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$TipoCuenta->Codigo."'>
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
