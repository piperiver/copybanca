<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Librerias\UtilidadesClass;
use Carbon\Carbon;
use DB;
use App\Valoracion;
use App\User;
use App\HuellaConsulta;
use App\Obligacion;
use App\ProcesoJuridico;
use App\Token;
use App\Adjunto;
use App\Estudio;
use App\TipoAdjunto;
use App\BancoEntidades;
use App\Librerias\ComponentAdjuntos;

class GestionObligacionesController extends Controller
{
    
    protected $forma = 'GESTO';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }
        
        if(!UtilidadesClass::validaGestionObligaciones($id)){
            return redirect(config('constantes.RUTA')."Estudio/".$id);
        }
        
        $Estudio = Estudio::where('id',$id)->get();
        
        foreach ($Estudio as $Estudio){        
            $idValoracion = $Estudio->Valoracion;
            $Obligaciones = Obligacion::where('Valoracion',$Estudio->Valoracion)->whereIn("EstadoCuenta", ["Al Día", "En Mora", "Castigada"])->where("Estado", "Activo")->orderBy('Entidad', 'asc')->orderBy('SaldoActual', 'desc')->get();            
                    
        }
        
        return view('pages.GestionObligaciones.index')->with('Obligaciones',$Obligaciones)->with('idValoracion',$idValoracion)->with('idEstudio', $id);
    }
    
    function GestionObligacionesValoracion($idValoracion){
        UtilidadesClass::validaGestionObligacionesValoracion($idValoracion);
        $Obligaciones = Obligacion::where('Valoracion',$idValoracion)->whereIn("EstadoCuenta", ["Al Día", "En Mora", "Castigada"])->orderBy('Entidad', 'asc')->orderBy('SaldoActual', 'desc')->get();            
        return view('pages.GestionObligaciones.obligacionesRepetidas')->with('Obligaciones',$Obligaciones)->with('idValoracion',$idValoracion);
    }

   function procesarObligaciones(Request $request){   
        if(isset($request->obligaciones)){
            $update = Obligacion::whereIn('id', $request->obligaciones)->update(["Estado" => "Inhabilitado"]);
        }else{
           $update = true;
        }
        $updateValoracion = Valoracion::where("id", $request->idValoracion)->update(["Filtro" => 1]);
        if($update && $updateValoracion){
            return json_encode(["STATUS" => true]);
        }else{
            return json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar eliminar las obligaciones repetidas, Verifique su conexi&oacute;n a internet, recargue la p&aacute;gina e intentelo de nuevo"]);
        }        
//        $listObligaciones = Obligacion::where("Valoracion", $request->idValoracion)->whereIn("EstadoCuenta", ["Al Día", "En Mora", "Castigada"])->where("Estado", "Activo")->orderBy('Entidad', 'asc')->orderBy('SaldoActual', 'desc')->get();
    }
    
    function procesarObligacionesDesprendible(Request $request){
        
        if(isset($request->obligaciones)){
            $update = Obligacion::whereIn('id', $request->obligaciones)->update(["Desprendible" => "S", "Compra" => "S"]);                    
        }
        
        Obligacion::where("Valoracion",$request->idValoracion)
                    ->where("EstadoCuenta","Castigada")
                    ->orWhere("EstadoCuenta","En Mora")
                    ->orWhere("EstadoCuenta","Castigadas")
                    ->update(["Compra" => "S"]);
        
        $ingreso = Estudio::select("IngresoBase")->where("id",$request->idEstudio)->get();
        $egreso = Estudio::select("TotalEgresos")->where("id",$request->idEstudio)->get();
        
        $ComponentAdjuntos = new ComponentAdjuntos();
        ob_start();       
        ?>
          <div id='divParrafo'>
              <p style='margin:3px 0;' class='text-center uppercase'><strong>Adiciona obligaciones no reportadas en centrales,<br>Digita el ingreso y egreso y Adjunta el desprendible.</strong></p>
                     </div>
                        <hr>
                            <div class='row text-center'>
                                <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos borde-lateral'>
                                    <div class='form-group'>
                                        <label for='Ingresos'>INGRESOS</label>
                                        <div class='input-group'>
                                            <div class='input-group-addon'><i style='color: #2f353b;' class='fa fa-plus-square-o' aria-hidden='true'></i></div>
                                            <input class='form-control miles' type='text' id='ingresos' name='ingresos' value='<?php echo number_format($ingreso[0]->IngresoBase,0,'.','.')?>'>
                                            <div class='cPagaduria'></div>
                                        </div>                             
                                    </div>
                                </div>

                                <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos borde-lateral'>
                                    <div class='form-group'>
                                        <label for='Egresos'>EGRESOS</label>
                                        <div class='input-group'>
                                            <div class='input-group-addon'><i style='color: #2f353b;' class='fa fa-minus-square-o' aria-hidden='true'></i></div>
                                            <input class='form-control miles' id='egresos' name='egresos' value='<?php echo number_format($egreso[0]->TotalEgresos,0,'.','.')?>'>
                                        </div>                             
                                    </div>
                                </div>                                  
                            </div>                                                
                            <div class='row text-center'> 
                            <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos borde-lateral'>
                                <div class='form-group'>
                                    <label for='Entidad'>ENTIDAD</label>
                                    <div class='input-group'>
                                        <div class='input-group-addon'><i style='color: #2f353b;' class='fa fa-university' aria-hidden='true'></i></div>
                                        <input class='campo form-control' type='text' id='pagaduria' name='pagaduria'>
                                        <div class='cPagaduria'></div>
                                    </div>                             
                                </div>
                            </div>
                            
                            <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 column-campos borde-lateral'>
                                <div class='form-group'>
                                    <label for='Cuota'>CUOTA</label>
                                    <div class='input-group'>
                                        <div class='input-group-addon'><i style='color: #2f353b;' class='fa fa-usd' aria-hidden='true'></i></div>
                                        <input class='form-control miles' id='Cuota' name='Cuota'>
                                    </div>                             
                                </div>
                            </div>                            
                            
                            <button type='button' class='btn btn-default btnPrueba' id='Adicionar' name='Adicionar' data-url='<?php echo config('constantes.RUTA')?>GestionObligaciones/procesarObligacionesNuevas'>Adicionar</button>
                        </div>
                        <br>
                        <div class='table-responsive div-obligaciones' id='divTables'></div>
                        <br>
                        <div class='row'>
                                <div class='col-md-10 col-md-offset-1 column-campos borde-lateral'>
                                    <?php echo $ComponentAdjuntos->dspFormulario($request->idEstudio, config("constantes.KEY_ESTUDIO"), config("constantes.DESPRENDIBLE"), config("constantes.MDL_VALORACION"), [config("constantes.AUTORIZACION_DE_CONSULTA")], "locked")?>
                                </div>
                        </div>
        <?php
        $htmlBody = ob_get_clean();
                
        $htmlFooter = "<h4 class='modal-title pull-left uppercase text-white' id='myModalLabel'>Informacion Desprendible</h4>
                        <button type='button' class='btn btn-default' id='Anterior' data-url='{{ config('constantes.RUTA') }}Consultas'>Cancelar</button>
                        <button type='button' style='border-radius: 4px!important;border:1px solid #000!important;' class='btn btn-danger btnPrueba' id='fnsProcess' data-url='".config('constantes.RUTA').'GestionObligaciones/ActualizarEst'."'>Terminar</button>";
        
        echo json_encode(["url" => config('constantes.RUTA')."Estudio/".$request->idValoracion, "htmlBody" => $htmlBody, "htmlFooter" => $htmlFooter]);
    }
    
    function procesarObligacionesNuevas(Request $request){
        
        $obligacion = new Obligacion;
        $obligacion->NumeroObligacion = "00000";
        $obligacion->Valoracion = $request->Valoracion;
        $obligacion->Entidad = $request->Entidad;
        $obligacion->Naturaleza = "Libranza";
        $obligacion->Calidad = "Principal";
        $obligacion->SaldoMora = "0";
        $obligacion->SaldoActual = "0";
        $obligacion->SaldoActualOriginal = "0";
        $obligacion->CuotaTotal = str_replace(".", "", $request->Cuota);
        $obligacion->ValorPagar = "0";
        $obligacion->FechaApertura = "";
        $obligacion->FechaVencimiento = "";
        $obligacion->ValorInicial = "0";
        $obligacion->ValorCuota = str_replace(".", "", $request->Cuota);
        $obligacion->NumeroCuotasMora = "0";
        $obligacion->Compra = "S";
        $obligacion->EstadoCuenta = "Al Día";
        $obligacion->Nit = "";
        $obligacion->Desprendible = "S";
        $obligacion->Estado = "Activo";
        $obligacion->calificacion = "";
        $obligacion->comportamiento = "";
        $obligacion->oficina = "";
        $obligacion->tipoCuenta = "LBZ";        
        $obligacion->cuotasVigencia = "";
        $obligacion->marca = "Sistema";
        
        $obligacion->save();
        
        BancoEntidades::firstOrCreate(['nombre' => $request->Entidad]);
        
        $obligacionesNuevas = Obligacion::where("Valoracion", $request->Valoracion)->where("Marca","Sistema")->get();
        
        $htmlTable = "<table class='table table-hover text-center listaSelObligaciones'>
                                <thead class='head-inverse'>
                                    <tr>";
                                        $htmlTable .= "<th class='text-center'>".config('constantes.EST_CONT2_ENTIDAD')."</th>";
                                        /*$htmlTable .= "<th class='text-center'>".config('constantes.EST_CONT2_SALDO')."</th>";*/
                                        /*$htmlTable .= "<th class='text-center'>".config('constantes.EST_CONT2_TIPO')."</th>";*/
                                        $htmlTable .= "<th class='text-center'>".config('constantes.EST_CONT1_CUO')."</th>";                                        
                          $htmlTable .= "</tr>";
                      $htmlTable .= "</thead>";
                      $htmlTable .= "<tbody>";
                                
                                foreach($obligacionesNuevas as $obligacionesTab){
                                    $htmlTable .= "<tr>";
                                        $htmlTable .= "<td>".$obligacionesTab->Entidad."</td>";
                                        /*$htmlTable .= "<td>".$obligacionesTab->SaldoActual."</td>";*/
                                        /*$htmlTable .= "<td>".$obligacionesTab->TipoCuenta."</td>";*/
                                        $htmlTable .= "<td>".number_format($obligacionesTab->CuotaTotal,0,'.','.')."</td></tr>";
                                }
                                $htmlTable .= "</tbody></table>";


        //return $obligacionesNuevas;
        //$obligacion->NumeroObligacion = str_replace(".", "", $request->EstudioIngreso);
                                        
        
        
        return json_encode(["htmlTable" => $htmlTable]);
    }
    
    function actEstudio(Request $request){
        
        Estudio::where('id', $request->Estudio)
               ->update(['IngresoBase' => str_replace(".", "", $request->Ingresos), 'TotalEgresos' => str_replace(".", "",$request->Egresos), 'Estado' => 'RAD']);
        
        $url = config('constantes.RUTA')."Estudio/".$request->Estudio;
        return json_encode(["uri" => $url]);
    }

    function agregarObligacion($id){
        return view('pages.Estudio.components.__agregarObligaciones')->with('id',$id);
    }

    function guardarObligacion($id, Request $request){
        $estudio = Estudio::find($id);
        $obligacion = new Obligacion;
        $obligacion->NumeroObligacion = "00000";
        $obligacion->Valoracion = $estudio->Valoracion;
        $obligacion->Entidad = $request->Entidad;
        $obligacion->Naturaleza = "Libranza";
        $obligacion->Calidad = "Principal";
        $obligacion->SaldoMora = "0";
        $obligacion->SaldoActual = "0";
        $obligacion->SaldoActualOriginal = "0";
        $obligacion->CuotaTotal = str_replace(".", "", $request->ValorCuota);
        $obligacion->ValorPagar = "0";
        $obligacion->FechaApertura = "";
        $obligacion->FechaVencimiento = "";
        $obligacion->ValorInicial = "0";
        $obligacion->ValorCuota = str_replace(".", "", $request->ValorCuota);
        $obligacion->NumeroCuotasMora = "0";
        $obligacion->Compra = "S";
        $obligacion->EstadoCuenta = "Al Día";
        $obligacion->Nit = "";
        $obligacion->Desprendible = "S";
        $obligacion->Estado = "Activo";
        $obligacion->calificacion = "";
        $obligacion->comportamiento = "";
        $obligacion->oficina = "";
        $obligacion->tipoCuenta = "LBZ";
        $obligacion->cuotasVigencia = "";
        $obligacion->marca = "Sistema";

        $obligacion->save();
        return redirect('Estudio/'.$id);
    }
}
