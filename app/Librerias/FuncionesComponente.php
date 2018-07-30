<?php

namespace App\Librerias;

use DB;
use App\Adjunto;
use App\Obligacion;
use App\Estudio;
use App\TipoAdjunto;
use App\gestionObligaciones;
use App\log_gestionObligaciones;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Auth;
use App\Tarea;

class FuncionesComponente{
    
    static $estadosGestionObligacion = [
                                                                        "SOL" => "SOLICITADA",
                                                                        "RAD" => "RADICADA",
                                                                        "VEN" => "VENCIDA",
                                                                        "CAN" => "CANCELADA",
                                                                        "PAG" => "PAGADA"
                                                                        ];
    
    function prueba(){
        return "EPAA";
    }
    
    function traerTablaAdjuntos($request = false, $idPadre = false, $modulo = false, $tabla = false, $tipoAdj = false){
        $idPadreO = 0;        
        if($request == false){            
            $idPadreO = $idPadre;
            if($tipoAdj == false){                
                $adjuntos = DB::table('gestionobligaciones')->where("gestionobligaciones.id_obligacion", $idPadre)->whereIn("gestionobligaciones.estado", [config("constantes.GO_SOLICITADA"), config("constantes.GO_RADICADA"), config("constantes.GO_VENCIDA"), config("constantes.GO_PAGADA")])->orderBy("gestionobligaciones.created_at")->get();
            }else{                
                $adjuntos = DB::table('gestionobligaciones')->where("gestionobligaciones.id_obligacion", $idPadre)->where("gestionobligaciones.tipoAdjunto", $tipoAdj)->whereIn("gestionobligaciones.estado", [config("constantes.GO_SOLICITADA"), config("constantes.GO_RADICADA"), config("constantes.GO_VENCIDA"), config("constantes.GO_PAGADA")])->orderBy("gestionobligaciones.created_at")->get();
            }            
        }else{            
            $idPadreO = $datos->idPadre;
            $datos = json_decode(decrypt($request->otrosDatos));
            $adjuntos = DB::table('gestionobligaciones')->where("gestionobligaciones.id_obligacion", $datos->idPadre)->whereIn("gestionobligaciones.estado", [config("constantes.GO_SOLICITADA"), config("constantes.GO_RADICADA"), config("constantes.GO_VENCIDA"), config("constantes.GO_PAGADA")])->orderBy("gestionobligaciones.created_at")->get();            
        }  
        
        if(count($adjuntos) > 0){
            $html = '
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Estado</th>                            
                            <th>Sol</th>    
                            <th>Rad</th>    
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="containerListaAdjuntosObligaciones'.$idPadreO.'">';
            
            foreach ($adjuntos as $adjunto){
                $idUnicoElements = uniqid();                
                $nombreTipo = $adjunto->tipoAdjunto;
                if($adjunto->tipoAdjunto == config("constantes.CERTIFICACIONES_DEUDA")){
                    $nombreTipo = "Certificación de deuda";
                }elseif($adjunto->tipoAdjunto == config("constantes.PAZ_SALVO")){
                    $nombreTipo = "Paz y Salvo";
                }
                
                
                $nameEstado = FuncionesComponente::$estadosGestionObligacion[$adjunto->estado];                
                $html .= '
                     <tr id="'.$idUnicoElements.'">
                            <td>'.  date("d-m-Y", strtotime($adjunto->updated_at)) .'</td>
                            <td>'.$nombreTipo .'</td>
                            <td>'.$nameEstado.'</td>';                
                if($adjunto->id_adjuntoSolicitud > 0){
                    $html .= '<td><a class="color-negro" title="Visualizar" href="'.config('constantes.RUTA').'visualizar/'.$adjunto->id_adjuntoSolicitud.'" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a></td>';
                }else{
                    $html .= '<td></td>';
                }
                
                if($adjunto->id_adjunto > 0){
                    $html .= '<td><a class="color-negro" title="Visualizar" href="'.config('constantes.RUTA').'visualizar/'.$adjunto->id_adjunto.'" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a></td>';
                }else{
                    $html .= '<td></td>';
                }
                
                if($adjunto->estado == config("constantes.GO_PAGADA") || ($adjunto->tipoAdjunto == config("constantes.PAZ_SALVO") && $adjunto->estado == config("constantes.GO_RADICADA"))){
                    $html .= '<td></td></tr>';
                }else{
                    $html .= '<td class="text-center"><a title="Eliminar" style="cursor: pointer" class="deleteAdjuntoObligaciones color-redA margin-left-5"  data-infoadjunto=\''.json_encode($adjunto).'\' data-delparent="#'.$idUnicoElements.'" data-adjunto='.$adjunto->id.' data-url="'.config('constantes.RUTA').'Estudio/EliminarAdjuntos">
                                        <span class="fa fa-remove"></span>
                                    </a></td>
                        </tr>';
                }
                    
            }
            $html .= '
                    </tbody>
                </table>';            
        }else{
            $html =  '<div class="alert alert-warning" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Mensaje:</strong>
                    <p>No hay adjuntos cargados hasta el momento</p>                    
                  </div>';
        }
        
        return $html;        
    }
    
    /*
     * Funcion que valida si todas las gestiones obligaciones del estudio en tesoreria estan en estado Pagado. Si es asi pasa a Cartera el estudio
     */
    function checkPasarCartera($idObligacion = false, $idEstudio = false){
        
        if($idObligacion != false){
            $obligacion = Obligacion::find($idObligacion);
            $valoracion = $obligacion->Valoracion;            
        }elseif($idEstudio != false){
            $estudio = Estudio::find($idEstudio);
            $valoracion = $estudio->Valoracion;                        
        }
        
        $faltantes = DB::select('SELECT gestionobligaciones. *  FROM gestionobligaciones WHERE gestionobligaciones.id_obligacion in
                                                                (SELECT obligaciones.id FROM obligaciones WHERE obligaciones.Valoracion = '.$valoracion.' AND obligaciones.Compra = "S" and obligaciones.Estado = "Activo") 
                                                                AND gestionobligaciones.tipoAdjunto = "'.config("constantes.CERTIFICACIONES_DEUDA").'" 
                                                                AND gestionobligaciones.estado in ("'. config("constantes.GO_RADICADA").'", "'.config("constantes.GO_VENCIDA").'", "'.config("constantes.GO_PAGADA").'")
                                                                order by gestionobligaciones.estado = "'.config("constantes.GO_VENCIDA").'", gestionobligaciones.estado = "'.config("constantes.GO_RADICADA").'", gestionobligaciones.estado = "'.config("constantes.GO_PAGADA").'"');
        
        $pasarCartera = true;       
        $arrayIdObligacionesProcesadas = [];
        for($i = 0; $i < count($faltantes) ; $i++){
            if(in_array($faltantes[$i]->id_obligacion, $arrayIdObligacionesProcesadas)){                
                continue;
            }
            if($faltantes[$i]->estado == config("constantes.GO_PAGADA") ){
                $arrayIdObligacionesProcesadas[] =$faltantes[$i]->id_obligacion; 
            }else{
                $pasarCartera = false;
                break;
            }            
        }
        
        $giradoCliente = DB::select('SELECT estudios.Saldo as Saldo , sum(giroscliente.Valor) as pagadoCliente
                                                                    FROM estudios 
                                                                    LEFT JOIN giroscliente ON giroscliente.Estudio = estudios.id                                                                     
                                                                    WHERE estudios.Valoracion = '.$valoracion.' and estudios.Estado in("'.config("constantes.ESTUDIO_TESORERIA").'", "'.config("constantes.ESTUDIO_PROCESO_TESORERIA").'")
                                                                    GROUP BY estudios.id, estudios.Saldo, estudios.Desembolso');
                  
         if(isset($giradoCliente[0]->Saldo) && $giradoCliente[0]->Saldo > 0){
             if($giradoCliente[0]->pagadoCliente < $giradoCliente[0]->Saldo){             
                 $pasarCartera = false;
             }             
         }
         
        $cambioEstado = false;
        if($pasarCartera){
            $cambioEstado = DB::table('estudios')->where("Valoracion", $valoracion)->whereIn("Estado", [config("constantes.ESTUDIO_TESORERIA"), config("constantes.ESTUDIO_PROCESO_TESORERIA")])->update(['Estado' => config("constantes.ESTUDIO_CARTERA")]);        
        }
        return $cambioEstado;
    }
    
    /*
     * Funcion que actualiza el estado de la gestion obligacion a Pagado y adicinalmente revisa si el estudio esta en estado Tesoreria para pasarlo a proceso tesoreria, si no lo deja en el estado que esta
     */
    function cambiarEstadoGestionObligaciones($request){   
        $datos = json_decode(decrypt($request->otrosDatos));          
        
        $result = $updateGestionObligaciones = DB::table('gestionobligaciones')->where("id_obligacion", $datos->idPadre)
                                                                                                                              ->where("tipoAdjunto", config("constantes.CERTIFICACIONES_DEUDA"))
                                                                                                                              ->whereIn("estado", [config("constantes.GO_RADICADA"), config("constantes.GO_VENCIDA")])
                                                                            ->orderBy('created_at', 'DESC')
                                                                            ->take(1)
                                                                            ->update(['estado' => config("constantes.GO_PAGADA")]);
        
        
        $infoEstudio = DB::select('SELECT * from estudios where estudios.Valoracion =  (SELECT obligaciones.Valoracion from obligaciones where obligaciones.id = '.$datos->idPadre.')');        
        
        if(count($infoEstudio) > 0 && $infoEstudio[0]->Estado == config("constantes.ESTUDIO_TESORERIA")){
            $infoEstudio = DB::select('UPDATE estudios SET estado = "'.config("constantes.ESTUDIO_PROCESO_TESORERIA").'" 
                                                                WHERE estudios.Valoracion =  (SELECT obligaciones.Valoracion FROM obligaciones WHERE obligaciones.id = '.$datos->idPadre.')');                    
        }
        
        $this->checkPasarCartera($datos->idPadre);
        
        return $result;
        
    }
    function devolverEstadoGestionObligaciones($infoAdjunto){
           
        $infoGestionObligacion = DB::table('gestionobligaciones')->where("id_obligacion", $infoAdjunto["idPadre"])
                                                                                                    ->where("tipoAdjunto", config("constantes.CERTIFICACIONES_DEUDA"))
                                                                                                    ->where("estado", config("constantes.GO_PAGADA"))
                                                                                                    ->orderBy('created_at', 'DESC')
                                                                                                    ->take(1)->get();
        
          if(strtotime($infoGestionObligacion[0]->fechaVencimiento) < time()){
              $estado = config("constantes.GO_VENCIDA");
          }else{
              $estado = config("constantes.GO_RADICADA");
          }
        return DB::table('gestionobligaciones')->where("id_obligacion", $infoAdjunto["idPadre"])
                                                                        ->where("tipoAdjunto", config("constantes.CERTIFICACIONES_DEUDA"))
                                                                        ->where("estado", config("constantes.GO_PAGADA"))
                                                                        ->orderBy('created_at', 'DESC')
                                                                        ->take(1)
                                                                        ->update(['estado' => $estado]);        
    }
    
    function tablaVisado($request){
        $datos = json_decode(decrypt($request->otrosDatos));        
        $adjuntos = Adjunto::where("idPadre", $datos->idPadre)->where("Tabla", config("constantes.KEY_ESTUDIO"))->where("Modulo", config("constantes.MDL_VALORACION"))->whereIn("TipoAdjunto", [config("constantes.SOLICITUD_VISADO"), config("constantes.VISADO")])->orderBy("TipoAdjunto")->get();
        $html = $this->contruirTablaVisado($adjuntos);
        return $html;
    }
    
    function contruirTablaVisado($adjuntos){
        $html = "";
        foreach ($adjuntos as $adjunto){
            $tipoAdjunto = "";
            if($adjunto->TipoAdjunto == config("constantes.SOLICITUD_VISADO")){
                $tipoAdjunto = "Solicitud Visado";
            }elseif($adjunto->TipoAdjunto == config("constantes.VISADO")){
                $tipoAdjunto = "Radicaci&oacute;n Visado";
            }
            $html .= "
                <tr>
                    <td>{$adjunto->created_at}</td>
                    <td>{$tipoAdjunto}</td>
                    <td><a class='color-negro' title='Visualizar' href='".config('constantes.RUTA')."visualizar/$adjunto->id' target='_blank'><span class='fa fa-paperclip fa-1x color-negro'></span></a></td>                    
                </tr>
                ";
        }
        
        return $html;        
        
    }
    
}