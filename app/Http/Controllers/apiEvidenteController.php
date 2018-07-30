<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class apiEvidenteController extends Controller
{
    
    /*****************************************
     * Funciones para el proceso de EVIDENTE *
     *****************************************/

     function consumoVerificar(Request $request){
         
       
        if(isset($request->cedula) && !empty($request->cedula) && 
           isset($request->fechaExpedicion) && !empty($request->fechaExpedicion) &&
           in_array($request->funcion, ["VALIDAR", "PREGUNTAS", "VERIFICAR"])){
            
            $fecha = $request->fechaExpedicion;
            $arrayFecha = explode("/", $fecha);
            $fechaExpedicion = mktime(-5, 0, 0, $arrayFecha[1], $arrayFecha[0], $arrayFecha[2]) * 1000;          
            
            $cedula = $request->cedula;
            $funcion = $request->funcion;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://vtmsoluciones.com/webservices/data_consultarHC2.php?evidente=true");
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, "funcion=$funcion&identificacion=$cedula&fechaExpedicion=$fechaExpedicion");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $valores = curl_exec ($ch);
            return $valores;
            //return response()->json([$request->nombre]);
        }else{
            
            return "no llego nada";
        }
     }
    
}
