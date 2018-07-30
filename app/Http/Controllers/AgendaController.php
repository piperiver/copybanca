<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use DB;
use PDF;
use App\Agenda;
use App\Estudio;
use App\Valoracion;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{   
    public function detalleAgenda(){
        $Agendas = Agenda::where('usuario', '=', Auth::user()->id)->get();
        
        if(count($Agendas) == 0){
            $json = [];
        }else{
            foreach($Agendas as $Agenda){
                $json[] = array("id"=>$Agenda->id,"start"=>$Agenda->inicio,"end"=>$Agenda->fin,"title"=>$Agenda->titulo,"lugar"=>$Agenda->lugar,"descripcion"=>$Agenda->descripcion);

            }
        }    
        $jsonResponse = json_encode($json);
        return view('pages.Agenda.index')->with('usuario',$jsonResponse);
    }
        
    public function create(Request $request){
        date_default_timezone_set('UTC');
        $condiciones = ['titulo' => 'required',
                        'inicio' => 'required',
                        'fin' => 'required',
                        'lugar' => 'required',
                        'descripcion' => 'required'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }
        
        $Agenda = Agenda::where('inicio', '=', $request->inicio)
                                        ->where('fin', '=', $request->fin)
                                        ->where('usuario', '=', $request->usuario)->first();
        
        if(!empty($Agenda))
        {
            $mensaje = ['Ya tienes una cita programada para este espacion: '.$request->Inicio.'-'.$request->fin.' Por favor ingresa una diferente.'];
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }
         
        $Agenda = new Agenda;
        $Agenda->titulo = $request->titulo;
        $Agenda->inicio = date('Y-m-d H:i:s',strtotime($request->inicio));
        $Agenda->fin = date('Y-m-d H:i:s',strtotime($request->fin));
        $Agenda->lugar = $request->lugar;
        $Agenda->descripcion = $request->descripcion;
        $Agenda->usuario = $request->usuario;
        
        $Agenda->save();
        
        return response()->json(['Mensaje' => 'Cita creada correctamente']);        
    }
    
    public function destroy(Request $request){
        
        $Agenda = new Agenda;
        
        $Agenda::destroy($request->eventID);
        
        return response()->json(['Mensaje' => 'Cita cancelada correctamente']);
    }
    
    public function update(Request $request){
        
                                
        $Agenda = Agenda::where('usuario', '=', $request->usuario)
                                ->where('fin', '=', $request->fin)
                                ->where('inicio', '=', $request->inicio)->first();
        
        if(!empty($Agenda))
        {
            $mensaje = ['Este usuario ya tiene una cita programada para este espacion: '.$request->Inicio.'-'.$request->fin.' Por favor ingresa una diferente.'];
            return response()->json(['errores' => true, 'Mensaje' => $mensaje]);
        }
         
        $Agenda = Agenda::find($request->id);

        $Agenda->titulo = $request->titulo;
        $Agenda->inicio = date('Y-m-d H:i:s',strtotime($request->inicio));
        $Agenda->fin = date('Y-m-d H:i:s',strtotime($request->fin));

        $Agenda->save();
        
        return response()->json(['Mensaje' => 'Cita creada correctamente']);        
        
    }
    
    public function all(){
        $Agendas = DB::table('agenda')
                ->join('users','users.id','=','agenda.usuario')
                ->select(DB::raw("CONCAT(users.nombre,' ',users.apellido) AS nombre"),'agenda.*')
                ->get();
        if(count($Agendas) == 0){
            $json = [];
        }else{
            foreach($Agendas as $Agenda){
                $json[] = array("id"=>$Agenda->id,"start"=>$Agenda->inicio,"end"=>$Agenda->fin,"title"=>$Agenda->titulo,"lugar"=>$Agenda->lugar,"descripcion"=>$Agenda->descripcion,"nombre"=>$Agenda->nombre);

            }
        }    
        $jsonResponse = json_encode($json);
        return view('pages.Agenda.comercial')->with('datos',$jsonResponse);
    }
    
    public function allUser(Request $request){
        
        if($request->user == ''){
            $Agendas = DB::table('agenda')
                ->join('users','users.id','=','agenda.usuario')
                ->select(DB::raw("CONCAT(users.nombre,' ',users.apellido) AS nombre"),'agenda.*')
                ->get();
            if(count($Agendas) == 0){
                $json = [];
            }else{
                foreach($Agendas as $Agenda){
                    $json[] = array("id"=>$Agenda->id,"start"=>$Agenda->inicio,"end"=>$Agenda->fin,"title"=>$Agenda->titulo,"lugar"=>$Agenda->lugar,"descripcion"=>$Agenda->descripcion,"nombre"=>$Agenda->nombre);

                }
            }
        }else{
            $Agendas = Agenda::where('usuario', '=', $request->user)->get();
        
            if(count($Agendas) == 0){
                $json = [];
            }else{
                foreach($Agendas as $Agenda){
                $json[] = array("id"=>$Agenda->id,"start"=>$Agenda->inicio,"end"=>$Agenda->fin,"title"=>$Agenda->titulo,"lugar"=>$Agenda->lugar,"descripcion"=>$Agenda->descripcion);
                }
            }
        }
            
        $jsonResponse = json_encode($json);
        return $jsonResponse;
    }
     
}
