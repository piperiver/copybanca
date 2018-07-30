<?php

namespace App\Http\Controllers;

use FontLib\TrueType\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\gestionObligaciones;
use App\log_gestionObligaciones;
use App\Estudio;
use App\Valoracion;
use App\Causacion;
use App\Adjunto;
use App\EntidadBancaria;
use App\Pago;
use App\pagoBalance;
use App\Balance;
use App\LogCertificaciones;
use App\Librerias\UtilidadesClass;
use App\Librerias\CifrasEnLetras;
use DB;
use PDF;
use Excel;
use DateTime;
use App\Librerias\ComponentAdjuntos;
use App\Librerias\FuncionesComponente;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CarteraController extends Controller {

    protected $forma = "CAR";//Variable para controlar los permisos sobre la vista
    
    /*
     * Variable con las pagadurias configuradas para el pago masivo. Se requiere el nombre y el nit de cada una
     */
    var $pagadurias_PagoMasivo = [
        0 => [
            "NOMBRE" => "SEM CALI",
            "NIT" => "890399011-3"
        ],
        1 => [
            "NOMBRE" => "FODE VALLE",
            "NIT" => "890399029-5"
        ]
    ];

    /*
     * Esta funcion retorna la vista general de la cartera
     */
    function dspLista() {

        if (!UtilidadesClass::ValidarAcceso($this->forma)) {
            return view('errors.401');
        }
        
        //Obtenemos el listado de estudios
        $EstudiosCartera = DB::select("SELECT estudios.id as idEstudio , estudios.Pagaduria as pagaduriaEstudio,  estudios.*,  users.id as idUsuario, CONCAT(users.nombre, ' ', users.apellido) as nombre, users.cedula as cedula, estudios.cuota as cuota, valoraciones.id as idVal,
                                       (SELECT saldoCapital FROM balance where balance.idEstudio = estudios.id order by balance.id desc limit 1) as saldoCapital                                            
                                       FROM estudios
                                       JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                       JOIN users ON valoraciones.Usuario = users.id                                       
                                       WHERE estudios.Estado IN('" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')                                       
                                       ORDER BY FIELD(estudios.Estado, '" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')");
        
        //Se seleccionan datos generales de la cartera para ver como esta
        $datosCartera = DB::select("SELECT id,ValorCredito, Cuota, Estado 
                                        FROM estudios
                                        WHERE Estado IN('" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')");
        $sumatoriaCapital = 0;
        foreach ($EstudiosCartera as $estudioSaldo) {
            if ($estudioSaldo->saldoCapital == NULL) {
                $sumatoriaCapital = $sumatoriaCapital + $estudioSaldo->ValorCredito;
            } else {
                $sumatoriaCapital = $sumatoriaCapital + $estudioSaldo->saldoCapital;
            }
        }

        $contSumatoriaCartera = count($datosCartera);
        $SumatoriaCartera = collect($datosCartera)->sum('ValorCredito');

        $CarteraVigente = collect($datosCartera)->whereIn('Estado', [config("constantes.ESTUDIO_PROCESO_TESORERIA"), config("constantes.ESTUDIO_CARTERA")]);
        $contSumatoriaCarteraVigente = count($CarteraVigente);
        $SumatoriaCarteraVigente = collect($CarteraVigente)->sum('ValorCredito');

        $contRecaudoVigente = count($CarteraVigente);
        $SumatoriaRecaudo = collect($CarteraVigente)->sum('Cuota');

        $RetornaSumatoriaCartera = (isset($SumatoriaCartera)) ? $SumatoriaCartera : 0;
        $RetornaSumatoriaCarteraVigente = (isset($SumatoriaCarteraVigente)) ? $SumatoriaCarteraVigente : 0;
        $RetornaSumatoriaRecaudo = (isset($SumatoriaRecaudo)) ? $SumatoriaRecaudo : 0;

        return view('pages.Cartera.list')->with("EstudiosCartera", $EstudiosCartera)
                        ->with("SumatoriaCartera", $RetornaSumatoriaCartera)
                        ->with("contSumatoriaCartera", $contSumatoriaCartera)
                        ->with("SumatoriaCarteraVigente", $RetornaSumatoriaCarteraVigente)
                        ->with("contCarteraVigente", $contRecaudoVigente)
                        ->with("SumatoriaRecaudo", $RetornaSumatoriaRecaudo)
                        ->with("contRecaudoVigente", $contRecaudoVigente)
                        ->with("sumatoriaCapital", $sumatoriaCapital);
    }
    
    /*
     * Esta funcion retorna la vista del estado de cuenta de el id del estudio que se le envie
     */
    function estadoCuenta($idEstudio) {
        $infoEstudio = $this->validarAccesoCartera($idEstudio);
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
                                            WHERE causacion.idEstudio = ' . $idEstudio . ' order by causacion.fechaCausacion, pago.id ');

        $listadoPagos = Pago::where("idEstudio", $idEstudio)->get();

        $pagosImpresos = [];
        $ultimaFecha = "";
        $ultimoIdPago = "";
        
        

        $data = [];
        $temporalPago = 0;
        $acumuladoDeuda = 0;
        foreach ($listado as $fila) {

            if (!is_null($fila->idPago) && $temporalPago != $fila->idPago) {
                $temporalPago = $fila->idPago;
                $data[$fila->idPago] = [
                    "infoPago" => [
                        "id" => $fila->idPago,
                        "valor" => number_format($fila->pago, 2, ",", "."),
                        "saldoFavor" => number_format($fila->saldoFavor, 2, ",", "."),
                        "fechaPago" => date("m-d", strtotime($fila->fechaPago))
                    ]
                ];
            }else if(is_null($fila->idPago)){
                $fila->idPago = uniqid("sinPago_");
                $data[$fila->idPago] = [
                    "infoPago" => [
                        "id" => "",
                        "valor" => "",
                        "saldoFavor" => "",
                        "fechaPago" => ""
                    ]
                ];
            }
            
            $acumuladoDeuda += $fila->balanceSeguro + $fila->balanceInteresMora + $fila->balanceInteresCorriente;
            $data[$fila->idPago]["infoData"][] = [
                "fechaCausacion" => $fila->fechaCausacion,
                "causadoSeguro" => $fila->causadoSeguro,
                "causadoInteresMora" => $fila->causadoInteresMora,
                "causadoInteresCorriente" => $fila->causadoInteresCorriente,
                "causadoAbonoCapital" => $fila->causadoAbonoCapital,
                "pagadoSeguro" => $fila->pagadoSeguro,
                "pagadoInteresMora" => $fila->pagadoInteresMora,
                "pagadoInteresCorriente" => $fila->pagadoInteresCorriente,
                "pagadoAbonoCapital" => $fila->pagadoAbonoCapital,
                "balanceSeguro" => $fila->balanceSeguro,
                "balanceInteresMora" => $fila->balanceInteresMora,
                "balanceInteresCorriente" => $fila->balanceInteresCorriente,
                "balanceAbonoCapital" => $fila->balanceAbonoCapital,
                "balanceSaldoCapital" => $fila->balanceSaldoCapital,
                "balanceValorTotal" => $fila->balanceSaldoCapital + ($acumuladoDeuda * -1)
            ];
        }

        return view('pages/Cartera/estadoCuenta')
                        ->with("idEstudio", $idEstudio)
                        ->with("infoEstudio", $infoEstudio[0])
                        ->with("data", $data)
                        ->with("listadoPagos", $listadoPagos)
                        ->with("pagosImpresos", $pagosImpresos)
                        ->with("ultimaFecha", $ultimaFecha)
                        ->with("ultimoIdPago", $ultimoIdPago);
    }

    /*
     * Esta funcion retorna la vista detallada de la cartera de un cliente en especifico
     */
    function detalleCartera($idEstudio) {
        $infoEstudio = $this->validarAccesoCartera($idEstudio);
        $utilidadesClass = new UtilidadesClass();
        $obligacionesUsuario = DB::table('estudios')->where('estudios.id', $idEstudio)
                ->join('obligaciones', 'obligaciones.Valoracion', '=', 'estudios.Valoracion')
                ->select('obligaciones.id', 'obligaciones.Entidad')->where('obligaciones.Compra', 'S')->where('obligaciones.Estado', 'Activo')
                ->get();

        $obligacionesUsuarioProcess = $this->checkAdjuntosObligaciones($obligacionesUsuario);
        $deudaLaFecha = ceil($this->calcularCuandoDebeHoy($idEstudio));
        
        $sumatoriaRecaudo = DB::table('pago')->select(DB::raw('SUM(pago.pago) as pago'))->where('pago.idEstudio', '=', $idEstudio)->get();

        //obtenemos lo recaudado en intereses corrientes
        $sumatoriaInteresesRecaudadosQuery = DB::table("pago_balance")->select(DB::raw('SUM(pago_balance.interesCorriente) as valor'))->join("balance", "balance.id", "pago_balance.idBalance")->where("balance.idEstudio", $idEstudio)->get();
        $valorInteresesRecaudados = (is_null($sumatoriaInteresesRecaudadosQuery[0]->valor)) ? 0 : $sumatoriaInteresesRecaudadosQuery[0]->valor;

        //Obtenemos lo recaudado en intereses de mora
        $sumatoriaMoraRecaudadaQuery = DB::table("pago_balance")->select(DB::raw('SUM(pago_balance.interesMora) as valor'))->join("balance", "balance.id", "pago_balance.idBalance")->where("balance.idEstudio", $idEstudio)->get();
        $valorMoraRecaudada = (is_null($sumatoriaMoraRecaudadaQuery[0]->valor)) ? 0 : $sumatoriaMoraRecaudadaQuery[0]->valor;

        //obtenemos el valor de los costos
        $infoEstudio[0]->DatosCostos = (isset($infoEstudio[0]->DatosCostos) && !empty($infoEstudio[0]->DatosCostos)) ? (array) json_decode($infoEstudio[0]->DatosCostos) : false;
        $valorCostos = ($infoEstudio[0]->DatosCostos != false && $infoEstudio[0]->DatosCostos["totalCostosV"] > 0) ? $infoEstudio[0]->DatosCostos["totalCostosV"] : 0;

        //Se obtienen todos los pagos realizados
        $cuotasRecaudadas = Pago::select("pago.*", "adjuntos.id as idAdjunto")
                ->join("adjuntos", "adjuntos.idPadre", "pago.id")
                ->where("idEstudio", $idEstudio)
                ->where("adjuntos.Tabla", "PagoIndividualCartera")
                ->orderby("pago.fecha", "ASC")
                ->orderby("pago.created_at", "ASC")
                ->get();

        //Obtenemos el ultimo balance ya que necesitaremos el saldo a capital, y la ultima causacion, porque necesitamos el cobro de interes corriente
        $lastBalance = DB::table('balance')->where("idEstudio", $idEstudio)->orderBy('id', 'desc')->first();

        //Se calcula la rentabilidad
        $reduccionCostos = ($infoEstudio[0]->Estado == config("constantes.ESTUDIO_BANCO") && $lastBalance->saldoCapital > 0) ? $lastBalance->saldoCapital : 0;
        $rentabilidad = $valorInteresesRecaudados + $valorMoraRecaudada + $valorCostos - $reduccionCostos;

        //Variable acumulativa para los saldos a favor
        $sumSaldosFavor = 0;

        $cuotasRecaudadas = Pago::select("pago.*", "adjuntos.id as idAdjunto")->where("idEstudio", $idEstudio)->join("adjuntos", "adjuntos.idPadre", "pago.id")->where("adjuntos.Tabla", "PagoIndividualCartera")->get();

        //Array que guardara la informacion de los pagos
        $infoPagos = [];
        //Procesamos los pagos o cuotas
        foreach ($cuotasRecaudadas as $cuota) {
            if ($cuota->saldoFavor == 0) {//Si no tiene saldo a favor
                //obtenemos la informacion de el ultimo balance generado por el pago
                $balance = Balance::select("balance.saldoCapital")
                        ->join("pago_balance", "balance.id", "pago_balance.idBalance")
                        //->where("pago_balance.abonoCapital", ">", 0)
                        ->where("pago_balance.idPago", $cuota->id)
                        ->orderby("pago_balance.created_at", "DESC")
                        ->first();
                //Obtenemos el saldo a capital como quedo a esa altura
                $saldoCapital = $balance->saldoCapital;
            } else {//si tiene saldo a favor
                //vamos acumulando el saldo a favor, ya que si hay varios el ultimo saldo a favor generado deberia reducir mas el saldo a capital que los que fueron antes
                $sumSaldosFavor += $cuota->saldoFavor;

                //obtenemos los dias del mes del pago
                $cantDiasMes = date("t", strtotime($cuota->fecha));

                //obtenemos el dia exacto del pago
                $diaPago = date("d", strtotime($cuota->fecha));

                //Se calcula cuantos dias restan para que termine el mes
                $diasRestantes = $cantDiasMes - $diaPago;

                //ahora calculamos los intereses por los dias restantes para que termine el mes
                $lastCausacion = DB::table('causacion')->where("idEstudio", $idEstudio)->orderBy('id', 'desc')->first();
                $totalIntereses = $lastCausacion->interesCorriente * $diasRestantes;

                //Ahora calculamos el saldo a capital con el que quedaria el usuario a fin de mes, cuando se cause el capital y se utilice el saldo a favor
                $saldoCapital = $lastBalance->saldoCapital + $totalIntereses - $sumSaldosFavor;
            }

            $infoPagos[] = [
                "infoPago" => $cuota,
                "saldoCapital" => $saldoCapital
            ];
        }

        $infoPagos = array_reverse($infoPagos);

        //Se obtiene el acumulado de las cuotas recaudadas
        $totalRecaudado = ($cuotasRecaudadas->sum("pago") > 0) ? $cuotasRecaudadas->sum("pago") : 0;
        //se cuentan cuantas cuotas fueron recaudadas
        $numCuotasRecaudadas = count($cuotasRecaudadas);
        //Se calcula el promedio del valor de lo recaudado
        $estadoCuentaCuotas = ($totalRecaudado > 0 && $numCuotasRecaudadas > 0) ? $totalRecaudado / $numCuotasRecaudadas : 0;


        //Obtenenmos la informacion de la certificacion o retornara un false si no se ha generado ninguna
        $infoCertificacion = $this->validarCertificacion($idEstudio);

        if ($infoCertificacion == false) {
            $proyeccionCertificacion = number_format($this->proyectarCertificacionesDeuda($idEstudio), 0, ",", "");
        } else {
            $proyeccionCertificacion = number_format($infoCertificacion[0]->valorProyectado, 0, ",", ".");
        }
        //Validamos primeramente que hayan pagos
        if (count($infoPagos) > 0) {
            //Si el estudio esta en estado Banco, el saldo capital que debe mostrar es 0 ya que la deuda ha sido terminada
            if ($infoEstudio[0]->Estado == config("constantes.ESTUDIO_BANCO")) {
                $infoPagos[0]["saldoCapital"] = 0;
            } elseif ($infoCertificacion !== false) {//Si tiene certificacion generada, el saldo a capital que debe el cliente debe ser el generado por la certificacion
                $infoPagos[0]["saldoCapital"] = $infoCertificacion[0]->valorProyectado;
            }
        }

        //El proceso siguiente es para validar si el usuario tiene saldos a favor. Esto puede pasar cuando el pago es mayor a lo que debe, en ese momento el cliente queda con un saldo a favor
        if ($infoEstudio[0]->Estado == config("constantes.ESTUDIO_BANCO")) { //Si el estudio esta en estado banco puede tener saldos a favor, de lo contrario no
            $valorSaldoDevolucionCliente = $this->totalSaldoFavor($idEstudio);
        } else {
            $valorSaldoDevolucionCliente = false;
        }

        $porcentajeRentabilidad = number_format(($rentabilidad * 100) / $infoEstudio[0]->Desembolso, 2, ".", ",");

        $comerciales = $utilidadesClass->listComerciles();
        $listComerciales = [];

        if ($comerciales != false) {
            foreach ($comerciales as $comercial) {
                $listComerciales[][$comercial->id] = $comercial->nombre . " " . $comercial->apellido;
            }
        }

        /*
         * Proceso para seleccionar el comercial de cartera
         */
        $comercialSeleccionado = "";
        if (isset($infoEstudio[0]->ComercialCartera) && !empty($infoEstudio[0]->ComercialCartera)) {
            $comercialSeleccionado = $infoEstudio[0]->ComercialCartera;
        } else {
            $comercialValoracion = Valoracion::where("id", $infoEstudio[0]->Valoracion)->select("valoraciones.Comercial")->get();
            $comercialSeleccionado = (!is_null($comercialValoracion) && count($comercialValoracion) > 0) ? $comercialValoracion[0]->Comercial : "";
        }

        /*
         * Proceso para obtener el banco que finalmente acepto al cliente
         */
        //Primero se obtiene la lista de bancos que se mostraran en el Select
        $listBancos = EntidadBancaria::all();
        $opcionesBancos = [];
        foreach ($listBancos as $banco) {
            $opcionesBancos[][$banco->Id] = $banco->Descripcion;
        }

        //Ahora validamos el banco a mostrar como seleccionado
        $bancoSeleccionado = "";
        if (isset($infoEstudio[0]->BancoFinal) && !empty($infoEstudio[0]->BancoFinal)) {
            $bancoSeleccionado = $infoEstudio[0]->BancoFinal;
        } elseif (isset($infoEstudio[0]->DatosBanco) && !empty($infoEstudio[0]->DatosBanco)) {
            $infoBanco = json_decode($infoEstudio[0]->DatosBanco);
            $bancoSeleccionado = ($infoBanco->bancoSeleccionado > 0) ? $infoBanco->bancoSeleccionado : "";
        }

        /*
         * Proceso para mostrar el valor aprobado por el banco
         */
        $valorAprobadoBanco = (isset($infoEstudio[0]->ValorAprobadoBanco) && $infoEstudio[0]->ValorAprobadoBanco > 0) ? number_format($infoEstudio[0]->ValorAprobadoBanco, 0, ",", ".") : 0;


        /*
         * Proceso para listar y setear los estados de cartera
         */
        $listEstadosCartera = [
            "ESTADO1" => "ESTADO1",
            "ESTADO2" => "ESTADO2"
        ];
        $estadoCarteraSeleccionado = (isset($infoEstudio[0]->EstadoCartera) && !empty($infoEstudio[0]->EstadoCartera)) ? $infoEstudio[0]->EstadoCartera : "";
        
        /*
         * proceso para calcular la cantidad de dias de la rentabilidad
         */
        $adjuntoVisado = Adjunto::where("idPadre", $idEstudio)->where("TipoAdjunto", config("constantes.VISADO"))->first();
        if(count($adjuntoVisado) > 0){
            $fechaInicio = new DateTime($adjuntoVisado->created_at);
            if($infoEstudio[0]->Estado == config("constantes.ESTUDIO_BANCO") && false){
                $ultimoPago = Pago::where("idEstudio", $idEstudio)->orderBy("created_at", "DESC")->first();
                $fechaFin = new DateTime($ultimoPago->created_at);
            }else{
                $fechaFin = new DateTime("now");
            }
            
            $diferencia = date_diff($fechaInicio, $fechaFin);
            $diferencia = $diferencia->days;
        }
               
        return view('pages/Cartera/detalle')
                        ->with("valorSaldoDevolucionCliente", $valorSaldoDevolucionCliente)
                        ->with("rentabilidad", $rentabilidad)
                        ->with("infoPagos", $infoPagos)
                        ->with("infoEstudio", $infoEstudio[0])
                        ->with("utilidadesClass", $utilidadesClass)
                        ->with("idEstudio", $idEstudio)
                        ->with("obligacionesUsuario", $obligacionesUsuarioProcess)
                        ->with("deudaLaFecha", $deudaLaFecha)
                        ->with("proyeccionCertificacion", $proyeccionCertificacion)
                        ->with("infoCertificacion", $infoCertificacion)
                        ->with("estadoCuentaCuotas", $estadoCuentaCuotas)
                        ->with("porcentajeRentabilidad", $porcentajeRentabilidad)
                        ->with("comerciales", $listComerciales)
                        ->with("comercialSeleccionado", $comercialSeleccionado)
                        ->with("bancoSeleccionado", $bancoSeleccionado)
                        ->with("opcionesBancos", $opcionesBancos)
                        ->with("valorAprobadoBanco", $valorAprobadoBanco)
                        ->with("listEstadosCartera", $listEstadosCartera)
                        ->with("estadoCarteraSeleccionado", $estadoCarteraSeleccionado)
                        ->with("diferencia", $diferencia)
                        ->with("totalRecaudo", $sumatoriaRecaudo);
    }

    function devolucion_reintegro($idEstudio) {
        Pago::where("idEstudio", $idEstudio)->update(["saldoFavor" => 0]);
        return redirect(config('constantes.RUTA') . "DetalleCartera/" . $idEstudio);
    }

    function eliminarCertificacion(Request $request) {
        $updateCertificacion = LogCertificaciones::where("id", $request->infoAdjunto)
                ->update(["estado" => "0"]);
        if ($updateCertificacion) {
            $mensaje = "Registro actualizado exitosamente";
            $valorProyectado = number_format($this->proyectarCertificacionesDeuda($request->id), 0, ",", ".");
            $status = true;
        } else {
            $mensaje = "Hubo un error, intenta nuevamente";
            $status = false;
        }
        echo json_encode(["Mensaje" => $mensaje, "valorCertificado" => $valorProyectado, "STATUS" => $status]);
    }

    function dspTabla($idEstudio, $idVal) {
        $infoEstudio = $this->validarAccesoCartera($idEstudio);

        $tasa = $infoEstudio[0]->Tasa / 100;
        $plazo = $infoEstudio[0]->Plazo;
        $cuota = $infoEstudio[0]->Cuota;
        $seguro = $infoEstudio[0]->costoSeguro;

        $valorCreditoReal = $this->calcularCredito($cuota - $seguro, $tasa, $plazo);

        $validarPagos = DB::table('obligaciones')->select('adjuntos.created_at as fecha')->join('gestionobligaciones', 'gestionobligaciones.id_obligacion', '=', 'obligaciones.id')
                ->join('adjuntos', 'adjuntos.idPadre', '=', 'gestionobligaciones.id_obligacion')
                ->where('Valoracion', $idVal)
                ->where('gestionobligaciones.estado', config('constantes.GO_PAGADA'))
                ->where('adjuntos.TipoAdjunto', config('constantes.SOPORTE_PAGO'))
                ->orderBy('adjuntos.created_at', 'asc')
                ->first();

        return view('pages.Cartera.index')
                        ->with("tasa", $tasa)
                        ->with("plazo", $plazo)
                        ->with("cuota", $cuota)
                        ->with("seguro", $seguro)
                        ->with("infoEstudio", $infoEstudio)
                        ->with("fechaInicio", $validarPagos->fecha)
                        ->with("idVal", $idVal)
                        ->with("vlrCredito", $valorCreditoReal);
    }

    function validarAccesoCartera($idEstudio) {
        if (!is_numeric($idEstudio)) {
            $vista = view('errors.101')->with("mensaje", "El identificador debe ser numerico");
            echo $vista->render();
            die;
        }

        $infoEstudio = DB::table('estudios')->where("estudios.id", $idEstudio)
                        ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                        ->join('users', 'valoraciones.Usuario', '=', 'users.id')
                        ->select("estudios.Pagaduria as pagaduriaEstudio", "estudios.id as idEstudio", "estudios.Estado as EstadoEstudio", "users.*", "estudios.*")->get();

        if (!isset($infoEstudio) || count($infoEstudio) <= 0) {
            $vista = view('errors.101')->with("mensaje", "El estudio al que desea ingresar no existe");
            echo $vista->render();
            die;
        }

        if ($infoEstudio[0]->EstadoEstudio == "ing" || $infoEstudio[0]->EstadoEstudio == "ING") {
            $vista = redirect(config('constantes.RUTA') . "GestionObligaciones/" . $idEstudio);
            echo $vista->render();
            die;
        }

        if ($infoEstudio[0]->EstadoEstudio != config("constantes.ESTUDIO_CARTERA") && $infoEstudio[0]->EstadoEstudio != config("constantes.ESTUDIO_BANCO") && $infoEstudio[0]->EstadoEstudio != config("constantes.ESTUDIO_PROCESO_TESORERIA") && $infoEstudio[0]->EstadoEstudio != config("constantes.ESTUDIO_TESORERIA")) {
            $vista = view('errors.101')->with("mensaje", "El estudio se encuentra en estado " . $infoEstudio[0]->EstadoEstudio);
            echo $vista->render();
            die;
        }

        return $infoEstudio;
    }

    function checkAdjuntosObligaciones($listObligaciones) {
        for ($i = 0; $i < count($listObligaciones); $i++) {
            //Variables para controlar la accion permitida al usuario en el cargue de el adjunto(CDD, PYS) y si tiene adjunto de tipo CDD                    
            $listObligaciones[$i]->optionGestionObligacionesPYS = "showAll";
            $listObligaciones[$i]->idAdjunto = 0;
            $listObligaciones[$i]->tieneAdjuntos = false;

            $gestionObligacionPYS = gestionObligaciones::where("id_obligacion", $listObligaciones[$i]->id)
                            ->where("tipoAdjunto", config("constantes.PAZ_SALVO_CARTERA"))
                            ->whereIn("estado", [config("constantes.GO_VENCIDA"),
                                config("constantes.GO_RADICADA"),
                                config("constantes.GO_SOLICITADA")])
                            ->orderBy("created_at", "DESC")->first();
            if (isset($gestionObligacionPYS->id)) {
                $listObligaciones[$i]->tieneAdjuntos = true;
                if ($gestionObligacionPYS->estado == config("constantes.GO_RADICADA")) {
                    $listObligaciones[$i]->optionGestionObligacionesPYS = "hidden";
                } elseif ($gestionObligacionPYS->estado == config("constantes.GO_SOLICITADA")) {
                    $listObligaciones[$i]->optionGestionObligacionesPYS = "showRad";
                } else {
                    $listObligaciones[$i]->optionGestionObligacionesPYS = "showAll";
                }
            }

            $adjuntoPazYSalvo = Adjunto::where("idPadre", $listObligaciones[$i]->id)->where("Tabla", config("constantes.KEY_OBLIGACION"))->where("TipoAdjunto", config("constantes.CARTERA_PAZ_SALVO"))->get();
            if (count($adjuntoPazYSalvo) > 0) {
                $listObligaciones[$i]->pazSalvo = count($adjuntoPazYSalvo);
            } else {
                $listObligaciones[$i]->pazSalvo = 0;
            }
        }
        return $listObligaciones;
    }

    function calcularCredito($cuota, $tasa, $plazo) {
        return $cuota * ( ( (pow(1 + $tasa, $plazo)) - 1 ) / ( $tasa * (pow(1 + $tasa, $plazo)) ) );
    }

    function crearPdf($idEstudio, $idVal) {
        $infoEstudio = $this->validarAccesoCartera($idEstudio);

        $tasa = $infoEstudio[0]->Tasa / 100;
        $plazo = $infoEstudio[0]->Plazo;
        $cuota = $infoEstudio[0]->Cuota;
        $seguro = $infoEstudio[0]->costoSeguro;
        $meses = [];

        $valorCreditoReal = $this->calcularCredito($cuota - $seguro, $tasa, $plazo);

        $validarPagos = DB::table('obligaciones')->select('adjuntos.created_at as fecha')->join('gestionobligaciones', 'gestionobligaciones.id_obligacion', '=', 'obligaciones.id')
                ->join('adjuntos', 'adjuntos.idPadre', '=', 'gestionobligaciones.id_obligacion')
                ->where('Valoracion', $idVal)
                ->where('gestionobligaciones.estado', config('constantes.GO_PAGADA'))
                ->where('adjuntos.TipoAdjunto', config('constantes.SOPORTE_PAGO'))
                ->orderBy('adjuntos.created_at', 'asc')
                ->first();

        for ($cont = 1; $cont <= $plazo; $cont++) {
            array_push($meses, date('m-Y', strtotime("+$cont month", strtotime($validarPagos->fecha))));
        }
        $data = [
            "infoEstudio" => $infoEstudio,
            "tasa" => $tasa,
            "plazo" => $plazo,
            "cuota" => $cuota,
            "seguro" => $seguro,
            "fecha_inicio" => $validarPagos->fecha,
            "meses" => $meses,
            "valorCreditoReal" => $valorCreditoReal
        ];
        set_time_limit(0);
        $pdf = PDF::loadView('pages.Cartera.pdfList', compact("data", "idEstudio"))->setPaper('a4', 'portrait');
        return $pdf->stream();
    }

    function verPdf($idEstudio) {

        $infoEstudio = $this->validarAccesoCartera($idEstudio);

        $tasa = $infoEstudio[0]->Tasa / 100;
        $plazo = $infoEstudio[0]->Plazo;
        $cuota = $infoEstudio[0]->Cuota;
        $seguro = $infoEstudio[0]->costoSeguro;

        $valorCreditoReal = $this->calcularCredito($cuota - $seguro, $tasa, $plazo);

        $data = [
            "infoEstudio" => $infoEstudio,
            "tasa" => $tasa,
            "plazo" => $plazo,
            "cuota" => $cuota,
            "seguro" => $seguro,
            "valorCreditoReal" => $valorCreditoReal
        ];


        return view('pages.Cartera.pdfList')->with("data", $data)->with("idEstudio", $idEstudio);
    }

    function validarExistenciaEstudio($idEstudio) {
        if (!is_numeric($idEstudio)) {
            $vista = view('errors.101')->with("mensaje", "El identificador debe ser numerico");
            echo $vista->render();
            die;
        }

        $infoEstudio = Estudio::find($idEstudio);

        if (!isset($infoEstudio->id)) {
            $vista = view('errors.101')->with("mensaje", "El estudio al que desea ingresar no existe");
            echo $vista->render();
            die;
        }

        if ($infoEstudio->EstadoEstudio == "ing" || $infoEstudio->EstadoEstudio == "ING") {
            $vista = redirect(config('constantes.RUTA') . "GestionObligaciones/" . $idEstudio);
            echo $vista->render();
            die;
        }

        return $infoEstudio;
    }

    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * ++++++++++++++++++++FUNCIONES PRINCIPALES DEL ALGORITMO DE CERTETA++++++++++++++++++++++++
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     */

    function calcularCuandoDebeHoy($idEstudio) {
        $totalDeuda = $this->totalDeudaCliente($idEstudio);
        $totalSaldoFavor = $this->totalSaldoFavor($idEstudio);
        $deuda = $totalDeuda - $totalSaldoFavor;
        return $deuda;
    }

    function totalDeudaCliente($idEstudio) {

        $lastBalance = DB::table('balance')->where("idEstudio", $idEstudio)->orderBy('id', 'desc')->first();

        $deudaSeguro = DB::table('balance')->select(DB::raw('SUM(seguro) as seguro'))
                        ->where("seguro", "<", 0)
                        ->where("idEstudio", $idEstudio)->get();

        $deudaInteresesMora = DB::table('balance')->select(DB::raw('SUM(interesMora) as interesMora'))
                        ->where("interesMora", "<", 0)
                        ->where("idEstudio", $idEstudio)->get();

        $deudaInteresesCorriente = DB::table('balance')->select(DB::raw('SUM(interesCorriente) as interesCorriente'))
                        ->where("interesCorriente", "<", 0)
                        ->where("idEstudio", $idEstudio)->get();


        $saldoCapital = ($lastBalance && $lastBalance->saldoCapital > 0) ? $lastBalance->saldoCapital : 0;
        $seguro = ($deudaSeguro && $deudaSeguro[0]->seguro * -1 > 0) ? $deudaSeguro[0]->seguro * -1 : 0;
        $interesesMora = ($deudaInteresesMora && $deudaInteresesMora[0]->interesMora * -1 > 0) ? $deudaInteresesMora[0]->interesMora * -1 : 0;
        $interesesCorrientes = ($deudaInteresesCorriente && $deudaInteresesCorriente[0]->interesCorriente * -1 > 0) ? $deudaInteresesCorriente[0]->interesCorriente * -1 : 0;

        $deudaTotal = $saldoCapital + $seguro + $interesesMora + $interesesCorrientes;
        return $deudaTotal;
    }

    function totalSaldoFavor($idEstudio) {
        $pagosConSaldo_A_Favor = Pago::where("idEstudio", $idEstudio)->where("saldoFavor", ">", 0)->select(DB::raw('SUM(saldoFavor) as saldoFavor'))->get();
        return (isset($pagosConSaldo_A_Favor[0]->saldoFavor) && $pagosConSaldo_A_Favor[0]->saldoFavor > 0) ? $pagosConSaldo_A_Favor[0]->saldoFavor : 0;
    }

    function formPago(Request $request) {

        $objUtilidades = new UtilidadesClass();

        if (!isset($request->soporte) || !$request->file('soporte')->isValid()) {
            session(['fechaPago' => $request->fechaPago]);
            session(['ValorPago' => $request->ValorPago]);
            return redirect()->back()->withErrors(['Debe cargar el soporte.']);
        }

        $archivo = $request->file('soporte');
        $extension = $archivo->getClientOriginalExtension();

        if (!in_array($extension, $objUtilidades->extensionesPermitidas)) {
            session(['fechaPago' => $request->fechaPago]);
            session(['ValorPago' => $request->ValorPago]);
            return redirect()->back()->withErrors(['Solo se permiten archivos de tipo: [' . implode(" | ", $objUtilidades->extensionesPermitidas) . ']']);
        }

        $validacion = Validator::make(
                        [
                    'fechaPago' => $request->fechaPago,
                    'ValorPago' => $request->ValorPago
                        ], [
                    'fechaPago' => 'required',
                    'ValorPago' => 'required'
                        ]
        );


        if ($validacion->fails()) {
            return redirect()->back()->withErrors($validacion->errors());
        }


        if ($request->tipoPago === "Individual") {
            $valorPago = str_replace(".", "", $request->ValorPago);
            $response = $this->desicionPago($request->idEstudio, $valorPago, $request->fechaPago, $request->file('soporte'));
        } elseif ($request->tipoPago === "Certificacion") {
            //Se obtiene la informacion de la certificacion
            $infoCertificacion = $this->validarCertificacion($request->idEstudio);

            //Consultamos la ultima causacion para saber desde que fecha se debe iniciar la causacion de los dias restantes
            $lastCausacion = DB::table('causacion')->where("idEstudio", $request->idEstudio)->orderBy('id', 'desc')->first();
            if (is_null($lastCausacion)) {
                return redirect()->back()->withErrors(['El credito no tiene causaciones creadas.']);
            }

            //convertimos la fecha limite de la certificacion en timestamp
            $fechaCertificacion = strtotime($infoCertificacion[0]->diaCorte . "-" . $infoCertificacion[0]->mesVigencia . " " . $infoCertificacion[0]->anioVigencia);

            //A la ultima fecha de la causacion se le suma un dia para que sea nuestro punto de partida
            $fechaPorCausar = strtotime('+1 day', strtotime($lastCausacion->fechaCausacion));

            //Proceso para causar los dias restantes a la fecha de vencimiento de la certificacion.
            //Si la fecha por causar es menor o igual al limite, entonces que lo cause, 
            //si no es porque ya esta causado todo porque la fecha por causar seria mayor al la fecha limite de la certificacion
            while ($fechaPorCausar <= $fechaCertificacion) {

                //Causamos este dia
                $this->causar($request->idEstudio, date("d-m-Y", $fechaPorCausar));

                //Se le va sumando un dia a la fecha para ir causando todos los dias
                $fechaPorCausar = strtotime('+1 day', $fechaPorCausar);
            }
            //Pasamos el estudio a estado Banco ya que se ha cancelado la totalidad del mismo
            Estudio::where("id", $request->idEstudio)->update(["estado" => config("constantes.ESTUDIO_BANCO")]);

            //inactivamos la certificacion porque ya fue cancelada
            LogCertificaciones::where("id", $infoCertificacion[0]->id)->update(["estado" => 2]);


            //Se limipia la variable de pago para que quede procesable y ejecutamos el pago.
            $valorPago = str_replace(".", "", $request->ValorPago);
            $response = $this->desicionPago($request->idEstudio, $valorPago, $request->fechaPago, $request->file('soporte'), true);
        } else {
            return redirect()->back()->withErrors(['No se pudo leer el tipo de pago(Individual, Certificaci&oacute;n. Por favor recargue la pagine e intente de nuevo)']);
        }


        if ($response["STATUS"]) {
            return redirect()->back()->with("OK", $response["MENSAJE"]);
        } else {
            return redirect()->back()->withErrors($response["MENSAJE"]);
        }
        
    }

    function desicionPago($idEstudio, $valorPago, $fechaPago, $soporte, $pagoCertificacion = false) {
        //Variables generales de la funcion
        $objUtilidades = new UtilidadesClass();
        $validacion = [];

        //Variable para controlar si se debe abonar a capitar al sobrante o guardar en saldo a favor
        $abonarSobranteACapital = false;

        //Se obtiene el valor de la deuda total 
        $deuda = ceil($this->calcularCuandoDebeHoy($idEstudio));

        //Si el pago es mayor o igual alo que debe, quiere decir que saldara la deuda
        if ($valorPago >= $deuda || $pagoCertificacion === true) {
            //Se procede a utilizar los saldos a favor
            $this->utilizarSaldoFavor($idEstudio);
            $abonarSobranteACapital = true;
        } else {
            $abonarSobranteACapital = false;
        }

        DB::beginTransaction();
        $idPago = $this->crearPago($idEstudio, $valorPago, $fechaPago);

        if ($idPago == false) {
            DB::rollBack();
            $validacion["STATUS"] = false;
            $validacion["MENSAJE"] = "Ocurrio un problema al intentar almacenar el pago [" . __LINE__ . "]";
            return $validacion;
        }

        $NombreOriginal = $soporte->getClientOriginalName();
        $extension = $soporte->getClientOriginalExtension();
        
        if($pagoCertificacion === true){
            $tipoAdjunto = config("constantes.PAGO_CERTIFICACIONDEUDA");
        }else{
            $tipoAdjunto = config("constantes.SOPORTE_RECAUDO");
        }

        $id = $objUtilidades->registroAdjunto($idPago, 'PagoIndividualCartera', $NombreOriginal, $extension, $tipoAdjunto, config("constantes.MDL_VALORACION"));
        if ($id == false) {
            DB::rollBack();
            $validacion["STATUS"] = false;
            $validacion["MENSAJE"] = "Ocurrio un problema al intentar almacenar el adjunto en la base de datos [" . __LINE__ . "]";
            return $validacion;
        }

        set_time_limit(0);
        $subido = \Storage::disk('adjuntos')->put($id, \File::get($soporte));

        if ($subido) {
            $this->pagar($idEstudio, $valorPago, $idPago, $abonarSobranteACapital);
            DB::commit();
            $validacion["STATUS"] = true;
            $validacion["MENSAJE"] = "El pago se adiciono correctamente.";
            //return redirect()->back()->with("OK", "El pago se adiciono correctamente.");
        } else {
            DB::rollBack();
            $validacion["STATUS"] = false;
            $validacion["MENSAJE"] = "Ocurrio un problema al cargar el soporte [" . __LINE__ . "]";
            //return redirect()->back()->withErrors("Ocurrio un problema al cargar el soporte, Por favor intentelo de nuevo");
        }

        return $validacion;
    }

    function crearPago($idEstudio, $valorPago, $fechaPago) {
        $pago = new Pago;
        $pago->idEstudio = $idEstudio;
        $pago->pago = $valorPago;
        $pago->fecha = $fechaPago;
        $insertPago = $pago->save();
        return $pago->id;
    }

    /*
     * Funcion para repartir todos los pagos que el usuario ingrese
     */

    function pagar($idEstudio, $valorPago, $idPago, $abonarSobranteACapital) {

        $balancesCliente = DB::table("balance")->select("causacion.fechaCausacion", "balance.*")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("balance.idEstudio", $idEstudio)->get();

        $deudaAbonoCapital = 0;
        $nuevoCapital = false;
        if (count($balancesCliente) > 0) {
            $valorPago = round($valorPago, 3);
            foreach ($balancesCliente as $balance) {
                //Si el saldo de capital fue modificado deberia tomarse el nuevo valor y no el que estaba anteriormente en la base de datos
                if ($nuevoCapital != false) {
                    $balance->saldoCapital = $nuevoCapital;
                }

                //pasamos a positivo los valores para manejarlos mas facilmente                
                $balance->seguro = ($balance->seguro < 0) ? round($balance->seguro * -1, 3) : false;
                $balance->interesMora = ($balance->interesMora < 0) ? round($balance->interesMora * -1, 3) : false;
                $balance->interesCorriente = ($balance->interesCorriente < 0) ? round($balance->interesCorriente * -1, 3) : false;

                $update = [];

                $guardarPagoBalance = false;
                $pagoBalance = new pagoBalance;

                //Se valida si alcanza para pagar el seguro
                if ($balance->seguro != false && $valorPago > 0 && $valorPago >= $balance->seguro) {
                    $pagoBalance->seguro = $balance->seguro;
                    $guardarPagoBalance = true;
                    $valorPago = round($valorPago - $balance->seguro, 3);
                    $update["seguro"] = 0;
                } elseif ($balance->seguro != false && $valorPago > 0) {
                    $pagoBalance->seguro = $valorPago;
                    $guardarPagoBalance = true;
                    $update["seguro"] = round(($balance->seguro - $valorPago) * -1, 3);
                    $valorPago = 0;
                }

                //Se valida si alcanza para pagar los intereses de mora                
                if ($balance->interesMora != false && $valorPago > 0 && $valorPago >= $balance->interesMora) {
                    $pagoBalance->interesMora = $balance->interesMora;
                    $guardarPagoBalance = true;
                    $valorPago = round($valorPago - $balance->interesMora, 3);
                    $update["interesMora"] = 0;
                } elseif ($balance->interesMora != false && $valorPago > 0) {
                    $pagoBalance->interesMora = $valorPago;
                    $guardarPagoBalance = true;
                    $update["interesMora"] = round(($balance->interesMora - $valorPago) * -1, 3);
                    $valorPago = 0;
                }

                //Se valida si alcanza para pagar los intereses corrientes                                
                if ($balance->interesCorriente != false && $valorPago > 0 && $valorPago >= $balance->interesCorriente) {
                    $pagoBalance->interesCorriente = $balance->interesCorriente;
                    $guardarPagoBalance = true;
                    $valorPago = round($valorPago - $balance->interesCorriente, 3);
                    $update["interesCorriente"] = 0;
                } elseif ($balance->interesCorriente != false && $valorPago > 0) {
                    $pagoBalance->interesCorriente = $valorPago;
                    $guardarPagoBalance = true;
                    $update["interesCorriente"] = round(($balance->interesCorriente - $valorPago) * -1, 3);
                    $valorPago = 0;
                }

                //Se valida si alcanza para pagar el abono a capital                
                if ($valorPago > 0 && $balance->abonoCapital < 0 && $valorPago >= round($balance->abonoCapital * -1, 3)) {
                    $deudaAbonoCapital = round($balance->abonoCapital * -1, 3);
                    $pagoBalance->abonoCapital = round($balance->abonoCapital * -1, 3);

                    $guardarPagoBalance = true;
                    $update["abonoCapital"] = 0;
                    $nuevoCapital = round($balance->saldoCapital + $balance->abonoCapital, 3);
                    $valorPago = round($valorPago + $balance->abonoCapital, 3);
                } elseif ($valorPago > 0 && $balance->abonoCapital < 0) {
                    $pagoBalance->abonoCapital = $valorPago;
                    $guardarPagoBalance = true;
                    $update["abonoCapital"] = round($balance->abonoCapital + $valorPago, 3);
                    $nuevoCapital = round($balance->saldoCapital - $valorPago, 3);
                    $valorPago = 0;
                }

                if ($nuevoCapital != false) {
                    $update["saldoCapital"] = $nuevoCapital;
                }

                if (count($update) > 0) {
                    $actualizacion = Balance::where("id", $balance->id)->update($update);
                    if ($actualizacion && $guardarPagoBalance) {
                        $pagoBalance->idPago = $idPago;
                        $pagoBalance->idBalance = $balance->id;
                        $pagoBalance->save();
                    }
                }
            }

            if ($valorPago > 0) {
                if ($abonarSobranteACapital) {
                    //obtenemos el capital actual
                    $capitalActual = ($nuevoCapital != false) ? $nuevoCapital : $balancesCliente->last()->saldoCapital;
                    //se calcula el nuevo saldo de capital restandole el dinero que sobro
                    $nuevoSaldoCapital = round($capitalActual - $valorPago, 3);

                    if ($nuevoSaldoCapital < 0) {
                        $saldoFavor = $nuevoSaldoCapital * -1;
                        $nuevoSaldoCapital = 0; //Se adiciono para que si pagan de mas lo iguale a 0 y asi no dañe la informacion
                    } else {
                        $saldoFavor = 0;
                    }

                    if ($saldoFavor > 0) {
                        Pago::where("id", $idPago)->update(["saldoFavor" => $saldoFavor]);
                    }

                    //Seran pagos diferentes cuando se causa capital y seran iguales cuando entra la cuota del cliente
                    if ($pagoBalance->idPago == $idPago) {

                        //se guarda el nuevo saldo de capital y el valor adicional que se abono en el capital en el ultimo balance
                        Balance::where("id", $balancesCliente->last()->id)->update(["abonoCapital" => 0, "saldoCapital" => $nuevoSaldoCapital]);

                        //El pago balance debe obtener el valor total (abono capital + abono extraordinario) que se abono a capital                        
                        //si en este pago balance que se va a utilizar ya se habia registrado un abono a capital, hay que sumarlo en los valores para no alterar el pago realizaro realmente a capital.
                        $abonoCapitalRealizado = (isset($pagoBalance->abonoCapital) && $pagoBalance->abonoCapital > 0) ? $pagoBalance->abonoCapital : 0;
                        $actualizacionPagoBalance = pagoBalance::where("id", $pagoBalance->id)->update(["abonoCapital" => $valorPago - $saldoFavor + $abonoCapitalRealizado]);
                    } else {
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
                    $valorPago = 0;
                    if ($nuevoSaldoCapital == 0) {
                        Estudio::where("id", $idEstudio)->update(["estado" => config("constantes.ESTUDIO_BANCO")]);
                    }
                } else {//                    
                    Pago::where("id", $idPago)->update(["saldoFavor" => $valorPago]);
                    $valorPago = 0;
                }
            }
        }
    }

    /*
     * Funcion que se ejecuta cada dia para realizar la causacion
     */

    function cronJobsCausacion() {
        $EstudiosCartera = DB::select("SELECT estudios.id                                            
                                       FROM estudios                                       
                                       WHERE estudios.Estado IN('" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')");
        
        foreach ($EstudiosCartera as $estudio){
            $this->causar($estudio->id, date("Y-m-d"));
        }
        
    }
  

    /*
     * Funcion para causar cada uno de los dias
     * $idEstudio = id unico del estudio que se pagara
     * $fechaCausar: Y-m-d
     */

    function causar($idEstudio, $fechaCausar) {

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
        if (count($balanceCliente) > 0) {
            //Si el dia actual esta entre la fecha de pago, se deba causar mora pero no sobre el capital del mes que falta por pagar en estos dias sino por el capital de los meses anteriores que no se pagaron
            if (date("d", strtotime($fechaCausar)) >= 1 && date("d", strtotime($fechaCausar)) <= 15) {
                //Se selecciona el primer dia del mes anterior para que obtenga los balances con fechas mas viejas que la hallada
//                    $nuevafecha = strtotime('-1 month' , strtotime(date("Y-m")));
                $nuevafecha = strtotime('-1 month', strtotime($fechaCausar));
                $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital", "fechaCausacion")->join("causacion", "causacion.id", "=", "balance.idCausacion")->where("causacion.fechaCausacion", "<=", date('Y-m-d', $nuevafecha))->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");
            } else {//se obtiene todo el abono a capital faltante ya que en este punto ya se tiene certeza de cual es el capital que deuda el cliente
                $totalBalancesEnMora = DB::table("balance")->select("balance.abonoCapital")->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                $totalCapitalEnMora = $totalBalancesEnMora->sum("abonoCapital");
            }
            $totalCapitalEnMora = $totalCapitalEnMora * -1;
            if ($totalCapitalEnMora > 0) {
                $valorInteresMoraDia = round(($totalCapitalEnMora * ($tasaMora / 100)) / $cantDiasMes, 3);
                if (date("d", strtotime($fechaCausar)) == 16) {
                    if ($valorInteresMoraDia > 0 && $balanceCliente->last()->interesMora !== $valorInteresMoraDia) {
//                            DB::select('UPDATE causacion SET interesMora = '.$valorInteresMoraDia.' WHERE causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                        DB::table('causacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)) . '-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)) . '-15')
                                ->update(['interesMora' => $valorInteresMoraDia]);

//                            DB::select('UPDATE balance JOIN causacion on causacion.id = balance.idCausacion SET balance.interesMora = '.($valorInteresMoraDia * -1).' where causacion.fecha >= "'.date("Y-m", strtotime($fechaCausar)).'-1" and causacion.fecha <= "'.date("Y-m", strtotime($fechaCausar)).'-15"');
                        DB::table('balance')
                                ->join('causacion', 'causacion.id', '=', 'idCausacion')
                                ->where('fechaCausacion', ">=", date("Y-m", strtotime($fechaCausar)) . '-1')
                                ->where('fechaCausacion', "<=", date("Y-m", strtotime($fechaCausar)) . '-15')
                                ->update(['balance.interesMora' => ($valorInteresMoraDia * -1)]);
                    }
                }
            }
            //capturamos el valor del capital
            $valorCapital = $balanceCliente->last()->saldoCapital;
        } else {//Si no se toma el valor del credito como capital inicial                      
            $valorCapital = $infoEstudio->ValorCredito;
        }

        //Se calcula el valor de interes para cada uno de los dias del mes        
        $valorInteresDia = round(($valorCapital * ($infoEstudio->Tasa / 100)) / $cantDiasMes, 3);

        $causacion = new Causacion;
        $causacion->idEstudio = $idEstudio;
        $causacion->fechaCausacion = date("Y-m-d  G:i:s", strtotime($fechaCausar));
        $causacion->interesCorriente = $valorInteresDia;

        //Si es el primer dia del mes casamos el seguro
        if (date("d", strtotime($fechaCausar)) == "1") {
            $causacion->seguro = $valorSeguro;
        }

        //Se calcula la mora        
        if ($valorInteresMoraDia > 0) {
            $causacion->interesMora = $valorInteresMoraDia;
        }

        //Obtenemos el ultimo dia de ese mes para validar si estamos en ese dia y asi causar el capital
        $fecha = new DateTime();
        $fecha->modify('last day of this month');
//        if(date("d", strtotime($fechaCausar)) == $fecha->format('d')){
        if (date("d", strtotime($fechaCausar)) == $cantDiasMes) {
            $interesesDelMes = $valorInteresDia * $cantDiasMes;
            $abonoCapital = $infoEstudio->Cuota - $valorSeguro - $interesesDelMes;
            $causacion->abonoCapital = $abonoCapital;
        }
        $causacion->save();
        $this->balance($idEstudio, $causacion, $valorCapital);

        if (date("d", strtotime($fechaCausar)) == $cantDiasMes) {
            $this->utilizarSaldoFavor($idEstudio);
        }
    }

    /*
     * Funcion encargada de utilizar los saldos a favor del cliente para abonarlos a capital
     */

    function utilizarSaldoFavor($idEstudio) {
        $pagosConSaldo_A_Favor = Pago::where("idEstudio", $idEstudio)->where("saldoFavor", ">", 0)->get();
        foreach ($pagosConSaldo_A_Favor as $pago) {
            $abonarSobranteACapital = true;
            Pago::where("id", $pago->id)->update(["saldoFavor" => 0]);
            $this->pagar($idEstudio, $pago->saldoFavor, $pago->id, $abonarSobranteACapital);
        }
    }

    function balance($idEstudio, $causacion, $valorCapital) {
        $balance = new Balance;
        $balance->idEstudio = $idEstudio;
        $balance->idCausacion = $causacion->id;
        $balance->seguro = (isset($causacion->seguro)) ? $causacion->seguro * -1 : 0;
        $balance->interesMora = (isset($causacion->interesMora)) ? $causacion->interesMora * -1 : 0;
        $balance->interesCorriente = (isset($causacion->interesCorriente)) ? $causacion->interesCorriente * -1 : 0;
        $balance->abonoCapital = (isset($causacion->abonoCapital)) ? $causacion->abonoCapital * -1 : 0;
        $balance->saldoCapital = $valorCapital;
        $balance->save();
    }

    function guardarFechasRadicacion(Request $request) {
        if ($request->estado == "PRAD") {
            $this->radicarPazAndSalvo($request);
        }
    }

    function GuardarFechas(Request $request) {
        if ($request->estado == "PSOL") {
            $estado = "SOL";
            $tipoAdjunto = config("constantes.PAZ_SALVO_CARTERA");
        } else {
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

        if ($result) {
            $this->guardarLogGestionObligaciones($gestionObligaciones->id, $request->idObligacion, $tipoAdjunto, false, $request->fechaSolicitud, $request->fechaEntrega, false, false, $estado, $request->idAdjunto);
            $objFuncionesComponente = new FuncionesComponente();
            $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion, false, false, config("constantes.PAZ_SALVO_CARTERA"));

            echo json_encode(["STATUS" => true, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
        } else {
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas, por favor refresque la pagina e intentelo de nuevo"]);
        }
    }

    function radicarPazAndSalvo($request) {
        $estado = "RAD";
        $tipoAdjunto = config("constantes.PAZ_SALVO_CARTERA");

        $utilidadesClass = new UtilidadesClass();
        $tienePazSalvo = $utilidadesClass->validaAdjunto($request->idObligacion, config("constantes.KEY_OBLIGACION"), config("constantes.SOPORTE_PAGO"));

        $gestionObligaciones = gestionObligaciones::where("estado", config("constantes.GO_SOLICITADA"))
                        ->where("id_obligacion", $request->idObligacion)
                        ->where("tipoAdjunto", $tipoAdjunto)->get();

        if (count($gestionObligaciones) > 0) {
            $idGestionObligacion = $gestionObligaciones[0]->id;
            $result = $gestionObligaciones[0]->update(["estado" => $estado,
                "id_adjunto" => $request->idAdjunto,
                "fechaRadicacion" => $request->fechaRadicacion]);

            if ($result) {
                $this->guardarLogGestionObligaciones($idGestionObligacion, $request->idObligacion, $tipoAdjunto, $request->idAdjunto, false, false, $request->fechaRadicacion, false, $estado);
                $objFuncionesComponente = new FuncionesComponente();
                $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion, false, false, config("constantes.PAZ_SALVO_CARTERA"));
                echo json_encode(["STATUS" => true, "tienePazSalvo" => $tienePazSalvo, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
                die;
            } else {
                $this->borrarAdjunto($request->idAdjunto);
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas (no actualizo el estado), por favor refresque la pagina e intentelo de nuevo " . __LINE__]);
                die;
            }
        } else {
            $gestionObligaciones = new gestionObligaciones;
            $gestionObligaciones->id_obligacion = $request->idObligacion;
            $gestionObligaciones->tipoAdjunto = $tipoAdjunto;
            $gestionObligaciones->estado = $estado;
            $gestionObligaciones->id_adjunto = $request->idAdjunto;
            $gestionObligaciones->fechaRadicacion = $request->fechaRadicacion;
            $resultInsert = $gestionObligaciones->save();
            if ($resultInsert) {
                $this->guardarLogGestionObligaciones($gestionObligaciones->id, $request->idObligacion, $tipoAdjunto, $request->idAdjunto, false, false, $request->fechaRadicacion, false, $estado);
                $objFuncionesComponente = new FuncionesComponente();
                $html = $objFuncionesComponente->traerTablaAdjuntos(false, $request->idObligacion, false, false, config("constantes.PAZ_SALVO_CARTERA"));
                echo json_encode(["STATUS" => true, "tienePazSalvo" => $tienePazSalvo, "MENSAJE" => "Fechas almacenadas satisfactoriamente", "TABLA" => $html]);
                die;
            } else {
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al tratar de almacenar las fechas(No creo el dato) , por favor refresque la pagina e intentelo de nuevo"]);
            }
        }
    }

    function guardarLogGestionObligaciones($idGestionObligacion, $idObligacion = false, $tipoAdjunto = false, $idAdjunto = false, $fechaSolicitud = false, $fechaEntrega = false, $fechaRadicacion = false, $fechaVencimiento = false, $estado = false, $idAdjuntoSolicitud = false) {

        $idObligacion = (!empty($idObligacion) && $idObligacion != false) ? $idObligacion : null;
        $tipoAdjunto = (!empty($tipoAdjunto) && $tipoAdjunto != false) ? $tipoAdjunto : null;
        $idAdjunto = (!empty($idAdjunto) && $idAdjunto != false) ? $idAdjunto : null;
        $fechaSolicitud = (!empty($fechaSolicitud) && $fechaSolicitud != false) ? $fechaSolicitud : null;
        $fechaEntrega = (!empty($fechaEntrega) && $fechaEntrega != false) ? $fechaEntrega : null;
        $fechaRadicacion = (!empty($fechaRadicacion) && $fechaRadicacion != false) ? $fechaRadicacion : null;
        $fechaVencimiento = (!empty($fechaVencimiento) && $fechaVencimiento != false) ? $fechaVencimiento : null;
        $estado = (!empty($estado) && $estado != false) ? $estado : null;

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
        if (!empty($idAdjuntoSolicitud) && $idAdjuntoSolicitud != false) {
            $logGestionObligaciones->id_adjuntoSolicitud = $idAdjuntoSolicitud;
        }
        $logGestionObligaciones->usuario = Auth::user()->id;
        return $logGestionObligaciones->save();
    }

    function proyectarCertificacionesDeuda($idEstudio) {

        $infoEstudio = $this->validarAccesoCartera($idEstudio);

        /*
         * Primero hay que proyectar los intereses de este mes, 
         * estos pueden ser hasta el 15 o hasta fin de mes dependiendo de la fecha actual
         */

        $diaDeCorte = (date("d") < 15) ? 15 : date("t");

        //obtenemos la ultima causacion para ver cuanto se esta cobrando de interes corriente y de inetereses de mora
        $lastCausacion = DB::table('causacion')->where("idEstudio", $idEstudio)->orderBy('id', 'desc')->first();


        if(!isset($lastCausacion)){
            return 0;
        }

        //Se calculan los dias restantes para llegar al limite (15 o ultimo dia del mes)
        $diasRestantes = $diaDeCorte - date("d");

        //Proyeccion de intereses de mora
        if ($lastCausacion->interesMora > 0) {
            $banderaMora = true;
            $moraDelMesActual = $lastCausacion->interesMora * $diasRestantes;
        } else {
            $banderaMora = false;
            $moraDelMesActual = 0;
        }

        //se proyectan los intereses corrientes en este mes
        $interesesCorrientesDelMesActual = $lastCausacion->interesCorriente * $diasRestantes;



        /*
         * Segundo hay que proyectar los intereses (mora y corrientes) del mes siguiente en donde aplique
         */

        $seguro = 0;
        $interesesCorrientesDelMesSiguiente = 0;
        $interesesMoraDelMesSiguiente = 0;

        //Si es Mayor a la fecha de corte habria que proyectar los intereses del mes siguiente.
        if (date("d") > 15) {
            $seguro = $infoEstudio[0]->costoSeguro;

            //Primero se calcula cuantos dias tiene el mes siguiente
            $cantDiasMesSiguiente = date("t", strtotime("+1 month", strtotime("1-" . date("m-Y"))));

            //Traemos el ultimo balance para saber el saldo a capital actual
            $lastBalance = DB::table('balance')->where("idEstudio", $idEstudio)->orderBy('id', 'desc')->first();
            $valorCapital = $lastBalance->saldoCapital;
            $valorInteresDiaMesSiguiente = round(($valorCapital * ($infoEstudio[0]->Tasa / 100)) / $cantDiasMesSiguiente, 3);
            //Solamente se cobran los 15 dias del mes ya que hasta esa fecha se proyecta la certificacion
            $interesesCorrientesDelMesSiguiente = $valorInteresDiaMesSiguiente * 15;

            if ($banderaMora) {
                //se obtiene todo el abono a capital faltante ya que en este punto ya se tiene certeza de cual es el capital que deuda el cliente
                $totalBalancesEnMora = DB::table("balance")->select(DB::raw('SUM(balance.abonoCapital) as abonoCapital'))->where("balance.idEstudio", $idEstudio)->where("balance.abonoCapital", "<", 0)->get();
                $totalCapitalEnMora = $totalBalancesEnMora[0]->abonoCapital * -1;

                if ($totalCapitalEnMora > 0) {
                    $tasaMora = 2.42;
                    $valorInteresMoraDiaMesSiguiente = round(($totalCapitalEnMora * ($tasaMora / 100)) / 15, 3);
                    //Solamente se cobran los 15 dias del mes ya que hasta esa fecha se proyecta la certificacion
                    $interesesMoraDelMesSiguiente = $valorInteresMoraDiaMesSiguiente * 15;
                }
            }
        }


        /*
         * Finalmente se suma lo que debe hoy(restando los saldos a favor que tenga) contra los intereses corrientes y de mora tanto de el mes actual como de el siguiente (si corresponde)
         */
        $deuda = $this->calcularCuandoDebeHoy($idEstudio);
        $valorProyectado = $deuda + $interesesCorrientesDelMesActual + $moraDelMesActual + $interesesCorrientesDelMesSiguiente + $interesesMoraDelMesSiguiente;
        return $valorProyectado;
    }

    function pagoMasivo() {

        return view('pages.Cartera.Migrador.pagoMasivo')->with("pagaduriasPagoMasivo", $this->pagadurias_PagoMasivo);
    }

    function getData(Request $request) {

        if (isset($request->archivo) && $request->file('archivo')->isValid()) {
            $extension = $request->file('archivo')->getClientOriginalExtension();

            if (strtoupper($extension) == "PDF") {
                $response = $this->getDataPDF($request->archivo);
                echo json_encode($response);
                die;
            } elseif (strtoupper($extension) == "XLSX" || strtoupper($extension) == "XLS") {
                $response = $this->getDataExcel($request->archivo);
                echo json_encode($response);
                die;
            } else {
                $STATUS["VALUE"] = false;
                $STATUS["MENSAJE"] = "El archivo cargado tiene una extensi&oacute;n no soportada.";
            }
        } else {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "El archivo cargado esta da&ntilde;ado";
        }
        echo json_encode(["STATUS" => $STATUS]);
        die;
    }

    function getDataPDF($archivo) {
        $usuarios = [
            "USUARIOS" => "",
            "TOTAL" => 0
        ];
        $STATUS = [];
        $pagaduria = false;
        $total = 0;

        //Extraemos el contenido del pdf
        $contenido = $this->lecturaPDF($archivo);

        if (strlen($contenido) <= 1) {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "No fue posible extraer la informaci&oacute;n del documento.";
            return ["STATUS" => $STATUS];
        }

        //Corremos el proceso para seleccionar la pagaduria a la que pertenece el documento
        foreach ($this->pagadurias_PagoMasivo as $itemPagaduria) {//se recorren las pagadurias
            if (preg_match("/{$itemPagaduria["NIT"]}/", $contenido)) {//Se busca si en el contenido se encuentra en Nit de la pagaduria en cuestion
                $pagaduria = $itemPagaduria["NOMBRE"]; //Se setea el nombre
                break;
            }
        }

        //Si se encuentra la pagaduria a la que pertenece el documento, se corre el proceso de extraccion
        if ($pagaduria !== false) {

            switch ($pagaduria) {
                case $this->pagadurias_PagoMasivo[0]["NOMBRE"]://SEM CALI

                    $STATUS["VALUE"] = true;
                    //Se configura un array con informacin necesaria para la extraccion
                    $expresiones["GENERAL"] = "/[0-9]+(,[0-9]*)*	([a-zA-ZáéíóúÁÉÍÓÚñÑ]+)( {1,5}[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*	[0-9]+([,.][0-9]*)*/"; //Expresion regular que obtiene la fila con la informacion del cliente
                    $expresiones["CEDULA"] = "/[0-9]+,[0-9]+,[0-9]+(,[0-9]*)?/"; //Expresion regular que de la fila extrar unicamente las cedulas
                    $expresiones["NOMBRE"] = "/([a-zA-ZáéíóúÁÉÍÓÚñÑ]+)( {1,5}[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)+/"; //Expresion regular que de la fila extrae unicamente nos nombres
                    $expresiones["VALOR"] = "/(	[0-9]+)(,[0-9]+)+(.[0-9]+)/"; //Expresion regular que de la fila extrae unicamente los valores 
                    $expresiones["REPLACE_VALOR"]["SEARCH"] = ","; //informacion necesaria para realizar el replace a el valor
                    $expresiones["REPLACE_VALOR"]["REPLACE"] = ""; //informacion necesaria para realizar el replace a el valor
                    //Se extraen los usuarios del documento
                    $usuarios = $this->extraerData($contenido, $expresiones);

                    if (count($usuarios["USUARIOS"]) == 0) {//Si no se encontraron usuarios
                        $STATUS["VALUE"] = false;
                        $STATUS["MENSAJE"] = "No hay usuarios extra&iacute;dos del documento.";
                    }

                    break;
                case $this->pagadurias_PagoMasivo[1]["NOMBRE"]://FODE VALLE


                    $STATUS["VALUE"] = true;
                    $expresiones["GENERAL"] = "/[0-9]+(.[0-9])*	([a-zA-ZáéíóúÁÉÍÓÚñÑ]+)( {1,5}[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*	[0-9]+(.[0-9])*(,[0-9]*)/";
                    $expresiones["CEDULA"] = "/[0-9]+(.[0-9])*/";
                    $expresiones["NOMBRE"] = "/([a-zA-ZáéíóúÁÉÍÓÚñÑ]+)( {1,5}[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)+/";
                    $expresiones["VALOR"] = "/[0-9]+(.[0-9])*(,[0-9]*)/";
                    $expresiones["REPLACE_VALOR"]["SEARCH"] = [".", ","];
                    $expresiones["REPLACE_VALOR"]["REPLACE"] = ["", "."];

                    $usuarios = $this->extraerData($contenido, $expresiones);

                    if (!array_key_exists("USUARIOS", $usuarios) || count($usuarios["USUARIOS"]) == 0) {
                        $STATUS["VALUE"] = false;
                        $STATUS["MENSAJE"] = "No hay usuarios extra&iacute;dos del documento";
                    }

                    break;

                default:
                    $STATUS["VALUE"] = false;
                    $STATUS["MENSAJE"] = "La pagaduria no existe.";
                    break;
            }
        } else {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "El documento cargado no pertenece a ninguna de las pagadur&iacute;as configuradas.";
        }

        return ["STATUS" => $STATUS, "USUARIOS" => $usuarios["USUARIOS"], "PAGADURIA" => $pagaduria, "TOTAL" => $usuarios["TOTAL"]];
    }

    function getDataExcel($archivo) {
        //$address = public_path('archivos/NOVIEMBRE.xlsx');

        require '../vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $xls_data = $spreadsheet->getActiveSheet()->toArray();
        } catch (Exception $exc) {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "Ocurrio un problema al intentar extraer la informaci&iacute;n del archivo. [" . __LINE__ . "]";
            return ["STATUS" => $STATUS];
        }

        //Parametrizacion de columnas para SEM CALI
        $colum_Cedula = 5;
        $colum_Nombre = 10;
        $colum_Valor = 23;
        $colum_Nit = 6;
        //Fin parametrizacion

        $usuarios = [];
        $llavePagaduria = false; //Esta variable contendra la posicion de la pagaduria encontrada
        $encontroNit = false; //Bandera para validar si ya se encontro 
        $total = 0;
        //Se recorren las filas
        foreach ($xls_data as $fila) {

            if ($encontroNit === false && !is_null($fila[$colum_Nit])) {

                $probableNit = $fila[$colum_Nit]; //Este elemento puede probablemente ser uno de los NIT parametrizados en el array
                //Se verifica si el NIT probable se encuentra en la lista y esta parametrizado
                $result = array_search($probableNit, array_column($this->pagadurias_PagoMasivo, "NIT"));
                if ($result !== false) {
                    //Si esta en la lista cargamos el valor y dejamos de buscar porque ya lo encontramos, asi que se procedera a buscar los usuarios
                    $llavePagaduria = $result;
                    $encontroNit = true;
                } else {
                    continue;
                }
            } elseif ($encontroNit === false) {
                continue;
            }

            //Ahora iniciamos la recoleccion de los usuarios
            if ($llavePagaduria !== false) {
                if (!is_null($fila[$colum_Cedula]) && is_numeric(str_replace(",", "", $fila[$colum_Cedula]))) {

                    $cedula = (!is_null($fila[$colum_Cedula])) ? str_replace(",", ".", $fila[$colum_Cedula]) : false;
                    $nombre = (!is_null($fila[$colum_Nombre])) ? $fila[$colum_Nombre] : false;
                    $valor = (!is_null($fila[$colum_Valor])) ? str_replace(",", "", $fila[$colum_Valor]) : false;

                    if ($cedula !== false && $nombre !== false && $valor !== false) {
                        $total += $valor;

                        $usuarios[] = [
                            "cedula" => $cedula,
                            "nombre" => $nombre,
                            "valor" => "$" . number_format($valor, 2, ",", ".")
                        ];
                    }
                }
            }
        }

        if ($encontroNit === false) {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "La pagaduria no existe.";
            return ["STATUS" => $STATUS];
        }

        if (count($usuarios) == 0) {
            $STATUS["VALUE"] = false;
            $STATUS["MENSAJE"] = "No hay usuarios extra&iacute;dos del documento.";
            return ["STATUS" => $STATUS];
        }

        $STATUS["VALUE"] = true;
        return ["STATUS" => $STATUS, "USUARIOS" => $usuarios, "PAGADURIA" => $this->pagadurias_PagoMasivo[$llavePagaduria]["NOMBRE"], "TOTAL" => "$" . number_format($total, 2, ",", ".")];
    }

    function lecturaPDF($archivo) {

        include '../vendor/autoload.php';

        // Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();

        //$pdf    = $parser->parseFile('../storage/lecturaPdf/DICIMEBRE.pdf');
        $pdf = $parser->parseFile($archivo);

        // Retrieve all pages from the pdf file.
        $pages = $pdf->getPages();

        // Loop over each page to extract text.       
        $contenido = "";
        foreach ($pages as $page) {
            $contenido .= $page->getText();
        }
        return $contenido;
    }

    function extraerData($contenido, $expresiones) {
        //Array con la informacion de los usuarios filtrados
        $usuariosExtraidos = [];
        $total = 0;

        preg_match_all($expresiones["GENERAL"], $contenido, $matches);
        if ($matches != false && isset($matches[0]) && count($matches) > 0) {
            foreach ($matches[0] as $cliente) {
                preg_match_all($expresiones["CEDULA"], $cliente, $cedulas);
                preg_match_all($expresiones["NOMBRE"], $cliente, $nombres);
                preg_match_all($expresiones["VALOR"], $cliente, $valores);

                if ($cedulas !== false && $nombres !== false && $valores !== false && isset($cedulas[0]) && isset($nombres[0]) && isset($valores[0]) && count($cedulas) > 0 && count($nombres) > 0 && count($valores) > 0) {


                    $valor = str_replace($expresiones["REPLACE_VALOR"]["SEARCH"], $expresiones["REPLACE_VALOR"]["REPLACE"], $valores[0][0]);
                    $total += $valor;

                    $usuariosExtraidos[] = [
                        "cedula" => str_replace(",", ".", $cedulas[0][0]),
                        "nombre" => $nombres[0][0],
                        "valor" => "$" . number_format($valor, 2, ",", ".")
                    ];
                }
            }
        }

        /* $usuariosExtraidos[]= [
          "cedula" => "94.521.171",
          "nombre" => "GUSTAVO EFREN ORDOÃEZ FERNANDEZ",
          "valor" => "$750.000,00"
          ]; */
        return ["USUARIOS" => $usuariosExtraidos, "TOTAL" => "$" . number_format($total, 2, ",", ".")];
    }

    function pagoMasivoPagar(Request $request) {
        if (isset($request->soporte) || $request->file('archivo')->isValid()) {

            //Variables Generales de la funcion
            $listaFinal = [];

            if (!empty($request->data)) {
                $usuarios = json_decode($request->data);
                foreach ($usuarios->arrayUsuarios as $infoUser) {
                    $identificaciones = $this->validarExistenciaUsuario(str_replace(".", "", $infoUser[1]));
                    if ($identificaciones != false) {
                        $deuda = $this->calcularCuandoDebeHoy($identificaciones[0]->idEstudio);
                        if ($deuda > 0) {

                            $valorPago = str_replace([".", "$", ","], ["", "", "."], $infoUser[3]);
                            $response = $this->desicionPago($identificaciones[0]->idEstudio, $valorPago, date("Y-m-d"), $request->file('archivo'));

                            $balance = ($response["STATUS"]) ? $deuda - $valorPago : $deuda;
                            $listaFinal[] = [
                                "infoUser" => $infoUser,
                                "MENSAJE" => $response["MENSAJE"],
                                "STATUS" => $response["STATUS"],
                                "BALANCE" => $balance
                            ];
                        } else {
                            $listaFinal[] = [
                                "infoUser" => $infoUser,
                                "MENSAJE" => "El usuario no debe nada.",
                                "STATUS" => true,
                                "BALANCE" => 0
                            ];
                        }
                    } else {
                        $listaFinal[] = [
                            "infoUser" => $infoUser,
                            "MENSAJE" => "El usuario no existe. o el estudio esta en estado diferente de [CAR, PRT, BANC]",
                            "STATUS" => true,
                            "BALANCE" => 0
                        ];
                        //El usuario no existe o el estudio se encuentra en un estado diferente a cartera, PRt o BANC
                    }
                }
            }


            echo json_encode(["STATUS" => true, "CONTENIDO" => $this->construirTabla($listaFinal)]);
        } else {
            echo json_encode(["STATUS" => false, "MENSAJE" => "El archivo a procesar esta da&ntilde;ado"]);
        }
    }

    function construirTabla($listado) {
        $html = '
                    <h3 class="text-center bold">RESULTADOS</h3>
                    <table class="table table-striped table-hover table-condensed text-center table-bordered"> 
                        <thead class="fondoHeaderTabla">
                            <tr>
                                <th class="text-center">CÉDULA</th>
                                <th class="text-center">NOMBRE</th>
                                <th class="text-center">VALOR</th>
                                <th class="text-center">BALANCE</th>
                                <th class="text-center">ERROR</th>
                                <th class="text-center">MENSAJE</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($listado as $item) {
            $html .= '
                            <tr>
                                <td>' . $item['infoUser'][1] . '</td>                           
                                <td>' . $item['infoUser'][2] . '</td>                           
                                <td>' . $item['infoUser'][3] . '</td>          
                                <td>' . number_format(ceil($item['BALANCE']), 0, ",", ".") . '</td>          
                                <td>' . (($item['STATUS']) ? "NO" : "SI") . '</td>
                                <td>' . $item['MENSAJE'] . '</td>
                            </tr>
                         ';
        }

        $html .= '</tbody>
                    </table>';

        return $html;
    }

    function validarExistenciaUsuario($cedula) {
        $result = Estudio::join("valoraciones", "valoraciones.id", "estudios.Valoracion")
                        ->join("users", "users.id", "valoraciones.Usuario")
                        ->where("users.cedula", $cedula)
                        ->whereIn("estudios.estado", [config("constantes.ESTUDIO_CARTERA"), config("constantes.ESTUDIO_PROCESO_TESORERIA")])
                        ->select("users.id as idUsuario", "valoraciones.id as idValoracion", "estudios.id as idEstudio")->get();

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function generarPYS($idEstudio) {

        $infoEstudio = DB::table('estudios')->select('users.nombre', 'users.apellido', 'users.cedula', 'estudios.Pagaduria', 'estudios.id')
                ->where('estudios.id', $idEstudio)
                ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                ->join('users', 'valoraciones.Usuario', '=', 'users.id')
                ->get();
        //return view('pages.Cartera.pdfPys')->with("infoEstudio", $infoEstudio[0]);
        $data = [
            "nombre" => $infoEstudio[0]->nombre,
            "apellido" => $infoEstudio[0]->apellido,
            "cedula" => $infoEstudio[0]->cedula,
            "pagaduria" => $infoEstudio[0]->Pagaduria
        ];

        $pdf = PDF::loadView('pages.Cartera.pdfPys', compact("data", "idEstudio"))->setPaper('letter', 'portrait');
        set_time_limit(0);
        return $pdf->stream();
    }

    public function generarCDD(Request $request) {
        $infoEstudio = DB::table('estudios')->select(DB::raw("CONCAT(users.nombre,' ',users.apellido) AS nombre"), 'users.cedula', 'estudios.Pagaduria', 'estudios.id', 'estudios.Cuota')
                ->where('estudios.id', $request->id)
                ->join('valoraciones', 'valoraciones.id', '=', 'estudios.Valoracion')
                ->join('users', 'valoraciones.Usuario', '=', 'users.id')
                ->get();
        $valorProyectado = $request->vlrCertificado;
        $objCifrasEnLetras = new CifrasEnLetras();
        $valorLetras = $objCifrasEnLetras->convertirCifrasEnLetras($valorProyectado);
        $cuotaLetras = $objCifrasEnLetras->convertirCifrasEnLetras(number_format($infoEstudio[0]->Cuota, 0, ",", ""));
        $fechaProyeccion = strtotime('+1 month', strtotime(date('Y-m-d')));
        $mesProyecccion = date('F', $fechaProyeccion);
        $anioProyeccion = date('Y', $fechaProyeccion);

        $data = [
            "id_estudio" => $request->id,
            "nombre" => (string) utf8_decode($infoEstudio[0]->nombre),
            "cedula" => (string) $infoEstudio[0]->cedula,
            "valorProyectado" => (string) $valorProyectado,
            "valorLetras" => (string) $valorLetras,
            "pagaduria" => (string) $infoEstudio[0]->Pagaduria,
            "valorCuota" => (string) $infoEstudio[0]->Cuota,
            "diaCorte" => (string) config('constantes.CAR_FECHA_CORTE'),
            "mesVigencia" => (string) $mesProyecccion,
            "anioVigencia" => (string) $anioProyeccion,
            "cuotaLetras" => (string) $cuotaLetras,
            "usuarioCreacion" => (string) Auth::user()->id,
            "estado" => (string) "1"
        ];
        $insert = DB::table('log_certificaciones')->insert($data);
        $datos = $this->validarCertificacion($request->id);
        return $datos;
    }

    public function consultarCertificacion($idEstudio) {
        /* pdt validar sino existe certificacion mostrar error */
        $infoCertificacion = $this->validarCertificacion($idEstudio);
        $data = [
            "id_estudio" => $idEstudio,
            "nombre" => $infoCertificacion[0]->nombre,
            "cedula" => $infoCertificacion[0]->cedula,
            "valorProyectado" => $infoCertificacion[0]->valorProyectado,
            "valorLetras" => $infoCertificacion[0]->valorLetras,
            "pagaduria" => $infoCertificacion[0]->pagaduria,
            "valorCuota" => $infoCertificacion[0]->valorCuota,
            "diaCorte" => config('constantes.CAR_FECHA_CORTE'),
            "mesVigencia" => $infoCertificacion[0]->mesVigencia,
            "anioVigencia" => $infoCertificacion[0]->anioVigencia,
            "cuotaLetras" => $infoCertificacion[0]->cuotaLetras,
            "usuarioCreacion" => (string) Auth::user()->id,
            "estado" => "1"
        ];
        $pdf = PDF::loadView('pages.Cartera.pdfCdd', compact("data"));
        set_time_limit(0);
        return $pdf->stream();
    }

    /* PDTE BORRAR */

    public function generarPdfCertificacion($idEstudio) {
        $data = $this->generarCDD($idEstudio);
        $pdf = PDF::loadView('pages.Cartera.pdfCdd', compact("data"));
        set_time_limit(0);
        return $pdf->stream();
    }

    /* ------------- */

    public function validarCertificacion($idEstudio) {
        $infoCertificacion = LogCertificaciones::join('users', 'users.id', '=', 'log_certificaciones.usuarioCreacion')
                ->where('log_certificaciones.id_estudio', '=', $idEstudio)
                ->whereIn('log_certificaciones.estado', [1, 2])
                ->select('log_certificaciones.*', DB::raw('concat(users.nombre, " ",users.apellido) as comercial'))
                ->get();

        if (count($infoCertificacion)) {
            return $infoCertificacion;
        } else {
            return false;
        }
    }

    /*
     * Funcion para actualizar el comercial seleccionado en la vista de detalle de cartera
     */

    function setComercialCartera(Request $request) {
        try {
            $result = Estudio::where("id", $request->pk)->update(["ComercialCartera" => $request->value]);
            if ($result) {
                echo json_encode(["STATUS" => true, "MENSAJE" => "Informaci&oacute;n almacenada con &eacute;xito"]);
            } else {
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar. Intente de nuevo"]);
            }
        } catch (\Illuminate\Database\QueryException $exc) {
            $mensaje = (isset($exc->errorInfo[2])) ? $exc->errorInfo[2] : $exc->getMessage();
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar [" . $mensaje . "]"]);
        }
    }

    /*
     * Funcion para guardar el banco para el que se fue el cliente en cartera
     */

    function setBancoCartera(Request $request) {
        try {
            $result = Estudio::where("id", $request->pk)->update(["BancoFinal" => $request->value]);
            if ($result) {
                echo json_encode(["STATUS" => true, "MENSAJE" => "Informaci&oacute;n almacenada con &eacute;xito"]);
            } else {
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar. Intente de nuevo"]);
            }
        } catch (\Illuminate\Database\QueryException $exc) {
            $mensaje = (isset($exc->errorInfo[2])) ? $exc->errorInfo[2] : $exc->getMessage();
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar [" . $mensaje . "]"]);
        }
    }

    /*
     * Funcion para almacenar el valor que el banco aprobo
     */

    function setValorAprobadoBanco(Request $request) {
        try {
            $result = Estudio::where("id", $request->pk)->update(["ValorAprobadoBanco" => str_replace(".", "", $request->value)]);
            if ($result) {
                echo json_encode(["STATUS" => true, "MENSAJE" => "Informaci&oacute;n almacenada con &eacute;xito"]);
            } else {
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar. Intente de nuevo"]);
            }
        } catch (\Illuminate\Database\QueryException $exc) {
            $mensaje = (isset($exc->errorInfo[2])) ? $exc->errorInfo[2] : $exc->getMessage();
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar [" . $mensaje . "]"]);
        }
    }

    /*
     * funcion para actualizar el estado de cartera que se seleccione
     */

    function setEstadoCartera(Request $request) {
        try {
            $result = Estudio::where("id", $request->pk)->update(["EstadoCartera" => str_replace(".", "", $request->value)]);
            if ($result) {
                echo json_encode(["STATUS" => true, "MENSAJE" => "Informaci&oacute;n almacenada con &eacute;xito"]);
            } else {
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar. Intente de nuevo"]);
            }
        } catch (\Illuminate\Database\QueryException $exc) {
            $mensaje = (isset($exc->errorInfo[2])) ? $exc->errorInfo[2] : $exc->getMessage();
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar guardar [" . $mensaje . "]"]);
        }
    }

}
