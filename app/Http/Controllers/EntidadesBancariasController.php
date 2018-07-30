<?php

namespace App\Http\Controllers;

use App\Pagaduria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use App\EntidadBancaria;
use App\Obligacion;
use App\EntidadesObligaciones;

class EntidadesBancariasController extends Controller
{
    protected $forma = "ENBAN";
  
    public function index()
    {
        $pagadurias = Pagaduria::all();
        $nombramientos = ['PROP'=>'PROPIEDAD','PRUE'=>'P. PRUEBA','DEF'=>'P. V. DEF','FIJO'=>'T. FIJO', 'INDEF'=>'T. INDEFIN', 'OTHER'=>'OTRO', 'PENS'=> 'PENSIONADO'];
        $cargos = ['ADM'=>'ADMINISTRATIVO','DOC'=>'DOCENTE','PEN'=>'PENSIONADO'];
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Utilidad = new UtilidadesClass();
        $EntidadesBancarias = EntidadBancaria::orderBy('Id', 'ASC')->get();
        $Entidades = Obligacion::select('Entidad')->groupBy('Entidad')->get();
        $EntidadesBancarias = $Utilidad->Ordenar($EntidadesBancarias);
        return view('pages.EntidadesBancarias.index')->with('Bancos', $EntidadesBancarias)->with('Entidades', $Entidades)->with('forma', $this->forma)->with('pagadurias', $pagadurias)->with('nombramientos', $nombramientos)->with('cargos', $cargos);
    }

    public function create(Request $request)
    {
       /* $condiciones = ['Codigo' => 'required|max:4|unique:EntidadesBancarias',
                      'Descripcion' => 'required|max:1000'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max',
                     'unique' => 'El Código esta repetido.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        */        
        $Banco = new EntidadBancaria($request->all());
        $Banco->save();

        $Utilidad = new UtilidadesClass();
        $Bancos = EntidadBancaria::orderBy('Id', 'ASC')->get();
        $Bancos = $Utilidad->Ordenar($Bancos);
        $tabla = $this->tabla($Bancos);

        return response()->json(['Mensaje' => 'El registro se ha Guardar.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        /*$condiciones = ['Descripcion' => 'required|max:1000'];
      
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
     
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }*/
        
        $Banco = EntidadBancaria::find($request->input('Id'));
        $Banco->fill($request->all());
        $Banco->save();

        $Utilidad = new UtilidadesClass();
        $Bancos = EntidadBancaria::orderBy('Id', 'ASC')->get();
        $Bancos = $Utilidad->Ordenar($Bancos);
        $tabla = $this->tabla($Bancos);
        
        return response()->json(['Mensaje' => 'El registro se ha Actualizado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Banco = EntidadBancaria::find($request->input('Id'));
        $Banco->delete();

        $Utilidad = new UtilidadesClass();
        $Bancos = EntidadBancaria::orderBy('Id', 'ASC')->get();
        $Bancos = $Utilidad->Ordenar($Bancos);
        $tabla = $this->tabla($Bancos);

        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
                                 'tabla' => $tabla]);
    }

  public function listarBancos()
  {
      $Bancos = EntidadBancaria::orderBy('Id', 'ASC')->get();

      return $Bancos;
  }

    public function tabla($Bancos)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                          <th> Id </th>
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
                foreach($Bancos as $Banco)
                {
                    $tabla .=
                    "<tr id='".$Banco->Id."'>
                        <td>". $Banco->Id ."</td>
                        <td>". $Banco->Descripcion ."</td>
                        <td>". $Banco->updated_at ."</td>
                        <td>". $Banco->created_at ."</td>";
                        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                        {
                            $tabla .= "<td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' data-codigo='".$Banco->Id."' data-descripcion='".$Banco->Descripcion."' data-tasa='".$Banco->Tasa."' data-castigo='".$Banco->CastigoMora."' data-paz='".$Banco->PazSalvo."' data-politicas='".$Banco->Politica."' data-dcto='".$Banco->DtoInicial."' data-pdata='".$Banco->PuntajeData."' data-pcifin='".$Banco->PuntajeCifin."' data-entidades='".$Banco->Entidades."'>
                                                <i class='fa fa-edit'></i>
                                           </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-codigo='".$Banco->Id."'>
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
