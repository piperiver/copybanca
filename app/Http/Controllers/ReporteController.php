<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use View;
use PDF;
use Excel;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    protected $forma = "REPOR";
    
    function reportesCentrales(){
       
        //Meses
        
        $meses = array(
            1 =>"Enero",
            2 =>"Febrero",
            3 =>"Marzo",
            4 =>"Abril",
            5 =>"Mayo",
            6 =>"Junio",
            7 =>"Julio",
            8 =>"Agosto",
            9 =>"Septiembre",
            10 =>"Octubre",
                11 =>"Noviembre",
            12 =>"Diciembre",            
        );
        //Año actual
        $annoActual = date('Y');
        return view('pages.Reportes.index')
                        ->with('annoActual',$annoActual)
                        ->with('meses',$meses);
    
    }
    
    function generarReporteCifin($mes, $anno){
        
       //Calcular el estado de la obligacion
       //Sacar el Valor real pagado
       /*
        * Cedula Cliente - Completar 15 digitos
        * Numero de la Obligaciòn
        * Codigo Sucursal - 0001
        * Calidad Cliente - Que responsabilidad tiene un credito
        * Estado - Como se encuentra el credito
        * Fecha de corte 
        * Fecha exigibilidad y prescripcion 
        * Fecha de pago del cliente
        * Tipo de pago -
        * Periocidad - Que periocidad tiene la obligacion
        * Cuotas pagadas
        * Coutas pactadas
        * Si hay Mora
        * Valor cargo fijo es muy similar a la couta
        * Correo del cliente
        * Celular del cliente 
        * Genera archivo plano partida la informacion en columnas
        * 
       */

        //Formato de fechas AAMMDD
        $reporteCifin = DB::select("
                                    SELECT estudios.id as idEstudio, 
                                           users.cedula as Cedula, 
                                           CONCAT(users.nombre, ' ', users.apellido) as nombre, 
                                           users.ciudad as ciudadCorrespondencia, 
                                           users.direccion as direccionCorrespondencia, 
                                           users.email as correoCorrespondencia, 
                                           users.telefono as celuCorrespondencia, 
                                           estudios.Desembolso as desembolso, 
                                           estudios.Plazo as plazo,
                                           estudios.Cuota as cuotaMensual, 
                                           estudios.created_at as created_at, 
                                           (SELECT saldoCapital FROM balance where balance.idEstudio = estudios.id order by balance.id desc limit 1) as saldoDeuda,
                                           (SELECT COUNT(balance.id) FROM balance where balance.idEstudio = estudios.id) as cuotasPagadas,
                                           (SELECT adjuntos.updated_at FROM obligaciones 
                                                   LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = '" . config("constantes.KEY_OBLIGACION") . "' AND adjuntos.TipoAdjunto = '" . config("constantes.SOPORTE_PAGO") . "' "
            . "                            JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.tipoAdjunto = '" . config("constantes.CERTIFICACIONES_DEUDA") . "' AND gestionobligaciones.estado in('" . config("constantes.GO_PAGADA") . "') 
                                                   WHERE obligaciones.Valoracion = valoraciones.id AND obligaciones.Estado = 'Activo' AND obligaciones.Compra = 'S' order by adjuntos.updated_at desc limit 1) as FechaDesembolsoInicial                                                                                       
                                   FROM estudios
                                   JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                   JOIN users ON valoraciones.Usuario = users.id                                       
                                   WHERE estudios.Estado IN('" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "') AND MONTH(estudios.created_at) = ".$mes." AND YEAR(estudios.created_at) = ".$anno."                                      
                                   ORDER BY FIELD(estudios.Estado, '" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')");

        $reporteCifin = collect($reporteCifin);
        /*$reporteData = collect($reporteData)->map(function($value, $key){
            $datetime = new DateTime($value->created_at);
            $annoEstudio = $datetime->format('w');
            $mesEstudio = $datetime->format('n');

            if($annoEstudio == $anno && $mesEstudio == $mes){
                return $value;
            }
        });
        */
        $reporteCifin->map(function ($reporte) {
            $date = new DateTime($reporte->FechaDesembolsoInicial);
            $cuotasEsperadas = $date->diff(new DateTime())->m;
            $auxiliar_date = $date;
            $reporte->calificacion = "A";
            $reporte->novedad = 01;
            $reporte->estadoCuenta = 01;
            //Si las cuotas pagadas es menor que las cuotas esperadas esta en mora
            if ($cuotasEsperadas != $reporte->cuotasPagadas) {
                $mesesEnMora = $cuotasEsperadas - $reporte->cuotasPagadas;
                $diasEnMora = $mesesEnMora * 30;
                $reporte->estadoCuenta = 02;
                $reporte->edadMora = $diasEnMora;
                $pagos = collect(DB::select("SELECT * from pago where idEstudio=" . $reporte->idEstudio))->map(function ($value, $key) {
                    $value->fecha = substr($value->fecha, 0, 10);
                    return $value;
                });
                $fechas_de_pago = [];
                for ($i = 0; $i < $cuotasEsperadas; $i++) {
                    $auxiliar_date->modify('+' . $i . ' month');

                    $exists = $pagos->filter(function ($value, $key) use ($auxiliar_date) {
                        return substr($value->fecha, 0, 7) === $auxiliar_date->format('Y-m');
                    });
                    if ($exists->isEmpty()){
                        $fechas_de_pago[] = $auxiliar_date->format('Y-m');
                    }else{
                        if((int)substr($exists[0]->fecha, 8, 9) > 15){
                            $second_date = new DateTime($exists[0]->fecha);
                            $second_date->modify('first day of this month');
                            $fechas_de_pago[] = $second_date->format('Y-m');
                        };
                    }
                }
                $value = 0;
                foreach ($fechas_de_pago as $fecha){
                    $value +=  DB::select("SELECT SUM(interesCorriente + coalesce(interesMora, 0 ) + coalesce(abonoCapital, 0)) as saldo from causacion where idEstudio=" . $reporte->idEstudio)[0]->saldo;
                }
                $reporte->mesesEnMora = $mesesEnMora;

                switch($mesesEnMora){
                    case ($mesesEnMora > 12):
                        $reporte->calificacion = "E";
                        break;
                    case ($mesesEnMora > 6):
                        $reporte->calificacion = "D";
                        break;
                    case ($mesesEnMora > 3):
                        $reporte->calificacion = "C";
                        break;
                    case ($mesesEnMora > 2):
                        $reporte->calificacion = "B";
                        break;

                }

                $reporte->diasEnMora = $diasEnMora;

                switch($diasEnMora){
                    case ($diasEnMora >= 30 && $diasEnMora < 60):
                        $reporte->novedad = 6;
                        break;
                    case ($diasEnMora >= 60 && $diasEnMora < 90):
                        $reporte->novedad = 7;
                        break;
                    case ($diasEnMora >= 90 && $diasEnMora < 120):
                        $reporte->novedad = 8;
                        break;
                    case ($diasEnMora == 120):
                        $reporte->novedad = 9;
                        break;
                    case ($diasEnMora > 120):
                        $reporte->novedad = 12;
                        $reporte->estadoCuenta = 5;
                        break;

                }
                $reporte->valorSaldoMora = $value;
            }
            //Si hay un pago total $reporte->novedad = 05  $reporte->estadoCuenta = 3;

            $date->modify('last day of this month');

            $reporte->fechaInicioContrato = $date->format('Y-m-d');
            $date->modify('+' . $reporte->plazo . ' month');
            $reporte->fechaFinDelContrato = $date->format('Y-m-d');

            //Definicion Formato de fecha reporte
            $reporte->fechaInicioContrato = str_replace("-","", $reporte->fechaInicioContrato);
            $reporte->fechaFinDelContrato = str_replace("-","", $reporte->fechaFinDelContrato);


            $reporte->tipoIdentificacion = 1;
            $reporte->numObligacion = "NO REGISTRADO POR EL SISTEMA";
            $reporte->situaTitular = 0;
            $reporte->responsable = 00;
            $reporte->formaPago = 0;

            $reporte->estadoOrigenCuenta = 0;
            $reporte->fechaEstadoOrigen = $reporte->fechaInicioContrato;
            $reporte->fechaEstadoCuenta = $reporte->fechaInicioContrato;

            $reporte->adjetivo = 00;
            $reporte->fechaAdjetivo = "";

            if($reporte->novedad == 01){
                $reporte->edadMora = 000;
            }

            $reporte->valorDisponible = 0;

            $reporte->clausulaPermanencia = $reporte->plazo;
            $reporte->fechaClausulaPermanencia = $reporte->fechaInicioContrato;

            //Segun el mes actual ultimo dia del mes anterior

            $mesAnterior = new DateTime();
            $mesAnterior->modify('last day of this month');
            $mesAnterior->format('d/m/Y');
            $reporte->fechalimitePago = str_replace("-","",$mesAnterior->format('Y-m-d'));
            $reporte->fechaPago = str_replace("-","",$mesAnterior->format('Y-m-d'));

            //Datos de la Oficina
            $reporte->codigoDaneCorrespondencia = "NO REGISTRADO POR EL SISTEMA";
            $reporte->departamenteCorrespondencia = "NO REGISTRADO POR EL SISTEMA";

            /*Datos por definir
                <th> Fecha de desembolso Inicial</th>
             *
                <td>{{$registro->FechaDesembolsoInicial}}</td>
             *
             */

        });
        
        return $reporteCifin;
    }
    
    public function generarReporteData($mes,$anno){
        
        //Formato de fechas AAMMDD
        $reporteData = DB::select("
                                    SELECT estudios.id as idEstudio, 
                                           users.cedula as Cedula, 
                                           CONCAT(users.nombre, ' ', users.apellido) as nombre, 
                                           users.ciudad as ciudadCorrespondencia, 
                                           users.direccion as direccionCorrespondencia, 
                                           users.email as correoCorrespondencia, 
                                           users.telefono as celuCorrespondencia, 
                                           estudios.Desembolso as desembolso, 
                                           estudios.Plazo as plazo,
                                           estudios.Cuota as cuotaMensual, 
                                           estudios.created_at as created_at, 
                                           (SELECT saldoCapital FROM balance where balance.idEstudio = estudios.id order by balance.id desc limit 1) as saldoDeuda,
                                           (SELECT COUNT(balance.id) FROM balance where balance.idEstudio = estudios.id) as cuotasPagadas,
                                           (SELECT adjuntos.updated_at FROM obligaciones 
                                                   LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = '" . config("constantes.KEY_OBLIGACION") . "' AND adjuntos.TipoAdjunto = '" . config("constantes.SOPORTE_PAGO") . "' "
                    . "                            JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.tipoAdjunto = '" . config("constantes.CERTIFICACIONES_DEUDA") . "' AND gestionobligaciones.estado in('" . config("constantes.GO_PAGADA") . "') 
                                                   WHERE obligaciones.Valoracion = valoraciones.id AND obligaciones.Estado = 'Activo' AND obligaciones.Compra = 'S' order by adjuntos.updated_at desc limit 1) as FechaDesembolsoInicial                                                                                       
                                   FROM estudios
                                   JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                   JOIN users ON valoraciones.Usuario = users.id                                       
                                   WHERE estudios.Estado IN('" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "') AND MONTH(estudios.created_at) = ".$mes." AND YEAR(estudios.created_at) = ".$anno."                                      
                                   ORDER BY FIELD(estudios.Estado, '" . config("constantes.ESTUDIO_PROCESO_TESORERIA") . "','" . config("constantes.ESTUDIO_CARTERA") . "', '" . config("constantes.ESTUDIO_BANCO") . "')");
            
            $reporteData = collect($reporteData);                       
            /*$reporteData = collect($reporteData)->map(function($value, $key){
                $datetime = new DateTime($value->created_at);
                $annoEstudio = $datetime->format('w');
                $mesEstudio = $datetime->format('n');
                
                if($annoEstudio == $anno && $mesEstudio == $mes){
                    return $value;
                }
            });
            */
            $reporteData->map(function ($reporte) {
                $date = new DateTime($reporte->FechaDesembolsoInicial);
                $cuotasEsperadas = $date->diff(new DateTime())->m;
                $auxiliar_date = $date;
                $reporte->calificacion = "A";
                $reporte->novedad = 01;
                $reporte->estadoCuenta = 01;
                //Si las cuotas pagadas es menor que las cuotas esperadas esta en mora
                if ($cuotasEsperadas != $reporte->cuotasPagadas) {
                    $mesesEnMora = $cuotasEsperadas - $reporte->cuotasPagadas;
                    $diasEnMora = $mesesEnMora * 30;
                    $reporte->estadoCuenta = 02;
                    $reporte->edadMora = $diasEnMora;
                    $pagos = collect(DB::select("SELECT * from pago where idEstudio=" . $reporte->idEstudio))->map(function ($value, $key) {
                        $value->fecha = substr($value->fecha, 0, 10);
                        return $value;
                    });
                    $fechas_de_pago = [];
                    for ($i = 0; $i < $cuotasEsperadas; $i++) {
                        $auxiliar_date->modify('+' . $i . ' month');

                        $exists = $pagos->filter(function ($value, $key) use ($auxiliar_date) {
                            return substr($value->fecha, 0, 7) === $auxiliar_date->format('Y-m');
                        });
                        if ($exists->isEmpty()){
                            $fechas_de_pago[] = $auxiliar_date->format('Y-m');
                        }else{
                            if((int)substr($exists[0]->fecha, 8, 9) > 15){
                                $second_date = new DateTime($exists[0]->fecha);
                                $second_date->modify('first day of this month');
                                $fechas_de_pago[] = $second_date->format('Y-m');
                            };
                        }
                    }
                    $value = 0;
                    foreach ($fechas_de_pago as $fecha){
                        $value +=  DB::select("SELECT SUM(interesCorriente + coalesce(interesMora, 0 ) + coalesce(abonoCapital, 0)) as saldo from causacion where idEstudio=" . $reporte->idEstudio)[0]->saldo;
                    }
                    $reporte->mesesEnMora = $mesesEnMora;

                    switch($mesesEnMora){
                        case ($mesesEnMora > 12):
                            $reporte->calificacion = "E";
                            break;
                        case ($mesesEnMora > 6):
                            $reporte->calificacion = "D";
                            break;
                        case ($mesesEnMora > 3):
                            $reporte->calificacion = "C";
                            break;
                        case ($mesesEnMora > 2):
                            $reporte->calificacion = "B";
                            break;

                    }
                    
                    $reporte->diasEnMora = $diasEnMora;
                    
                    switch($diasEnMora){
                        case ($diasEnMora >= 30 && $diasEnMora < 60):
                            $reporte->novedad = 6;
                            break;
                        case ($diasEnMora >= 60 && $diasEnMora < 90):
                            $reporte->novedad = 7;
                            break;
                        case ($diasEnMora >= 90 && $diasEnMora < 120):
                            $reporte->novedad = 8;
                            break;
                        case ($diasEnMora == 120):
                            $reporte->novedad = 9;
                            break;
                        case ($diasEnMora > 120):
                            $reporte->novedad = 12;
                            $reporte->estadoCuenta = 5;
                            break;

                    }
                    $reporte->valorSaldoMora = $value;
                }
                //Si hay un pago total $reporte->novedad = 05  $reporte->estadoCuenta = 3;
                
                $date->modify('last day of this month');
                
                $reporte->fechaInicioContrato = $date->format('Y-m-d');
                $date->modify('+' . $reporte->plazo . ' month');
                $reporte->fechaFinDelContrato = $date->format('Y-m-d');
                
                //Definicion Formato de fecha reporte
                $reporte->fechaInicioContrato = str_replace("-","", $reporte->fechaInicioContrato);
                $reporte->fechaFinDelContrato = str_replace("-","", $reporte->fechaFinDelContrato);
                
                
                $reporte->tipoIdentificacion = 1;
                $reporte->numObligacion = "NO REGISTRADO POR EL SISTEMA";
                $reporte->situaTitular = 0;
                $reporte->responsable = 00;
                $reporte->formaPago = 0;
                
                $reporte->estadoOrigenCuenta = 0;
                $reporte->fechaEstadoOrigen = $reporte->fechaInicioContrato;
                $reporte->fechaEstadoCuenta = $reporte->fechaInicioContrato;
                
                $reporte->adjetivo = 00;
                $reporte->fechaAdjetivo = "";
                
                if($reporte->novedad == 01){
                    $reporte->edadMora = 000;
                }
                
                $reporte->valorDisponible = 0;
                
                $reporte->clausulaPermanencia = $reporte->plazo;
                $reporte->fechaClausulaPermanencia = $reporte->fechaInicioContrato;
                
                //Segun el mes actual ultimo dia del mes anterior
                
                $mesAnterior = new DateTime();
                $mesAnterior->modify('last day of this month');
                $mesAnterior->format('d/m/Y');
                $reporte->fechalimitePago = str_replace("-","",$mesAnterior->format('Y-m-d'));
                $reporte->fechaPago = str_replace("-","",$mesAnterior->format('Y-m-d'));
                
                //Datos de la Oficina
                $reporte->codigoDaneCorrespondencia = "NO REGISTRADO POR EL SISTEMA";
                $reporte->departamenteCorrespondencia = "NO REGISTRADO POR EL SISTEMA";
                
                /*Datos por definir 
                    <th> Fecha de desembolso Inicial</th>
                 * 
                    <td>{{$registro->FechaDesembolsoInicial}}</td>
                 *                  
                 */
                
            });

            return $reporteData;
        
    }
    
    public function ajaxLstTable(Request $request){
        
       $data = $request->all();
       
       $html = "";
       $msg = "";
       $title = "";
        
       if($data['tipo_reporte'] == 1 ){

             $reporte = $this->generarReporteData($data['mes'],$data['anno']);
             
             if(count($reporte) == 0){
                 $msg = "No hay datos registrados";
             }
             
             $html = view::make('pages.Reportes.tablaReporteData')
                         ->with('reporteData',$reporte)    
                         ->render();
             
             $title = "Reporte Data Credito";

        }

        if($data['tipo_reporte'] == 2 ){

             $reporte = $this->generarReporteCifin($data['mes'],$data['anno']);
             
             if(count($reporte) == 0){
                 $msg = "No hay datos registrados";
             }
             
             $html = view::make('pages.Reportes.tablaReporteCifin')
                         ->with('reporteData',$reporte)    
                         ->render();
             
             $title = "Reporte Trasunion";

        }

        echo json_encode([
            "tabla_reporte" => $html,
            "msg" => $msg,
            "title" => $title
        ]);
          
    }
    
}


