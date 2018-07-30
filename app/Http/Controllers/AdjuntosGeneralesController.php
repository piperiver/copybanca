<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Adjunto;
use Illuminate\Support\Facades\Auth;
use DB;
use View;
use PDF;
use Excel;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdjuntosGeneralesController extends Controller
{
    protected $forma = "ADJUG";
    
    //Adjuntos generales
    protected $adjuntosGenerales = array(
                                            array("FAC","FORMATO DE ATORIZACIÓN DE CONSULTA"),        
                                        );
    
    protected $padre_id = 10; 
                                           
    function listAdjuntosGenerales(){
        
        $tiposAdjuntos = array_column($this->adjuntosGenerales, 0);
        
        $adjuntos = Adjunto::whereIn("TipoAdjunto",$tiposAdjuntos)
                                ->where("Tabla", "adjuntosGenerales")
                                ->where("Modulo", "VALO")
                                ->where("idPadre", $this->padre_id)
                                ->get();
        
        $count = 0;              
        foreach ($tiposAdjuntos as $tA){
            
            if (count(Adjunto::where("TipoAdjunto",$tA)->get()) > 0){
                 unset($this->adjuntosGenerales[$count]);
            }
            $count++;
        }
        
        return view('pages.AdjuntosGenerales.index')
                        ->with('adjuntos',$adjuntos )
                        ->with('padre_id',$this->padre_id )
                        ->with('forma',$this->forma )
                        ->with('adjuntosGenerales',$this->adjuntosGenerales );
    
    }
    
    
    
}


