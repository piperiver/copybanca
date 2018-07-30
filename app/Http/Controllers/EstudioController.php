<?php

namespace App\Http\Controllers;

use App\Pagaduria;
use App\Parametro;
use App\SolicitudConsulta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ValoracionesController;
use Illuminate\Support\Facades\Auth;
use App\Obligacion;
use App\Estudio;
use App\TipoAdjunto;
use App\Valoracion;
use App\HuellaConsulta;
use App\ProcesoJuridico;
use App\EntidadBancaria;
use App\gestionObligaciones;
use App\log_gestionObligaciones;
use App\User;
use App\Adjunto;
use App\Ingresos_adicionales;
use App\RelacionObligaciones;
use App\LogAprobacion;
use DB;
use App\Librerias\UtilidadesClass;
use App\Librerias\ComponentAdjuntos;
use App\Librerias\FuncionesComponente;
use Illuminate\View\View;
use App\ProcesosJuridicos;
use App\Juicios;

class EstudioController extends Controller
{
    protected $formaAdjuntos = 'ESTAJ';

    protected  $estadosEstudio = [
                                                          "SAV"  => "GUARDADO",
                                                          "RAD"  => "RADICADO",
                                                          "NVI"  => "NO VIABLE",
                                                          "ING"  => "INGRESADO",
                                                          "VIA"  => "VIABLE",
                                                          "FIR"  => "FIRMADO",
                                                          "VIS"  => "VISADO",
                                                          "TES"  => "TESORERIA",
                                                          "PRT"  => "PROCESO TESORERIA",
                                                          "CAR"  => "CARTERA",
                                                          "NEG"  => "NEGADO",
                                                          "APR"  => "APROBADO",
                                                          "PEN"  => "PENDIENTE",
                                                          "BAN"  => "BANCO",
                                                          "PRE"  => "PRE APROBADO",
                                                          "COM"  => "Comite",
                                                          "DES"  => "DESISTIO",
                                                          "APR"  => "Aprobado"
                                                        ];



    function checkAdjuntosObligaciones($listObligaciones){
        $valoracionesController = new ValoracionesController();
        for($i = 0; $i < count($listObligaciones); $i++){
                //Variables para controlar la accion permitida al usuario en el cargue de el adjunto(CDD, PYS) y si tiene adjunto de tipo CDD
                    $listObligaciones[$i]->optionGestionObligacionesCDD = "showSol";
                    $listObligaciones[$i]->optionGestionObligacionesPYS = "showAll";
                    $listObligaciones[$i]->idAdjunto = 0;
                    $listObligaciones[$i]->tieneAdjuntos = false;

                    //Se consulta la informacion de la ultima gestion de la obligacion de las obligaciones de tipo CDD ejecutada y se permiten acciones dependiendo del estado en la que este
                    $gestionObligacionCDD = gestionObligaciones::where("id_obligacion", $listObligaciones[$i]->id)
                                                                                                ->where("tipoAdjunto", config("constantes.CERTIFICACIONES_DEUDA"))
                                                                                                ->whereIn("estado",[  config("constantes.GO_VENCIDA"),
                                                                                                                                    config("constantes.GO_RADICADA"),
                                                                                                                                    config("constantes.GO_PAGADA"),
                                                                                                                                    config("constantes.GO_SOLICITADA")])
                                                                                                ->orderBy("created_at", "DESC")->first();
                    if(isset($gestionObligacionCDD->id)){
                            $listObligaciones[$i]->tieneAdjuntos = true;
                            if($gestionObligacionCDD->estado == config("constantes.GO_RADICADA")){
                                $listObligaciones[$i]->optionGestionObligacionesCDD = "hidden";
                                $listObligaciones[$i]->idAdjunto = 1;
                            }elseif($gestionObligacionCDD->estado == config("constantes.GO_VENCIDA")){
                                $listObligaciones[$i]->optionGestionObligacionesCDD = "showSol";
                                $listObligaciones[$i]->idAdjunto = 1;
                            }elseif($gestionObligacionCDD->estado == config("constantes.GO_SOLICITADA")){
                                $listObligaciones[$i]->optionGestionObligacionesCDD = "showRad";
                            }elseif($gestionObligacionCDD->estado == config("constantes.GO_PAGADA")){
                                $listObligaciones[$i]->optionGestionObligacionesCDD = "hidden";
                                $listObligaciones[$i]->idAdjunto = 1;
                            }else{
                                $listObligaciones[$i]->optionGestionObligacionesCDD = "showSol";
                            }

                    }

                    //Se consulta la informacion de la ultima gestion de la obligacion de las obligaciones de tipo PYS ejecutada y se permiten acciones dependiendo del estado en la que este
                    $gestionObligacionPYS = gestionObligaciones::where("id_obligacion", $listObligaciones[$i]->id)
                                                                                                ->where("tipoAdjunto", config("constantes.PAZ_SALVO"))
                                                                                                ->whereIn("estado",[  config("constantes.GO_VENCIDA"),
                                                                                                                                    config("constantes.GO_RADICADA"),
                                                                                                                                    config("constantes.GO_SOLICITADA")])
                                                                                                ->orderBy("created_at", "DESC")->first();
                    if(isset($gestionObligacionPYS->id)){
                            $listObligaciones[$i]->tieneAdjuntos = true;
                            if($gestionObligacionPYS->estado == config("constantes.GO_RADICADA")){
                                $listObligaciones[$i]->optionGestionObligacionesPYS = "hidden";
                            }elseif($gestionObligacionPYS->estado == config("constantes.GO_SOLICITADA")){
                                $listObligaciones[$i]->optionGestionObligacionesPYS = "showRad";
                            }else{
                                $listObligaciones[$i]->optionGestionObligacionesPYS = "showAll";
                            }

                    }

                    //Se validan soportes de pago
                    $adjuntoSoportePago = Adjunto::where("idPadre", $listObligaciones[$i]->id)->where("Tabla", config("constantes.KEY_OBLIGACION"))->where("TipoAdjunto", config("constantes.SOPORTE_PAGO"))->get();
                    if(count($adjuntoSoportePago) > 0){
                        $listObligaciones[$i]->soportePago = count($adjuntoSoportePago);
                    }else{
                        $listObligaciones[$i]->soportePago = false;
                    }

                    //Se validan los pay y salvos
                    $adjuntoPazYSalvo = Adjunto::where("idPadre", $listObligaciones[$i]->id)->where("Tabla", config("constantes.KEY_OBLIGACION"))->where("TipoAdjunto", config("constantes.PAZ_SALVO"))->get();
                    if(count($adjuntoPazYSalvo) > 0){
                        $listObligaciones[$i]->pazSalvo = count($adjuntoPazYSalvo);
                    }else{
                        $listObligaciones[$i]->pazSalvo = 0;
                    }





                if(isset($listObligaciones[$i]->ValorInicial) && !empty($listObligaciones[$i]->ValorInicial) && isset($listObligaciones[$i]->SaldoActual) && !empty($listObligaciones[$i]->SaldoActual) && ((float)$listObligaciones[$i]->ValorInicial) >= ((float)$listObligaciones[$i]->SaldoActual) && ((float)$listObligaciones[$i]->ValorInicial) > 0){
                    $listObligaciones[$i]->PorcentajeDeuda = $listObligaciones[$i]->SaldoActual / $listObligaciones[$i]->ValorInicial * 100;
                    $listObligaciones[$i]->PorcentajeDeuda =number_format($listObligaciones[$i]->PorcentajeDeuda, 2, ",", ".");
                }else{
                    $listObligaciones[$i]->PorcentajeDeuda = 0;
                }

                $listObligaciones[$i]->SaldoActual = (isset($listObligaciones[$i]->SaldoActual) && !empty($listObligaciones[$i]->SaldoActual))?  number_format(((float) $listObligaciones[$i]->SaldoActual), 0,  ",", ".") : 0;
                $listObligaciones[$i]->ValorCuota = (isset($listObligaciones[$i]->ValorCuota) && !empty($listObligaciones[$i]->ValorCuota))?  number_format(((float) $listObligaciones[$i]->ValorCuota), 0,  ",", ".") : 0;
                $listObligaciones[$i]->ValorInicial = (isset($listObligaciones[$i]->ValorInicial) && !empty($listObligaciones[$i]->ValorInicial))?  number_format(((float) $listObligaciones[$i]->ValorInicial), 0,  ",", ".") : 0;
                $listObligaciones[$i]->SaldoMora = (isset($listObligaciones[$i]->SaldoMora) && !empty($listObligaciones[$i]->SaldoMora))?  number_format(((float) $listObligaciones[$i]->SaldoMora), 0,  ",", ".") : 0;

                if(!in_array($listObligaciones[$i]->tipoCuenta , $valoracionesController->cuentasCuotaVariable) && $listObligaciones[$i]->EstadoCuenta == "Al Día"){
                    $listObligaciones[$i]->TipoCuotaEstudio = "CuotaFija";
                }elseif(in_array($listObligaciones[$i]->tipoCuenta , $valoracionesController->cuentasCuotaVariable)){
                    $listObligaciones[$i]->TipoCuotaEstudio = "CuotaVariable";
                }else{
                    $listObligaciones[$i]->TipoCuotaEstudio = "false";
                }

                switch ($listObligaciones[$i]->EstadoCuenta) {
                    case "Al Día":
                            $listObligaciones[$i]->EstadoCuenta = "DÍA";
                            break;
                    case "En Mora":
                            $listObligaciones[$i]->EstadoCuenta = "MORA";
                            break;
                    case "Castigada":
                            $listObligaciones[$i]->EstadoCuenta = "CAST";
                            break;
                    case "PYS":
                            $listObligaciones[$i]->EstadoCuenta = "PYS";
                            break;
                    default :
                            $listObligaciones[$i]->EstadoCuenta = "NA";
                            break;
                }

            }
            return $listObligaciones;
    }
    function getDesprendiblesToObligaciones($listObligaciones){
        $listDesprendible= $listObligaciones->filter(function ($value, $key) {
                return $value->Desprendible == "S";
         });
            return $listDesprendible;
    }
     function getObligacionesNoDesprendible($listObligaciones){
        $listNoDesprendible= $listObligaciones->filter(function ($value, $key) {
                return $value->Desprendible == "N";
         });
            return $listNoDesprendible;
    }    
    /*
     * Funcion para desplegar la vista de estudio
     */
    function desplegarEstudio($idEstudio, $tipoAdjunto = false){
        $infoEstudio = Estudio::find($idEstudio);
        $parametrosCostos = Parametro::where('Tipo', 'COSTOS')->get();
        $parametrosArray = array();
        foreach ($parametrosCostos as $parametro){
            $parametrosArray[$parametro->Codigo] = $parametro->Valor;
        }
        if(!isset($infoEstudio) || $infoEstudio == false){
            return view('errors.101')->with("mensaje", "El estudio al que desea ingresar no existe");
        }
                
        if($infoEstudio->Estado == "ING" || $infoEstudio->Estado == "ing"){
            return redirect(config('constantes.RUTA')."GestionObligaciones/".$idEstudio);            
        }
        
        $idValoracion = $infoEstudio->Valoracion;
        $infoValoracion = Valoracion::find($idValoracion);
        
        if($infoValoracion != false){
            if (isset($infoValoracion->pagaduria_related))
            {
                $infoValoracion->pagaduria_id = Pagaduria::where('nombre', $infoValoracion->Pagaduria)->first()->id;
                $infoValoracion->save();
            }
            $pagaduria_id = $infoValoracion->pagaduria_id;
            $listObligacionesInhabilitadas = Obligacion::where("Valoracion", $idValoracion)->where("Estado", "Inhabilitado")->orderBy('NumeroObligacion', 'desc')->get();
            $listObligacionesOriginales= Obligacion::where("Valoracion", $idValoracion)->where("Estado", "Activo")->where('EstadoCuenta', "<>", "Cerrada")->orderBy('Compra', 'desc')->orderBy('EstadoCuenta', 'desc')->get();    
            $listObligacionesCerradas = Obligacion::where("Valoracion", $idValoracion)->where("Estado", "Activo")->where('EstadoCuenta',  "Cerrada")->orderBy('marca', 'desc')->get();                
            $listObligacionesCerradas = $this->ReemplazarCodigos($listObligacionesCerradas);
                                    
            //Se filtran las obligaciones en las que han tenido moras
            $entidadesDondeQuedoEnMora = $listObligacionesCerradas->filter(function ($value, $key) {
                                                return preg_match("/[1-9]+/" ,$value->comportamiento);            
                                            });
            
            // Se toman las entidades que quedaron en mora, se arma un array y se etiquetan como en mora o normal para agregar comportamiento a las obligaciones cerradas y se organizan
            $entidadesDondeQuedoEnMora = $entidadesDondeQuedoEnMora->toArray();
            $entidadesDondeQuedoEnMora = [];
            $idEntidadesDondeQuedoEnMora = array_column($entidadesDondeQuedoEnMora, 'id');                        
            
            for ($i = 0; $i < count($listObligacionesCerradas); $i++){
                if(in_array($listObligacionesCerradas[$i]->id, $idEntidadesDondeQuedoEnMora)){
                    $listObligacionesCerradas[$i]->ComportamientoEnt = "Mora";
                }else{
                    $listObligacionesCerradas[$i]->ComportamientoEnt = "Normal";
                }
            }
            
            $listObligacionesCerradas = $listObligacionesCerradas->sortBy('ComportamientoEnt');
            
            //Si encontro entidades donde ha tenido mora, lo pasamos a array y seleccionamos las columnas que necesitamos
            if(count($entidadesDondeQuedoEnMora) > 0){                
                $entidadesDondeQuedoEnMora = array_column($entidadesDondeQuedoEnMora, 'Entidad');                                
            }
                    
            $sumaComprasSaldo = $listObligacionesOriginales->where('Compra', "S")->sum("SaldoActual");
            $sumaComprasCuotas = $listObligacionesOriginales->where('Compra', "S")->sum("ValorCuota");            
                        
            //Valida en todas la obligaciones, cuales tiene o no adjuntos
            $listObligaciones = $this->checkAdjuntosObligaciones($listObligacionesOriginales);            
            
                        
            $listHuellas = HuellaConsulta::where("Valoracion", $idValoracion)->get();
            $listHuellasCont = count($listHuellas);
            
            $vistaProcesosJuridicos = $this->vistaProcesosJuridicos($idValoracion);
            
            //Se obtiene la informacion del usuario al que se le realizo la valoracion y tambien su edad
            $user = User::find($infoValoracion->Usuario);
            $user->edad = (isset($user->fecha_nacimiento) && !empty($user->fecha_nacimiento))? $this->getEdad($user->fecha_nacimiento) : "";
                                                 
            $utilidades = new UtilidadesClass();
            $bancosDisponibles = $utilidades->obtenerValorParametro("BANCODIS");
            $SMLV = $utilidades->obtenerValorParametro("SMLV");
            $tasaCredito = $utilidades->obtenerValorParametro("TASACR");
            $plazoCredito = $utilidades->obtenerValorParametro("PLAZOCR");
            $leyDocentes = $utilidades->obtenerValorParametro("DLEYDOC");
            $descuento1 = $utilidades->obtenerValorParametro("DESCUEN1");
            $descuento2 = $utilidades->obtenerValorParametro("DESCUEN2");
            $descuento3 = $utilidades->obtenerValorParametro("DESCUEN3");
            $descuento4 = $utilidades->obtenerValorParametro("DESCUEN4");            
            $EdadSeguro = $utilidades->obtenerValorParametro("SEGUEDAD");            
            $retiroForzoso = $utilidades->obtenerValorParametro("RETFOR");            
            $valorXmillon = (!is_null($infoEstudio->valorXmillon) && $infoEstudio->valorXmillon >= 0)? (int) $infoEstudio->valorXmillon : $utilidades->obtenerValorParametro("VALORSEG");            
            
            $ingresosAdicionales = $this->createTableIngresosAdicionales($infoEstudio->id);
            
            $comerciales = false;        
            $comercialSeleccionado = false;
            if(Auth::user()->perfil == config('constantes.PERFIL_ADMIN') || Auth::user()->perfil == config('constantes.PERFIL_OFICINA') || Auth::user()->perfil == config('constantes.PERFIL_ROOT')){            
                $comerciales = User::where("perfil", config('constantes.PERFIL_COMERCIAL'))->get();
                $comercialSeleccionado = (isset($infoValoracion->Comercial) && !is_null($infoValoracion->Comercial))? $infoValoracion->Comercial : false;
            } 
            
            $infoEstudio->Tasa = (!empty($infoEstudio->Tasa)) ? (float) $infoEstudio->Tasa : 0;
            $infoEstudio->Plazo = (!empty($infoEstudio->Plazo)) ? (int) $infoEstudio->Plazo : 0;
            $infoEstudio->Cuota = (!empty($infoEstudio->Cuota)) ? number_format($infoEstudio->Cuota, 0, ",", ".") : 0;            
            $infoEstudio->ValorCredito = (!empty($infoEstudio->ValorCredito)) ? number_format($infoEstudio->ValorCredito, 0, ",", ".") : 0;
            $infoEstudio->IngresoBase = (!empty($infoEstudio->IngresoBase)) ? number_format($infoEstudio->IngresoBase, 0, ",", ".") : 0;
            $infoEstudio->TotalEgresos = (!empty($infoEstudio->TotalEgresos)) ? number_format($infoEstudio->TotalEgresos, 0, ",", ".") : 0;
            $infoEstudio->ValorCompras = (!empty($infoEstudio->ValorCompras)) ? number_format($infoEstudio->ValorCompras, 0, ",", ".") : 0;
            $infoEstudio->Disponible = (!empty($infoEstudio->Disponible)) ? number_format($infoEstudio->Disponible, 0, ",", ".") : 0;
            $infoEstudio->VlrCuotaCompras = (!empty($infoEstudio->VlrCuotaCompras)) ? number_format($infoEstudio->VlrCuotaCompras, 0, ",", ".") : 0;
            $infoEstudio->AntiguedadMeses = (!empty($infoEstudio->AntiguedadMeses)) ?  $infoEstudio->AntiguedadMeses : 0;
            $infoEstudio->Cupo = (!empty($infoEstudio->Cupo)) ? number_format($infoEstudio->Cupo, 0, ",", ".") : 0;
            $infoEstudio->MesesRetiroForzoso = (!empty($infoEstudio->MesesRetiroForzoso)) ? (int) $infoEstudio->MesesRetiroForzoso : 0;
            $infoEstudio->Edad = (!empty($infoEstudio->Edad)) ? (int) $infoEstudio->Edad : 0;            
            $infoEstudio->MesesVigenciaSeguro = (!empty($infoEstudio->MesesVigenciaSeguro)) ? (int) $infoEstudio->MesesVigenciaSeguro : 0;
            $infoEstudio->PlazoMaximo = (!empty($infoEstudio->PlazoMaximo)) ? (int) $infoEstudio->PlazoMaximo : 0;
            $infoEstudio->CuotaMaxima = (!empty($infoEstudio->CuotaMaxima)) ? number_format($infoEstudio->CuotaMaxima, 0, ",", ".") : 0;
            $infoEstudio->CapDescuentoDesprendible = (!empty($infoEstudio->CapDescuentoDesprendible)) ? number_format($infoEstudio->CapDescuentoDesprendible, 0, ",", ".") : 0;
            $infoEstudio->GastoFijo = (!empty($infoEstudio->GastoFijo)) ? (float) $infoEstudio->GastoFijo : 0;
            $infoEstudio->Capacidad = (!empty($infoEstudio->Capacidad)) ? number_format($infoEstudio->Capacidad, 0, ",", ".") : 0;
            $infoEstudio->Saldo = (!empty($infoEstudio->Saldo)) ? number_format($infoEstudio->Saldo, 0, ",", ".") : 0;
            $infoEstudio->Desembolso = (!empty($infoEstudio->Desembolso)) ? number_format($infoEstudio->Desembolso, 0, ",", ".") : 0;                        
            $infoEstudio->DatosBeneficios = (isset($infoEstudio->DatosBeneficios) && !empty($infoEstudio->DatosBeneficios))?  (array) json_decode($infoEstudio->DatosBeneficios)  : false;
            $infoEstudio->DatosCostos = (isset($infoEstudio->DatosCostos) && !empty($infoEstudio->DatosCostos))?  (array) json_decode($infoEstudio->DatosCostos)  : false;
            
            //Si el estudio no tiene guardada la informacion de los bancos entonces toma la informacion general de los bancos
            if(!isset($infoEstudio->DatosBanco) || empty($infoEstudio->DatosBanco)){
                if(!empty($infoEstudio->Pagaduria)){
                    $bancosEncontrados = EntidadBancaria::where('Politica', 'like', "%".$infoEstudio->Pagaduria."%")->get();                      
                    if(count($bancosEncontrados) > 0){
                        $infoEstudio->DatosBanco = json_encode([
                                                                                                "bancoSeleccionado" => false,
                                                                                                "bancos" => $bancosEncontrados
                                                                                            ]);
                    }else{
                        $infoEstudio->DatosBanco = false;
                    }
                }else{
                    $infoEstudio->DatosBanco = false;
                }       
             }             
            
            
             
            $obligacionesCuotaFija = $listObligaciones->filter(function ($value, $key) {
                return $value->TipoCuotaEstudio == "CuotaFija";
             });
            $obligacionesCuotaVariable= $listObligaciones->filter(function ($value, $key) {
                return $value->TipoCuotaEstudio == "CuotaVariable";
             });
             
             $obligacionesCompradas = $listObligaciones->filter(function ($value, $key) {
                return ($value->Compra == "S" && $value->EstadoCuenta == "DÍA");
             });
             
             $totalCompras  = $obligacionesCompradas->sum(function ($obligacion) {
                 if($obligacion->TipoCuotaEstudio == "CuotaVariable"){
                     $cuota = str_replace(".", "", $obligacion->CuotasProyectadas);
                 }else{
                     $cuota = str_replace(".", "", $obligacion->ValorCuota);
                 }
                 return $cuota;
             });
             
             $sumaTotalCuotaFija = $obligacionesCuotaFija->sum(function ($obligacion) {
                    if(!empty($obligacion->ValorCuota) && $obligacion->ValorCuota > 0){
                        $cuota = str_replace(".", "", $obligacion->ValorCuota);
                    }elseif(!empty($obligacion->CuotasProyectadas) && $obligacion->CuotasProyectadas > 0){
                        $cuota = $obligacion->CuotasProyectadas;
                    }else{
                        $cuota = 0;
                    }
                 return $cuota;                  
             });
             
             $sumaTotalCuotaVariable = $obligacionesCuotaVariable->sum(function ($obligacion) {
                 return $obligacion->CuotasProyectadas;
             });
             
             $ComponentAdjuntos = new ComponentAdjuntos();
             $autorizacionConsulta = 0;
             if(count($ComponentAdjuntos->adjunto_exist($infoEstudio->Valoracion, config("constantes.MDL_VALORACION"), config("constantes.KEY_AUTORIZACION"), config("constantes.AUTORIZACION_DE_CONSULTA")))){
                 $autorizacionConsulta = 1;
             }else{
                $solicitud = SolicitudConsulta::where('valoracion_id',$infoEstudio->Valoracion)->first();    
                if($solicitud){
                    $autorizacionConsulta = 1;
                }    
             }             
             
             $arrayFaltaAdjunto = [];
//             if($tipoAdjunto != false){
//                 $sinAdjuntos = DB::select('SELECT DISTINCT(obligaciones.id), gestionobligaciones.estado from obligaciones LEFT JOIN gestionobligaciones 
//                                                                                                                                                    ON gestionobligaciones.id_obligacion = obligaciones.id 
//                                                                                                                                                    AND gestionobligaciones.tipoAdjunto = "'.$tipoAdjunto.'"                                                                                                                                                     
//                                                    WHERE obligaciones.Valoracion = '.$infoEstudio->Valoracion.' and obligaciones.Compra = "S"');
//                 foreach ($sinAdjuntos as $noAdjunto){
//                     if($noAdjunto->estado != "RAD"){
//                        $arrayFaltaAdjunto[]= $noAdjunto->id;
//                     }
//                 }                 
//             }            
            $adjuntosVisado = Adjunto::where("idPadre", $infoEstudio->id)->where("Tabla", config("constantes.KEY_ESTUDIO"))->where("Modulo", config("constantes.MDL_VALORACION"))->whereIn("TipoAdjunto", [config("constantes.SOLICITUD_VISADO"), config("constantes.VISADO")])->orderBy("TipoAdjunto")->get();
            $objFuncionesComponente = new FuncionesComponente();            
            $htmlVisado = $objFuncionesComponente->contruirTablaVisado($adjuntosVisado);
            $adjuntoVisado = count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.VISADO")));
            $adjuntoSolicitudVisado = count($ComponentAdjuntos->adjunto_exist($infoEstudio->id, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.SOLICITUD_VISADO")));
            $objectPaguduria =$infoValoracion->pagaduria_related;
            $parametrosArray['PROVECTUS'] = $objectPaguduria->provectus;
            $login = Auth::user();

            $aprobaciones = LogAprobacion::select("*")
                                            ->with(array('usuario' => function($query){
                                                $query->select('id','nombre','primerApellido','apellido','perfil');
                                            }))
                                            ->where('estudio_id',$infoEstudio->id)->get();
            $hoy = date("Y-m-d");
            $creat_at_estudio = $infoEstudio->created_at->format('Y-m-d');

            $datetime1 = new \DateTime($creat_at_estudio);
            $datetime2 = new \DateTime($hoy);
            $interval = date_diff($datetime1, $datetime2);
            $dias = $interval->format('%R%a');
            //dd($infoEstudio->created_at);
            return view('pages.Estudio.index')->with("infoValoracion", $infoValoracion)
                                                                    ->with("infoUser", $user)
                                                                    ->with("login", $login)
                                                                    ->with("aprobaciones", $aprobaciones)
                                                                    ->with("infoEstudio", $infoEstudio)
                                                                    ->with("dias", $dias)
                                                                    ->with("listObligaciones", $listObligaciones)
                                                                    ->with("listObligacionesInhabilitadas", $listObligacionesInhabilitadas)
                                                                    ->with("listObligacionesCerradas", $listObligacionesCerradas)                                                                    
                                                                    ->with("entidadesDondeQuedoEnMora", $entidadesDondeQuedoEnMora)
                                                                    ->with("idEntidadesDondeQuedoEnMora", $idEntidadesDondeQuedoEnMora)
                                                                    ->with("obligacionesCuotaVariable", $obligacionesCuotaVariable)                                                                    
                                                                    ->with("obligacionesCuotaFija", $obligacionesCuotaFija)                                                                                                                                       
                                                                    ->with("obligacionesCompradas", $obligacionesCompradas)
                                                                    ->with("totalCompras", $totalCompras)
                                                                    ->with("sumaTotalCuotaFija", $sumaTotalCuotaFija)                                                                                                                                       
                                                                    ->with("sumaTotalCuotaVariable", $sumaTotalCuotaVariable)                                                                                                                                       
                                                                    ->with("listHuellas", $listHuellas)
                                                                    ->with("listHuellasCount", $listHuellasCont)
                                                                    ->with("sumaComprasSaldo", number_format($sumaComprasSaldo, 0 , ",", "."))
                                                                    ->with("sumaComprasCuotas", number_format($sumaComprasCuotas, 0 , ",", "."))
                                                                    ->with("valorXmillon", $valorXmillon)                    
                                                                    ->with("parametros", json_encode([
                                                                                                                                "bancosDisponibles" => $bancosDisponibles,
                                                                                                                                "SMLV" => $SMLV,                                                                                                                                
                                                                                                                                "tasaCredito" => $tasaCredito,
                                                                                                                                "plazoCredito" => $plazoCredito,
                                                                                                                                "leyDocentes" => $leyDocentes, 
                                                                                                                                "descuento1" => $descuento1, 
                                                                                                                                "descuento2" => $descuento2, 
                                                                                                                                "descuento3" => $descuento3, 
                                                                                                                                "descuento4" => $descuento4]))
                                                                    
                                                                   
                                                                    ->with("componentsBootstrap", true)
                                                                    ->with('comercialSeleccionado', $comercialSeleccionado)
                                                                    ->with('comerciales', $comerciales)
                                                                    ->with("ingresosAdicionales", $ingresosAdicionales)
                                                                    ->with("EdadSeguro" , $EdadSeguro)                                                                 
                                                                    ->with("retiroForzoso" , $retiroForzoso)                                                                 
                                                                    ->with("vistaProcesosJuridicos", $vistaProcesosJuridicos)                                                                    
                                                                    ->with("adjuntoAutorizacionConsulta", $autorizacionConsulta)
                                                                    ->with("estadosEstudio", $this->estadosEstudio)
                                                                    ->with("arrayFaltaAdjunto", $arrayFaltaAdjunto)
                                                                    ->with("adjuntoVisado", $adjuntoVisado)
                                                                    ->with("adjuntoSolicitudVisado", $adjuntoSolicitudVisado)
                                                                    ->with("htmlVisado", $htmlVisado)
                                                                    ->with('pagaduria_object', $objectPaguduria)
                                                                    ->with('parametrosArray', json_encode($parametrosArray));
        }else{
            echo 'No existe tal valoracion';
        }
    }
    
    
     function getDataJuridico(Request $request){
        
        $response = $this->consultarCentralesDataJuridico($request->cedula);
        
        $objValoracionController = new ValoracionesController();
        $objValoracionController->idValoracion = $request->idValoracion;
        $objValoracionController->insertarProcesosJuridicos($response);
        $vista = $this->vistaProcesosJuridicos($request->idValoracion);
        return $vista;
    }
    
     function consultarCentralesDataJuridico($cedula){
        if(!isset($cedula)){
            return json_encode(["STATUS" => false, "mensaje" => "No fue posible obtener la identificacion del usuario"]);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,config('constantes.URL_DATAJURIDICO'));
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "cedula=".$cedula);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$valores = curl_exec ($ch);

        $Respuesta = json_decode($valores);

        return $Respuesta;
    }
    
    function vistaProcesosJuridicos($idValoracion){
        $listProcesosJuridicos = ProcesosJuridicos::where("idValoracion", $idValoracion)->orderBy("fechaConsulta", "DESC")->get();
        return view("pages.Estudio.procesosJuridicos")->with("listProcesosJuridicos", $listProcesosJuridicos);
    }



    function estudioTracking($idEstudio){
        $estudio = Estudio::find($idEstudio);
        return view("pages.Estudio.tracking")->with("Tracking", $estudio->audits()->get()->reverse());
    }

    function actualizarBancos(Request $request){
        $bancos = false;

        $infoEstudio = Estudio::find($request->idEstudio);

        $bancosEncontrados = EntidadBancaria::where('Politica', 'like', "%".$infoEstudio->Pagaduria."%")->get();
        if(count($bancosEncontrados) > 0){
            $bancos = json_encode([
                                                        "bancoSeleccionado" => false,
                                                        "bancos" => $bancosEncontrados,
                                                    ]);
        }

        echo json_encode($bancos);
    }
    function getEstadoValidaciones($obligacion){
        
    }
    function ReemplazarCodigos($lstObligaciones){
        $json = json_decode(config("tablasData"));
        $jsonSifin = json_decode(config("tablasSifin"));
        
        foreach ($lstObligaciones as $obligacion){
            /*
             * Tratando de obtener el estado de cuenta y el estado de pago
             */            
            if($obligacion->EstadoCuentaCodigo == 10){
                $obligacion->EstadoCuentaPagoNombre = (isset($json->tabla43->{$obligacion->EstadoCuentaCodigo}))? $json->tabla43->{$obligacion->EstadoCuentaCodigo} : "N/A";                
            }elseif($obligacion->EstadoPagoCodigo == 46 && $obligacion->FormaPagoCodigo == 3){
                $obligacion->EstadoCuentaPagoNombre = "PAGO JUR.";                
            }elseif($obligacion->EstadoPagoCodigo == 46 && $obligacion->FormaPagoCodigo != 3){
                $obligacion->EstadoCuentaPagoNombre = "PAGO VOL.";                
            }else{
                if(isset($json->tabla4->{$obligacion->EstadoPagoCodigo})){
                    $obligacion->EstadoCuentaPagoNombre = $json->tabla4->{$obligacion->EstadoPagoCodigo}->nombre;
                    $obligacion->EstadoCuentaPagoComportamiento = $json->tabla4->{$obligacion->EstadoPagoCodigo}->comportamiento;
                    $obligacion->EstadoCuentaPagoVigenteCerrada = $json->tabla4->{$obligacion->EstadoPagoCodigo}->vigenteCerrada;
                    $obligacion->EstadoCuentaPagoDescripcion = $json->tabla4->{$obligacion->EstadoPagoCodigo}->descripcion;                    
                }else{
                    $obligacion->EstadoCuentaPagoNombre =  "N/A";
                }
            }
            
            $obligacion->EstadoOrigenNombre = (isset($json->tabla44->{$obligacion->EstadoOrigenCodigo}))? $json->tabla44->{$obligacion->EstadoOrigenCodigo} : "N/A";
            $obligacion->EstadoPlasticoNombre = (isset($json->tabla42->{$obligacion->EstadoPlasticoCodigo}))? $json->tabla42->{$obligacion->EstadoPlasticoCodigo} : "N/A";            
            $obligacion->EstadoObligacionNombre = (isset($jsonSifin->tabla3->{$obligacion->EstadoObligacion}))? $jsonSifin->tabla3->{$obligacion->EstadoObligacion} : "N/A";                                                                                       
                    
        }
        return $lstObligaciones;
    }
    
    function calcularCreditos($cuota, $tasa, $plazo){

        $valorCreditoReal = $cuota * (  ( (pow(1+$tasa, $plazo))-1 ) / ( $tasa*(pow(1+$tasa, $plazo)) )  );
        $valorDesembolsoReal = $valorCreditoReal / 1.2;
        $costos = $valorCreditoReal - $valorDesembolsoReal;

        return  [
                        "valorCreditoReal" => $valorCreditoReal,
                        "valorDesembolsoReal" => $valorDesembolsoReal,
                        "costos" => $costos
                    ];    
    }
    
    function getEdad($fechaNacimiento){
        $pos = strpos($fechaNacimiento, "/");
        if($pos !== false){
            return "";
        }
        
        if(isset($fechaNacimiento) && !empty($fechaNacimiento)){
                $nacimiento = explode("-", $fechaNacimiento);
                
                //Valodacion para cuando no sea un annio
                if(((int) strlen($nacimiento[0])) < 4){
                    return "";
                }
                
                //Validacion para cuando no sea un mes
                if(((int) $nacimiento[1]) > 12){
                    return "";
                }
                
                $edad = date("Y")  -  $nacimiento[0];
                if(date("m") > $nacimiento[1]){
                    return $edad;
                }elseif(date("m") == $nacimiento[1] && date("d") >= $nacimiento[2]){
                    return $edad;
                }else{
                    return (($edad-1) <= 0)? "0" : ($edad-1);
                }
        }else{
            return "";
        }    
    }
   function getTotalSaldoComprasByObligaciones($obligaciones){
       //Se obtiene el valor total del saldo de las obligaciones que se compraran
        $listCompraObligaciones = $obligaciones->filter(function ($value, $key) {
                return $value->Compra == "S";
         });
         return $listCompraObligaciones->sum("SaldoActual");
   }
    function updatePago(Request $request){        
        $update = Obligacion::where("NumeroObligacion", $request->obligacion)->where("Valoracion", $request->valoracion)->update(["Compra" => $request->pago]);        
        
        $listObligaciones = Obligacion::where("Valoracion", $request->valoracion)->where("Estado", "Activo")->get();        
        //Se obtiene el total de el valor cuota de los desprendibles
        $listCompraDesprendible= $listObligaciones->filter(function ($value, $key) {
                return $value->Compra == "S" && $value->Desprendible == "S";
        });
        
        //la suma total del salgo de las obligaciones que se compraran
        $totalSaldoCompras = $this->getTotalSaldoComprasByObligaciones($listObligaciones);
        
        //Suma total de las compras del desprendible
        $totalCompraDesprendible = $listCompraDesprendible->sum("ValorCuota");
        
        echo json_encode(["totalCompraDesprendible" => number_format($totalCompraDesprendible, 0, ",", "."), "totalSaldoCompras" => number_format($totalSaldoCompras, 0, ",", ".") ]);        
    }
    function compraCartera(Request $request){
        
        if($request->pertenece != "false"){            
            $update = Obligacion::where("NumeroObligacion", $request->pertenece)->where("Valoracion", $request->valoracion)->update(["Entidad" => $request->entidad, "ValorCuota" => $request->cuota, "Desprendible" => "S"]);
            $update;
        }else{            
            $obligacion = new Obligacion;
            $obligacion->NumeroObligacion = "Est".time();
            $obligacion->Valoracion = $request->valoracion;
            $obligacion->Entidad = $request->entidad;
            $obligacion->ValorCuota = $request->cuota;            
            $obligacion->NumeroCuotasMora = 0;            
            $obligacion->Desprendible = "S";
            $obligacion->save();        
        }
        
        $listObligaciones = Obligacion::where("Valoracion", $request->valoracion)->where("Estado", "Activo")->get();            
        //$tbody["sumaSaldo"] = number_format($listObligaciones->sum('SaldoActual'), 0, ",", ".");
        $listObligaciones = $this->checkAdjuntosObligaciones($listObligaciones);
        $tbody["obligaciones"] = $this->tableObligaciones($listObligaciones);
        
        
        
        $listDesprendible = $this->getDesprendiblesToObligaciones($listObligaciones);
        $tbody["desprendibles"] = $this->tableDesprendible($listDesprendible);
        
        $listObligacionesNoDesprendibles = $this->getObligacionesNoDesprendible($listObligaciones);
        $tbody["optionDesprendible"] = $this->htmlOption($listObligacionesNoDesprendibles);
        
        echo json_encode($tbody);
    }
    function htmlOption($obligacionesNoDesprendible){
        $html = '<option value="-1">Seleccione una opción</option>';
        foreach($obligacionesNoDesprendible as $obligacion) {
            $html.=  "<option value='$obligacion->NumeroObligacion' data-entidad='$obligacion->Entidad' data-saldo='$obligacion->SaldoActual'>$obligacion->Entidad - $obligacion->SaldoActual</option>";
        }
        return $html;
    }
    function tableDesprendible($listDesprendible){
        $tbody = "";
        foreach($listDesprendible as $desprendible){
        $desprendible->ValorCuota = (isset($desprendible->ValorCuota) && !empty($desprendible->ValorCuota))? number_format($desprendible->ValorCuota, 0, ",", ".") : 0;
         $tbody.= " <tr>
                                <td>$desprendible->Entidad</td>
                                <td>$desprendible->ValorCuota</td>
                            </tr>";
        }
        return $tbody;        
    }
    function tableObligaciones($listObligaciones){
        $tbody = "";
        foreach($listObligaciones as $obligacion){    
            $checked = ($obligacion->Compra == 'S')? 'checked' : '';
            $tbody .= "<tr>
                               <td>
                                    $obligacion->Entidad
                               </td>
                               <td>
                                    <a href='#' id='Estado' name='Estado' class='inputEditable' data-type='text' data-pk='{valoracion: \"$obligacion->Valoracion\", obligacion: \"$obligacion->NumeroObligacion\" }' data-url='".config('constantes.RUTA')."/Estudio/updEntidadEstadoSaldo' data-title='Ingrese el Estado'>$obligacion->EstadoCuenta</a>
                               </td>
                               <td>
                                    <a href='#' id='SaldoActual' name='SaldoActual' data-inputclass='inputEditableMiles' class='inputEditable' data-type='text'  data-pk='{valoracion: \"$obligacion->Valoracion\", obligacion: \"$obligacion->NumeroObligacion\" }' data-url='".config('constantes.RUTA')."/Estudio/updEntidadEstadoSaldo' data-title='Ingrese el Saldo Actual'>$obligacion->SaldoActual</a>
                               </td>                               
                               <td><input type='checkbox' class='make-switch' data-size='small'  data-on-text='SI' data-off-text='NO' data-url=' ".config('constantes.RUTA') ."' data-obligacion='$obligacion->NumeroObligacion' data-valoracion='$obligacion->Valoracion'  $checked ></td>";
         
            
                $tbody .="<td><a class='pointer text-center' data-toggle='modal' data-target='#modalAdjunto $obligacion->NumeroObligacion ' id='Enlace $obligacion->NumeroObligacion '>Disponible</a></td>";                                        
                $tbody .= '<td><a class="color-negro deleteObligacion" title="Eliminar" data-valoracion="'.$obligacion->Valoracion.'" data-id="'.$obligacion->NumeroObligacion.'" data-url="'.config("constantes.RUTA").'"><span class="fa fa-trash fa-1x"></span></a></td>';
                $tbody .= "</tr>";
        }
        return $tbody;
    }
    function getTableCompras($idValoracion){
        $valoracionesController = new ValoracionesController();
        $listObligacionesOriginales = Obligacion::where("Valoracion", $idValoracion)->where("Estado", "Activo")->orderBy('EstadoCuenta', 'desc')->get();        
        
        $totalSumaCompras = 0;
        $htmlCompras = "";        
        
                foreach ($listObligacionesOriginales as $obligacion){
                    if($obligacion->Compra == "S" && $obligacion->EstadoCuenta ==  "Al Día"){
                       if(in_array($obligacion->tipoCuenta , $valoracionesController->cuentasCuotaVariable)){
                            //Obligaciones tipo cuota Variable
                            $cuota = $obligacion->CuotasProyectadas;                        
                        }else{                    
                            $cuota = $obligacion->ValorCuota;
                        }                
                    }else{
                        continue;
                    }

                    $htmlCompras.= "  
                            <tr>
                                <td>{$obligacion->Entidad}</td>
                                <td>".number_format($cuota, 0, ",", ".")."</td>
                            </tr>";
                                
                    $totalSumaCompras += $cuota;                
                }
           
        
        
        if(empty($htmlCompras)){
            $UtilidadesClass = new UtilidadesClass();
            $htmlCompleto = $UtilidadesClass->createMessage("No se han seleccionado obligaciones para comprar", "warning");
        }else{
            $htmlCompleto = '<table class="table table-striped table-hover text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ENTIDAD</th>
                                                    <th class="text-center">CUOTA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            '.$htmlCompras.'
                                            </tbody>
                                        </table>';            
        }
        
        
        
        return ["html" => $htmlCompleto, "totalCompras" => number_format($totalSumaCompras, 0, ",", ".")];
    }
    function updEntidadEstadoSaldo(Request $request){                
        $resultCompras = false;
        $htmlCuotaFija = false;
        if($request->name == "Entidad"){
            $update = Obligacion::where("id", $request->pk["obligacion"])->update(["Entidad" => $request->value]);            
        }elseif($request->name == "Estado"){
            $valor = ($request->value == "DÍA")? "Al Día" : (($request->value == "MORA")? "En Mora": (($request->value == "CAST")? "Castigada" : ""));
            $update = Obligacion::where("id", $request->pk["obligacion"])->update(["EstadoCuenta" => $valor]);
            
            if($valor == "Al Día"){
                $valoracionesController = new ValoracionesController();
                $infoObligacion = Obligacion::find($request->pk["obligacion"]);                     
                
                if(!in_array($infoObligacion->tipoCuenta , $valoracionesController->cuentasCuotaVariable)){
                    $proyectada = false;
                    if (!empty($infoObligacion->ValorCuota) && $infoObligacion->ValorCuota > 0) {
                        $cuota = number_format($infoObligacion->ValorCuota, 0, ",", ".");                                
                    }elseif (!empty($infoObligacion->CuotasProyectadas) && $infoObligacion->CuotasProyectadas > 0) {
                        $proyectada = true;
                        $cuota = number_format($infoObligacion->CuotasProyectadas, 0, ",", ".");
                    } else {
                        $cuota = 0;
                    }
                    $color = ($proyectada)? 'style="color:blue"' : "";
                    $htmlCuotaFija= ' <tr id="rowObligacionCuotaFija'.$infoObligacion->id.'">
                                                    <td>'.$infoObligacion->Entidad.'</td>
                                                    <td '.($color).' id="keyCuotaFija'. $infoObligacion->id .'" class="listObligacionCuotaFija">'.$cuota.'</td>
                                                </tr>';
                }
            }            
        }elseif($request->name == "SaldoActual"){
            $update = Obligacion::where("id", $request->pk["obligacion"])->update(["SaldoActual" => str_replace(".", "", $request->value)]);            
        }elseif($request->name == "ValorCuota"){            
            $update = Obligacion::where("id", $request->pk["obligacion"])->update(["ValorCuota" => str_replace(".", "", $request->value)]);                        
        }elseif($request->name == "Pago"){            
            $update = Obligacion::where("id", $request->pk["obligacion"])->update(["Compra" => $request->value]);                        
        } 
        
        
        
        
        
        
        $sumaComprasSaldo = 0;
        $sumaComprasCuotas = 0;
        if($request->name == "SaldoActual" || $request->name == "ValorCuota" || $request->name == "Pago"){
            $listObligaciones = Obligacion::where("Valoracion", $request->pk["valoracion"])->where("Estado", "Activo")->get();
            $sumaComprasSaldo = $listObligaciones->where('Compra', "S")->sum("SaldoActual");
            $sumaComprasCuotas = $listObligaciones->where('Compra', "S")->sum("ValorCuota");   
        }
        
        $resultCompras = $this->getTableCompras($request->pk["valoracion"]);  
        
        echo json_encode([                                        
                                        "valueCambio" => $request->value , 
                                        "nameCambio" => $request->name , 
                                        "idObligacion" => $request->pk["obligacion"], 
                                        //"htmlCuotaFija" => $htmlCuotaFija, //Para cuando se cambia el tipo de obligacion y se convierte en obligacion de cuota fija
                                        "sumaComprasSaldo"=>  number_format($sumaComprasSaldo, 0 , ",", "."), 
                                        "sumaComprasCuotas"=>  number_format($sumaComprasCuotas, 0 , ",", "."),
                                        //"infoCompras"=>   json_encode($resultCompras)
                                    ]);
        
    }

    function deleteObligacion(Request $request){
        $update = Obligacion::where("NumeroObligacion", $request->idObligacion)->where("Valoracion", $request->valoracion)->update(["Estado" => "Inhabilitado"]);        
        
        $listObligaciones = Obligacion::where("Valoracion", $request->valoracion)->where("Estado", "Activo")->get();                    
        //Se suma el Saldo Actual de todas las obligaciones
        $sumaSaldo = $listObligaciones->sum('SaldoActual');
        
        //Se obtiene el total de el valor cuota de los desprendibles
        $listCompraDesprendible= $listObligaciones->filter(function ($value, $key) {
                return $value->Compra == "S" && $value->Desprendible == "S";
        });
        
        //la suma total del saldo de las obligaciones que se compraran
        $totalSaldoCompras = $this->getTotalSaldoComprasByObligaciones($listObligaciones);
        
        //Se suma el valor de la cuota de las obligaciones del desprendible que se van a comprar
        $totalCompraDesprendible = $listCompraDesprendible->sum("ValorCuota");
        
        //se traen las obligaciones del deprendible
        $listDesprendible = $this->getDesprendiblesToObligaciones($listObligaciones);
        
        //se crea el html de la tabla desprendible
        $desprendibles = $this->tableDesprendible($listDesprendible);
        
        //
        $listObligacionesOriginales = $this->checkAdjuntosObligaciones($listObligaciones);
        $tablaObligaciones = $this->tableObligaciones($listObligacionesOriginales);
        
        $listObligacionesNoDesprendibles = $this->getObligacionesNoDesprendible($listObligaciones);
        $htmlOption = $this->htmlOption($listObligacionesNoDesprendibles);
        
        
        
        echo json_encode(["htmlDesprendibles" => $desprendibles, "tablaObligaciones"=> $tablaObligaciones, "sumaSaldo"=>  number_format($sumaSaldo, 0 , ",", "."), "totalCompraDesprendible" => number_format($totalCompraDesprendible, 0, ",", ".") , "htmlOption" => $htmlOption, "MENSAJE" => "Obligacion eliminada satisfactoriamente", "totalSaldoCompras" => number_format($totalSaldoCompras, 0, ",", ".")]);
    }
    
    /*
     * Funcion para desplegar el listado de todos los estudios radicados
     */
    function viewRadicacionEstudio(){
    
        $perfiles = [
                                config("constantes.PERFIL_OFICINA"),
                                config("constantes.PERFIL_ADMIN"),
                                config("constantes.PERFIL_ROOT")
                            ];
        if(in_array(Auth::user()->perfil, $perfiles)){
            $Estudios = DB::table('estudios')
                                                                ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                                                                ->join('users', 'valoraciones.Usuario', '=', 'users.id')                                                                
                                                                ->select("estudios.id as idEstudio", "estudios.Pagaduria as pagaduriaEstudio",  "estudios.*", "valoraciones.*", "users.id as idUsuario", "users.*", "estudios.estado as estadoEstudio")->
                                                                orderBy('estudios.id', 'DESC')
                                                                ->select('estudios.*',
                                                                         'valoraciones.Comercial',
                                                                         'users.id as idUsuario',
                                                                         'users.cedula',
                                                                         'users.nombre',
                                                                         'users.apellido'
                                                                        )
                                                                ->get();
        } elseif(Auth::user()->perfil == config("constantes.PERFIL_COMERCIAL")){
            $Estudios = DB::table('estudios')->where("valoraciones.comercial", Auth::user()->id)
                                                                ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                                                                ->join('users', 'valoraciones.Usuario', '=', 'users.id')                                                                
                                                                ->select("estudios.id as idEstudio", "estudios.Pagaduria as pagaduriaEstudio",  "estudios.*", "valoraciones.*", "users.id as idUsuario", "users.*", "estudios.estado as estadoEstudio")->
                                                                orderBy('estudios.id', 'DESC')
                                                                ->select('estudios.*',
                                                                         'valoraciones.Comercial',
                                                                         'users.id as idUsuario',
                                                                         'users.cedula',
                                                                         'users.nombre',
                                                                         'users.apellido'
                                                                        )->get();
        }else{
            return view('errors.401');
        }

         return view("pages.Estudio.list")->with("Estudios", $Estudios);
    }
    

    function viewAdjuntosEstudio($idEstudio, $idValoracion){
        if(!UtilidadesClass::ValidarAcceso($this->formaAdjuntos)){
            return view('errors.401');
        }
        
        if(!is_numeric($idEstudio)){
            return view('errors.101')->with("mensaje", "El identificador debe ser numerico");
        }
        
        $valEstudio = Estudio::find($idEstudio);
        if($valEstudio == false){
            return view('errors.101')->with("mensaje", "El código de estudio ingresado no existe");
        }
        
        $informacionTiposAdjuntos = [];
        $informacion = [];        
        $adjuntosGestionObligacionesOrdenado = [];
        $tiposAdjunto = TipoAdjunto::all();
        
        foreach($tiposAdjunto as $tipo){
            $informacionTiposAdjuntos[$tipo->Codigo] = $tipo->Descripcion;            
            
            if(in_array($tipo->Codigo, [config("constantes.SOL_CERTIFICACIONES_DEUDA"),
                                                        config("constantes.CERTIFICACIONES_DEUDA"),
                                                        config("constantes.AUT_CERTIFICACIONES_DEUDA"),
                                                        config("constantes.SOL_PAZ_SALVO"),
                                                        config("constantes.PAZ_SALVO")])){
                continue;
            
            }elseif($tipo->Codigo == config("constantes.SOPORTE_PAGO_CLIENTE")){
                $adjuntosQuery = DB::table('adjuntos')->select("adjuntos.*")->join("giroscliente", "giroscliente.id", "=", "adjuntos.idPadre")->where("giroscliente.Estudio", $idEstudio)->where("adjuntos.Tabla", "GirosCliente")->where("adjuntos.TipoAdjunto", $tipo->Codigo)->get();            
                if(count($adjuntosQuery) > 0){
                    foreach($adjuntosQuery as $adjuntoQuery){
                        $adjuntos []= [
                                            "created_at" => $adjuntoQuery->created_at,
                                            "updated_at" => $adjuntoQuery->updated_at,
                                            "NombreArchivo" => $adjuntoQuery->NombreArchivo,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "id" => $adjuntoQuery->id,
                                            "idPadre" => $adjuntoQuery->idPadre,
                                            "Tabla" => $adjuntoQuery->Tabla,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "usuario" => $adjuntoQuery->usuario,
                                        ];
                    }
                    
                                        
                }else{
                    continue;
                }
            }elseif($tipo->Codigo == config("constantes.SOPORTE_PAGO")){
                $adjuntosQuery = DB::table('adjuntos')->select("adjuntos.*")->join("obligaciones", "obligaciones.id", "=", "adjuntos.idPadre")->where("obligaciones.Valoracion", $idValoracion)->where("adjuntos.Tabla", "obligaciones")->where("adjuntos.TipoAdjunto", $tipo->Codigo)->get();            
                if(count($adjuntosQuery) > 0){
                    foreach($adjuntosQuery as $adjuntoQuery){
                        $adjuntos []= [
                                            "created_at" => $adjuntoQuery->created_at,
                                            "updated_at" => $adjuntoQuery->updated_at,
                                            "NombreArchivo" => $adjuntoQuery->NombreArchivo,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "id" => $adjuntoQuery->id,
                                            "idPadre" => $adjuntoQuery->idPadre,
                                            "Tabla" => $adjuntoQuery->Tabla,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "usuario" => $adjuntoQuery->usuario,
                                        ];
                    }
                    
                                        
                }else{
                    continue;
                }                
            }else{
                $adjuntosQuery = Adjunto::where("TipoAdjunto", $tipo->Codigo)->where("Tabla", "adjuntosEstudio")->where("Modulo", "VALO")->where("idPadre", $idEstudio)->get();               
                if(count($adjuntosQuery) > 0){                    
                    foreach($adjuntosQuery as $adjuntoQuery){
                        $adjuntos []= [
                                            "created_at" => $adjuntoQuery->created_at,
                                            "updated_at" => $adjuntoQuery->updated_at,
                                            "NombreArchivo" => $adjuntoQuery->NombreArchivo,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "id" => $adjuntoQuery->id,
                                            "idPadre" => $adjuntoQuery->idPadre,
                                            "Tabla" => $adjuntoQuery->Tabla,
                                            "Extension" => $adjuntoQuery->Extension,
                                            "usuario" => $adjuntoQuery->usuario,
                                        ];
                    }                                        
                }else{
                    continue;
                }  
            }           
            
            $adjuntosTMP = (count($adjuntos) > 0)? $adjuntos : false;
            $informacion[] = [
                "infoTipo" => [
                    "Codigo" => $tipo->Codigo,
                    "Descripcion" => $tipo->Descripcion
                ],
                "adjuntos" => $adjuntosTMP
            ];
            unset($adjuntos);
        }          
            
            $adjuntosGestionObligaciones = DB::select('SELECT
                                                                                                *
                                                                                            FROM
                                                                                                adjuntos
                                                                                            WHERE
                                                                                                adjuntos.id IN(
                                                                                                SELECT
                                                                                                    gestionobligaciones.id_adjuntoSolicitud
                                                                                                FROM
                                                                                                    gestionobligaciones
                                                                                                JOIN obligaciones ON obligaciones.id = gestionobligaciones.id_obligacion
                                                                                                WHERE
                                                                                                    obligaciones.Valoracion = '.$idValoracion.' AND gestionobligaciones.estado <> "CAN" 
                                                                                                UNION
                                                                                            SELECT
                                                                                                gestionobligaciones.id_adjunto
                                                                                            FROM
                                                                                                gestionobligaciones
                                                                                            JOIN obligaciones ON obligaciones.id = gestionobligaciones.id_obligacion
                                                                                            WHERE
                                                                                                obligaciones.Valoracion = '.$idValoracion.' AND gestionobligaciones.estado <> "CAN" 
                                                                                            ) order by adjuntos.TipoAdjunto');
                        
            if(count($adjuntosGestionObligaciones) > 0){
                  foreach ($adjuntosGestionObligaciones as $adjuntoGO){
                      $adjuntosGestionObligacionesOrdenado[$adjuntoGO->TipoAdjunto][] = $adjuntoGO;
                  }
            }
            
            $InfoUser = DB::table('estudios')->where("estudios.id", $idEstudio)
                                        ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                                        ->join('users', 'valoraciones.Usuario', '=', 'users.id')                                    
                                        ->select("users.*")->get();                    

            $ComponentAdjuntos = new ComponentAdjuntos();
            $adjuntoVisado = count($ComponentAdjuntos->adjunto_exist($idEstudio, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.VISADO")));
            $adjuntoSolicitudVisado = count($ComponentAdjuntos->adjunto_exist($idEstudio, config("constantes.MDL_VALORACION"), config("constantes.KEY_ESTUDIO"), config("constantes.SOLICITUD_VISADO")));
                
            $arrayIgnore = [config("constantes.AUTORIZACION_DE_CONSULTA"),
                                        config("constantes.CERTIFICACIONES_DEUDA"), 
                                        config("constantes.SOL_CERTIFICACIONES_DEUDA"), 
                                        config("constantes.AUT_CERTIFICACIONES_DEUDA"),
                                        config("constantes.SOL_PAZ_SALVO"),
                                        config("constantes.PAZ_SALVO"),                                                                                                                                                                                    
                                        config("constantes.SOPORTE_PAGO"), 
                                        config("constantes.INGRESOS_ADICIONALES"),
                                        config("constantes.FOTO_PERFIL"),
                                        config("constantes.SOPORTE_PAGO_CLIENTE"),
                                        config("constantes.SOPORTES_ADICIONALES")];
        
        
        if($adjuntoVisado > 0){
            $arrayIgnore[] = config("constantes.SOLICITUD_VISADO");
            $arrayIgnore[] = config("constantes.VISADO");
        }elseif($adjuntoSolicitudVisado > 0){
            $arrayIgnore[] = config("constantes.SOLICITUD_VISADO");
        }
        
        //------------------------------------- se obtiene el adjunto de la autorizacion de consulta que esta asociado a la valoracion a la cual pertenece el estudio
        $adjuntos = Adjunto::where("TipoAdjunto", "AUT")->where("Tabla", "autorizacionValoracion")->where("Modulo", "VALO")->where("idPadre", $idValoracion)->get();                
        return view('pages.Estudio.adjuntos')->with('informacion',$informacion)
                                                  ->with('usuario',$InfoUser[0])
                                                  ->with('forma',$this->formaAdjuntos)
                                                  ->with('autorizaciones', (count($adjuntos) > 0)? $adjuntos : false )                                                  
                                                  ->with('informacionTiposAdjuntos', $informacionTiposAdjuntos)
                                                  ->with('adjuntosGestionObligacionesOrdenado', $adjuntosGestionObligacionesOrdenado)
                                                  ->with('adjuntoVisado', $adjuntoVisado)
                                                  ->with('adjuntoSolicitudVisado', $adjuntoSolicitudVisado)
                                                  ->with('arrayIgnore', $arrayIgnore)
                                                  ->with('idEstudio',$idEstudio);
    }


    function actualizarCostos(Request $request){
        $estudio = Estudio::find($request->id);
        $estudio->DatosCostos = json_encode(['ajusteCostos' => $request->ajusteCostos, 'totalCostosV' => $request->totalCostosV ]);
        $estudio->save();
        return response()->json(['message'=>'Ajuste de costos actualizado con exito', 'totalCostosV'=> $request->totalCostosV]);
    }

    function addIngresosAdicionales(Request $request){
        $ingresos_adicionales = new Ingresos_adicionales;
        $ingresos_adicionales->tipo = $request->tipo;
        $ingresos_adicionales->valor = $request->valor;
        $ingresos_adicionales->id_estudio = $request->idEstudio;
        $ingresos_adicionales->save();
        
        $html = $this->createTableIngresosAdicionales($request->idEstudio);
        
        echo json_encode($html);
        
    }
    function createTableIngresosAdicionales($idEstudio){

        $ingresosAdicionales = DB::select("SELECT ingresos_adicionales.*, IFNULL(adjuntos.id, 'false') idAdjunto 
                                                                    FROM ingresos_adicionales 
                                                                    LEFT JOIN adjuntos ON ingresos_adicionales.id = adjuntos.idPadre   
                                                                                                        AND adjuntos.Tabla = '".config("constantes.KEY_ESTUDIO")."'     
                                                                                                        AND adjuntos.TipoAdjunto = '".config("constantes.INGRESOS_ADICIONALES")."' 
                                                                                                        AND adjuntos.Modulo = '".config("constantes.MDL_VALORACION")."'
                                                                    WHERE ingresos_adicionales.id_estudio = '$idEstudio'");     
                
        $modales = "";
        $totalIngresosAdicionales = 0;
        if(count($ingresosAdicionales) > 0){       
            
                    $html = '<table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Valor</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                    foreach ($ingresosAdicionales as $ingreso){
                        $totalIngresosAdicionales = $totalIngresosAdicionales + $ingreso->valor;
                        $html.= '
                                        <tr>
                                            <td>'.$ingreso->tipo.'</td>
                                            <td>'.number_format($ingreso->valor, 0, ",", ".").'</td>
                                            <td id="dsp_modal'.config("constantes.INGRESOS_ADICIONALES").'-'.$ingreso->id.'">';
                                            
                        if($ingreso->idAdjunto != "false"){
                            $html.=  '<a class="color-negro" title="Visualizar" href="'.config('constantes.RUTA').'visualizar/'.$ingreso->idAdjunto.'" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a>';
                        }else{                    
                            $html.= '<a class="pointer color-negro cerrarModalPadre"  data-toggle="modal" data-target="#modal'.config("constantes.INGRESOS_ADICIONALES").'-'.$ingreso->id.'"><span class="fa fa-arrow-up fa-1x color-negro" title="Adjuntar Certificado Ingreso Adicional"></span></a>';
                            $modales.= $this->createHtmlIngresosAdicionalesModal($ingreso->id);
                        }         
                        $html.= '</td>
                                    <td><a class="EliminarIngresoAdicional" data-ing="'.$ingreso->id.'" data-parent="'.$idEstudio.'" title="Eliminar"><span class="fa fa-trash fa-1x color-negro"></span></a></td>
                            </tr>';
                    }
                    $html.= '</tbody>
                                    </table>
                        ';
        }else{
            $html =  '<div class="alert alert-warning" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Mensaje:</strong>
                    <p>No se han ingresado ingresos adicionales</p>                    
                  </div>';
        }
        
        return ["html" => $html, "modales" => $modales, "totalIngresosAdicionales" => number_format($totalIngresosAdicionales, 0, ",", ".")];

    }
    
    function createHtmlIngresosAdicionalesModal($idIngresoAdicional){
        $componentsAdjuntos = new ComponentAdjuntos();        
        
        ob_start();       
        ?>
         <div class="modal fade modalesAdjuntosIngAdicionales" id="modal<?php echo config("constantes.INGRESOS_ADICIONALES")."-".$idIngresoAdicional ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Carga de Certificado Laboral</h4>            
                                    </div>
                                    <div class="modal-body">  
                                        <div class="row">
                                            <div class="col-md-12">                       
                                            <?php $componentsAdjuntos->dspFormulario($idIngresoAdicional, config("constantes.KEY_ESTUDIO"), config("constantes.INGRESOS_ADICIONALES"), config("constantes.MDL_VALORACION"), false, "function", "cambiarUpload") ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Terminar</button>        
                                    </div>
                                </div>
                            </div>
                        </div>
         <?php
      $modal = ob_get_clean();      
      return $modal;
    }
    
    function delIngresosAdicionales(Request $request){
        $ingresoAdicional = Ingresos_adicionales::find($request->idIngreso);
        $ingresoAdicional->delete();
        
        $adjunto = Adjunto::where("idPadre", $request->idIngreso)->where("Tabla", config("constantes.KEY_ESTUDIO"))->where("TipoAdjunto", config("constantes.INGRESOS_ADICIONALES"))->where("Modulo", config("constantes.MDL_VALORACION"))->get();
        
        if(isset($adjunto[0]->id)){
            if(file_exists(storage_path("adjuntos")."/".$adjunto[0]->id)){
                    unlink(storage_path("adjuntos")."/".$adjunto[0]->id);
            }            
            $archivo = Adjunto::find($adjunto[0]->id);
            $archivo->delete();
        }
        
        $html = $this->createTableIngresosAdicionales($request->idEstudio);        
        echo json_encode($html);

    }               
    function guardarMiniCalculadora(Request $request){
        $Estudio = Estudio::find($request->idEstudio);
        if(isset($Estudio->id)){
            $Estudio->IngresoBase = $request->ingreso;
            $Estudio->TotalEgresos = $request->egreso;
            $Estudio->Ley1527 = $request->ley1527;
            $Estudio->Cupo = $Estudio->ValorCompras + $request->cupo;
            $Estudio->Disponible = $request->cupo;
            $guardado = $Estudio->save();
            
            if($guardado){
                echo json_encode(["MENSAJE" => "Información Guardada Correctamente"]);
            }else{
                echo json_encode(["MENSAJE" => "No posible almacenar la Información, por favor intente de nuevo."]);
            }
        }
        
        
    }
    
    function validateCertificadosEnCompras($idValoracion){
        $componentsAdjuntos = new ComponentAdjuntos();    
        
        $result = DB::select('SELECT obligaciones.id as idObligacion, adjuntos.id as idAdjunto
                            FROM `obligaciones` 
                            LEFT JOIN adjuntos 
                            ON adjuntos.idPadre = obligaciones.id and adjuntos.Tabla = "adjuntosEstudio" 
                            AND adjuntos.TipoAdjunto = "CDD" and adjuntos.Modulo = "VALO" 
                            WHERE Valoracion = '.$idValoracion);
        
        $tienenAdjuntos = true;
        foreach ($result as $item){            
            if(is_null($item->idAdjunto) || !$item->idAdjunto){
                $tienenAdjuntos = false;
                break;
            }                        
        }
        return $tienenAdjuntos;        
    }

    function aprobarEstudio(Request $request){

        $datos = $request->all();

        $aprobacion = new LogAprobacion($datos);
        $aprobacion->save();

        $aprobacionesEstudio = LogAprobacion::select("*")
            ->with(array('usuario' => function($query){
                $query->select('id','nombre','primerApellido','apellido','perfil');
            }))
            ->where('estudio_id',$datos["estudio_id"])->get();

        $html = view('pages.Estudio.logAprobacion')
            ->with('aprobaciones',$aprobacionesEstudio)
            ->render();

        $login = Auth::user();

        if(count($aprobacionesEstudio) == 1){
            echo json_encode(["Message" => "El estudio fue aprobado por el usuario ".$login->nombre." ".$login->apellido,"table"=>$html,"bandera"=>"OFF"]);

        }elseif(count($aprobacionesEstudio) >= 2){
            $Estudio = Estudio::find($datos["estudio_id"]);
            $Estudio->aprobado = 1;
            $Estudio->save();
            echo json_encode(["Message" => "El estudio fue aprobado satisfactoriamente","table"=>$html,"bandera" =>"ON"]);
        }

    }
   
    function guardarEstudio(Request $request){        
            
                $datos = $request->all();   
                $listadoObligaciones = json_decode($datos["infoObligaciones"]);
                
                foreach($listadoObligaciones as $updateObligaciones){
                    $obligacion = Obligacion::find($updateObligaciones->id);
                    $obligacion->EstadoCuenta = ($updateObligaciones->EstadoCuenta == "DÍA")? "Al Día" : 
                                                                                    (($updateObligaciones->EstadoCuenta == "MORA")? "En Mora": 
                                                                                                                            (($updateObligaciones->EstadoCuenta == "CAST")? "Castigada" : ""));
                    $obligacion->SaldoActual = str_replace(".", "", $updateObligaciones->SaldoActual);
                    $obligacion->ValorCuota = str_replace(".", "", $updateObligaciones->ValorCuota);
                    $obligacion->Compra = $updateObligaciones->Compra;
                    $obligacion->save();        
                }
                
                
                $Estudio = Estudio::find($datos["idEstudio"]);
                if(isset($Estudio->id)){                    
                    /*
                     *Asignacion de datos para guardar en la BDD
                     */  
                    $valorCompras = (empty($datos["compras"]))? 0 : $datos["compras"];
                    $Estudio->TipoContrato = $datos["tipoContrato"];
                    $Estudio->MesesRetiroForzoso = (empty($datos["mesesRetiroForzoso"]))? 0 : $datos["mesesRetiroForzoso"];
                    $Estudio->Edad = (empty($datos["edad"]))? 0 : $datos["edad"];
                    $Estudio->FechaInicioContrato = $datos["fechaInicioContrato"];                    
                    $Estudio->AntiguedadMeses = (empty($datos["antiguedad"]))? 0 : $datos["antiguedad"];  
                    $Estudio->cargo = $datos["cargo"];
                    $Estudio->Seguro= $datos["seguroVida"];
                    $Estudio->MesesVigenciaSeguro= (empty($datos["mesesSeguroVida"]))? 0 : $datos["mesesSeguroVida"];
                    $Estudio->PlazoMaximo= (empty($datos["plazoMaximo"]))? 0 : $datos["plazoMaximo"];
                    $Estudio->CuotaMaxima= (empty($datos["cuotaMaxima"]))? 0 : $datos["cuotaMaxima"];
                    $Estudio->Disponible= (empty($datos["disponible"]))? 0 : $datos["disponible"];
                    $Estudio->ValorCompras= $valorCompras;
                    $Estudio->CapDescuentoDesprendible= (empty($datos["descuento"]))? 0 : $datos["descuento"];
                    $Estudio->GastoFijo= (empty($datos["gastoFijo"]))? 0 : $datos["gastoFijo"];
                    $Estudio->Capacidad= (empty($datos["capacidad"]))? 0 : $datos["capacidad"];                    
                    $Estudio->Tasa= (empty($datos["tasa"]))? 0 : $datos["tasa"];
                    $Estudio->Plazo= (empty($datos["plazo"]))? 0 : $datos["plazo"];
                    $Estudio->Cuota= (empty($datos["cuota"]))? 0 : $datos["cuota"];
                    $Estudio->ValorCredito= (empty($datos["valorCredito"]))? 0 : $datos["valorCredito"];
                    $Estudio->Saldo= (empty($datos["saldo"]))? 0 : $datos["saldo"];
                    $Estudio->Desembolso= (empty($datos["desembolso"]))? 0 : $datos["desembolso"];
                    $Estudio->Garantia= (empty($datos["garantia"]))? 0 : $datos["garantia"];
                    $Estudio->DatosCostos= (empty($datos["datosCostos"]))? "" : $datos["datosCostos"];
                    $Estudio->DatosBeneficios= (empty($datos["datosBeneficios"]))? "" : $datos["datosBeneficios"];                    
                    $Estudio->cuotaVisado = $datos["cuotaVisado"];
                    $Estudio->Estado = $datos["estado"];
                    $Estudio->costoSeguro = $datos["costoSeguro"];
                    $Estudio->valorXmillon = $datos["valorXmillon"];
                    if($request->accion == "VIAB"){                        
                        $Estudio->viabilizado = true;
                    }
                    if(!empty($datos["datosBanco"])){
                        $Estudio->DatosBanco = $datos["datosBanco"];
                    }
                    $Estudio->aprobado = (empty($datos["aprobado"]))? 0 : $datos["aprobado"];
                    
                    //Se adicionan los campos de la modal del calculo del cupo
                    $Estudio->IngresoBase = (empty($datos["ingreso"]))? 0 : $datos["ingreso"];
                    $Estudio->TotalEgresos = (empty($datos["egreso"]))? 0 : $datos["egreso"];
                    $Estudio->Ley1527 = (empty($datos["ley1527"]))? 0 : $datos["ley1527"];
                    $Estudio->Cupo = $valorCompras + ((empty($datos["cupo"]))? 0 : $datos["cupo"]);
                    
                    $return = $Estudio->save();
                    
                    $updateFechaNacimiento = DB::select('UPDATE users SET  fecha_nacimiento= "'.$datos["fechaNacimiento"].'" 
                                                                WHERE users.id in(SELECT valoraciones.Usuario FROM valoraciones WHERE valoraciones.id = '.$Estudio->Valoracion.')');
                    
                    if($return){
                        /*if(isset($request->lstObligacionesComprar)){
                            $compras = explode(",", $request->lstObligacionesComprar);
                            $update_Compras = DB::table('obligaciones')->whereIn('id', $compras)->update(["Compra" => "S"]);                            
                        }
                        if(isset($request->lstObligacionesNoComprar)){
                            $noCompras = explode(",", $request->lstObligacionesNoComprar);
                            $update_no_Compras = DB::table('obligaciones')->whereIn('id', $noCompras)->update(["Compra" => "N"]);                    
                        }
                        if(isset($request->nuevosSaldos)){
                            $arrayObjectSaldos = explode(",", $request->nuevosSaldos);
                            foreach ($arrayObjectSaldos as $object){
                                $infoObligacion = explode("[{-}]", $object);
                                $updateSaldos = Obligacion::where("id", $infoObligacion[0])->update(["SaldoActual" => $infoObligacion[1]]);
                            }
                        }*/
                        echo json_encode(["STATUS" => true, "Message" => "El estudio fue guardado satisfactoriamente"]);
                    }else{
                        echo json_encode(["STATUS" => false, "Message" => "Ocurrio un problema al intentar guardar el estudio. Intente de nuevo"]);
                    }
                }else{
                    echo json_encode(["STATUS" => false, "Message" => "No es posible almacenar la informacion, ya que el estudio no existe"]);
                }        
    }
    function negarEstudio(Request $request){
        $result = Estudio::where("id", $request->idEstudio)->update(["estado" => "NEG"]);
        if($result){
            echo json_encode(["STATUS" => true]);
        }else{
            echo json_encode(["STATUS" => false]);
        }
    }
    function EliminarAdjuntos(Request $request){        
        $gestionObligacion = gestionObligaciones::find($request->infoAdjunto["id"]);
        $gestionObligacion->estado = "CAN";
        $result = $gestionObligacion->save();        
        if($result){
            $this->guardarLogGestionObligaciones($request->infoAdjunto["id"], $request->infoAdjunto["id_obligacion"], $request->infoAdjunto["tipoAdjunto"], 
                                                                       $request->infoAdjunto["id_adjunto"], $request->infoAdjunto["fechaSolicitud"], $request->infoAdjunto["fechaEntrega"], 
                                                                       $request->infoAdjunto["fechaRadicacion"], $request->infoAdjunto["fechaVencimiento"], config("constantes.GO_CANCELADA"));
            echo json_encode(["STATUS" => true, "Message" => "Se ha cancelado el documento correctamente"]);
        }else{
            echo json_encode(["STATUS" => false, "Message" => "No fue posible cancelar el documento. por favor recargue la pagina e intente de nuevo"]);
        }
    }
    function radicarCertificacionDeuda($request){
        $estado = "RAD";
        $tipoAdjunto = config("constantes.CERTIFICACIONES_DEUDA");
       
        $gestionObligaciones = gestionObligaciones::where("estado", config("constantes.GO_SOLICITADA"))
                                                                                ->where("id_obligacion", $request->idObligacion)
                                                                                ->where("tipoAdjunto", $tipoAdjunto)->get();        
        
        if(count($gestionObligaciones) > 0){
            $idGestionObligacion = $gestionObligaciones[0]->id;        
            $result = $gestionObligaciones[0]->update(["estado" => $estado, 
                        "id_adjunto" => $request->idAdjunto, 
                        "fechaRadicacion" => $request->fechaRadicacion, 
                        "fechaVencimiento"=> $request->fechaVencimiento]);        
            if($result){
                 if(!is_null($request->valorCertificado)){
                    $obligacion = Obligacion::find($request->idObligacion);
                    $obligacion->SaldoActual = str_replace(".", "", $request->valorCertificado);
                    $obligacion->save();
                }
                
                $this->guardarLogGestionObligaciones($idGestionObligacion, $request->idObligacion, $tipoAdjunto, $request->idAdjunto, false, false, $request->fechaRadicacion, $request->fechaVencimiento, $estado);
                $objFuncionesComponente = new FuncionesComponente();            
                $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion);
                echo json_encode(["STATUS" => true, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
                die;
            }else{
                $this->borrarAdjunto($request->idAdjunto);
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas (no actualizo el estado), por favor refresque la pagina e intentelo de nuevo ".__LINE__]);
                die;
            }
        }else{            
            $this->borrarAdjunto($request->idAdjunto);
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas (no existe en gestion obligacion), por favor refresque la pagina e intentelo de nuevo ".__LINE__]);
            die;
        }
    }
    function radicarPazAndSalvo($request){
        $estado = "RAD";
        $tipoAdjunto = config("constantes.PAZ_SALVO");
        
        $utilidadesClass = new UtilidadesClass();
        $tienePazSalvo = $utilidadesClass->validaAdjunto($request->idObligacion, config("constantes.KEY_OBLIGACION"), config("constantes.SOPORTE_PAGO"));        
        
        $gestionObligaciones = gestionObligaciones::where("estado", config("constantes.GO_SOLICITADA"))
                                                                                ->where("id_obligacion", $request->idObligacion)
                                                                                ->where("tipoAdjunto", $tipoAdjunto)->get();
        
        if(count($gestionObligaciones) > 0){
            $idGestionObligacion = $gestionObligaciones[0]->id;        
            $result = $gestionObligaciones[0]->update(["estado" => $estado, 
                        "id_adjunto" => $request->idAdjunto, 
                        "fechaRadicacion" => $request->fechaRadicacion]);        
            
            if($result){
                if($tienePazSalvo == false){
                    $obligacion = Obligacion::find($request->idObligacion);
                    $obligacion->Compra = "N";
                    $obligacion->ValorCuota = 0;
                    $obligacion->SaldoActual = 0;
                    $obligacion->EstadoCuenta = "PYS";
                    $obligacion->save();
                }

                    $this->guardarLogGestionObligaciones($idGestionObligacion, $request->idObligacion, $tipoAdjunto, $request->idAdjunto, false, false, $request->fechaRadicacion, false, $estado);
                    $objFuncionesComponente = new FuncionesComponente();            
                    $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion);            
                echo json_encode(["STATUS" => true, "tienePazSalvo" => $tienePazSalvo, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);                
                die;
            }else{
                $this->borrarAdjunto($request->idAdjunto);
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas (no actualizo el estado), por favor refresque la pagina e intentelo de nuevo ".__LINE__]);
                die;
            }
        }else{            
            $gestionObligaciones = new gestionObligaciones;
            $gestionObligaciones->id_obligacion = $request->idObligacion;
            $gestionObligaciones->tipoAdjunto = $tipoAdjunto;
            $gestionObligaciones->estado = $estado;
            $gestionObligaciones->id_adjunto = $request->idAdjunto;
            $gestionObligaciones->fechaRadicacion = $request->fechaRadicacion;
            $resultInsert = $gestionObligaciones->save();
             if($resultInsert){
                 if($tienePazSalvo == false){
                    $obligacion = Obligacion::find($request->idObligacion);
                    $obligacion->Compra = "N";
                    $obligacion->EstadoCuenta = "PYS";
                    $obligacion->save();
                 }                       
                    $this->guardarLogGestionObligaciones($gestionObligaciones->id, $request->idObligacion, $tipoAdjunto, $request->idAdjunto, false, false, $request->fechaRadicacion, false, $estado);
                    $objFuncionesComponente = new FuncionesComponente();            
                    $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion);
                    echo json_encode(["STATUS" => true, "tienePazSalvo" => $tienePazSalvo, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
                    die;
                }else{
                    echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas(No creo el dato) , por favor refresque la pagina e intentelo de nuevo"]);
                }   
        }
    }
    function guardarFechasRadicacion(Request $request){
        if($request->estado == "CRAD"){ 
            $this->radicarCertificacionDeuda($request);
        }elseif($request->estado == "PRAD"){
            $this->radicarPazAndSalvo($request);
        }       
    }
    function guardarLogGestionObligaciones($idGestionObligacion, $idObligacion = false, $tipoAdjunto = false, $idAdjunto = false, $fechaSolicitud = false, $fechaEntrega = false, $fechaRadicacion = false, $fechaVencimiento = false, $estado = false, $idAdjuntoSolicitud = false){
        
        $idObligacion = (!empty($idObligacion) && $idObligacion != false)? $idObligacion : null;
        $tipoAdjunto = (!empty($tipoAdjunto) && $tipoAdjunto != false)? $tipoAdjunto : null;
        $idAdjunto = (!empty($idAdjunto) && $idAdjunto != false)? $idAdjunto : null;        
        $fechaSolicitud = (!empty($fechaSolicitud) && $fechaSolicitud != false)? $fechaSolicitud : null;
        $fechaEntrega = (!empty($fechaEntrega) && $fechaEntrega != false)? $fechaEntrega : null;
        $fechaRadicacion = (!empty($fechaRadicacion) && $fechaRadicacion != false)? $fechaRadicacion : null;
        $fechaVencimiento = (!empty($fechaVencimiento) && $fechaVencimiento != false)? $fechaVencimiento : null;
        $estado = (!empty($estado) && $estado != false)? $estado : null;
        
        $logGestionObligaciones = new log_gestionObligaciones;
        $logGestionObligaciones->id_gestionObligacion = $idGestionObligacion;
        $logGestionObligaciones->id_obligacion = $idObligacion;        
        $logGestionObligaciones->tipoAdjunto = $tipoAdjunto;
        $logGestionObligaciones->id_adjunto = $idAdjunto;
        $logGestionObligaciones->fechaSolicitud = $fechaSolicitud;
        $logGestionObligaciones->fechaEntrega = $fechaEntrega;
        $logGestionObligaciones->fechaRadicacion = $fechaRadicacion;
        $logGestionObligaciones->fechaVencimiento = $fechaVencimiento;
        $logGestionObligaciones->estado = $estado;
        if(!empty($idAdjuntoSolicitud) && $idAdjuntoSolicitud != false){
            $logGestionObligaciones->id_adjuntoSolicitud = $idAdjuntoSolicitud;
        }
        $logGestionObligaciones->usuario = Auth::user()->id;
        return  $logGestionObligaciones->save();
    }
    function borrarAdjunto($idAdjunto){
        if(file_exists(storage_path("adjuntos")."/".$idAdjunto)){
                $eliminado = unlink(storage_path("adjuntos")."/".$idAdjunto);
            }else{
                $eliminado = false;
            }
            
            if($eliminado){
                $archivo = Adjunto::find($idAdjunto);
                $archivo->delete();
            }
    }
    
    function GuardarFechas(Request $request){        
        if($request->estado == "CSOL"){ 
            $estado = "SOL";
            $tipoAdjunto = config("constantes.CERTIFICACIONES_DEUDA");
        }elseif($request->estado == "PSOL"){
            $estado = "SOL";
            $tipoAdjunto = config("constantes.PAZ_SALVO");        
        }else{
            $estado = "NSL";
        }
        
        $gestionObligaciones = new gestionObligaciones;   
        $gestionObligaciones->id_obligacion = $request->idObligacion;
        $gestionObligaciones->fechaSolicitud = $request->fechaSolicitud;
        $gestionObligaciones->fechaEntrega = $request->fechaEntrega;
        $gestionObligaciones->estado = $estado;
        $gestionObligaciones->tipoAdjunto = $tipoAdjunto;
        $gestionObligaciones->id_adjuntoSolicitud = $request->idAdjunto;
        $result = $gestionObligaciones->save();
        
        if($result){
            $this->guardarLogGestionObligaciones($gestionObligaciones->id, $request->idObligacion, $tipoAdjunto, false, $request->fechaSolicitud, $request->fechaEntrega, false, false, $estado, $request->idAdjunto);
            $objFuncionesComponente = new FuncionesComponente();            
            $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion);
            
            echo json_encode(["STATUS" => true, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
        }else{
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas, por favor refresque la pagina e intentelo de nuevo"]);
        }
            
    }
    
    function cambiarEstado(Request $request){
        $estudio = Estudio::find($request->idEstudio);
        $estudio->Estado = $request->estado;
        echo $estudio->save();
    }
    
    function migrarAdjuntos(){
        set_time_limit(0);
        $Adjuntos = Adjunto::where("Tabla", "AdjuntosValoracion")->where("TipoAdjunto", "<>", "AUT")->get();
        
        $arrayEstudiosOK = [];
        $arrayEstudiosNO = [];
        $arrayNN = [];
        foreach ($Adjuntos as $adjunto){
            $estudio = Estudio::where("Valoracion", $adjunto->idPadre)->get();
            
            if(isset($estudio[0]->id)){
                $update = Adjunto::where("id", $adjunto->id)->update(["idPadre" => $estudio[0]->id, "Tabla" => "adjuntosEstudio"]);                                        
                if($update){
                    $arrayEstudiosOK[] = ["Valoracion" => $estudio[0]->Valoracion, "Estudio" => $estudio[0]->id];
                }else{
                    $arrayEstudiosNO[] = ["Valoracion" => $estudio[0]->Valoracion, "Estudio" => $estudio[0]->id];
                }
            }else{
                $arrayNN[] = $adjunto;
            }
            
        }
        
        echo '<pre>';
        echo '<h1>Estudios Actualizados satisfactoriamente</h1>';
        print_r($arrayEstudiosOK);
        echo '</pre>';
        
        echo '<pre>';
        echo '<h1>Estudios de Actualizacion fallida</h1>';
        print_r($arrayEstudiosNO);
        echo '</pre>';
        
        echo '<pre>';
        echo '<h1>NN</h1>';
        print_r($arrayNN);
        echo '</pre>';
    }

    function cargarFormulario(Request $request){

    }
    function calcularCupo(Request $request){
        $valoracion = Estudio::find($request->estudio)->valoracion;
        if (!count($valoracion->pagaduria_related))
        {
            $valoracion->pagaduria_id = Pagaduria::where('nombre', $valoracion->Pagaduria)->first()->id;
            $valoracion->save();
        }

        return response()->json($valoracion->pagaduria_related->calcularCupo($request->ingreso,$request->egreso));
    }
}
