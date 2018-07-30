<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Librerias\UtilidadesClass;
use App\TipoAdjunto;
use App\Adjunto;

class TiposAdjuntoController extends Controller
{
    protected $forma = "TIADJ";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $TiposAdjunto = TipoAdjunto::orderBy('Codigo', 'ASC')->get();
        $TiposAdjunto = $Utilidad->Ordenar($TiposAdjunto);
        return view('pages.TiposAdjunto.index')->with('TiposAdjunto', $TiposAdjunto)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:3|unique:TiposAdjunto',
                        'Descripcion' => 'required|max:255'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a :max Caracteres',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $TipoAdjunto = new TipoAdjunto($request->all());
        $TipoAdjunto->Codigo = strtoupper($request->Codigo);
        $TipoAdjunto->save();

        $tabla = $this->tabla();

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
        
        $TipoAdjunto = TipoAdjunto::find($request->input('Codigo'));
        $TipoAdjunto->fill($request->all());
        $TipoAdjunto->save();
        
        $tabla = $this->tabla();
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        
        $adjuntos = Adjunto::where("tipoAdjunto",$request->input('Codigo'))->get();
                
        if(count($adjuntos) == 0){
            $TipoAdjunto = TipoAdjunto::find($request->input('Codigo'));
            $TipoAdjunto->delete();

            $mensaje = "El registro se ha Eliminado.";
        }else{
            $mensaje = "No es posible eliminar el tipo de adjunto, ya que existen archivos cargados que estan relacionados a este tipo de adjunto.";
        }
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => $mensaje,
                                 'tabla' => $tabla]);    
        
    }

  public function listarTiposAdjunto()
  {
      $TiposAdjunto = TipoAdjunto::orderBy('Codigo', 'ASC')->get();

      return $TiposAdjunto;
  }

    public function tabla()
    {
        $Utilidad = new UtilidadesClass();
        $TiposAdjunto = TipoAdjunto::orderBy('Codigo', 'ASC')->get();
        $TiposAdjunto = $Utilidad->Ordenar($TiposAdjunto);
        
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
                foreach($TiposAdjunto as $TipoAdjunto)
                {
                    $tabla .=
                    "<tr id='".$TipoAdjunto->Codigo."'>
                        <td>". $TipoAdjunto->Codigo ."</td>
                        <td>". $TipoAdjunto->Descripcion ."</td>
                        <td>". $TipoAdjunto->updated_at ."</td>
                        <td>". $TipoAdjunto->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$TipoAdjunto->Codigo."' data-descripcion='".$TipoAdjunto->Descripcion."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$TipoAdjunto->Codigo."'>
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