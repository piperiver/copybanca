<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use DB;
use App\Parametro;

class ParametrosController extends Controller
{
    protected $forma = 'PARAM';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Parametros = DB::select("SELECT PARAMETROS.Codigo,PARAMETROS.Descripcion,PARAMETROS.Valor,PARAMETROS.Tipo,
                                         PARAMETROS.Modulo CodigoModulo,PARAMETROS.created_at,PARAMETROS.updated_at,
                                         MODULOS.Descripcion Modulo
                                    FROM PARAMETROS
                                    JOIN MODULOS ON MODULOS.Codigo = PARAMETROS.Modulo
                                   ORDER BY PARAMETROS.Codigo");
        $Utilidad = new UtilidadesClass();
        $Parametros = $Utilidad->Ordenar($Parametros);

        return view('pages.Parametros.index')->with('Parametros', $Parametros)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Codigo' => 'required|max:8|unique:Parametros',
                        'Descripcion' => 'required|max:1000',
                        'Valor' => 'required|max:100',
                        'Tipo' => 'required|max:50',
                        'Modulo' => 'required|max:4'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $Parametro = new Parametro($request->all());
        $Parametro->Codigo = strtoupper($request->Codigo);
        $Parametro->save();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                            'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Descripcion' => 'required|max:1000',
                       'Valor' => 'required|max:100',
                       'Tipo' => 'required|max:50',
                       'Modulo' => 'required|max:4'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        $Parametro = Parametro::find($request->input('Codigo'));
        $Parametro->fill($request->all());
        $Parametro->save();

        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Parametros = Parametro::find($request->input('Codigo'));
        $Parametros->delete();
        
        $tabla = $this->tabla();

        return response()->json(['Mensaje' => 'El registro se ha eliminado.',
                              'tabla' => $tabla]);
    }

    public function tabla()
    {
        $Parametros = DB::select("SELECT PARAMETROS.Codigo,PARAMETROS.Descripcion,PARAMETROS.Valor,PARAMETROS.Tipo,
                                         PARAMETROS.Modulo CodigoModulo,PARAMETROS.created_at,PARAMETROS.updated_at,
                                         MODULOS.Descripcion Modulo
                                    FROM PARAMETROS
                                    JOIN MODULOS ON MODULOS.Codigo = PARAMETROS.Modulo
                                   ORDER BY PARAMETROS.Codigo");
        $Utilidad = new UtilidadesClass();
        $Parametros = $Utilidad->Ordenar($Parametros);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Código </th>
                            <th> Descripción </th>
                            <th> Valor </th>
                            <th> Tipo </th>
                            <th> Módulo </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($Parametros as $Parametro)
                    {
                        $tabla .=
                        "<tr id='".$Parametro->Codigo."'>
                          <td>". $Parametro->Codigo ."</td>
                          <td>". $Parametro->Descripcion ."</td>
                          <td>". $Parametro->Valor ."</td>
                          <td>". $Parametro->Tipo ."</td>
                          <td>". $Parametro->Modulo ."</td>
                          <td>". $Parametro->updated_at ."</td>
                          <td>". $Parametro->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                    $tabla .= 
                                    "<td> <a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Parametro->Codigo."' data-descripcion='".$Parametro->Descripcion."' data-valor='".$Parametro->Valor."' data-tipo='".$Parametro->Tipo."'   data-modulo='".$Parametro->CodigoModulo."'>
                                        <i class='fa fa-edit'></i>
                                    </a></td>";
                            
                           }
                     $tabla .= "</tr>";
                }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    public function calcularIvaCredito(Request $request)
    {
        $Utilidad = new UtilidadesClass();
        $ValorParametro = $Utilidad->obtenerValorParametro('IVA');
        $ValorParametro = (((double)$ValorParametro) / 100);
        $Valor = (((double)$request->Valor) / 100);
        $Valor = number_format((($Valor * $ValorParametro) * 100),2);

        $Parametro = Parametro::find('IVACREDI');
        $Parametro->Valor = $Valor;
        $Parametro->save();

        $Parametros = Parametro::orderBy('Codigo', 'ASC')->get();
        $Parametros = $Utilidad->Ordenar($Parametros);
        $tabla = $this->tabla($Parametros);

        return response()->json(['tabla' => $tabla]);
    }
}
