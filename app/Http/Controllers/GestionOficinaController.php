<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use App\GestionOficina;
use App\Obligacion;
use App\Tarea;
use App\Estudio;
use DB;
use App\Librerias\ComponentAdjuntos;

class GestionOficinaController extends Controller
{
    protected $forma = "GESOF";
  
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        /**Obtiene las valoraciones que hasta el momento no tienen ningun estudio asociado**/
        $gestionValoraciones = $this->valoraciones();

        /*
         *Obtiene los estudios no viables
        */
        $estudiosNoViables = $this->noViables();

        /**
         * Obtiene todos los certificados de deudas y las libranzas que se han cargado y que faltan por cargar 
         * de todos los estudios que se encuentran en estado firmado o aprobado gestión
         * viables firmados
         * 
        **/
        $gestionComercial = $this->gestionComercial();

        /*Obtiene los estudio que falta ser aprobados*/
        $comiteCredito = $this->comiteCredito();

       /*
        *Obtiene los visados y soportes adicionales que se han cargado o que faltan por cargar cuando el estudio se encuentra en estado visado
        * 
        */
       $gestionFabrica = $this->gestionFabrica();
       
       /**
        * Obtiene todos los soportes de pagos en estado pagado, 
        * vencido o radicado de los estudios que se encuentra en estado 
        * tesoreria o proceso de tesoreria
       **/
        $gestionTesoreria = $this->gestionTesoreria();

        $gestionTesoreria = collect($gestionTesoreria)->groupBy('estudio');

        $pendiente = 0;
        $contTesoreria = 0;

        foreach ($gestionTesoreria as $ges){

            foreach ($ges as $item){

                if($item->entidad == 'Saldo Cliente' && $item->valorXgirar <= 0){
                    $item->estado = 'PAGADO';
                }
                if($item->entidad == 'Saldo Cliente'){
                    $pendiente += $item->valorXgirar;
                }

                $pendiente += $item->saldoObligacion;
                $contTesoreria++;
            }

        }
        
        /**
         * 
         * Obtiene los paz y salvo que se han cargado o que faltan por cargar de los estudios 
         * que se encuentra en estado proceso de tesoreria, cartera y banco
        **/
        $gestionCartera = $this->gestionCartera();

        $gestionCartera = collect($gestionCartera)->where('banderaSoportePago','Cargado en TES');
        $gestionCartera = collect($gestionCartera)->where('estado','<>','RAD');

        $listComercial = array('SOL','VEN', 'CAN', 'No Cargada');
        $listFabrica = array('No Cargado');
        $listCartera = array('No Cargado','CAN','SOL','VEN');
        
        $arrComercial = array();
        $arrFabrica = array();
        $arrCartera = array();
        
        $sumComercial = 0;
        $sumFabrica = 0;
        $sumCartera = 0;
        $sumComite = collect($comiteCredito)->sum('ValorCredito');

        foreach($gestionComercial as $gesElement){          
            if(!in_array($gesElement->estudio, $arrComercial)){
                array_push($arrComercial, $gesElement->estudio);
                $sumComercial += $gesElement->Credito;
            }    
        }
        
        
        foreach($gestionFabrica as $gesElement){          
            if(!in_array($gesElement->estudio, $arrFabrica)){
                array_push($arrFabrica, $gesElement->estudio);
                $sumFabrica += $gesElement->Credito;
            }    
        }
        
        foreach($gestionCartera as $gesElement){
            if(!in_array($gesElement->estudio, $arrCartera)){
                array_push($arrCartera, $gesElement->estudio);
                $sumCartera += $gesElement->Credito;

            }    
        }
        
        $contGValoracion = count($gestionValoraciones);                 
        $contNoViable = count($estudiosNoViables);
        $contComite = count($comiteCredito);
        $contGComercial = UtilidadesClass::validateList($gestionComercial, $listComercial, 'estado');
        $contGFabrica = UtilidadesClass::validateList($gestionFabrica, $listFabrica, 'estado');
        $contGCartera = UtilidadesClass::validateList($gestionCartera, $listCartera, 'estado');

        return view('pages.GestionOficina.index')
                                                     ->with('gestionValoraciones', $gestionValoraciones)
                                                     ->with('gestionComercial', $gestionComercial)
                                                     ->with('gestionFabrica', $gestionFabrica)
                                                     ->with('gestionTesoreria', $gestionTesoreria)
                                                     ->with('comiteCredito', $comiteCredito)
                                                     ->with('estudiosNoViables', $estudiosNoViables)
                                                     ->with('gestionCartera', $gestionCartera)
                                                     ->with('contGValoracion', $contGValoracion)
                                                     ->with('contNoViable', $contNoViable)
                                                     ->with('contComite', $contComite)
                                                     ->with('contGComercial', $contGComercial)
                                                     ->with('contGFabrica', $contGFabrica)
                                                     ->with('contTesoreria', $contTesoreria)
                                                     ->with('contGCartera', $contGCartera)
                                                     ->with('sumComercial', $sumComercial)
                                                     ->with('sumFabrica', $sumFabrica)
                                                     ->with('pendiente', $pendiente)
                                                     ->with('sumComite', $sumComite)
                                                     ->with('sumCartera', $sumCartera);
    }

    public function valoraciones(){

        return  DB::select("SELECT 
                               CONCAT(users.nombre,' ',users.apellido) as cliente,
                               users.cedula as cedula,
                               'Valoracion' as concepto,
                               'Pendiente' as estado,
                               Valoraciones.created_at as fecha_ingreso,
                               solicitudes_consulta.clave_desprendible as clave_desprendible,
                               Valoraciones.Pagaduria as Pagaduria,
                               CONCAT(comercial.nombre,' ',comercial.apellido) as Comercial,
                               Valoraciones.id as id,
                               solicitudes_consulta.id as solicitud_id
                               FROM Valoraciones
                               LEFT JOIN users ON users.id = valoraciones.Usuario
                               LEFT JOIN solicitudes_consulta ON solicitudes_consulta.valoracion_id = Valoraciones.id
                               LEFT JOIN pagadurias ON pagadurias.id = Valoraciones.pagaduria_id
                               LEFT JOIN users AS comercial ON comercial.id = Valoraciones.Comercial 
                               WHERE valoraciones.id NOT IN(SELECT estudios.Valoracion as 'id' FROM estudios)");

    }

    public function noViables(){

        return DB::select("
                            SELECT 
                            estudios.id,
                            CONCAT(users.nombre,' ',users.apellido) as cliente,
                            users.cedula,
                            users.telefono,
                            users.email,
                            estudios.Saldo,
                            estudios.Desembolso,
                            estudios.Pagaduria,
                            estudios.ValorCredito,
                            CONCAT(comercial.nombre,' ',comercial.apellido) as Comercial,
                            estudios.created_at
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            LEFT JOIN users AS comercial ON comercial.id = Valoraciones.Comercial 
                            WHERE estudios.Estado = 'NVI' ");

    }

    public function gestionComercial(){

        return DB::select("
                            SELECT 
                            users.id as idUsuario,
                            obligaciones.id as idObligacion,
                            valoraciones.id as valoracion, 
                            estudios.id as estudio,
                            CONCAT(users.nombre,' ',users.apellido) as nombre, 
                            obligaciones.Entidad as entidad,
                            CASE 
                                WHEN gestionobligaciones.id != '' THEN gestionobligaciones.estado ELSE 'No Cargada'
                            END as estado,
                            gestionobligaciones.TipoAdjunto as tipoadj,
                            gestionobligaciones.id as gestionObligacion_id,
                            gestionobligaciones.fechaEntrega as FechaEntrega,
                            gestionobligaciones.fechaVencimiento as FechaVencimiento,
                            estudios.ValorCredito as Credito,
                            'N' as Adjunto,
                             'NN' as tarea,
                            (SELECT CONCAT(nombre,' ',apellido) from users WHERE id = Valoraciones.Comercial) as Comercial
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            INNER JOIN obligaciones ON obligaciones.Valoracion = valoraciones.id
                            LEFT JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id 
                                      AND  gestionobligaciones.tipoAdjunto = 'CDD'
                            WHERE obligaciones.Compra = 'S' AND obligaciones.Estado = 'Activo' AND estudios.Estado IN ('FIR','PEN','VIA')

                            UNION   

                            SELECT 
                            valoraciones.Usuario as idUsuario,
                            'N' as idObligacion,
                            estudios.Valoracion as valoracion,
                            estudios.id as estudio, 
                            CONCAT(users.nombre,' ',users.apellido) as nombre,
                            'Solicitud de Crédito' as entidad,
                            CASE 
                                WHEN adjuntos.id != '' THEN 'Cargada' ELSE 'No Cargada'
                            END as estado,
                            adjuntos.TipoAdjunto as tipoadj,
                            'LBZ gestionObligacion_id' as gestionObligacion_id,
                            '' as FechaEntrega,
                            '' as Fechavencimiento,
                            estudios.ValorCredito as Credito,
                            'N' as Adjunto,
                            'NN' as tarea,
                            (SELECT CONCAT(nombre,' ',apellido) from users WHERE id = Valoraciones.Comercial) as Comercial
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            LEFT OUTER JOIN adjuntos ON adjuntos.idPadre = estudios.id AND adjuntos.TipoAdjunto = 'LBZ'
                            WHERE estudios.estado IN ('FIR','PEN','VIA')
                            
                            UNION
                            SELECT valoraciones.Usuario as idUsuario,
                                'N' as idObligacion,
                                estudios.Valoracion as valoracion,
                                estudios.id as estudio, 
                                CONCAT(users.nombre,' ',users.apellido) as nombre,
                                'ADICIONAL' as entidad,     
                                CASE 
                                    WHEN adjuntos.id != '' THEN 'Cargada' ELSE 'No Cargado'
                                END as estado,
                                adjuntos.TipoAdjunto as tipoadj,
                                'SAD gestionObligacion_id' as gestionObligacion_id,
                                '' as FechaEntrega,
                                adjuntos.created_at as FechaVencimiento,
                                estudios.ValorCredito as Credito,
                                tareas.id_adjunto as Adjunto,
                                tareas.id as tarea,
                                (SELECT CONCAT(nombre,' ',apellido) from users WHERE id = Valoraciones.Comercial) as Comercial
                                FROM tareas 
                                INNER JOIN estudios ON estudios.id = tareas.id_estudio
                                INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                INNER JOIN users ON users.id = valoraciones.Usuario
                                LEFT OUTER JOIN adjuntos ON adjuntos.idPadre = tareas.id AND adjuntos.TipoAdjunto = 'SAD'
                                WHERE estudios.Estado IN ('FIR','PEN','VIA')
                            ORDER BY idUsuario, estado DESC");

    }

    public function comiteCredito(){

        return DB::select("
                            SELECT 
                            estudios.id,
                            CONCAT(users.nombre,' ',users.apellido) as cliente,
                            users.cedula,
                            users.telefono,
                            users.email,
                            estudios.Saldo,
                            estudios.Desembolso,
                            estudios.Pagaduria,
                            estudios.ValorCredito,
                            estudios.created_at
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            WHERE estudios.Estado = 'COM' ");

    }

    public function gestionFabrica(){

        return DB::select("SELECT 
                                    valoraciones.Usuario as idUsuario,
                                    estudios.Valoracion as valoracion,
                                    estudios.id as estudio, 
                                    CONCAT(users.nombre,' ',users.apellido) as nombre,
                                    'Visado' as entidad,     
                                    CASE 
                                        WHEN adjuntos.id != '' THEN 'Cargado' ELSE 'No Cargado'
                                    END as estado,
                                    adjuntos.TipoAdjunto as tipoadj,
                                    adjuntos.created_at as FechaVencimiento,
                                    estudios.ValorCredito as Credito,
                                    'N' as Adjunto,
                                    'N' as tarea
                                    FROM estudios 
                                    INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                    INNER JOIN users ON users.id = valoraciones.Usuario
                                    LEFT OUTER JOIN adjuntos ON adjuntos.idPadre = estudios.id AND adjuntos.TipoAdjunto = 'VIS'
                                        AND adjuntos.Tabla = 'adjuntosEstudio'
                                    WHERE estudios.Estado IN ('VIS')

                                    UNION

                                    SELECT 
                                    valoraciones.Usuario as idUsuario,
                                    estudios.Valoracion as valoracion,
                                    estudios.id as estudio, 
                                    CONCAT(users.nombre,' ',users.apellido) as nombre,
                                    'ADICIONAL' as entidad,     
                                    CASE 
                                        WHEN adjuntos.id != '' THEN 'Cargado' ELSE 'No Cargado'
                                    END as estado,
                                    adjuntos.TipoAdjunto as tipoadj,
                                    adjuntos.created_at as FechaVencimiento,
                                    estudios.ValorCredito as Credito,
                                    tareas.id_adjunto as Adjunto,
                                    tareas.id as tarea
                                    FROM tareas 
                                    INNER JOIN estudios ON estudios.id = tareas.id_estudio
                                    INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                    INNER JOIN users ON users.id = valoraciones.Usuario
                                    LEFT OUTER JOIN adjuntos ON adjuntos.idPadre = tareas.id AND adjuntos.TipoAdjunto = 'SAD'
                                      AND adjuntos.Tabla = 'adjuntosEstudio'
                                    WHERE estudios.Estado IN ('VIS') 
                                ORDER BY estudio, estado DESC");

    }

    public function gestionTesoreria(){

        return DB::select("
                            SELECT 
                            users.id as idUsuario,
                            obligaciones.id as idObligacion,
                            valoraciones.id as valoracion, 
                            estudios.id as estudio,
                            CONCAT(users.nombre,' ',users.apellido) as nombre, 
                            obligaciones.Entidad as entidad,
                            gestionobligaciones.estado as estado,
                            adjuntos.TipoAdjunto as tipoadj,
                            adjuntos.created_at AS fechaPago,
                            gestionobligaciones.fechaVencimiento as FechaVencimiento,
                            estudios.ValorCredito as Credito,
                            
                            (SELECT SUM(obligaciones.SaldoActual) from obligaciones 
                                JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = 'obligaciones' AND adjuntos.TipoAdjunto = 'SPA'
                                WHERE obligaciones.Valoracion = estudios.Valoracion) as valorComprado,
                                
                            (SELECT
                                CASE WHEN SUM(giroscliente.Valor) > 0 THEN SUM(giroscliente.Valor) ELSE 0 END
                            FROM
                                giroscliente
                            WHERE
                                giroscliente.Estudio = estudios.id) as valorGirado,
                            estudios.Saldo as Saldo,
                            estudios.Desembolso as Desembolso,
                            0 as valorXgirar,
                            obligaciones.SaldoActual as 'saldoObligacion'
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            INNER JOIN obligaciones ON obligaciones.Valoracion = valoraciones.id
                            LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = 'obligaciones' AND adjuntos.TipoAdjunto = 'SPA'
                            INNER JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.tipoAdjunto = 'CDD' AND gestionobligaciones.estado in('RAD', 'VEN')
                            WHERE obligaciones.Compra = 'S' AND obligaciones.Estado = 'Activo' AND estudios.Estado IN ('TES', 'PRT')
                            
                            UNION   

                            SELECT
                            users.id as idUsuario,
                            '' as idObligacion,
                            valoraciones.id as valoracion,
                            estudios.id as estudio,
                            CONCAT(users.nombre,' ',users.apellido) as nombre,
                            'Saldo Cliente' as entidad,
                            'PENDIENTE' as estado,
                            '' as tipoadj,
                            NULL as fechaPago,
                            '' as FechaVencimiento,
                            estudios.ValorCredito as Credito,
                            (SELECT SUM(obligaciones.SaldoActual) from obligaciones 
                                JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = 'obligaciones' AND adjuntos.TipoAdjunto = 'SPA'
                                WHERE obligaciones.Valoracion = estudios.Valoracion) AS valorComprado,
                                
                            (SELECT
                                CASE WHEN SUM(giroscliente.Valor) > 0 THEN SUM(giroscliente.Valor) ELSE 0 END
                            FROM
                                giroscliente
                            WHERE
                                giroscliente.Estudio = estudios.id) AS valorGirado,
                             
                            estudios.Saldo as Saldo,
                            estudios.Desembolso as Desembolso,
                            estudios.Saldo - (SELECT CASE WHEN SUM(giroscliente.Valor) > 0 THEN SUM(giroscliente.Valor) ELSE 0 END FROM giroscliente WHERE giroscliente.Estudio = estudios.id) as valorXgirar,
                            0 as 'saldoObligacion'
                            FROM estudios 
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            WHERE estudios.Saldo > 0 AND estudios.Estado IN ('TES', 'PRT')
                            
                            ORDER BY idUsuario
                            ");

    }

    public function gestionCartera(){

        return DB::select(" SELECT 
                            users.id as idUsuario,
                            valoraciones.id as valoracion, 
                            estudios.id as estudio,
                            CONCAT(users.nombre,' ',users.apellido) as nombre, 
                            estudios.ValorCredito as Credito,
                            obligaciones.id as id_obligacion, 
                            obligaciones.Entidad as entidad,
                            CASE 
                                WHEN gestionobligaciones.id != '' THEN gestionobligaciones.estado ELSE 'No Cargado'
                            END as estado,
                            CASE 
                                WHEN adjuntos.id != '' THEN 'Cargado en TES' ELSE 'No Cargado en TES'
                            END as banderaSoportePago,
                            gestionobligaciones.fechaEntrega as FechaEntrega,
                            gestionobligaciones.fechaVencimiento as FechaVencimiento,
                            estudios.Estado as estadoEstudio
                            FROM estudios
                            INNER JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                            INNER JOIN users ON users.id = valoraciones.Usuario
                            INNER JOIN obligaciones ON obligaciones.Valoracion = valoraciones.id AND obligaciones.Compra = 'S' AND obligaciones.Estado = 'Activo'
                            LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = 'obligaciones' AND adjuntos.TipoAdjunto = 'SPA'
                            LEFT JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.tipoAdjunto = 'PSC' AND gestionobligaciones.estado IN ('RAD', 'VEN', 'SOL','PAG')
                            WHERE estudios.Estado IN ('PRT','CAR')
                            ORDER BY idUsuario
                            ");

    }

}
