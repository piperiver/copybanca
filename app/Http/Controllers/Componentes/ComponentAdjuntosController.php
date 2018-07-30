<?php

namespace App\Http\Controllers\Componentes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\ComponentAdjuntos;
use App\Librerias\UtilidadesClass;
use App\Librerias\FuncionesComponente;
use App\Adjunto;
use Illuminate\Support\Facades\Storage;
use DB;


class ComponentAdjuntosController extends Controller
{
    function ControlUpload(Request $request){
        
        $datos = json_decode(decrypt($request->otrosDatos));
//        $archivo = $request->file('ComponentArchivo');
        if (!isset($request->ComponentArchivo) || !$request->file('ComponentArchivo')->isValid()) {            
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte.[Mensaje: ".$request->file('ComponentArchivo')->getErrorMessage()."]"]);
            die();
        }

        $archivo = $request->ComponentArchivo;
        $extension = $archivo->getClientOriginalExtension();

        $objComponenteAdjuntos = new ComponentAdjuntos();
        
        $tipoAdjunto = ($datos->tipoAdjunto != false) ? $datos->tipoAdjunto : $request->tipoAdjunto;        
        $nombreAdjunto = ($datos->nombreAdjunto != false)? str_replace(" ", "_", $datos->nombreAdjunto) : $request->NombreArchivo;
        $nombreAdjunto = str_replace(".", "", $nombreAdjunto);
        $id = $objComponenteAdjuntos->save($datos->idPadre, $datos->tabla, $nombreAdjunto, $extension, $tipoAdjunto, $datos->modulo, $archivo);
        $datos->idAdjunto = $id;
        
        $returnPHP  = false;
        if($datos->functionPHP != false){
            $objFuncionesComponente = new FuncionesComponente();
            if(method_exists($objFuncionesComponente, $datos->functionPHP)){
                $returnPHP = $objFuncionesComponente->{$datos->functionPHP}($request);
            }else{
                $returnPHP = "El metodo ".($datos->functionPHP)." no existe en la clase FuncionesComponente";
            }
        }
        
        $itemsTabla = false;
        if($datos->dspTabla){                       
            $itemsTabla = $objComponenteAdjuntos->createTableOfAdjuntos($datos->idPadre, $datos->modulo, $datos->tabla, $datos->tipoAdjunto, $datos->function);
        }
        $datos->tipoAdjunto = $tipoAdjunto;
        echo json_encode(["STATUS" => true, "MENSAJE" => "Adjunto cargado satisfactoriamente.", "datos" => $datos, "itemsTabla" => $itemsTabla, "returnPHP" => $returnPHP]);
    }

    function pruebaeval(){
        $function  = "pruebaaa";
        $objFuncionesComponente = new FuncionesComponente();
        if(method_exists($objFuncionesComponente, $function)){
        $res = $objFuncionesComponente->{$function}();
        }else{
            $res = "No existe";
        }
        var_dump($res);
    }
    public function descargar($id){
        
        $archivo = Adjunto::find($id);
        if(!is_null($archivo) || $archivo != false){
            if(!file_exists(storage_path("adjuntos")."/".$id)){
                abort(404);
            }
            
            header("Content-type:application/".$archivo->Extension);
            header("Content-Disposition:attachment;filename='$archivo->NombreArchivo.$archivo->Extension'");
            readfile(storage_path("adjuntos")."/".$id);
        }
    }
    public function visualizar($id){
                
        $archivo = Adjunto::find($id);
        if(!is_null($archivo) || $archivo != false){
            if(!file_exists(storage_path("adjuntos")."/".$id)){
                abort(404);
            }            
            if(exif_imagetype(storage_path("adjuntos")."/".$id)){
                header("Content-type:$archivo->Extension");
            }else{
                header("Content-type:application/$archivo->Extension");
            }            
            header("Content-Disposition:inline;filename='$archivo->NombreArchivo'");
            readfile(storage_path("adjuntos")."/".$id);
            exit;

        }
    }
    function eliminar(Request $request){                
        
         try{            
            if(file_exists(storage_path("adjuntos")."/".$request->idAdjunto)){
                $eliminado = unlink(storage_path("adjuntos")."/".$request->idAdjunto);
            }else{
                $eliminado = false;
            }
            
            if($eliminado){
                $archivo = Adjunto::find($request->idAdjunto);
                $archivo->delete();               
                if($request->funcionphp != false){
                    $objFuncionesComponente = new FuncionesComponente();
                    if(method_exists($objFuncionesComponente, $request->funcionphp)){
                        $objFuncionesComponente->{$request->funcionphp}($request->infoAdjunto);
                    }
                }
                echo json_encode(["STATUS" => true, "MENSAJE" => "Archivo eliminado satisfactoriamente"]);
            }else{
                echo json_encode(["STATUS" => false, "MENSAJE" => "Error al tratar de eliminar el adjunto cod.401"]);    
            }
            
         }catch (Exception $e) {
            echo json_encode(["STATUS" => false, "MENSAJE" => "Error al tratar de eliminar el adjunto cod.402 ".$e->getMessage()]);
         }
         
    }
    
}
