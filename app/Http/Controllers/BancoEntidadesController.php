<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BancoEntidades;
use App\User;
use Illuminate\Support\Facades\Auth;

class BancoEntidadesController extends Controller
{
    public function index()
    { 
        return view('layouts-client.datos.index');
    }
    function getEntidades(Request $request){        
//        $BancoEntidades = BancoEntidades::where('nombre','LIKE', "%{$request->nombre}%")->orderBy("nombre", "ASC")->limit(10)->get();            
        $BancoEntidades = BancoEntidades::orderBy("nombre", "ASC")->get();        
        $arrayJson = "";
        foreach($BancoEntidades as $entidad){
            $arrayJson[] = $entidad->nombre;            
        }
        
        echo json_encode($arrayJson);
    }
    function updateDatosUsuario(Request $request){       
        
        
        
            $fechaExpedicion = str_replace("/", "-", $request->fecha);
            
            $respuesta = $this->EvidenteValidar($request->cedula, strtoupper($request->pApellido), strtoupper($request->nombre), strtotime($fechaExpedicion)* 1000);
            if($respuesta->STATUS){
                    $entidad = BancoEntidades::where("nombre", strtoupper($request->pagaduria))->first();        
                    if(!isset($entidad->id)){
                        $bancoEntidad = new BancoEntidades;
                        $bancoEntidad->nombre = strtoupper($request->pagaduria);
                        $bancoEntidad->save();
                    }           

                    $user = User::find(Auth::user()->id);
                    $user->nombre = $request->nombre;
                    $user->cedula = $request->cedula;
                    $user->apellido = $request->pApellido;
                    $user->fecha_expedicion = strtotime($fechaExpedicion);
                    $user->pagaduria = strtoupper($request->pagaduria);
                    $resInsert = $user->save();                
                    
                    if($request){
                        session(['RVal' => encrypt($respuesta->regValidacion), "cc" => encrypt($request->cedula)]);                        
                        echo json_encode(["STATUS" => $respuesta->STATUS, "mensaje" => $respuesta->mensaje]);
                    }else{
                        echo json_encode(["STATUS" => false, "mensaje" => "Ocurrio un problema al tratar de actualizar la información del usuario, por favor vuelva a intentarlo"]);
                    }
            }else{
                echo json_encode($respuesta);
            }
        
    }
     public function EvidenteValidar($identificacion, $primerApellido, $Nombre, $fechaExpedicion){        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,config('constantes.URL_EVIDENTE'));
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "funcion=VAL&identificacion=$identificacion&primerApellido=$primerApellido&nombre=$Nombre&fechaExpedicion=$fechaExpedicion");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $valores = curl_exec ($ch);
            
        $Respuesta = json_decode($valores);
        
        return $Respuesta;
    }
    function preguntas(Request $request){        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,config('constantes.URL_EVIDENTE'));
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "funcion=PRE&identificacion=".session("cc")."&regValidacion=".session("RVal"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $valores = curl_exec ($ch);
            
        $Respuesta = json_decode($valores);
        
    }
    
}
