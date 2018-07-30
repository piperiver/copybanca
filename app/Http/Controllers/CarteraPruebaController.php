<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\gestionObligaciones;
use App\log_gestionObligaciones;
use App\Estudio;
use App\Causacion;
use App\Adjunto;
use App\Pago;
use App\pagoBalance;
use App\Balance;
use App\Librerias\UtilidadesClass;
use DB;
use PDF;
use DateTime;
use App\Librerias\ComponentAdjuntos;
use App\Librerias\FuncionesComponente;

class CarteraPruebaController extends Controller
{
    function validarExistenciaEstudio($idEstudio){
        if(!is_numeric($idEstudio)){
            $vista =  view('errors.101')->with("mensaje", "El identificador debe ser numerico");
            echo $vista->render();
            die;
        }
        
        $infoEstudio = Estudio::find($idEstudio); 
                    
        if(!isset($infoEstudio->id)){
            $vista = view('errors.101')->with("mensaje", "El estudio al que desea ingresar no existe");
            echo $vista->render();
            die;
        }
                
        if($infoEstudio->EstadoEstudio == "ing" || $infoEstudio->EstadoEstudio == "ING"){
            $vista = redirect(config('constantes.RUTA')."GestionObligaciones/".$idEstudio);            
            echo $vista->render();
            die;
        }        
        
        return $infoEstudio;
    }    
    function CausarMes($idEstudio){
        $fechaCausar = "2017-11-1";
        $cantDiasMes = date("t", strtotime($fechaCausar)); //Se obtiene la cantidad de dias que tiene el mes        
        
        for($dia=1; $dia <= $cantDiasMes; $dia++){
            
            $infoEstudio = $this->validarExistenciaEstudio($idEstudio);
        
            $inicioMes;
            $finMes;
            $valorInteresMoraDia = 0;
            $valorInteresDia = 0;
            $valorSeguro = 1000;
            $valorCapital = 0;
            $tasaMora = 2.42;

            //Primero se consulta si el estudio ya tiene creado balances lo cual indicaria que el capital cambio
            $balanceCliente = Balance::where("idEstudio", $idEstudio)->get();
            if(count($balanceCliente) > 0){                
                //Si el dia actual esta entre la fecha de pago, se deba causar mora pero no sobre el capital del mes que falta por pagar en estos dias sino por el capital de los meses anteriores que no se pagaron
                if($dia >= 1 && $dia <= 15){                    
                    //Se selecciona el primer dia del mes anterior para que obtenga los balances con fechas mas viejas que la hallada
//                    $nuevafecha = strtotime('-1 month' , strtotime(date("Y-m")));
                    $nuevafecha = strtotime('-1 month' , strtotime($fechaCausar));
                    $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital","fechaCausacion")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("causacion.fechaCausacion", "<=", date('Y-m-d', $nuevafecha))->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                    $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");                                                            
                }else{//se obtiene todo el abono a capital faltante ya que en este punto ya se tiene certeza de cual es el capital que deuda el cliente
                    $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital")->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                    $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");                    
                }                
                $totalCapitalEnMora = $totalCapitalEnMora * -1;
                if($totalCapitalEnMora > 0){
                    $valorInteresMoraDia = round(($totalCapitalEnMora * ($tasaMora/100)) / $cantDiasMes, 3);
                    if($dia == 16){
                        if($valorInteresMoraDia > 0 && $balanceCliente->last()->interesMora !== $valorInteresMoraDia){
//                            DB::select('UPDATE causacion SET interesMora = '.$valorInteresMoraDia.' WHERE causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                            DB::table('causacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)).'-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)).'-15')
                                ->update(['interesMora' => $valorInteresMoraDia]);
                            
//                            DB::select('UPDATE balance JOIN causacion on causacion.id = balance.idCausacion SET balance.interesMora = '.($valorInteresMoraDia * -1).' where causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                            DB::table('balance')
                                ->join('causacion', 'causacion.id', '=', 'idCausacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)).'-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)).'-15')
                                ->update(['balance.interesMora' => ($valorInteresMoraDia * -1)]);                            
                        }
                    }
                }                
                //capturamos el valor del capital
               $valorCapital = $balanceCliente->last()->saldoCapital;
            }else{//Si no se toma el valor del credito como capital inicial                      
                $valorCapital = $infoEstudio->ValorCredito;
            }

            //Se calcula el valor de interes para cada uno de los dias del mes        
            $valorInteresDia = round(($valorCapital * ($infoEstudio->Tasa / 100)) / $cantDiasMes, 3); 

            $causacion = new Causacion;
            $causacion->idEstudio = $idEstudio;
//            $causacion->fecha = date("Y-m-d  G:i:s");
            $causacion->fechaCausacion = date("Y-m", strtotime($fechaCausar))."-".$dia." 00:00:00";
//            var_dump(date("Y-m", strtotime($fechaCausar))."-".$dia." 00:00:00");
            $causacion->interesCorriente = $valorInteresDia;

            //Si es el primer dia del mes casamos el seguro
            if($dia == "1"){
                $causacion->seguro = $valorSeguro;
            }

            //Se calcula la mora        
            if($valorInteresMoraDia > 0){
                $causacion->interesMora = $valorInteresMoraDia;
            }

            //Obtenemos el ultimo dia de ese mes para validar si estamos en ese dia y asi causar el capital
            $fecha = new DateTime($fechaCausar);
            $fecha->modify('last day of this month');
            if($dia == $fecha->format('d')){                
                $interesesDelMes = $valorInteresDia * $cantDiasMes;                
                $abonoCapital = $infoEstudio->Cuota - $valorSeguro - $interesesDelMes;
                $causacion->abonoCapital = $abonoCapital;                
            }
//            DB::connection()->enableQueryLog();
            $causacion->save();
//            var_dump(DB::getQueryLog());
            
            $this->balance($idEstudio, $causacion, $valorCapital);
            
        }
        
    }
   
    function causarRapido($idEstudio, $mes){
        set_time_limit(0);                
        $cantDiasMes = date("t", strtotime("2018-$mes-01")); //Se obtiene la cantidad de dias que tiene el mes        //Y-M-d               
        for($i = 1 ; $i <= $cantDiasMes ; $i++){            
            if($cantDiasMes == $i){
                continue;
            }
            $this->causar($idEstudio, "2018-$mes-".$i, false);
        }
        return redirect(config('constantes.RUTA')."PruebaCartera/".$idEstudio);
    }    
    function causar($idEstudio, $fechaCausar, $redirigir = true){
        
        $infoEstudio = $this->validarExistenciaEstudio($idEstudio);
        
        $inicioMes;
        $finMes;
        $valorInteresMoraDia = 0;        
        $valorInteresDia = 0;
        $valorSeguro = 19500;
        $valorCapital = 0;
        $tasaMora = 2.42;
        
        $cantDiasMes = date("t", strtotime($fechaCausar)); //Se obtiene la cantidad de dias que tiene el mes
        //Primero se consulta si el estudio ya tiene creado balances lo cual indicaria que el capital cambio
        $balanceCliente = Balance::where("idEstudio", $idEstudio)->get();
        if(count($balanceCliente) > 0){
            //Si el dia actual esta entre la fecha de pago, se deba causar mora pero no sobre el capital del mes que falta por pagar en estos dias sino por el capital de los meses anteriores que no se pagaron
                if(date("d", strtotime($fechaCausar)) >= 1 && date("d", strtotime($fechaCausar)) <= 15){
                    //Se selecciona el primer dia del mes anterior para que obtenga los balances con fechas mas viejas que la hallada
//                    $nuevafecha = strtotime('-1 month' , strtotime(date("Y-m")));
                    $nuevafecha = strtotime('-1 month' , strtotime($fechaCausar));
                    $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital","fechaCausacion")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("causacion.fechaCausacion", "<=", date('Y-m-d', $nuevafecha))->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                    $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");                                                            
                }else{//se obtiene todo el abono a capital faltante ya que en este punto ya se tiene certeza de cual es el capital que deuda el cliente
                    $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital")->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                    $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");                    
                }                
                $totalCapitalEnMora = $totalCapitalEnMora * -1;
                if($totalCapitalEnMora > 0){
                    $valorInteresMoraDia = round(($totalCapitalEnMora * ($tasaMora/100)) / $cantDiasMes, 3);
                    if(date("d", strtotime($fechaCausar)) == 16){
                        if($valorInteresMoraDia > 0 && $balanceCliente->last()->interesMora !== $valorInteresMoraDia){
//                            DB::select('UPDATE causacion SET interesMora = '.$valorInteresMoraDia.' WHERE causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                            DB::table('causacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)).'-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)).'-15')
                                ->update(['interesMora' => $valorInteresMoraDia]);
                            
//                            DB::select('UPDATE balance JOIN causacion on causacion.id = balance.idCausacion SET balance.interesMora = '.($valorInteresMoraDia * -1).' where causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                            DB::table('balance')
                                ->join('causacion', 'causacion.id', '=', 'idCausacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)).'-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)).'-15')
                                ->update(['balance.interesMora' => ($valorInteresMoraDia * -1)]);                            
                        }
                    }
                }                
                //capturamos el valor del capital
               $valorCapital = $balanceCliente->last()->saldoCapital;
        }else{//Si no se toma el valor del credito como capital inicial                      
            $valorCapital = $infoEstudio->ValorCredito;
        }
        
        //Se calcula el valor de interes para cada uno de los dias del mes        
        $valorInteresDia = round(($valorCapital * ($infoEstudio->Tasa / 100)) / $cantDiasMes, 3); 
        
        $causacion = new Causacion;
        $causacion->idEstudio = $idEstudio;
        $causacion->fechaCausacion = date("Y-m-d  G:i:s", strtotime($fechaCausar));
        $causacion->interesCorriente = $valorInteresDia;
        
        //Si es el primer dia del mes casamos el seguro
        if(date("d", strtotime($fechaCausar)) == "1"){
            $causacion->seguro = $valorSeguro;
        }
        
        //Se calcula la mora        
        if($valorInteresMoraDia > 0){
            $causacion->interesMora = $valorInteresMoraDia;
        }
        
        //Obtenemos el ultimo dia de ese mes para validar si estamos en ese dia y asi causar el capital
        $fecha = new DateTime();
        $fecha->modify('last day of this month');        
//        if(date("d", strtotime($fechaCausar)) == $fecha->format('d')){
        if(date("d", strtotime($fechaCausar)) == $cantDiasMes){
            $interesesDelMes = $valorInteresDia * $cantDiasMes;                
            $abonoCapital = $infoEstudio->Cuota - $valorSeguro - $interesesDelMes;
            $causacion->abonoCapital = $abonoCapital;              
        }        
        $causacion->save();        
        $this->balance($idEstudio, $causacion, $valorCapital);
        
        if(date("d", strtotime($fechaCausar)) == $cantDiasMes){
            $this->utilizarSaldoFavor($idEstudio);
        }
        
        if($redirigir){
            return redirect(config('constantes.RUTA')."PruebaCartera/".$idEstudio);            
        }
        
    }
    
    function utilizarSaldoFavor($idEstudio){
        $pagosConSaldo_A_Favor = Pago::where("idEstudio", $idEstudio)->where("saldoFavor", ">", 0)->get();
        foreach ($pagosConSaldo_A_Favor as $pago){
            $abonarSobranteACapital = true;
            Pago::where("id", $pago->id)->update(["saldoFavor" => 0]);
            $this->pagar($idEstudio, $pago->saldoFavor, $pago->id, $abonarSobranteACapital);
            //$this->validacionesPago($idEstudio, $pago->saldoFavor, $abonarSobranteACapital, $pago->id);
        }
        
//        $saldoAFavor = DB::table("pago_balance")->join("pago", "pago.id", "pago_balance.idPago")->where("pago.idEstudio", $idEstudio)->sum("pago_balance.saldoFavor");        
//        if(round($saldoAFavor, 3) > 0){            
//            $abonarSobranteACapital = true;
//            $this->validacionesPago($idEstudio, $saldoAFavor, $abonarSobranteACapital);
//            //se actualizan los saldos a favor a 0 ya que ese dinero acaba de ser utilizado
//            DB::table("pago_balance")->join("pago", "pago.id", "pago_balance.idPago")->where("pago.idEstudio", $idEstudio)->update(['pago_balance.saldoFavor' => 0]);            
//        }        
    }
    
    function balance($idEstudio, $causacion, $valorCapital){
        $balance = new Balance;
        $balance->idEstudio = $idEstudio;
        $balance->idCausacion = $causacion->id;
        $balance->seguro = (isset($causacion->seguro))? $causacion->seguro*-1 : 0;
        $balance->interesMora = (isset($causacion->interesMora))? $causacion->interesMora*-1 : 0;
        $balance->interesCorriente = (isset($causacion->interesCorriente))? $causacion->interesCorriente*-1 : 0;
        $balance->abonoCapital = (isset($causacion->abonoCapital))? $causacion->abonoCapital*-1 : 0;        
        $balance->saldoCapital = $valorCapital;
        $balance->save();
        
    }
    public function pruebaPago(){
        $idEstudio = $_GET["idEstudio"];
        $valorPago = $_GET["valorPago"];
        $abonarSobranteACapital = false;
        $this->validacionesPago($idEstudio, $valorPago, $abonarSobranteACapital);
        return redirect(config('constantes.RUTA')."PruebaCartera/".$idEstudio);
    }
    public function validacionesPago($idEstudio, $valorPago, $abonarSobranteACapital, $idPago = false){
        $infoEstudio = $this->validarExistenciaEstudio($idEstudio);
        
        if($valorPago <= 0){
            return false;
        }
        
        $balancesCliente = DB::table("balance")->select("causacion.fechaCausacion","balance.*")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("balance.idEstudio", $idEstudio)->get();                    
        /*
        * Temporal para colocar como fecha de pago el ultimo dia causado y asi simular que estabamos en ese dia
        */
       $infoCausacion = Causacion::find($balancesCliente->last()->idCausacion);
       /*
        * Fin temporal
        */
        if($abonarSobranteACapital == false){
            $pago = new Pago;
            $pago->idEstudio = $idEstudio;
            $pago->pago = $valorPago;
//            $pago->fecha = date("Y-m-d  G:i:s");
            $pago->fecha = date("Y-m-d", strtotime($infoCausacion->fechaCausacion))." ".date("G:i:s");
            $insertPago = $pago->save();
            $idPago = $pago->id;
        }
        $this->pagar($idEstudio, $valorPago, $idPago, $abonarSobranteACapital);
        
    }
    
    function pagar($idEstudio, $valorPago, $idPago, $abonarSobranteACapital){
                
        $balancesCliente = DB::table("balance")->select("causacion.fechaCausacion","balance.*")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("balance.idEstudio", $idEstudio)->get();                    
    
        $deudaAbonoCapital = 0;
        $nuevoCapital = false;
        if(count($balancesCliente) > 0){                        
            $valorPago = round($valorPago, 3);                                    
            foreach ($balancesCliente as $balance){                
                //Si el saldo de capital fue modificado deberia tomarse el nuevo valor y no el que estaba anteriormente en la base de datos
                if($nuevoCapital != false){
                    $balance->saldoCapital = $nuevoCapital;
                }
                
                //pasamos a positivo los valores para manejarlos mas facilmente                
                $balance->seguro = ($balance->seguro < 0)? round($balance->seguro * -1, 3): false;
                $balance->interesMora = ($balance->interesMora < 0)?round($balance->interesMora * -1, 3) : false;
                $balance->interesCorriente = ($balance->interesCorriente < 0)? round($balance->interesCorriente * -1, 3) : false;
                
                $update = [];
                
                $guardarPagoBalance = false;
                $pagoBalance = new pagoBalance;
                
                //Se valida si alcanza para pagar el seguro
                if($balance->seguro != false && $valorPago > 0 &&  $valorPago >= $balance->seguro){
                    $pagoBalance->seguro = $balance->seguro;
                    $guardarPagoBalance = true;                      
                    $valorPago = round($valorPago - $balance->seguro, 3);                    
                    $update["seguro"] = 0;                    
                }elseif($balance->seguro != false && $valorPago > 0){                    
                    $pagoBalance->seguro = $valorPago;
                    $guardarPagoBalance = true;
                    $update["seguro"] = round(($balance->seguro - $valorPago) * -1, 3);
                    $valorPago = 0;
                }
                
                //Se valida si alcanza para pagar los intereses de mora                
                if($balance->interesMora != false && $valorPago > 0 && $valorPago >= $balance->interesMora){
                    $pagoBalance->interesMora = $balance->interesMora;
                    $guardarPagoBalance = true;
                    $valorPago = round($valorPago - $balance->interesMora, 3);
                    $update["interesMora"] = 0;                    
                }elseif($balance->interesMora != false && $valorPago > 0){    
                    $pagoBalance->interesMora = $valorPago;
                    $guardarPagoBalance = true;
                    $update["interesMora"] = round(($balance->interesMora - $valorPago) * -1, 3);
                    $valorPago = 0;
                }
                
                //Se valida si alcanza para pagar los intereses corrientes                                
                if($balance->interesCorriente != false && $valorPago > 0 && $valorPago >= $balance->interesCorriente){
                    $pagoBalance->interesCorriente = $balance->interesCorriente;
                    $guardarPagoBalance = true;
                    $valorPago = round($valorPago - $balance->interesCorriente, 3);
                    $update["interesCorriente"] = 0;                    
                }elseif($balance->interesCorriente != false && $valorPago > 0){
                    $pagoBalance->interesCorriente = $valorPago;
                    $guardarPagoBalance = true;                    
                    $update["interesCorriente"] = round(($balance->interesCorriente - $valorPago) * -1, 3);
                    $valorPago = 0;
                }                
                
                //Se valida si alcanza para pagar el abono a capital                
                if($valorPago > 0 && $balance->abonoCapital < 0 && $valorPago >= round($balance->abonoCapital * -1, 3)){                                        
                    $deudaAbonoCapital = round($balance->abonoCapital * -1, 3);
                    $pagoBalance->abonoCapital = round($balance->abonoCapital * -1, 3);                    
                    
                    $guardarPagoBalance = true;
                    $update["abonoCapital"] = 0;
                    $nuevoCapital = round($balance->saldoCapital + $balance->abonoCapital, 3);                    
                    $valorPago = round($valorPago + $balance->abonoCapital, 3);                          
                }elseif($valorPago > 0 && $balance->abonoCapital < 0){
                    $pagoBalance->abonoCapital = $valorPago;                    
                    $guardarPagoBalance = true;
                    $update["abonoCapital"] = round($balance->abonoCapital + $valorPago, 3);                    
                    $nuevoCapital = round($balance->saldoCapital - $valorPago, 3);                    
                    $valorPago = 0;
                }
                
                if($nuevoCapital != false){
                    $update["saldoCapital"] = $nuevoCapital;
                }
                
                if(count($update) > 0){
                    $actualizacion = Balance::where("id", $balance->id)->update($update);
                    if($actualizacion && $guardarPagoBalance){
                        $pagoBalance->idPago = $idPago;
                        $pagoBalance->idBalance = $balance->id;
                        $pagoBalance->save();
                    }
                }
                
            }                   
            if($valorPago > 0){                
                if($abonarSobranteACapital){                    
                    //obtenemos el capital actual
                    $capitalActual = ($nuevoCapital != false)? $nuevoCapital : $balancesCliente->last()->saldoCapital;
                    //se calcula el nuevo saldo de capital restandole el dinero que sobro
                    $nuevoSaldoCapital = round($capitalActual  - $valorPago, 3);
                    
                    if($nuevoSaldoCapital < 0){
                        $saldoFavor = $nuevoSaldoCapital*-1;
                        $nuevoSaldoCapital = 0; //Se adiciono para que si pagan de mas lo iguale a 0 y asi no dañe la informacion
                    }else{
                        $saldoFavor = 0;
                    }
                    
                    if($saldoFavor > 0){
                         Pago::where("id", $idPago)->update(["saldoFavor" => $saldoFavor ]);                        
                    }
                    
                    //Seran pagos diferentes cuando se causa capital y seran iguales cuando entra la cuota del cliente
                    if($pagoBalance->idPago == $idPago){
                       
                        //se guarda el nuevo saldo de capital y el valor adicional que se abono en el capital en el ultimo balance
                        Balance::where("id", $balancesCliente->last()->id)->update(["abonoCapital" => 0, "saldoCapital" => $nuevoSaldoCapital]);
                        
                        //El pago balance debe obtener el valor total (abono capital + abono extraordinario) que se abono a capital                        
                        //Se obtiene el ultimo pago balance (tuvo que haber sido el de el abono de capital) y actualizamos el abono a capital realizado, sumandole el sobrante o abono extraordinario
                        $actualizacionPagoBalance = pagoBalance::where("id", $pagoBalance->id)->update(["abonoCapital" => $valorPago - $saldoFavor +  $deudaAbonoCapital]);                    
                    }else{                        
                        //se guarda el nuevo saldo de capital y total de abonos extraordinarios 
                        Balance::where("id", $balancesCliente->last()->id)->update(["abonoCapital" => 0, "saldoCapital" => $nuevoSaldoCapital]);
                        
                        //El pago balance debe obtener el valor total (abono capital + abono extraordinario) que se abono a capital
                        //Como no existe item asociado a este pago en la tabla pago_balance, ya que todo esta pago, entonces se procede a crear uno nuevo en donde se especifique otro abono a capital por el valor del saldo a favor asociado a este pago
                        $pagoBalance = new pagoBalance;
                        $pagoBalance->idPago = $idPago;
                        $pagoBalance->idBalance = $balancesCliente->last()->id;
                        $pagoBalance->abonoCapital = $valorPago - $saldoFavor + $deudaAbonoCapital;
                        $pagoBalance->save();
                    }                                        
                    $valorPago = 0 ;
                    if($nuevoSaldoCapital == 0){
                        Estudio::where("id", $idEstudio)->update(["estado" => config("constantes.ESTUDIO_BANCO")]);
                    }
                    
                }else{//                    
                    Pago::where("id", $idPago)->update(["saldoFavor" => $valorPago ]);                    
                    $valorPago = 0 ;                    
                }
                
                
            }
            
        }    
    }
    
    
    function BorrarTodo($idEstudio){
        Causacion::truncate();
        Balance::truncate();
        pagoBalance::truncate();
        Pago::truncate();
        Adjunto::where("Tabla", "PagoIndividualCartera")->where("TipoAdjunto", config("constantes.SOPORTE_RECAUDO"))->delete();
        return redirect(config('constantes.RUTA')."PruebaCartera/".$idEstudio);
    }
    
    function listaCartera($idEstudio, $fechaCausar = false, $mes = false){
        
        $infoEstudio = Estudio::find($idEstudio);
        
        $ultimoDiaInsertado = DB::table("causacion")->max("fechaCausacion");
        if(!empty($ultimoDiaInsertado)){
            $fechaCausar = strtotime('+1 day', strtotime($ultimoDiaInsertado));
            $fechaCausar = date("Y-m-d", $fechaCausar);
            $mes = date("m", strtotime($ultimoDiaInsertado)) + 1;
        }else{
            $fechaCausar = "2018-01-01";//Y-M-d        
            $mes = $mes + 1;
        }      
        
        $listado = DB::select('SELECT
                                                causacion.fechaCausacion as fechaCausacion,
                                                causacion.seguro AS causadoSeguro,
                                                causacion.interesMora AS causadoInteresMora,
                                                causacion.interesCorriente AS causadoInteresCorriente,
                                                causacion.abonoCapital AS causadoAbonoCapital,
                                                pago.id as idPago,
                                                pago.pago,
                                                pago.saldoFavor AS saldoFavor,
                                                pago.fecha AS fechaPago,
                                                pago_balance.seguro AS pagadoSeguro,
                                                pago_balance.interesMora AS pagadoInteresMora,
                                                pago_balance.interesCorriente AS pagadoInteresCorriente,
                                                pago_balance.abonoCapital AS pagadoAbonoCapital,                                                
                                                balance.seguro AS balanceSeguro,
                                                balance.interesMora AS balanceInteresMora,
                                                balance.interesCorriente AS balanceInteresCorriente,
                                                balance.abonoCapital AS balanceAbonoCapital,
                                                balance.saldoCapital AS balanceSaldoCapital
                                            FROM
                                                causacion
                                            JOIN balance ON balance.idCausacion = causacion.id
                                            LEFT JOIN pago_balance ON pago_balance.idBalance = balance.id
                                            LEFT JOIN pago ON pago.id = pago_balance.idPago                                            
                                            WHERE causacion.idEstudio = '.$idEstudio.' order by causacion.fechaCausacion, pago.id ');
        
        $listadoPagos = Pago::where("idEstudio", $idEstudio)->get();        
        
        $pagosImpresos = [];
        $ultimaFecha = "";
        $ultimoIdPago = "";
        ?>            
<style>
    .buttonPersonalizado{
        text-decoration: none;
        background: #ccc;
        padding: 5px;
        border: 1px solid #838383;
        color: #000;
        font-weight: bold;
    }
</style>

<p style="width: 25%; text-align: center; display: inline-block"><strong>Tasa: </strong><?= $infoEstudio->Tasa ?></p>
<p style="width: 25%; text-align: center; display: inline-block"><strong>Plazo: </strong><?= $infoEstudio->Plazo ?></p>
<p style="width: 25%; text-align: center; display: inline-block"><strong>Cuota: </strong><?= number_format($infoEstudio->Cuota, 0, ",", ".") ?></p>
<p style="width: 24%; text-align: center; display: inline-block"><strong>Valor Credito: </strong><?= number_format($infoEstudio->ValorCredito, 0, ",", ".") ?></p>
<hr>

<a href="<?= config("constantes.RUTA")."PruebaCausar/$idEstudio/$fechaCausar" ?>" class="buttonPersonalizado">CAUSAR</a>
<a href="<?= config("constantes.RUTA")."causarRapido/$idEstudio/$mes" ?>" class="buttonPersonalizado">CAUSAR SIGUIENTE MES</a>
<a href="<?= config("constantes.RUTA")."BorrarTodo/$idEstudio" ?>" class="buttonPersonalizado">BORRAR TODO</a>

<form action="<?= config("constantes.RUTA")."PruebaPago/" ?>" method="GET" style="display: inline-block;float: right;">    
    <input name="idEstudio" value="<?= $idEstudio ?>" type="hidden">
    <input name="valorPago" type="text">
    <input type="submit" value="PAGAR">
</form>

<table align="center" border="1" cellpadding="5" cellspacing="0" width="100%" style="text-align: center">
    <body>
            <tr>
                <td colspan="5" style="background: #feee99">
                    CAUSACION
                </td>
                <td style="background: #9ab591" colspan="4">
                    PAGO
                </td>
                <td colspan="4" style="background: #b2cbd8">
                    PAGOS BALANCE
                </td>
                <td colspan="5" style="background: #f2f2f2">
                    BALANCE
                </td>
            </tr>            
            <tr>
                <td style="background: #feee99">fecha</td>
                <td style="background: #feee99">seguro</td>
                <td style="background: #feee99">Interes Mora</td>
                <td style="background: #feee99">Interes Corriente  </td>
                <td style="background: #feee99">Abono Capital  </td>
                
                <td style="background: #9ab591">id</td>
                <td style="background: #9ab591">F.Pago</td>
                <td style="background: #9ab591">valor</td>
                <td style="background: #89adc0">Saldo a Favor</td>
                
                <td style="background: #b2cbd8">Seguro  </td>
                <td style="background: #b2cbd8">Interes Mora  </td>
                <td style="background: #b2cbd8">Interes Corriente  </td>
                <td style="background: #b2cbd8">Abono Capital  </td>
                
                <td style="background: #f2f2f2">Seguro  </td>
                <td style="background: #f2f2f2">Interes Mora  </td>
                <td style="background: #f2f2f2">Interes Corriente  </td>
                <td style="background: #f2f2f2">Abono Capital  </td>
                <td style="background: #f2f2f2">Saldo Capital  </td>
            </tr>
        <?php foreach ($listado as $item):            
            if($ultimaFecha == date("m-d", strtotime($item->fechaCausacion))){
                $blank = true;
            }else{
                $ultimaFecha = date("m-d", strtotime($item->fechaCausacion));
                $blank = false;
            }
            
            if(is_null($item->idPago) || $ultimoIdPago == $item->idPago){
                $showPago = false;
            }else{
                $ultimoIdPago = $item->idPago;
                $pagosImpresos[] = $item->idPago;
                $showPago = true;
            }
            
            ?>
            <tr>
                <?php if($blank == false){ ?>
                <td style="background: #feee99" nowrap><?= date("m-d", strtotime($item->fechaCausacion))  ?></td>
                <td style="background: #feee99"><?= number_format($item->causadoSeguro, 2, ",", ".")  ?></td>
                <td style="background: #feee99"><?= number_format($item->causadoInteresMora, 3, ",", ".")  ?></td>
                <td style="background: #feee99"><?= number_format($item->causadoInteresCorriente, 3, ",", ".")  ?></td>
                <td style="background: #feee99"><?= number_format($item->causadoAbonoCapital, 3, ",", ".")  ?></td>
                <?php  }else{ ?>
                <td style="background: #feee99"><?= date("m-d", strtotime($item->fechaCausacion))  ?></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <?php  } ?>
                
                <td style="background: #9ab591"><?= $item->idPago  ?></td>
                <td style="background: #9ab591"><?= ($showPago)? date("m-d", strtotime($item->fechaPago)) : ""  ?></td>
                <td style="background: #9ab591"><?= ($showPago)? number_format($item->pago, 3, ",", ".") : ""  ?></td>                
                <td style="background: #89adc0"><?= ($showPago)? number_format($item->saldoFavor, 3, ",", ".") : ""  ?></td>
                
                <td style="background: #b2cbd8"><?= number_format($item->pagadoSeguro, 3, ",", ".")  ?></td>
                <td style="background: #b2cbd8"><?= number_format($item->pagadoInteresMora, 3, ",", ".")  ?></td>
                <td style="background: #b2cbd8"><?= number_format($item->pagadoInteresCorriente, 3, ",", ".")  ?></td>
                <td style="background: #b2cbd8"><?= number_format($item->pagadoAbonoCapital, 3, ",", ".")  ?></td>
                
                <?php if($blank == false){ ?>
                <td style="background: #f2f2f2"><?= number_format($item->balanceSeguro, 3, ",", ".")  ?></td>
                <td style="background: #f2f2f2"><?= number_format($item->balanceInteresMora, 3, ",", ".")  ?></td>
                <td style="background: #f2f2f2"><?= number_format($item->balanceInteresCorriente, 3, ",", ".")  ?></td>
                <td style="background: #f2f2f2"><?= number_format($item->balanceAbonoCapital, 3, ",", ".")  ?></td>
                <td style="background: #f2f2f2"><?= number_format($item->balanceSaldoCapital, 3, ",", ".")  ?></td>
                <?php  }else{ ?>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <?php  } ?>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($listadoPagos as $pagosIndividuales): 
     
            if(in_array($pagosIndividuales->id, $pagosImpresos)){
                continue;
            } ?>
            <tr>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                <td style="background: #feee99"></td>
                
                <td style="background: #9ab591"><?= $pagosIndividuales->id  ?></td>
                <td style="background: #9ab591"><?= date("m-d", strtotime($pagosIndividuales->fecha)) ?></td>
                <td style="background: #9ab591"><?= number_format($pagosIndividuales->pago, 3, ",", ".")  ?></td>                
                <td style="background: #89adc0"><?= number_format($pagosIndividuales->saldoFavor, 3, ",", ".") ?></td>
                
                <td style="background: #b2cbd8"></td>
                <td style="background: #b2cbd8"></td>
                <td style="background: #b2cbd8"></td>
                <td style="background: #b2cbd8"></td>
                
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
                <td style="background: #f2f2f2"></td>
            </tr>
        <?php endforeach; ?>
    </body>
</table>


        <?php
        
    }  
}
