<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Estado;
use App\Perfil;
use App\Forma;
use App\User;
use App\Parametro;
use App\Librerias\UtilidadesClass;
use DB;

class EstadosController extends Controller
{
    protected $forma = "ESTAD";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $Estados = DB::select("SELECT ESTADOS.Codigo,ESTADOS.Descripcion,ESTADOS.idPadre,
                                      FORMAS.Codigo CodigoForma,IFNULL(FORMAS.Descripcion,'TODAS') Forma,
                                      ESTADOS.created_at,ESTADOS.updated_at
                                 FROM ESTADOS
                                 LEFT JOIN FORMAS 
                                   ON ESTADOS.Forma = FORMAS.Codigo
                                 ORDER BY ESTADOS.Codigo");
        $Estados = $Utilidad->Ordenar($Estados);
        $Formas = Forma::all();
        
        return view('pages.Estados.index')->with('Estados', $Estados)
                                          ->with('forma', $this->forma)
                                          ->with('Formas', $Formas);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:3|unique:Estados',
                        'Descripcion' => 'required|max:1000',
                        'Forma' => 'required|max:5'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Estado = new Estado($request->all());
        $Estado->Codigo = strtolower($request->Codigo);
        $Estado->save();
        
        $Estados = DB::select("SELECT ESTADOS.Codigo,ESTADOS.Descripcion,ESTADOS.idPadre,
                                      FORMAS.Codigo CodigoForma,IFNULL(FORMAS.Descripcion,'TODAS') Forma,
                                      ESTADOS.created_at,ESTADOS.updated_at
                                 FROM ESTADOS
                                 LEFT JOIN FORMAS 
                                   ON ESTADOS.Forma = FORMAS.Codigo
                                 ORDER BY ESTADOS.Codigo");
        $opciones = $this->listarOpciones($Estados);
        $tabla = $this->tabla($Estados);

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                 'tabla' => $tabla,
                                 'opciones' => $opciones]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:1000',
                        'Forma' => 'required|max:5'];
      
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
     
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $Estado = Estado::find($request->input('Codigo'));
        $Estado->fill($request->all());
        $Estado->save();

        $Estados = DB::select("SELECT ESTADOS.Codigo,ESTADOS.Descripcion,ESTADOS.idPadre,
                                      FORMAS.Codigo CodigoForma,IFNULL(FORMAS.Descripcion,'TODAS') Forma,
                                      ESTADOS.created_at,ESTADOS.updated_at
                                 FROM ESTADOS
                                 LEFT JOIN FORMAS 
                                   ON ESTADOS.Forma = FORMAS.Codigo
                                 ORDER BY ESTADOS.Codigo");
        $opciones = $this->listarOpciones($Estados);
        $tabla = $this->tabla($Estados);
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla,
                                 'opciones' => $opciones]);
    }

    public function destroy(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        $EstadoInPerfil = Perfil::where("Estado",$request->input('Codigo'))->get()->toArray();
        $Padre = Estado::where("idPadre",$request->Codigo)->first();
        $Usuario = User::where("estado",$request->Codigo)->first();
        $Parametro = Parametro::where("Valor",$request->Codigo)->first();
        if(isset($EstadoInPerfil[0]["Estado"]) || !is_null($Padre) || !is_null($Usuario) || !is_null($Parametro)){
            $mensaje = $Utilidad->getMessage(2, "danger" , [$request->input('Codigo')], basename( __FILE__ ), __LINE__);
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }else{
            $Estado = Estado::find($request->input('Codigo'));
            $Estado->delete();
        }
        
        $Estados = DB::select("SELECT ESTADOS.Codigo,ESTADOS.Descripcion,ESTADOS.idPadre,
                                      FORMAS.Codigo CodigoForma,IFNULL(FORMAS.Descripcion,'TODAS') Forma,
                                      ESTADOS.created_at,ESTADOS.updated_at
                                 FROM ESTADOS
                                 LEFT JOIN FORMAS 
                                   ON ESTADOS.Forma = FORMAS.Codigo
                                 ORDER BY ESTADOS.Codigo");
        $opciones = $this->listarOpciones($Estados);
        $tabla = $this->tabla($Estados);

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla,
                                 'opciones' => $opciones]);
    }

  public function listarEstados()
  {
      $Estados = Estado::orderBy('Codigo', 'ASC')->get();

      return $Estados->toArray();
  }

    public function tabla($Estados)
    {
        $Utilidad = new UtilidadesClass();
        $Estados = $Utilidad->Ordenar($Estados);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                          <th> Código </th>
                          <th> Descripción </th>
                          <th> Estado Padre </th>
                          <th> Forma </th>
                          <th> Ultima Actualización </th>
                          <th> Fecha Creación </th>";
                          if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                          {
                            $tabla .= "<th> Acción </th>";
                          }
                      $tabla .= "</tr>
                    </thead>
                    <tbody>";
                foreach($Estados as $Estado)
                {
                    $tabla .=
                    "<tr id='".$Estado->Codigo."'>
                        <td>". $Estado->Codigo ."</td>
                        <td>". $Estado->Descripcion ."</td>
                        <td>". $Estado->idPadre ."</td>
                        <td>". $Estado->Forma ."</td>
                        <td>". $Estado->updated_at ."</td>
                        <td>". $Estado->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Estado->Codigo."' data-descripcion='".$Estado->Descripcion."' data-idpadre='".$Estado->idPadre."' data-forma='".$Estado->CodigoForma."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$Estado->Codigo."'>
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

    public function listarOpciones($Estados)
    {
        $opciones = "<option value=''></option>";
        foreach ($Estados as $Estado)
        {
            $opciones .= "<option value='".$Estado->Codigo."'>".$Estado->Descripcion."</option>";
        }

        return $opciones;
    }
}