<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Librerias\UtilidadesClass;
use App\Librerias\FuncionesComponente;
use App\Tesoreria;
use App\Adjunto;
use App\GiroCliente;
use App\Estudio;
use DB;
use Carbon\Carbon;

class TesoreriaController extends Controller
{
    protected $forma = 'TESOR';

    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma))
        {
            return view('errors.401');
        }
        
        $totalGiradoAndSaldo = DB::select('SELECT estudios.id, estudios.Saldo, estudios.Desembolso, sum(giroscliente.Valor) as pagadoCliente
                                                                    FROM estudios 
                                                                    LEFT JOIN giroscliente ON giroscliente.Estudio = estudios.id                                                                     
                                                                    WHERE estudios.Estado in ("'.config("constantes.ESTUDIO_TESORERIA").'", "'.config("constantes.ESTUDIO_PROCESO_TESORERIA").'")
                                                                    GROUP BY estudios.id, estudios.Saldo, estudios.Desembolso');
        
        $totalDesembolso = 0;
        $totalClientesPagados = 0;
        $cantClientesPagados = 0;
        $totalClientesPorPagar = 0;
        $cantClientesPorPagar = 0;
        foreach ($totalGiradoAndSaldo as $giros){
            $totalDesembolso += $giros->Desembolso;
            $giros->pagadoCliente = (is_null($giros->pagadoCliente))? 0 : $giros->pagadoCliente;
            
            if($giros->Saldo  === $giros->pagadoCliente){
                $cantClientesPagados++;
                $totalClientesPagados+= $giros->pagadoCliente;
            }else{
                $cantClientesPorPagar++;
                $totalClientesPagados+= $giros->pagadoCliente;
                $totalClientesPorPagar+= $giros->Saldo - $giros->pagadoCliente;
                
            }
        }
                
//        $totalGiradoAndSaldo = DB::select('SELECT sum(estudios.Saldo) as totalSaldo, sum(estudios.Desembolso) as totalDesembolso, sum(giroscliente.Valor) as totalGirado FROM estudios
//                                                                        LEFT JOIN giroscliente ON giroscliente.Estudio = estudios.id
//                                                                        WHERE estudios.Estado in ("'.config("constantes.ESTUDIO_TESORERIA").'", "'.config("constantes.ESTUDIO_CARTERA").'")');
  
        $lstObligaciones = DB::select('SELECT
                                        	obligaciones.id,
                                            obligaciones.SaldoActual,
                                            adjuntos.id AS idAdjunto,
                                            adjuntos.TipoAdjunto as TipoAdjunto
                                        FROM
                                            obligaciones
                                        JOIN estudios ON estudios.Valoracion = obligaciones.Valoracion AND estudios.Estado IN(
                                                "'.config("constantes.ESTUDIO_TESORERIA").'",
                                                "'.config("constantes.ESTUDIO_PROCESO_TESORERIA").'"
                                            )
                                        LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = "obligaciones" AND adjuntos.TipoAdjunto in ("'.config("constantes.SOPORTE_PAGO").'", "'.config("constantes.PAZ_SALVO").'")
                                        WHERE
                                            obligaciones.Compra = "S" AND obligaciones.Estado = "Activo"');        
        
        $totalPagadas = 0;
        $cantPagadas = 0;
        $totalRestantes = 0;
        $cantRestantes = 0;
        foreach ($lstObligaciones as $obligacion){
            if($obligacion->TipoAdjunto == config("constantes.PAZ_SALVO")){
                continue;
            }
            if(is_null($obligacion->idAdjunto)){
                $cantRestantes++;
                $totalRestantes+=$obligacion->SaldoActual;
            }else{
                $cantPagadas++;
                $totalPagadas+= $obligacion->SaldoActual;
            }
        }
        
        
                
        $lstEstudios = Estudio::where("Estado", config("constantes.ESTUDIO_TESORERIA"))->where("Estado", config("constantes.ESTUDIO_PROCESO_TESORERIA"))->get();        
        $Descuento = 0;             
        foreach ($lstEstudios as $estudio){
            if(isset($estudio->DatosCostos) && !empty($estudio->DatosCostos)){
                $obj = json_decode($estudio->DatosCostos);
                $Descuento+= $obj->totalCostosV;
            }          
        }      
        
        $Tesoreria = DB::select('SELECT
                                                        estudios.id AS Estudio,
                                                        users.cedula,
                                                        users.nombre,
                                                        users.telefono,
                                                        users.email,
                                                        users.direccion,
                                                        users.departamento,
                                                        users.municipio,
                                                        users.pagaduria,
                                                        users.banco,
                                                        users.tipo_cuenta,
                                                        users.numero_de_cuenta,
                                                        estudios.Valoracion AS Valoracion,
                                                        estudios.Desembolso, 
                                                        (SELECT SUM(obligaciones.SaldoActual) from obligaciones 
                                                            JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = "'.config("constantes.KEY_OBLIGACION").'" AND adjuntos.TipoAdjunto = "'.config("constantes.SOPORTE_PAGO").'"
                                                            WHERE obligaciones.Valoracion = estudios.Valoracion) AS valorComprado,
                                                        
                                                        (SELECT
                                                            SUM(giroscliente.Valor)
                                                        FROM
                                                            giroscliente
                                                        WHERE
                                                            giroscliente.Estudio = estudios.id) AS valorGirado,
                                                        MIN(gestionobligaciones.fechaVencimiento) AS fechaVencimientoGestionObligacion
                                                    FROM
                                                        estudios
                                                        JOIN valoraciones ON valoraciones.id = estudios.Valoracion
                                                        JOIN users ON users.id = valoraciones.Usuario
                                                        JOIN obligaciones ON obligaciones.Valoracion = estudios.Valoracion AND obligaciones.Compra = "S" AND obligaciones.Estado = "Activo"
                                                        LEFT JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.estado = "'.config("constantes.GO_RADICADA").'"
                                                    WHERE
                                                        estudios.Estado IN("'.config("constantes.ESTUDIO_TESORERIA").'", "'.config("constantes.ESTUDIO_PROCESO_TESORERIA").'")
                                                        GROUP BY 
                                                            estudios.id, 
                                                            users.cedula, 
                                                            users.nombre,
                                                            users.telefono,
                                                            users.email,
                                                            users.direccion,
                                                            users.departamento,
                                                            users.municipio,
                                                            users.pagaduria,
                                                            users.banco,
                                                            users.tipo_cuenta,
                                                            users.numero_de_cuenta,
                                                            estudios.Valoracion, 
                                                            estudios.Desembolso 
                                                        ORDER BY
                                                        fechaVencimientoGestionObligacion ASC');            

        return view('pages.Tesoreria.consulta')->with('Tesorerias', $Tesoreria)                                                                                              
                                               ->with('totalDesembolso', $totalDesembolso)
                                               ->with('totalClientesPagados', $totalClientesPagados)
                                               ->with('cantClientesPagados', $cantClientesPagados)
                                               ->with('totalClientesPorPagar', $totalClientesPorPagar)
                                               ->with('cantClientesPorPagar', $cantClientesPorPagar)
                                               ->with('totalPagadas', $totalPagadas)
                                               ->with('cantPagadas', $cantPagadas)
                                               ->with('totalRestantes', $totalRestantes)                                               
                                               ->with('cantRestantes', $cantRestantes);
    }

    public function consulta(Request $request)
    {
        $Tesorerias = DB::select("SELECT ESTUDIOS.id Estudio, cedula, nombre,ESTUDIOS.created_at FechaEstudio,
                                         ESTUDIOS.Pagaduria, ESTUDIOS.Estado Estado, ValorCredito, USERS.id IdUser
                                    FROM USERS,VALORACIONES,ESTUDIOS
                                   WHERE Usuario = USERS.id
                                     AND VALORACIONES.id = ESTUDIOS.Valoracion
                                     AND cedula LIKE IFNULL(NULLIF(:cedu,''),'%')
                                     AND UPPER(nombre) LIKE UPPER(IFNULL(NULLIF(:nomb,''),'%'))
	                                 AND DATE_FORMAT(VALORACIONES.created_at,'%Y-%m-%d') LIKE IFNULL(NULLIF(:fecha,''),'%')
	                                 AND ESTUDIOS.Pagaduria LIKE IFNULL(NULLIF(:paga,''),'%')
	                                 AND VALORACIONES.Estado LIKE IFNULL(NULLIF(:esta,''),'%')
	                                 AND ValorCredito LIKE IFNULL(NULLIF(:cred,''),'%')", ['cedu' => $request->Cedula,
                                                                                           'nomb' => $request->Nombre,
                                                                                           'fecha' => $request->FechaValoracion,
                                                                                           'paga' => $request->Pagaduria,
                                                                                           'esta' => $request->Estado,
                                                                                           'cred' => $request->ValorCredito]);
        
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='resultado'>
                    <thead>
                      <tr>
                            <th> Obligación </th>
                            <th> Cedula </th>
                            <th> Nombre </th>
                            <th> Fecha Estudio </th>
                            <th> Pagaduria </th>
                            <th> Estado </th>
                            <th> Valor Credito </th>
                      </tr>
                    </thead>
                    <tbody>";  
                    foreach($Tesorerias as $Tesoreria)
                    {
                        $tabla .=
                        "<tr id='".$Tesoreria->Estudio."'>
                            <td>
                                <a href='DetalleTesoreria/".$Tesoreria->Estudio."/".$Tesoreria->Valoracion."' id='lkTesoreria' name='lkTesoreria' data-estudio='".$Tesoreria->Estudio."'>
                                        $Tesoreria->Estudio
                                </a>
                            </td>
                            <td>". $Tesoreria->cedula ."</td>
                            <td>". $Tesoreria->nombre ."</td>
                            <td>". $Tesoreria->FechaEstudio ."</td>
                            <td>". $Tesoreria->Pagaduria ."</td>
                            <td>". $Tesoreria->Estado ."</td>
                            <td>". $Tesoreria->ValorCredito ."</td>
                         </tr>";
                    }
        $tabla .= "</tbody></table>";
        return response()->json(['tabla' => $tabla]);
    }

    public function detalle($estudio,$valoracion,$tipoAdjun = FALSE)
    {
        $this->forma = "TEDET";
        
        if(!UtilidadesClass::ValidarAcceso($this->forma))
        {
            return view('errors.401');
        }
                         
        $infoUserEstudio = DB::select("SELECT                   users.id as users_id,
                                                                CONCAT(users.nombre,' ',users.apellido) as nombre,
                                                                users.cedula,
                                                                users.telefono,
                                                                users.email,
                                                                CONCAT(users.direccion,' ',users.ciudad) as direccion,
                                                                users.departamento,
                                                                users.municipio,
                                                                users.pagaduria,
                                                                users.banco,
                                                                users.tipo_cuenta,
                                                                users.numero_de_cuenta,
                                                                estudios.*                                                                
                                                FROM estudios 
                                                                JOIN valoraciones ON valoraciones.id = estudios.Valoracion 
                                                                JOIN users ON users.id = valoraciones.Usuario 
                                                WHERE estudios.id = :estu", ['estu' => $estudio]);
        if(count($infoUserEstudio) <= 0){
            return view('errors.101')->with("mensaje", "No existe información de tesoreria del estudio.");            
        }
        
        if($infoUserEstudio[0]->Estado != config("constantes.ESTUDIO_TESORERIA") && $infoUserEstudio[0]->Estado != config("constantes.ESTUDIO_PROCESO_TESORERIA")){
            if($infoUserEstudio[0]->Estado == config("constantes.ESTUDIO_CARTERA")){
                return redirect('Tesoreria');
            }else{
                return view('errors.101')->with("mensaje", "El estudio no se encuentra en el estado Tesoreria, por tal razón no es posible visualizar tal información.");
            }
        }
         
               
        $Obligaciones = DB::select('SELECT
                                            obligaciones.*,
                                            adjuntos.id AS idAdjunto,
                                            adjuntos.created_at AS fechaPago,
                                            gestionobligaciones.estado AS estadoGestionObligacion,
                                            gestionobligaciones.fechaVencimiento AS fechaVencimiento,
                                            gestionobligaciones.id AS idGestionObligacion
                                        FROM
                                            obligaciones    
                                        LEFT JOIN adjuntos ON adjuntos.idPadre = obligaciones.id AND adjuntos.Tabla = "'.config("constantes.KEY_OBLIGACION").'" AND adjuntos.TipoAdjunto = "'.  config("constantes.SOPORTE_PAGO").'"
                                        JOIN gestionobligaciones ON gestionobligaciones.id_obligacion = obligaciones.id AND gestionobligaciones.tipoAdjunto = "'.config("constantes.CERTIFICACIONES_DEUDA").'" AND gestionobligaciones.estado in("'.config("constantes.GO_RADICADA").'", "'.config("constantes.GO_VENCIDA").'", "'.config("constantes.GO_PAGADA").'")
                                        WHERE
                                            obligaciones.Valoracion = '.$valoracion.' AND obligaciones.Estado = "Activo" AND obligaciones.Compra = "S"
                                        ORDER BY
                                            gestionobligaciones.estado = "'.config("constantes.GO_VENCIDA").'", gestionobligaciones.estado = "'.config("constantes.GO_RADICADA").'", gestionobligaciones.estado = "'.config("constantes.GO_PAGADA").'"');
       
        $totalSaldoObligaciones = 0;
        $totalCompradas = 0;
        $arrayIdObligacionesProcesadas = [];
        for($i = 0; $i < count($Obligaciones) ; $i++){
            if(in_array($Obligaciones[$i]->id, $arrayIdObligacionesProcesadas)){                
                unset($Obligaciones[$i]);
            }else{
                $arrayIdObligacionesProcesadas[] = $Obligaciones[$i]->id;
                $totalSaldoObligaciones+= ($Obligaciones[$i]->Compra == "S")? $Obligaciones[$i]->SaldoActual : 0;
                if($Obligaciones[$i]->estadoGestionObligacion == config("constantes.GO_PAGADA") ){
                    $totalCompradas+= ($Obligaciones[$i]->Compra == "S" && !is_null($Obligaciones[$i]->idAdjunto))? $Obligaciones[$i]->SaldoActual : 0;                                
                }
            }
        }                
        $totalFaltanteCompras = $totalSaldoObligaciones - $totalCompradas;
                                
        $Giros = DB::select("SELECT giroscliente.*, adjuntos.id as idAdjunto
                                                            FROM GIROSCLIENTE 
                                                            LEFT JOIN adjuntos ON  
                                                                adjuntos.idPadre = giroscliente.id 
                                                                AND adjuntos.Tabla = 'GirosCliente'
                                                                AND adjuntos.TipoAdjunto = '".config("constantes.SOPORTE_PAGO_CLIENTE")."'  
                                                            WHERE Estudio = :estu
                                                            ORDER BY giroscliente.created_at DESC
                                                            ", ['estu' => $estudio]);        

        $totalGirado = 0;
        foreach ($Giros as $Giro){            
            $totalGirado+= $Giro->Valor;
        }        
        $restanteGiro = $infoUserEstudio[0]->Saldo - $totalGirado;
        
        $infoCostosEstudio = (isset($infoUserEstudio[0]->DatosCostos) && !empty($infoUserEstudio[0]->DatosCostos))?  (array) json_decode($infoUserEstudio[0]->DatosCostos)  : false;
        
        
        $progressComprasObligaciones = (($totalCompradas/$totalSaldoObligaciones) * 100);
        $valorGirar = (isset($infoUserEstudio[0]->Saldo ) && $infoUserEstudio[0]->Saldo  > 0)? $infoUserEstudio[0]->Saldo  : 0;
        $progressSaldoCliente = ($valorGirar > 0)? (($totalGirado/$valorGirar) * 100) : 100;
        
        
                
        return view('pages.Tesoreria.index')->with('infoUserEstudio', $infoUserEstudio)
                                                                      ->with("Obligaciones", $Obligaciones)
                                                                      ->with("totalSaldoObligaciones", $totalSaldoObligaciones)
                                                                      ->with("totalCompradas", $totalCompradas)
                                                                      ->with("totalFaltanteCompras", $totalFaltanteCompras)
                                                                      ->with("Giros", $Giros)
                                                                      ->with("totalGirado", $totalGirado)
                                                                      ->with("restanteGiro", $restanteGiro)
                                                                      ->with("infoCostosEstudio", $infoCostosEstudio)
                                                                      ->with("estudio", $estudio)
                                                                      ->with("progressComprasObligaciones", $progressComprasObligaciones)
                                                                      ->with("progressSaldoCliente", $progressSaldoCliente)
                                                                      ->with("valoracion", $valoracion);
                                            
    }

    public function actualizarTesoreria(Request $request)
    {
        $extensionesPermitidas = [
            "pdf",
            "jpg",
            "jpeg",
            "png"
        ];
        set_time_limit(0);
        $utilidad = new UtilidadesClass();
        $archivo = $request->file('fAdjunto');
        $NombreOriginal = $archivo->getClientOriginalName();        
        $extension = $archivo->getClientOriginalExtension();
        
        if(!in_array(strtolower($extension), $extensionesPermitidas)){
            return response()->json(['estadoPeticion' => false]);
        }
        
        $id = $utilidad->registroAdjunto($request->id,'Obligaciones',$NombreOriginal,$extension,'SPA','VALO');
        \Storage::disk('adjuntos')->put($id, \File::get($archivo));

        $Obligaciones = DB::select("SELECT Entidad,ValorPagar,OBLIGACIONES.created_at FechaLimite, NumeroObligacion,
                                           Valoracion, null Ruta, ValorPagar Pendiente, 0 Ejecutado, id
                                      FROM OBLIGACIONES
                                     WHERE Valoracion = :valo
                                       AND id NOT IN(SELECT idPadre FROM ADJUNTOS WHERE Tabla = 'Obligaciones')
                                       AND Compra = 'S'
                                     UNION
                                    SELECT Entidad,ValorPagar,OBLIGACIONES.created_at FechaLimite, NumeroObligacion,
                                           Valoracion, null Ruta, 0 Pendiente, ValorPagar Ejecutado, OBLIGACIONES.id id
                                      FROM OBLIGACIONES,ADJUNTOS
                                     WHERE OBLIGACIONES.id = idPadre
                                       AND Tabla = 'Obligaciones'
                                       AND Valoracion = :valo1
                                       AND Compra = 'S'", ['valo' => $request->Valoracion,
                                                                 'valo1' => $request->Valoracion]);


        $suma = 0;
        $pendiente = 0;
        $ejecutado = 0;
        
        foreach ($Obligaciones as $Obligacion)
        {
            $suma += $Obligacion->ValorPagar;
            $pendiente += $Obligacion->Pendiente;
            $ejecutado += $Obligacion->Ejecutado;

            $adjunto = Adjunto::where('idPadre', '=', $Obligacion->id)
                            ->where('Tabla', '=', 'Obligaciones')
                            ->where('TipoAdjunto', '=', 'SPA')
                            ->first();
            if(!is_null($adjunto))
            {
                $Obligacion->Ruta = $adjunto->id;
            }
        }
        $pendiente = $request->Pendiente - $request->ValorItem;
        $ejecutado = $request->Ejecutado + $request->ValorItem;
        $this->forma = "TEDET";

        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                        <tr>
                            <th> No. Obligación </th>
                            <th> Entidad </th>
                            <th> Valor Obligacion </th>
                            <th> F. Limite </th>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                            {
                                $tabla .= "<th> Soporte </th>";
                            }
             $tabla .= "</tr>
                    </thead>
                    <tbody>";
                    $fila = 0;
                    foreach($Obligaciones as $Obligacion)
                    {
                    $fila += 1;
                    $frmName = "frmAdjunto".$fila;
                    $tabla .= "<tr id='".$Obligacion->NumeroObligacion."'>
                                    <td>".$Obligacion->NumeroObligacion."</td>
                                    <td>".$Obligacion->Entidad."</td>
                                    <td>".number_format($Obligacion->ValorPagar)."</td>
                                    <td>".Carbon::parse($Obligacion->FechaLimite)->format('d-m-y')."</td>";
                                    if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                                    {
                                        $tabla .= "<td>
                                        <form id='".$frmName."' name='".$frmName."' class='frmItem' enctype='multipart/form-data'>
                                            <input type=\"hidden\" name=\"_token\" id=\"token\" value=\"".csrf_token()."\">";
                                        if($utilidad->validaAdjunto($Obligacion->id,'Obligaciones','SPA'))
                                        {
                                            $tabla .= "<a href='../../TesoreriaPrueba/".$Obligacion->Ruta."' class='btn btn-success'><span class='fa fa-cloud-download'></span> Descargar<a>";
                                            
                                        }
                                        else
                                        {
                                            $tabla .= "<input type='file' class='prueba' name='fAdjunto' data-frm='".$frmName."' data-valoracion='".$Obligacion->Valoracion."' data-id='".$Obligacion->id."' data-obligacion='".$Obligacion->NumeroObligacion."' data-valoritem='".$Obligacion->ValorPagar."'>";
                                        }
                                        $tabla .= "</form></td>";
                                    }
                                $tabla .= "</tr>";
                    }

        $tabla .= "</tbody></table>";

        return response()->json(['estadoPeticion' => true,
                                'NombreArchivo' => $NombreOriginal,
                                'Pendiente' => $pendiente,
                                'Ejecutado' => $ejecutado,
                                'tabla' => $tabla]);
    }

    public function descargarArchivo($id)
    {
        $archivo = Adjunto::find($id);
        header("Content-type:application/".$archivo->Extension);
        header("Content-Disposition:attachment;filename='$archivo->NombreArchivo'");
        readfile(storage_path("adjuntos")."/".$id);
    }

    public function adicionarGiroCliente(Request $request)
    {        
        $extensionesPermitidas = ["pdf","jpg","jpeg","png"];

        $condiciones = [  'Estudio' => 'required',
                                    'Valor' => 'required',                                    
                                    'TipoGiro' => 'required|max:10'];
        
        $mensajes = [ 'required' => 'Campo :attribute es Obligatorio.',
                                'max' => 'Campo :attribute no permite un numero mayor a :max caracteres'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);
        if ($validacion->fails()){
            return response()->json(['STATUS' => false, 'errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }       

        $archivo = $request->file('fAdjCliente');
        if(is_null($archivo)){
            return response()->json(['STATUS' => false, 'Mensaje' => "Debes Adjuntar el soporte para adicionar el giro."]);
        }
        
        $extension = $archivo->getClientOriginalExtension();        
        if(!in_array(strtolower($extension), $extensionesPermitidas)){
            return response()->json(['STATUS' => false, 'Mensaje' => "La extencion del archivo no es admitida. (".  implode(", ", $extensionesPermitidas).")"]);
        }
        
        $GiroCliente = new GiroCliente($request->all());
        $GiroCliente->save();
        
        set_time_limit(0);
        $utilidad = new UtilidadesClass();
        $NombreOriginal = $archivo->getClientOriginalName();        
        
        $id = $utilidad->registroAdjunto($GiroCliente->id,'GirosCliente',$NombreOriginal,$extension,config("constantes.SOPORTE_PAGO_CLIENTE"),'VALO');
        \Storage::disk('adjuntos')->put($id, \File::get($archivo));
        
        /**********************************************************/
                
        $objFuncionesComponentes = new FuncionesComponente();
        $objFuncionesComponentes->checkPasarCartera(false, $request->Estudio);
        
        $Giros = DB::select("SELECT giroscliente.*, adjuntos.id as idAdjunto
                                                            FROM GIROSCLIENTE 
                                                            LEFT JOIN adjuntos ON  
                                                                adjuntos.idPadre = giroscliente.id 
                                                                AND adjuntos.Tabla = 'giroscliente'
                                                                AND adjuntos.TipoAdjunto = 'SPA'  
                                                            WHERE Estudio = :estu
                                                            ORDER BY giroscliente.created_at DESC", ['estu' => $request->Estudio]);

        $totalGirado = 0;
        $html = "";
        foreach ($Giros as $giro){
            $html.= '<tr>
                                    <td class="uppercase"> '.($giro->TipoGiro ).'</td>
                                    <td>'. (($giro->Valor > 0)? "$".number_format($giro->Valor, 0, ",", ".") : 0 ).'</td>
                                    <td>'.(date('d-m-Y', strtotime($giro->created_at))).'</td>
                                    <td>
                                        <a class="color-negro pointer" title="Visualizar" href="'.(config("constantes.RUTA")).'/visualizar/'.($giro->idAdjunto).'" target="_blank">
                                            <span class="fa fa-paperclip fa-1x color-negro"></span></a>
                                    </td>
                                    <td><a class="pointer deleteGiro iconEliminar" data-id="'.($giro->id).'" data-url="'.(config("constantes.RUTA")).'" data-valor="'.intval($giro->Valor).'"><span class="fa fa-trash"></span></a></td>
                                </tr>';
            $totalGirado+= $giro->Valor;
        }        
         return response()->json([  'STATUS' => true,  
                                                    'Mensaje' => 'El registro se ha Guardado exitosamente.',
                                                    'htmlGiros' => $html,
                                                    'totalGirado' => number_format($totalGirado, 0, ",", ".")]);
    }
    
    function EliminarGiro(Request $request){
        $giro = GiroCliente::find($request->id);
        if(isset($giro->id)){
            $result = $giro->delete();
            if($result){
                echo json_encode(["STATUS" => true, "MENSAJE" => "El giro fue eliminado con éxito"]);
            }else{
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurrio un problema al intentar eliminar el Giro, por favor vuelva a intentarlo"]);
            }
        }else{
            echo json_encode(["STATUS" => false, "MENSAJE" => "El giro que intenta eliminar no existe en nuestra base de datos."]);
        }
        
    }
    function prueba(){
        $objFuncionesComponente = new FuncionesComponente();
        $objFuncionesComponente->cambiarEstadoGestionObligaciones(1);
    }

    public function generarPago(Request $request){
        $types = ['pages.Tesoreria.Pagos.transferencia','pages.Tesoreria.Pagos.cheque', 'pages.Tesoreria.Pagos.efectivo'];
        $data = $request->all();

        setlocale(LC_ALL,"es_ES");
        $mytime = Carbon::now();
        $data['today'] =$mytime->format('d/m/Y');
        //return view('pages.Tesoreria.Pagos.cheque')->with('data', $data);die;
        $pdf = PDF::loadView($types[$request->tipo], compact("data"))->setPaper('a4', 'portrait');
        return $pdf->stream();
    }
}