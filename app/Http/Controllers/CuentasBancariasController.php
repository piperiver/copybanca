<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CuentaBancaria;
use App\Librerias\UtilidadesClass;
use DB;

class CuentasBancariasController extends Controller
{

    protected $forma = 'CUBAN';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $CuentasBancarias = CuentaBancaria::orderBy('Banco', 'ASC')->get();

        return view('pages.CuentasBancarias.index')->with('CuentasBancarias', $CuentasBancarias)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Banco' => 'required|max:4',
                        'EntidadDesembolso' => 'required|max:50',
                        'TipoCuenta' => 'required|max:3',
                        'Cuenta' => 'required|max:200'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $CuentaBancaria = CuentaBancaria::where('Banco', '=', $request->Banco)
                                        ->where('EntidadDesembolso', '=', $request->EntidadDesembolso)
                                        ->where('TipoCuenta', '=', $request->TipoCuenta)->first();
        
        if(!empty($CuentaBancaria))
        {
            $mensaje = ['Combinación: '.$request->Banco.'-'.$request->EntidadDesembolso.'-'.$request->TipoCuenta.' Ya Existe.'];
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }

        $CuentaBancaria = new CuentaBancaria($request->all());
        $CuentaBancaria->save();

        $CuentasBancarias = CuentaBancaria::orderBy('Banco', 'ASC')->get();
        $tabla = $this->tabla($CuentasBancarias);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Cuenta' => 'required|max:200'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $CuentaBancaria = CuentaBancaria::where('Banco', '=', $request->Banco)
                                        ->where('EntidadDesembolso', '=', $request->EntidadDesembolso)
                                        ->where('TipoCuenta', '=', $request->TipoCuenta)
        ->update(['Cuenta' => $request->Cuenta]);
        
        $CuentasBancarias = CuentaBancaria::orderBy('Banco', 'ASC')->get();
        $tabla = $this->tabla($CuentasBancarias);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $CuentaBancaria = CuentaBancaria::where('Banco', '=', $request->Banco)
                                            ->where('EntidadDesembolso', '=', $request->EntidadDesembolso)
                                            ->where('TipoCuenta', '=', $request->TipoCuenta)
            ->delete();

        $CuentasBancarias = CuentaBancaria::orderBy('Banco', 'ASC')->get();
        $tabla = $this->tabla($CuentasBancarias);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                            'tabla' => $tabla]);
    }

    public function listarCuentas(Request $request)
    {
        /*
        ** a partir de la entidad seleccionada desde la vista consulta las cuentas 
           asociadas a esa entidad para luego llenar un select HTML
        */
        $Cuentas = DB::select("SELECT Cuenta, Descripcion
                                FROM CuentasBancarias,EntidadesBancarias
                               WHERE Banco = Codigo
                                 AND EntidadDesembolso = :entidad;", ['entidad' => $request->EntidadDesembolso]);

        $opciones = "";
        foreach($Cuentas as $Cuenta)
        {
            $opciones .= "<option value='".$Cuenta->Cuenta."'>".$Cuenta->Cuenta." / ".$Cuenta->Descripcion."</option>";
        }
        
        return response()->json(['Cuentas' => $opciones]);
    }

    public function tabla($CuentasBancarias)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Entidad Bancaria </th>
                            <th> Entidad de Desembolso </th>
                            <th> Tipo de Cuenta </th>
                            <th> Cuenta </th>
                            <th> Ultima Actualización </th>
                            <th> Fecha Creación </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<th> Acción </th>";
                            }
           $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    foreach($CuentasBancarias as $CuentaBancaria)
                    {
                        $tabla .=
                        "<tr id='".$CuentaBancaria->Banco."-".$CuentaBancaria->EntidadDesembolso."-".$CuentaBancaria->TipoCuenta."'>
                        <td>". $CuentaBancaria->Banco ."</td>
                        <td>". $CuentaBancaria->EntidadDesembolso ."</td>
                        <td>". $CuentaBancaria->TipoCuenta ."</td>
                        <td>". $CuentaBancaria->Cuenta ."</td>
                        <td>". $CuentaBancaria->updated_at ."</td>
                        <td>". $CuentaBancaria->created_at ."</td>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar") || UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .= "<td>";
                                if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                {
                                    $tabla .=
                                    "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' 
                                        data-banco='".$CuentaBancaria->Banco."' data-entidaddesembolso='".$CuentaBancaria->EntidadDesembolso."' data-tipocuenta='".$CuentaBancaria->TipoCuenta."' data-cuenta='".$CuentaBancaria->Cuenta."'>
                                        <i class='fa fa-edit'></i>
                                     </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-banco='".$CuentaBancaria->Banco."' data-entidaddesembolso='".$CuentaBancaria->EntidadDesembolso."' data-tipocuenta='".$CuentaBancaria->TipoCuenta."'>
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