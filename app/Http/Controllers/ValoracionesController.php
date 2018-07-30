<?php

namespace App\Http\Controllers;

use App\SolicitudConsulta;
use App\Pagaduria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Librerias\UtilidadesClass;
use App\Librerias\ComponentAdjuntos;
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
use App\CodigoPromocional;
use App\GiroCliente;
use App\ProcesosJuridicos;
use App\Juicios;

class ValoracionesController extends Controller{
    protected $forma = 'CVALO';
    
    var $cuentasCuotaVariable = [
        "TDC",
        "CBR",
        "SBG",
        "ROTA",
        "SICG",
        "SOBR",
        "SURO",
        "TCR",
        "UROT",
        "CRLZ",
        "CROT",
        "LBRT",
        "VDAR"
    ];
    
    public $idValoracion = 0;

    public function index()
    {
        $erroresFormulario = session('erroresValoracion', false);
        $txCedula = session('txCedula', false);
        $txPrimerApellido = session('txPrimerApellido', false);
        $txCelular = session('txCelular', false);
        $txEmail = session('txEmail', false);
        $txPagaduria = session('txPagaduria', false);                   
        $pagadurias = Pagaduria::all();

        return view('pages.Valoraciones.index')
                                                                        ->with("erroresFormulario", $erroresFormulario)
                                                                        ->with("txCedula", $txCedula)
                                                                        ->with("txPrimerApellido", $txPrimerApellido)
                                                                        ->with("txCelular", $txCelular)
                                                                        ->with("txEmail", $txEmail)                                                                        
                                                                        ->with("txPagaduria", $txPagaduria)
                                                                        ->with("pagadurias", $pagadurias);
     
    }

    public function listarValoraciones()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }

        $user = Auth::user();
        $Valoraciones = DB::select("SELECT VALORACIONES.id, VALORACIONES.Filtro, cedula, nombre, apellido, VALORACIONES.Pagaduria,VALORACIONES.created_at,
                                           IFNULL((SELECT nombre
                                                     FROM USERS
                                                    WHERE id = Comercial),'N/A') Comercial
                                      FROM VALORACIONES,USERS                                      
                                     WHERE VALORACIONES.Usuario = USERS.id
                                       AND ((:perfilUsuario = :comercial AND Comercial = :usuario) OR :perfilUsuario1 != :comercial1)
                                     ORDER BY VALORACIONES.created_at DESC",['perfilUsuario' => Auth::user()->perfil,
                                                                             'comercial' => config('constantes.PERFIL_COMERCIAL'),
                                                                             'usuario' => Auth::user()->id,
                                                                             'perfilUsuario1' => Auth::user()->perfil,
                                                                             'comercial1' => config('constantes.PERFIL_COMERCIAL')]);

        return view('pages.Valoraciones.consulta')->with('Valoraciones',$Valoraciones)
                                                        ->with('forma', $this->forma)
                                                        ->with('user', $user);
    }

    public function listarValoracionesCliente()
    {
        $Valoraciones = Valoracion::where('Usuario',Auth::user()->id)
                                  ->orderBy('id', 'desc')
                                  ->get();

        return view('layouts-client.valoracion.consulta')->with('Valoraciones',$Valoraciones);
    }

    public function consulaValoracion($id)//$id = false
    {
        $valoracion = Valoracion::find($id);
        if (!count($valoracion->pagaduria_related))
        {
            $valoracion->pagaduria_id = Pagaduria::where('nombre', $valoracion->Pagaduria)->first()->id;
            $valoracion->save();
        }
        $pagaduria = Pagaduria::find($valoracion->pagaduria_id);
        if(!$valoracion){
            return view('errors.101')->with("mensaje", "La valoraci&oacute;n a la que desea ingresar no existe");
        }
        
        if(!$valoracion->Filtro){
            return redirect(config('constantes.RUTA')."GestionObligacionesValoracion/".$id);
        }
        
        $datos = $this->armarRespuesta($id);
        
        $utilidades = new UtilidadesClass();
        $SMLV = $utilidades->obtenerValorParametro("SMLV");
        $tasaCredito = $utilidades->obtenerValorParametro("TASACR");
        $plazoCredito = $utilidades->obtenerValorParametro("PLAZOCR");
        $leyDocentes = $utilidades->obtenerValorParametro("DLEYDOC");
        $descuento1 = $utilidades->obtenerValorParametro("DESCUEN1");
        $descuento2 = $utilidades->obtenerValorParametro("DESCUEN2");
        $descuento3 = $utilidades->obtenerValorParametro("DESCUEN3");
        $descuento4 = $utilidades->obtenerValorParametro("DESCUEN4");
        
        
//        $user = User::find($infoValoracion->Usuario);
        
        $comerciales = false;        
        $comercialSeleccionado = false;
        if(Auth::user()->perfil == config('constantes.PERFIL_ADMIN') || Auth::user()->perfil == config('constantes.PERFIL_OFICINA') || Auth::user()->perfil == config('constantes.PERFIL_ROOT')){            
            $comerciales = User::where("perfil", config('constantes.PERFIL_COMERCIAL'))
                                ->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))->orderBy("nombre", "ASC")
                                ->get();
            $comercialSeleccionado = (isset($valoracion->Comercial) && !is_null($valoracion->Comercial))? $valoracion->Comercial : false;
        }       
        
        $estudio = Estudio::where("Valoracion", $id)->first();        
        $consultaEstudio = (isset($estudio) && $estudio != false)? $estudio : false;
        
        $user = User::find($valoracion->Usuario);

        return view('layouts-client.valoracion.index')->with('NombreCompleto', $datos["NombreCompleto"])
                                                             ->with('id', $id)
                                                             ->with('PuntajeData', $datos["PuntajeData"])
                                                             ->with('PuntajeCifin', $datos["PuntajeCifin"])
                                                             ->with('NumCastigadas', $datos["NumCastigadas"])
                                                             ->with('NumEnMora', $datos["NumEnMora"])
                                                             ->with('NumAlDia', $datos["NumAlDia"])
                                                             ->with('TotalCastigadas', $datos["TotalCastigadas"])
                                                             ->with('TotalEnMora', $datos["TotalEnMora"])
                                                             ->with('TotalAlDia', $datos["TotalAlDia"])
                                                             ->with('TotalHuellas', $datos["TotalHuellas"])
                                                             ->with('huellaData', $datos["huellaData"])
                                                             ->with('huellaCifin', $datos["huellaCifin"])
                                                             ->with('tEnMora', $datos["tEnMora"])
                                                             ->with('comerciales', $comerciales)
                                                             ->with('idValoracion', $id)
                                                             ->with('Estudio', $consultaEstudio)
                                                             ->with('comercialSeleccionado', $comercialSeleccionado)
                                                             ->with("parametros", json_encode([
                                                                "SMLV" => $SMLV,
                                                                 "tasaCredito" => $tasaCredito,
                                                                 "plazoCredito" => $plazoCredito,
                                                                 "leyDocentes" => $leyDocentes, 
                                                                 "descuento1" => $descuento1, 
                                                                 "descuento2" => $descuento2, 
                                                                 "descuento3" => $descuento3, 
                                                                 "descuento4" => $descuento4]))
                                                             ->with('tCastigadas',$datos["tCastigadas"])
                                                             ->with('tAlDia',$datos["tAlDia"])
                                                             ->with('Obligaciones',$datos["allObligaciones"])
                                                             ->with('infoUser',$user)
                                                            ->with('pagaduria', $pagaduria)
                                                             /*->with('NumProcesos',$datos["NumProcesos"])
                                                             ->with('tJuridico',$datos["tJuridico"])*/;
    }

    public function llamadoData($numIdentificacion,$primerApellido){
        //$numIdentificacion = bcrypt($numIdentificacion);
        //$primerApellido = bcrypt($primerApellido);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,config('constantes.URL_VALORACION'));
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "numIdentificacion=".$numIdentificacion."&primerApellido=".$primerApellido);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$valores = curl_exec ($ch);
            
        $Respuesta = json_decode($valores);
        
        return $Respuesta;
    }

    public function consumirDataCredito(Request $request)    {
        if(session('valido'))
        {
            $Usuario = User::find(Auth::user()->id);
            return $this->valoracion("3843763", $Usuario->apellido,$Usuario->telefono,$Usuario->email,$Usuario->pagaduria);
        }
    }

    public function consumirDataCredito1(Request $request){
        
            session(['txCedula' => $request->txCedula]);
            session(['txPrimerApellido' => $request->txPrimerApellido]);
            session(['txCelular' => $request->txCelular]);
            session(['txEmail' => $request->txEmail]);
            session(['txPagaduria' => $request->txPagaduria]);
            
            if (!isset($request->adjuntoAutorizacion) || !$request->file('adjuntoAutorizacion')->isValid()) {            
                session(['erroresValoracion' => "Ocurrio un problema al cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte.[Mensaje: ".$request->file('ComponentArchivo')->getErrorMessage()."]"]);
                return redirect('Valorar');
            }
            
            return $this->valoracion($request->txCedula, $request->txPrimerApellido,$request->txCelular,$request->txEmail,$request->txPagaduria, $request, false);
    }

    public function valorarSolicitud($id){
        
        $solicitud =  SolicitudConsulta::select('*')
                    ->with(array('pagaduria' => function($query){
                        $query->select('*');
                    }))
                    ->where('id', $id)->get()->first();
        
        return $this->valoracion($solicitud->cedula, $solicitud->apellido, $solicitud->telefono, $solicitud->email, $solicitud->pagaduria->nombre, false, $solicitud);
    }

    public function valoracion($txCedula,$txPrimerApellido, $txCelular, $txEmail,$txPagaduria, $datosFormulario, $solicitud_consulta){
        set_time_limit(0);
        $crearUsuario = true; //la intencion inicial es crear el usuario
        
        //Se verifica si el usuario existe en la base de datos
        $Usuario = User::where('cedula',$txCedula)->limit(1)->orderBy('id', 'desc')->get();        
        if(count($Usuario) > 0){
            $crearUsuario = false; //Si existe para que crearlo
            $Usuario = $Usuario[0];
//            $Valoracion = Valoracion::where('Usuario',$Usuario->id)->limit(1)->orderBy('id', 'desc')->get();
            //Verificamos si el usuario tiene valoraciones creadas
//            if(count($Valoracion) > 0){
                //Si tiene valoracion es redireccionado hacia ella
                
//            }
        }

        $solicitud = SolicitudConsulta::select('*')
                                      ->where('email',trim(strtolower($txEmail)))
                                      ->where('cedula',trim(strtolower($txCedula)))
                                      ->get();
        
        $pagaduria = Pagaduria::where('nombre',$txPagaduria)->first();
        
        /*foreach ($usuarios as $usuario){
            if (trim(strtolower($usuario->email)) == trim(strtolower($txEmail))){
                session(['erroresValoracion' => "Correo ya registrado, porfavor ingrese otro correo"]);
                return redirect('Valorar');
            }
        }*/
        
        // Se consulta la informacion financiera del usuario en centrales
        $data = $this->llamadoData($txCedula,$txPrimerApellido);
        //Verificamos si el Api retorno un error, en caso afirmativo lo mostramos al usuario
        if(isset($data->STATUS) && !$data->STATUS){
            session(['erroresValoracion' => $data->mensaje]);
            return redirect('Valorar');                
        }
        
        //Verificamos si el usuario es necesario crearlo.
        if($crearUsuario){
            //Se obtienen los ultimos 4 dijitos del celular para que esa sea la contraseña del usuario en el sistema
            $numCaracteres = strlen($txCelular);
            $clave = substr($txCelular, ($numCaracteres - 4), $numCaracteres);
            
            $sexo = (strtoupper($data->infoUser->sexo) == "MASCULINO")? "M" : "F";

            $Usuario = new User();
            $Usuario->nombre = $data->infoUser->nombres;
            $Usuario->apellido = $data->infoUser->primerApellido . " " . $data->infoUser->segundoApellido;
            $Usuario->cedula = $txCedula;
            $Usuario->sexo = $sexo;
            $Usuario->telefono = $txCelular;
            $Usuario->departamento = (isset($solicitud_consulta->departamento))? $solicitud_consulta->departamento : "";
            $Usuario->municipio = (isset($solicitud_consulta->municipio))? $solicitud_consulta->municipio : "";
            $Usuario->email = $txEmail;
            $Usuario->pagaduria = $pagaduria->nombre;
            $Usuario->password = bcrypt($clave);
            $Usuario->estado = config('constantes.ACTIVO');
            $Usuario->perfil = config('constantes.ID_PERFIL_CLIENTE');
            $resultCreacionUsuario = $Usuario->save();
            //Si hay errores en la insercion se le muestra al usuario
            if($resultCreacionUsuario == false){
                session(['erroresValoracion' => "Ocurrio un problema al intentar crear el usuario, por favor recargue la pagina e intentelo de nuevo. Si el problema persiste comuniquese con soporte."]);
                return redirect('Valorar');
            }
        }

        //Empezamos a llenar las tablas que se requieran para guardar toda la informacion de la valoracion            
            $Valoracion = new Valoracion();            
            $Valoracion->Usuario = $Usuario->id;
            $Valoracion->Filtro = 0;
            $Valoracion->PuntajeData = $data->infoData->score->puntaje;
            $Valoracion->PuntajeCifin = $data->infoCifin->score->puntaje;            
            $Valoracion->codSegData = $data->codigosSeguridad->data;
            $Valoracion->numInformeCifin = $data->codigosSeguridad->cifin;
            $Valoracion->codigoNombreArchivos = $data->CodigoArchivo;
            $Valoracion->infoCentrales = json_encode($data->JsonOriginal);
            $Valoracion->Pagaduria = $pagaduria->nombre;
            $Valoracion->pagaduria_id = $pagaduria->id;
            $Valoracion->UsuarioCreacion = Auth::user()->id;
            if($solicitud_consulta){
                $Valoracion->Comercial = $solicitud_consulta->user_id;
            }
            $resultCreacionValoracion = $Valoracion->save();
            //Si ocurre un error en la insercion de la valoracion se le desplega al usuario
             if($resultCreacionValoracion == false){
                session(['erroresValoracion' => "Ocurrio un problema al intentar crear la valoracion, comuniquese con soporte. [{$data->CodigoArchivo}]"]);
                return redirect('Valorar');
            }            
            
            $this->idValoracion = $Valoracion->id;
            
             //Se carga la autorización de consulta
            if($datosFormulario){
                $archivo = $datosFormulario->adjuntoAutorizacion;
                $extension = $archivo->getClientOriginalExtension();
                $objComponenteAdjuntos = new ComponentAdjuntos();
                $id = $objComponenteAdjuntos->save($this->idValoracion, config("constantes.KEY_AUTORIZACION"), "AutorizacionConsulta", $extension, config("constantes.AUTORIZACION_DE_CONSULTA"), config("constantes.MDL_VALORACION"), $archivo);
            }
            
            //Se almacenan los procesos Juridicos
            $this->insertarProcesosJuridicos($data->dataJuridico);
            
            //Se almacenan las huellas de consulta del usuario
            if(!isset($data->infoData->HuellaConsulta)){
                $data->infoData->HuellaConsulta = [];
            }
            if(!isset($data->infoCifin->HuellaConsulta)){
                $data->infoCifin->HuellaConsulta = [];
            }            
            $this->registrarHuellas($data->infoData->HuellaConsulta,$data->infoCifin->HuellaConsulta);
            //Se registran las obligaciones del usuario
            $sumas = $this->registrarObligaciones($data->obligaciones);
            //Se registran los procesos juridicos del usuario
            //$Procesos = $this->registrarProcesosJuridicos($data->dataJuridico);(isset($data->dataJuridico))? $data->dataJuridico : [];          
            
            
            //Al finalizar todo correctamente se procede a eliminar las variables de sesion del formulario
            session()->forget('txCedula');
            session()->forget('txPrimerApellido');
            session()->forget('txCelular');
            session()->forget('txEmail');
            session()->forget('txPagaduria');
            
            if(count($solicitud) == 0){
                 
                $solicitud = new SolicitudConsulta();
                
                $solicitud->cedula = trim(strtolower($txCedula));
                $solicitud->apellido = $txPrimerApellido;
                $solicitud->telefono = $txCelular;
                $solicitud->pagaduria_id = $pagaduria->id;
                $solicitud->estado = 1;
                $solicitud->valoracion_id = $Valoracion->id;
                $solicitud->save();
                
                if(!is_null($datosFormulario->adjuntoAutorizacion)){
                    
                    $archivo = $datosFormulario->adjuntoAutorizacion;
                    $extension = $archivo->getClientOriginalExtension();
                    $objComponenteAdjuntos = new ComponentAdjuntos();
                    $id = $objComponenteAdjuntos->save($solicitud->id, config("constantes.KEY_AUTORIZACION"), "AutorizacionConsulta", $extension, config("constantes.AUTORIZACION_DE_CONSULTA"), config("constantes.MDL_VALORACION"), $archivo);
                }
            }
            
            if($solicitud_consulta){
                $solicitud_consulta->valoracion_id = $Valoracion->id;
                $solicitud_consulta->save();
            }
            //Se redirecciona a la vista de filtro de obligaciones repetidas
            return redirect(config('constantes.RUTA')."GestionObligacionesValoracion/".$this->idValoracion);
    }
    
    public function getMensajeErrorDataJuridico($status, $mensajeError){
        
        if($status === "0"){
            
            if($mensajeError === "002"){
                return "Clave errada";
            }elseif($mensajeError === "009"){
                return "Nit no posee informaci&oacute;n judicial";
            }elseif($mensajeError === "013"){
                return "Se despliega la informaci&oacute;n del servicio";
            }elseif($mensajeError === "018"){
                return "Clave no habilitada";
            }elseif($mensajeError === "022"){
                return "No se encontr&oacute; informaci&oacute;n de juicio";
            }elseif($mensajeError === "023"){
                return "No se pudo obtener informaci&oacute;n. Por favor intente m&aacute;s tarde";
            }else{
                return "No se pudo obtener informaci&oacute;n. Por favor intente m&aacute;s tarde";
            }
            
        }elseif($status === "1"){
            return "consulta Exitosa";
        }elseif($status === "2"){
            return "No se pudo obtener informaci&oacute;n sobre la identificaci&oacute;n digitada";
        }
        
    }

    public function insertarProcesosJuridicos($dataJuridico){
        
        $tmp = (array) $dataJuridico->MensajeError;
        $dataJuridico->MensajeError = (empty($tmp))? "" : (string) $dataJuridico->MensajeError;        
        
        $procesosJuridicos = new ProcesosJuridicos();
        $procesosJuridicos->idValoracion = $this->idValoracion;
        $procesosJuridicos->fechaConsulta = date("Y-m-d  G:i:s");
        $procesosJuridicos->respuestaWs = json_encode($dataJuridico);
        $procesosJuridicos->usuario = Auth::user()->id;
        $procesosJuridicos->status = $dataJuridico->Status;
        $procesosJuridicos->mensajeError = $dataJuridico->MensajeError;
        $result = $procesosJuridicos->save();
        
        if($result && isset($dataJuridico->JuiciosDemandado->JuicioResumen) && count($dataJuridico->JuiciosDemandado->JuicioResumen) > 0){
            
            $idProcesoJuridico = $procesosJuridicos->id;
            
            if(count($dataJuridico->JuiciosDemandado->JuicioResumen) == 1){
                $this->insertarJuicio($dataJuridico->JuiciosDemandado->JuicioResumen, $idProcesoJuridico);
            }else{
            foreach ($dataJuridico->JuiciosDemandado->JuicioResumen as $juicio){
                    $this->insertarJuicio($juicio, $idProcesoJuridico);
                    
                }
            }
            
        }

    }
    public function insertarJuicio($juicio, $idProcesoJuridico){
                $arrayFechaInicioProceso = explode("/", $juicio->FechaInicioProceso);
                $fechaInicioProceso = date("Y-m-d  G:i:s", strtotime($arrayFechaInicioProceso[2]."/".$arrayFechaInicioProceso[1]."/".$arrayFechaInicioProceso[0]));
                
                $arrayFechaUltimoMovimiento = explode("/", $juicio->FechaUltimoMovimiento);
                $fechaUltimoMovimiento = date("Y-m-d  G:i:s", strtotime($arrayFechaUltimoMovimiento[2]."/".$arrayFechaUltimoMovimiento[1]."/".$arrayFechaUltimoMovimiento[0]));                
                
                $juicioObj = new Juicios;
                $juicioObj->idProcesoJuridico = $idProcesoJuridico;
                $juicioObj->ciudad = $juicio->Ciudad;
                $juicioObj->departamento = $juicio->Departamento;
                $juicioObj->estadoProceso = $juicio->EstadoProceso;
                $juicioObj->expediente = $juicio->Expediente;
                $juicioObj->fechaInicioProceso = $fechaInicioProceso;
                $juicioObj->fechaUltimoMovimiento = $fechaUltimoMovimiento;
                $juicioObj->idJuicio = $juicio->IdJuicio;
                $juicioObj->instanciaProceso = $juicio->InstanciaProceso;
                $juicioObj->nitsActor = $juicio->NitsActor;
                $juicioObj->nombresActor = $juicio->NombresActor;
                $juicioObj->nitsDemandado = $juicio->NitsDemandados;
                $juicioObj->nombresDemandado = $juicio->NombresDemandado;
                $juicioObj->numeroJuzgado = $juicio->NumeroJuzgado;
                $juicioObj->rangoPretenciones = $juicio->RangoPretenciones;
                $juicioObj->tieneGarantias = $juicio->TieneGarantias;
                $juicioObj->tipoDeCausa = $juicio->TipoDeCausa;
                $juicioObj->tipoJuzgado = $juicio->TipoJuzgado;
        return $juicioObj->save();
            
        }
    public function valoracionBackup($txCedula,$txPrimerApellido, $txCelular, $txEmail,$txPagaduria, $datosFormulario) //consumirDataCredito //Request $request
    {
        set_time_limit(0);
        $FechaSistema = new \DateTime();
        /*$Valoracion = Valoracion::where('Usuario',Auth::user()->id)
                                  ->limit(1)
                                  ->orderBy('id', 'desc')
                                  ->get();*/
        $Usuario = User::where('cedula',$txCedula)
                              ->limit(1)
                              ->orderBy('id', 'desc')
                              ->get();
        $boleano = false;
        if(isset($Usuario[0])){
            $Valoracion = Valoracion::where('Usuario',$Usuario[0]->id)
                                  ->limit(1)
                                  ->orderBy('id', 'desc')
                                  ->get();
            if(isset($Valoracion[0]))
            {
                $boleano = false;
//                $boleano = true;
            }
        }
        
        if($boleano){
            /*$FechaValoracion = strtotime($Valoracion[0]->created_at->format('d-m-Y'));
            $FechaSistema = strtotime($FechaSistema->format('d-m-Y'));
            $dias = round((($FechaSistema - $FechaValoracion) / 86400));
            if($dias < 30)
            {*/         
            $Valoracion = Valoracion::where('Usuario',$Usuario[0]->id)//Auth::user()->id
                                  ->limit(1)
                                  ->orderBy('id', 'desc')
                                  ->get();
            $datos = $this->armarRespuesta($Valoracion[0]->id);
            //return redirect('Valoraciones',['id' => $Valoracion[0]->id]);
            //return route('Valoraciones',[$Valoracion[0]->id]);//, [$Valoracion[0]->id]
            /*dd($datos);
            return redirect()->route('consumo',[$Valoracion[0]->id]);*/
            $utilidades = new UtilidadesClass();
            $SMLV = $utilidades->obtenerValorParametro("SMLV");
            $tasaCredito = $utilidades->obtenerValorParametro("TASACR");
            $plazoCredito = $utilidades->obtenerValorParametro("PLAZOCR");
            $leyDocentes = $utilidades->obtenerValorParametro("DLEYDOC");
            $descuento1 = $utilidades->obtenerValorParametro("DESCUEN1");
            $descuento2 = $utilidades->obtenerValorParametro("DESCUEN2");
            $descuento3 = $utilidades->obtenerValorParametro("DESCUEN3");
            $descuento4 = $utilidades->obtenerValorParametro("DESCUEN4");
            
            $comerciales = false;
            $comercialSeleccionado = false;
            if(Auth::user()->perfil == "ADM" || Auth::user()->perfil =="OFI" || Auth::user()->perfil =="ROT"){
                $comerciales = User::where("perfil", "COM")->get();
                $valoracion = Valoracion::find($Valoracion[0]->id);
                $comercialSeleccionado = (isset($valoracion->Comercial) && !is_null($valoracion->Comercial))? $valoracion->Comercial : false;
            }
            
            $estudio = Estudio::where("Valoracion", $Valoracion[0]->id)->first();
            $consultaEstudio = (isset($estudio))? $estudio : false;
            
            return view('layouts-client.valoracion.index')->with('NombreCompleto', $datos["NombreCompleto"])
                                                          ->with('PuntajeData', $datos["PuntajeData"])
                                                          ->with('idValoracion', $Valoracion[0]->id)
                                                          ->with('comerciales', $comerciales)
                                                          ->with('Estudio', $consultaEstudio)
                                                          ->with('comercialSeleccionado', $comercialSeleccionado)
                                                          ->with("parametros", json_encode([
                                                                "SMLV" => $SMLV,
                                                                 "tasaCredito" => $tasaCredito,
                                                                 "plazoCredito" => $plazoCredito,
                                                                 "leyDocentes" => $leyDocentes, 
                                                                 "descuento1" => $descuento1, 
                                                                 "descuento2" => $descuento2, 
                                                                 "descuento3" => $descuento3, 
                                                                 "descuento4" => $descuento4]))
                                                          ->with('PuntajeCifin', $datos["PuntajeCifin"])
                                                          ->with('NumCastigadas', $datos["NumCastigadas"])
                                                          ->with('NumEnMora', $datos["NumEnMora"])
                                                          ->with('NumAlDia', $datos["NumAlDia"])
                                                          ->with('TotalCastigadas', $datos["TotalCastigadas"])
                                                          ->with('TotalEnMora', $datos["TotalEnMora"])
                                                          ->with('TotalAlDia', $datos["TotalAlDia"])
                                                          ->with('TotalHuellas', $datos["TotalHuellas"])
                                                          ->with('huellaData', $datos["huellaData"])
                                                          ->with('huellaCifin', $datos["huellaCifin"])
                                                          ->with('tEnMora', $datos["tEnMora"])
                                                          ->with('tCastigadas',$datos["tCastigadas"])
                                                          ->with('tAlDia',$datos["tAlDia"])
                                                          ->with('infoUser', $Usuario)
                                                          /*->with('NumProcesos',$datos["NumProcesos"])
                                                          ->with('tJuridico',$datos["tJuridico"])*/;
            
        }else{
            $data = $this->llamadoData($txCedula,$txPrimerApellido);                        
            if(isset($data->STATUS) && !$data->STATUS){
                session(['erroresValoracion' => $data->mensaje]);
                return redirect('Valorar');                
            }
            
            if(strtoupper($data->infoUser->sexo) == "MASCULINO"){
                $sexo = "M";
            }else{
                $sexo = "F";
            }
            
            $numCaracteres = strlen($txCelular);
            $clave = substr($txCelular, ($numCaracteres - 4), $numCaracteres);
            
            if(!isset($Usuario[0])){
                $Usuario = new User();
                $Usuario->nombre = $data->infoUser->nombres;
                $Usuario->apellido = $data->infoUser->primerApellido . " " . $data->infoUser->segundoApellido;
//                $Usuario->primerApellido = $data->infoUser->primerApellido;
                $Usuario->cedula = $txCedula;
                $Usuario->sexo = $sexo;
                $Usuario->telefono = $txCelular;
                $Usuario->email = $txEmail;
                $Usuario->pagaduria = $txPagaduria;
                $Usuario->password = bcrypt($clave);
                $Usuario->estado = config('constantes.ACTIVO');
                $Usuario->perfil = config('constantes.ID_PERFIL_CLIENTE');
                $Usuario->save();
            }
            /************************************************************/
            
            $Valoracion = new Valoracion();
            if(!isset($Usuario[0])){
                $Valoracion->Usuario = $Usuario->id;//Auth::user()->id  ---- CAMBIO TEMPORAL    
            }else{
                $Valoracion->Usuario = $Usuario[0]->id;//Auth::user()->id  ---- CAMBIO TEMPORAL
                $Usuario[0]->update(['estado' => config('constantes.ACTIVO')]);
            }
            
            $Valoracion->PuntajeData = $data->infoData->score->puntaje;
            $Valoracion->PuntajeCifin = $data->infoCifin->score->puntaje;
            //Cambio por JFR {Se adicionan los codigos de consulta de data y cifin para corroborar la consulta en las centrales de riesgo}
                $Valoracion->codSegData = $data->codigosSeguridad->data;
                $Valoracion->numInformeCifin = $data->codigosSeguridad->cifin;
                $Valoracion->codigoNombreArchivos = $data->CodigoArchivo;
                $Valoracion->infoCentrales = json_encode($data->JsonOriginal);                
            //fin cambio
            $Valoracion->Pagaduria = $txPagaduria;
            $Valoracion->UsuarioCreacion = Auth::user()->id;
            if(Auth::user()->perfil == config('constantes.PERFIL_COMERCIAL')){
                $Valoracion->Comercial = Auth::user()->id;
            }
            $Valoracion->save();
            $this->idValoracion = $Valoracion->id;
                        
            $archivo = $datosFormulario->adjuntoAutorizacion;
            $extension = $archivo->getClientOriginalExtension();            
            $objComponenteAdjuntos = new ComponentAdjuntos();
            $id = $objComponenteAdjuntos->save($this->idValoracion, config("constantes.KEY_AUTORIZACION"), "AutorizacionConsulta", $extension, config("constantes.AUTORIZACION_DE_CONSULTA"), config("constantes.MDL_VALORACION"), $archivo);
                        
            if(!isset($data->infoData->HuellaConsulta)){
                $data->infoData->HuellaConsulta = [];
            }
            if(!isset($data->infoCifin->HuellaConsulta)){
                $data->infoCifin->HuellaConsulta = [];
            }
            
            $this->registrarHuellas($data->infoData->HuellaConsulta,$data->infoCifin->HuellaConsulta);
            $sumas = $this->registrarObligaciones($data->obligaciones);
            //$Procesos = $this->registrarProcesosJuridicos($data->dataJuridico);(isset($data->dataJuridico))? $data->dataJuridico : [];
            /*if(strtoupper($data->infoUser->sexo) == "MASCULINO")
            {
                $sexo = "M";
            }
            else
            {
                $sexo = "F";
            }
            $Usuario = User::where('email',Auth::user()->email)
            ->update(['nombre' => $data->infoUser->nombres,
                    'apellido' => $data->infoUser->primerApellido . " " . $data->infoUser->segundoApellido,
                    'cedula' => $data->infoUser->identificacion,
                    'sexo' => $sexo]);*/
            
            $NombreCompleto = $data->infoUser->nombreCompleto;
            $TotalHuellas = count($data->infoData->HuellaConsulta) + count($data->infoCifin->HuellaConsulta);

            $NumEnMora = (isset($data->obligaciones->enMora))? count($data->obligaciones->enMora) : 0;
            $NumCastigadas = (isset($data->obligaciones->castigadas))? count($data->obligaciones->castigadas): 0;
            $NumAlDia = (isset($data->obligaciones->alDia))? count($data->obligaciones->alDia) : 0;
            
            $tEnMoraa = (isset($data->obligaciones->enMora))? $data->obligaciones->enMora : [];
            $tCastigadass = (isset($data->obligaciones->castigadas))? $data->obligaciones->castigadas : [];
            $tAldia = (isset($data->obligaciones->alDia))? $data->obligaciones->alDia : [];
            $valHuellaData = (isset($data->infoData->HuellaConsulta))? $data->infoData->HuellaConsulta : [];
            $valHuellaCifin= (isset($data->infoCifin->HuellaConsulta))? $data->infoCifin->HuellaConsulta : [];
            $valScoreData= (isset($data->infoData->score->puntaje))? $data->infoData->score->puntaje : 0;
            $valScoreCifin= (isset($data->infoCifin->score->puntaje))? $data->infoCifin->score->puntaje : 0;
            $tJuridico = (isset($data->dataJuridico->JuiciosDemandado->JuicioResumen))? $data->dataJuridico->JuiciosDemandado->JuicioResumen : [];
            
            $utilidades = new UtilidadesClass();
            $SMLV = $utilidades->obtenerValorParametro("SMLV");
            $tasaCredito = $utilidades->obtenerValorParametro("TASACR");
            $plazoCredito = $utilidades->obtenerValorParametro("PLAZOCR");
            $leyDocentes = $utilidades->obtenerValorParametro("DLEYDOC");
            $descuento1 = $utilidades->obtenerValorParametro("DESCUEN1");
            $descuento2 = $utilidades->obtenerValorParametro("DESCUEN2");
            $descuento3 = $utilidades->obtenerValorParametro("DESCUEN3");
            $descuento4 = $utilidades->obtenerValorParametro("DESCUEN4");
            
            $comerciales = false;
            $comercialSeleccionado = false;
            if(Auth::user()->perfil == "ADM" || Auth::user()->perfil =="OFI" || Auth::user()->perfil =="ROT"){
                $comerciales = User::where("perfil", "COM")->get();                
                $comercialSeleccionado = (isset($Valoracion->Comercial) && !is_null($Valoracion->Comercial))? $Valoracion->Comercial : false;
            }
            
            $estudio = Estudio::where("Valoracion", $Valoracion->id)->first();
            $consultaEstudio = (isset($estudio))? $estudio : false;
            
            session()->forget('txCedula');
            session()->forget('txPrimerApellido');
            session()->forget('txCelular');
            session()->forget('txEmail');
            session()->forget('txPagaduria');
            
            return view('layouts-client.valoracion.index')->with('NombreCompleto', $NombreCompleto)
                                                          ->with("parametros", json_encode([
                                                                "SMLV" => $SMLV,
                                                                 "tasaCredito" => $tasaCredito,
                                                                 "plazoCredito" => $plazoCredito,
                                                                 "leyDocentes" => $leyDocentes, 
                                                                 "descuento1" => $descuento1, 
                                                                 "descuento2" => $descuento2, 
                                                                 "descuento3" => $descuento3, 
                                                                 "descuento4" => $descuento4]))
                                                          ->with('comerciales', $comerciales)
                                                          ->with('idValoracion', $Valoracion->id)
                                                          ->with('Estudio', $consultaEstudio)
                                                          ->with('comercialSeleccionado', $comercialSeleccionado)
                                                          ->with('PuntajeData', $valScoreData)
                                                          ->with('PuntajeCifin', $valScoreCifin)
                                                          ->with('NumCastigadas', $NumCastigadas)
                                                          ->with('NumEnMora', $NumEnMora)
                                                          ->with('NumAlDia', $NumAlDia)
                                                          ->with('TotalCastigadas', $sumas["castigadas"])
                                                          ->with('TotalEnMora', $sumas["enMora"])
                                                          ->with('TotalAlDia', $sumas["alDia"])
                                                          ->with('TotalHuellas', $TotalHuellas)
                                                          ->with('huellaData', $valHuellaData)
                                                          ->with('huellaCifin', $valHuellaCifin)
                                                          ->with('tEnMora', $tEnMoraa)
                                                          ->with('tCastigadas',$tCastigadass)
                                                          ->with('tAlDia', $tAldia)
                                                          ->with('infoUser', $Usuario)
                                                          /*->with('NumProcesos',$Procesos["NumProcesos"])
                                                          ->with('tJuridico',(array) $tJuridico)*/;
        }
    }

    public function registrarHuellas($Data,$Cifin)
    {
        for ($i=0; $i < count($Data); $i++) { 
            $Huella = new HuellaConsulta();
            $Huella->Valoracion = $this->idValoracion;
            $Huella->Entidad = $Data[$i]->entidad;
            $Huella->Fecha = $Data[$i]->fecha;
            $Huella->CentralInformacion = "Data Credito";
            $Huella->save();
        }
        for ($i=0; $i < count($Cifin); $i++) { 
            $Huella = new HuellaConsulta();
            $Huella->Valoracion = $this->idValoracion;
            $Huella->Entidad = $Cifin[$i]->entidad;
            $Huella->Fecha = $Cifin[$i]->fecha;
            $Huella->CentralInformacion = "Trans Union";
            $Huella->save();
        }
    }
    function migrarCuotasProyectadas(){
        $obligaciones = Obligacion::all();
        $utilidades = new UtilidadesClass();
        $porcentajeX50 = $utilidades->obtenerValorParametro("DESCUCP1");
        $porcentajeX4 = $utilidades->obtenerValorParametro("DESCUCP2");
        $arrayActualizadas []="Actualizadas";
        $ErrorActualizadas []= "Generaron Error";
        foreach ($obligaciones as $obligacion){
            
            if(in_array($obligacion->tipoCuenta, $this->cuentasCuotaVariable)){
                $CuotasProyectadas = $obligacion->ValorInicial * ((float) $porcentajeX50) * ((float) $porcentajeX4);                
            }else{
                $CuotasProyectadas = 0;
            }     
            
            $resultUpdate = $obligacionUpdate = Obligacion::where("id",$obligacion->id)->update(["CuotasProyectadas" => $CuotasProyectadas]);
            if($resultUpdate){
                echo "True: ".$obligacion->id." | ";
            }else{
                echo "False: ".$obligacion->id." | ";
            }
        }        
        
    }
    
    public function registrarObligaciones($Obligaciones)
    {
        $utilidades = new UtilidadesClass();
        $porcentajeX50 = $utilidades->obtenerValorParametro("DESCUCP1");
        $porcentajeX4 = $utilidades->obtenerValorParametro("DESCUCP2");
        
        $sumas["enMora"] = 0;
        if(isset($Obligaciones->enMora)){        
        for ($i=0; $i < count($Obligaciones->enMora); $i++){
            $Obligacion = new Obligacion();
            $sumas["enMora"] += $Obligaciones->enMora[$i]->saldoObligacion;
            $Obligacion->NumeroObligacion = $Obligaciones->enMora[$i]->numeroObligacion;
            $Obligacion->Valoracion = $this->idValoracion;
            $Obligacion->Entidad = $Obligaciones->enMora[$i]->nombreEntidad;
            $Obligacion->Naturaleza = $Obligaciones->enMora[$i]->lineaCredito;
            $Obligacion->Calidad = $Obligaciones->enMora[$i]->calidadDeudor;
            $Obligacion->SaldoMora = ($Obligaciones->enMora[$i]->valorMora == "") ? 0.0 : $Obligaciones->enMora[$i]->valorMora;
            $Obligacion->SaldoActual = ($Obligaciones->enMora[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->enMora[$i]->saldoObligacion;
            $Obligacion->SaldoActualOriginal = ($Obligaciones->enMora[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->enMora[$i]->saldoObligacion;
            $Obligacion->CuotaTotal = ($Obligaciones->enMora[$i]->totalCuotas == "") ? 0.0 : $Obligaciones->enMora[$i]->totalCuotas;
            $Obligacion->ValorPagar = 0;
            $Obligacion->FechaApertura = $Obligaciones->enMora[$i]->fechaApertura;
            $Obligacion->FechaVencimiento = $Obligaciones->enMora[$i]->fechaVencimiento;
            $Obligacion->ValorInicial = ($Obligaciones->enMora[$i]->valorInicial == "" || $Obligaciones->enMora[$i]->valorInicial == "1") ? (!empty($Obligaciones->enMora[$i]->cupoTotal) && $Obligaciones->enMora[$i]->cupoTotal > 0)? $Obligaciones->enMora[$i]->cupoTotal : 0.0 : $Obligaciones->enMora[$i]->valorInicial;
            $Obligacion->ValorCuota = ($Obligaciones->enMora[$i]->valorCuota == "") ? 0.0 : $Obligaciones->enMora[$i]->valorCuota;
            $Obligacion->NumeroCuotasMora = ($Obligaciones->enMora[$i]->numeroCuotasMora == "") ? 0.0 : $Obligaciones->enMora[$i]->numeroCuotasMora;
            $Obligacion->EstadoCuenta = "En Mora";
            //cambio por JFR {nuevos parametros de las obligaciones}
                $Obligacion->calificacion = (isset($Obligaciones->enMora[$i]->calificacion) && !empty($Obligaciones->enMora[$i]->calificacion))? $Obligaciones->enMora[$i]->calificacion : "NC";
                $Obligacion->comportamiento = (isset($Obligaciones->enMora[$i]->comportamiento) && !empty($Obligaciones->enMora[$i]->comportamiento))? $Obligaciones->enMora[$i]->comportamiento : "";
                $Obligacion->oficina = (isset($Obligaciones->enMora[$i]->oficina) && !empty($Obligaciones->enMora[$i]->oficina))? $Obligaciones->enMora[$i]->oficina : "";
                $Obligacion->tipoCuenta = (isset($Obligaciones->enMora[$i]->tipoCuenta) && !empty($Obligaciones->enMora[$i]->tipoCuenta))? $Obligaciones->enMora[$i]->tipoCuenta : "";
                $Obligacion->fechaActualizacion = (isset($Obligaciones->enMora[$i]->fechaActualizacion) && !empty($Obligaciones->enMora[$i]->fechaActualizacion))? strtotime($Obligaciones->enMora[$i]->fechaActualizacion) : 0;
                $Obligacion->cuotasVigencia = (isset($Obligaciones->enMora[$i]->cuotasVigencia) && !empty($Obligaciones->enMora[$i]->cuotasVigencia))? $Obligaciones->enMora[$i]->cuotasVigencia : "";
                $Obligacion->marca = $Obligaciones->enMora[$i]->marca;
                if(in_array($Obligaciones->enMora[$i]->tipoCuenta, $this->cuentasCuotaVariable) || $Obligaciones->enMora[$i]->valorCuota == ""){
                    $Obligacion->CuotasProyectadas = $Obligacion->ValorInicial * ((float) $porcentajeX50) * ((float) $porcentajeX4);                
                }else{
                    $Obligacion->CuotasProyectadas = 0;
                }               
                
                $Obligacion->EstadoCuentaCodigo = $Obligaciones->enMora[$i]->estadoCuenta;
                $Obligacion->EstadoPlasticoCodigo = $Obligaciones->enMora[$i]->estadoPlastico;
                $Obligacion->EstadoOrigenCodigo = $Obligaciones->enMora[$i]->estadoOrigen;
                $Obligacion->EstadoPagoCodigo = $Obligaciones->enMora[$i]->estadoPago;
                $Obligacion->FormaPagoCodigo = $Obligaciones->enMora[$i]->formaPago;
                $Obligacion->EstadoObligacion = $Obligaciones->enMora[$i]->estadoObligacion;
                
            //fin cambio
            $Obligacion->save();
        }
        }
        $sumas["castigadas"] = 0;
        if(isset($Obligaciones->castigadas)){     
            
        for ($i=0; $i < count($Obligaciones->castigadas); $i++){            
            $Obligacion = new Obligacion();
            $sumas["castigadas"] += $Obligaciones->castigadas[$i]->valorMora;
            $Obligacion->NumeroObligacion = $Obligaciones->castigadas[$i]->numeroObligacion;
            $Obligacion->Valoracion = $this->idValoracion;
            $Obligacion->Entidad = $Obligaciones->castigadas[$i]->nombreEntidad;
            $Obligacion->Naturaleza = $Obligaciones->castigadas[$i]->lineaCredito;
            $Obligacion->Calidad = $Obligaciones->castigadas[$i]->calidadDeudor;
            $Obligacion->SaldoMora = ($Obligaciones->castigadas[$i]->valorMora == "") ? 0.0 : $Obligaciones->castigadas[$i]->valorMora;
            $Obligacion->SaldoActual = ($Obligaciones->castigadas[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->castigadas[$i]->saldoObligacion;
            $Obligacion->SaldoActualOriginal = ($Obligaciones->castigadas[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->castigadas[$i]->saldoObligacion;
            $Obligacion->CuotaTotal = ($Obligaciones->castigadas[$i]->totalCuotas == "") ? 0.0 : $Obligaciones->castigadas[$i]->totalCuotas;
            $Obligacion->ValorPagar = 0;
            $Obligacion->FechaApertura = $Obligaciones->castigadas[$i]->fechaApertura;
            $Obligacion->FechaVencimiento = $Obligaciones->castigadas[$i]->fechaVencimiento;
            $Obligacion->ValorInicial = ($Obligaciones->castigadas[$i]->valorInicial == "" || $Obligaciones->castigadas[$i]->valorInicial == "1") ? (!empty($Obligaciones->castigadas[$i]->cupoTotal) && $Obligaciones->castigadas[$i]->cupoTotal > 0)? $Obligaciones->castigadas[$i]->cupoTotal : 0.0 : $Obligaciones->castigadas[$i]->valorInicial;            
            $Obligacion->ValorCuota = ($Obligaciones->castigadas[$i]->valorCuota == "") ? 0.0 : $Obligaciones->castigadas[$i]->valorCuota;
            $Obligacion->NumeroCuotasMora = ($Obligaciones->castigadas[$i]->numeroCuotasMora == "") ? 0.0 : $Obligaciones->castigadas[$i]->numeroCuotasMora;
            $Obligacion->EstadoCuenta = "Castigada";
            //cambio por JFR {nuevos parametros de las obligaciones}
                $Obligacion->calificacion = (isset($Obligaciones->castigadas[$i]->calificacion) && !empty($Obligaciones->castigadas[$i]->calificacion))? $Obligaciones->castigadas[$i]->calificacion : "NC";
                $Obligacion->comportamiento = (isset($Obligaciones->castigadas[$i]->comportamiento) && !empty($Obligaciones->castigadas[$i]->comportamiento))? $Obligaciones->castigadas[$i]->comportamiento : "";
                $Obligacion->oficina = (isset($Obligaciones->castigadas[$i]->oficina) && !empty($Obligaciones->castigadas[$i]->oficina))? $Obligaciones->castigadas[$i]->oficina : "";
                $Obligacion->tipoCuenta = (isset($Obligaciones->castigadas[$i]->tipoCuenta) && !empty($Obligaciones->castigadas[$i]->tipoCuenta))? $Obligaciones->castigadas[$i]->tipoCuenta : "";
                $Obligacion->fechaActualizacion = (isset($Obligaciones->castigadas[$i]->fechaActualizacion) && !empty($Obligaciones->castigadas[$i]->fechaActualizacion))? strtotime($Obligaciones->castigadas[$i]->fechaActualizacion) : 0;
                $Obligacion->cuotasVigencia = (isset($Obligaciones->castigadas[$i]->cuotasVigencia) && !empty($Obligaciones->castigadas[$i]->cuotasVigencia))? $Obligaciones->castigadas[$i]->cuotasVigencia : "";
                $Obligacion->marca = $Obligaciones->castigadas[$i]->marca;                    
                if(in_array($Obligaciones->castigadas[$i]->tipoCuenta, $this->cuentasCuotaVariable) || $Obligaciones->castigadas[$i]->valorCuota == ""){
                    $Obligacion->CuotasProyectadas = $Obligacion->ValorInicial * ((float) $porcentajeX50) * ((float) $porcentajeX4);                
                }else{
                    $Obligacion->CuotasProyectadas = 0;
                }   
                
                $Obligacion->EstadoCuentaCodigo = $Obligaciones->castigadas[$i]->estadoCuenta;
                $Obligacion->EstadoPlasticoCodigo = $Obligaciones->castigadas[$i]->estadoPlastico;
                $Obligacion->EstadoOrigenCodigo = $Obligaciones->castigadas[$i]->estadoOrigen;
                $Obligacion->EstadoPagoCodigo = $Obligaciones->castigadas[$i]->estadoPago;
                $Obligacion->FormaPagoCodigo = $Obligaciones->castigadas[$i]->formaPago;
                $Obligacion->EstadoObligacion = $Obligaciones->castigadas[$i]->estadoObligacion;
                
            //fin cambio
            $Obligacion->save();
        }
        }
        $sumas["alDia"] = 0;
        if(isset($Obligaciones->alDia)){        
        for ($i=0; $i < count($Obligaciones->alDia); $i++){
            $Obligacion = new Obligacion();
            $sumas["alDia"] += $Obligaciones->alDia[$i]->saldoObligacion;
            $Obligacion->NumeroObligacion = $Obligaciones->alDia[$i]->numeroObligacion;
            $Obligacion->Valoracion = $this->idValoracion;
            $Obligacion->Entidad = $Obligaciones->alDia[$i]->nombreEntidad;
            $Obligacion->Naturaleza = $Obligaciones->alDia[$i]->lineaCredito;
            $Obligacion->Calidad = $Obligaciones->alDia[$i]->calidadDeudor;
            $Obligacion->SaldoMora = ($Obligaciones->alDia[$i]->valorMora == "") ? 0.0 : $Obligaciones->alDia[$i]->valorMora;
            $Obligacion->SaldoActual = ($Obligaciones->alDia[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->alDia[$i]->saldoObligacion;
            $Obligacion->SaldoActualOriginal = ($Obligaciones->alDia[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->alDia[$i]->saldoObligacion;
            $Obligacion->CuotaTotal = ($Obligaciones->alDia[$i]->totalCuotas == "") ? 0.0 : $Obligaciones->alDia[$i]->totalCuotas;
            $Obligacion->ValorPagar = 0;
            $Obligacion->FechaApertura = $Obligaciones->alDia[$i]->fechaApertura;
            $Obligacion->FechaVencimiento = $Obligaciones->alDia[$i]->fechaVencimiento;
            $Obligacion->ValorInicial = ($Obligaciones->alDia[$i]->valorInicial == "" || ((float)$Obligaciones->alDia[$i]->valorInicial) <= 1) ? (!empty($Obligaciones->alDia[$i]->cupoTotal) && $Obligaciones->alDia[$i]->cupoTotal > 0)? $Obligaciones->alDia[$i]->cupoTotal : 0.0 : $Obligaciones->alDia[$i]->valorInicial;                        
            $Obligacion->ValorCuota = ($Obligaciones->alDia[$i]->valorCuota == "") ? 0.0 : $Obligaciones->alDia[$i]->valorCuota;
            $Obligacion->NumeroCuotasMora = ($Obligaciones->alDia[$i]->numeroCuotasMora == "") ? 0.0 : $Obligaciones->alDia[$i]->numeroCuotasMora;
            $Obligacion->EstadoCuenta = "Al Día";
            //cambio por JFR {nuevos parametros de las obligaciones}
                $Obligacion->calificacion = (isset($Obligaciones->alDia[$i]->calificacion) && !empty($Obligaciones->alDia[$i]->calificacion))? $Obligaciones->alDia[$i]->calificacion : "NC";
                $Obligacion->comportamiento = (isset($Obligaciones->alDia[$i]->comportamiento) && !empty($Obligaciones->alDia[$i]->comportamiento))? $Obligaciones->alDia[$i]->comportamiento : "";
                $Obligacion->oficina = (isset($Obligaciones->alDia[$i]->oficina) && !empty($Obligaciones->alDia[$i]->oficina))? $Obligaciones->alDia[$i]->oficina : "";
                $Obligacion->tipoCuenta = (isset($Obligaciones->alDia[$i]->tipoCuenta) && !empty($Obligaciones->alDia[$i]->tipoCuenta))? $Obligaciones->alDia[$i]->tipoCuenta : "";
                $Obligacion->fechaActualizacion = (isset($Obligaciones->alDia[$i]->fechaActualizacion) && !empty($Obligaciones->alDia[$i]->fechaActualizacion))? strtotime($Obligaciones->alDia[$i]->fechaActualizacion) : 0;
                $Obligacion->cuotasVigencia = (isset($Obligaciones->alDia[$i]->cuotasVigencia) && !empty($Obligaciones->alDia[$i]->cuotasVigencia))? $Obligaciones->alDia[$i]->cuotasVigencia : "";
                $Obligacion->marca = $Obligaciones->alDia[$i]->marca;
                if(in_array($Obligaciones->alDia[$i]->tipoCuenta, $this->cuentasCuotaVariable) || $Obligaciones->alDia[$i]->valorCuota == ""){
                    $Obligacion->CuotasProyectadas = $Obligacion->ValorInicial * ((float) $porcentajeX50) * ((float) $porcentajeX4);                
                }else{
                    $Obligacion->CuotasProyectadas = 0;
                }   
                
                $Obligacion->EstadoCuentaCodigo = $Obligaciones->alDia[$i]->estadoCuenta;
                $Obligacion->EstadoPlasticoCodigo = $Obligaciones->alDia[$i]->estadoPlastico;
                $Obligacion->EstadoOrigenCodigo = $Obligaciones->alDia[$i]->estadoOrigen;
                $Obligacion->EstadoPagoCodigo = $Obligaciones->alDia[$i]->estadoPago;
                $Obligacion->FormaPagoCodigo = $Obligaciones->alDia[$i]->formaPago;
                $Obligacion->EstadoObligacion = $Obligaciones->alDia[$i]->estadoObligacion;
                
            //fin cambio
            $Obligacion->save();
        }
        }
        if(isset($Obligaciones->cerradas)){        
            for ($i=0; $i < count($Obligaciones->cerradas); $i++){
                $Obligacion = new Obligacion();                
                $Obligacion->NumeroObligacion = $Obligaciones->cerradas[$i]->numeroObligacion;
                $Obligacion->Valoracion = $this->idValoracion;
                $Obligacion->Entidad = $Obligaciones->cerradas[$i]->nombreEntidad;
                $Obligacion->Naturaleza = $Obligaciones->cerradas[$i]->lineaCredito;
                $Obligacion->Calidad = $Obligaciones->cerradas[$i]->calidadDeudor;
                $Obligacion->SaldoMora = ($Obligaciones->cerradas[$i]->valorMora == "") ? 0.0 : $Obligaciones->cerradas[$i]->valorMora;
                $Obligacion->SaldoActual = ($Obligaciones->cerradas[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->cerradas[$i]->saldoObligacion;
                $Obligacion->SaldoActualOriginal = ($Obligaciones->cerradas[$i]->saldoObligacion == "") ? 0.0 : $Obligaciones->cerradas[$i]->saldoObligacion;
                $Obligacion->CuotaTotal = ($Obligaciones->cerradas[$i]->totalCuotas == "") ? 0.0 : $Obligaciones->cerradas[$i]->totalCuotas;
                $Obligacion->ValorPagar = 0;
                $Obligacion->FechaApertura = $Obligaciones->cerradas[$i]->fechaApertura;
                $Obligacion->FechaVencimiento = $Obligaciones->cerradas[$i]->fechaVencimiento;
                $Obligacion->ValorInicial = ($Obligaciones->cerradas[$i]->valorInicial == "" || ((float)$Obligaciones->cerradas[$i]->valorInicial) <= 1) ? (!empty($Obligaciones->cerradas[$i]->cupoTotal) && $Obligaciones->cerradas[$i]->cupoTotal > 0)? $Obligaciones->cerradas[$i]->cupoTotal : 0.0 : $Obligaciones->cerradas[$i]->valorInicial;                        
                $Obligacion->ValorCuota = ($Obligaciones->cerradas[$i]->valorCuota == "") ? 0.0 : $Obligaciones->cerradas[$i]->valorCuota;
                $Obligacion->NumeroCuotasMora = ($Obligaciones->cerradas[$i]->numeroCuotasMora == "") ? 0.0 : $Obligaciones->cerradas[$i]->numeroCuotasMora;
                $Obligacion->EstadoCuenta = "Cerrada";
                //cambio por JFR {nuevos parametros de las obligaciones}
                    $Obligacion->calificacion = (isset($Obligaciones->cerradas[$i]->calificacion) && !empty($Obligaciones->cerradas[$i]->calificacion))? $Obligaciones->cerradas[$i]->calificacion : "NC";
                    $Obligacion->comportamiento = (isset($Obligaciones->cerradas[$i]->comportamiento) && !empty($Obligaciones->cerradas[$i]->comportamiento))? $Obligaciones->cerradas[$i]->comportamiento : "";
                    $Obligacion->oficina = (isset($Obligaciones->cerradas[$i]->oficina) && !empty($Obligaciones->cerradas[$i]->oficina))? $Obligaciones->cerradas[$i]->oficina : "";
                    $Obligacion->tipoCuenta = (isset($Obligaciones->cerradas[$i]->tipoCuenta) && !empty($Obligaciones->cerradas[$i]->tipoCuenta))? $Obligaciones->cerradas[$i]->tipoCuenta : "";
                    $Obligacion->fechaActualizacion = (isset($Obligaciones->cerradas[$i]->fechaActualizacion) && !empty($Obligaciones->cerradas[$i]->fechaActualizacion))? strtotime($Obligaciones->cerradas[$i]->fechaActualizacion) : 0;
                    $Obligacion->cuotasVigencia = (isset($Obligaciones->cerradas[$i]->cuotasVigencia) && !empty($Obligaciones->cerradas[$i]->cuotasVigencia))? $Obligaciones->cerradas[$i]->cuotasVigencia : "";
                    $Obligacion->marca = $Obligaciones->cerradas[$i]->marca;
                    if(in_array($Obligaciones->cerradas[$i]->tipoCuenta, $this->cuentasCuotaVariable) || $Obligaciones->cerradas[$i]->valorCuota == ""){
                        $Obligacion->CuotasProyectadas = $Obligacion->ValorInicial * ((float) $porcentajeX50) * ((float) $porcentajeX4);                
                    }else{
                        $Obligacion->CuotasProyectadas = 0;
                    }   
                    
                    $Obligacion->EstadoCuentaCodigo = $Obligaciones->cerradas[$i]->estadoCuenta;
                    $Obligacion->EstadoPlasticoCodigo = $Obligaciones->cerradas[$i]->estadoPlastico;
                    $Obligacion->EstadoOrigenCodigo = $Obligaciones->cerradas[$i]->estadoOrigen;
                    $Obligacion->EstadoPagoCodigo = $Obligaciones->cerradas[$i]->estadoPago;
                    $Obligacion->FormaPagoCodigo = $Obligaciones->cerradas[$i]->formaPago;
                    $Obligacion->EstadoObligacion = $Obligaciones->cerradas[$i]->estadoObligacion;
                    
                //fin cambio
                $Obligacion->save();
            }
        }
        return $sumas;
    }

    public function registrarProcesosJuridicos($dataJuridico)
    {
        $Procesos["NumProcesos"] = count($dataJuridico->JuiciosDemandado->JuicioResumen);
        for($i=0; $i < $Procesos["NumProcesos"]; $i++)
        {
            $ProcesoJuridico = new ProcesoJuridico();
            $ProcesoJuridico->Valoracion = $this->idValoracion;
            $ProcesoJuridico->Ciudad = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->Ciudad;
            $ProcesoJuridico->Departamento = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->Departamento;
            $ProcesoJuridico->EstadoProceso = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->EstadoProceso;
            $ProcesoJuridico->Expediente = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->Expediente;
            $ProcesoJuridico->FechaInicioProceso = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->FechaInicioProceso;
            $ProcesoJuridico->FechaUltimoMovimiento = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->FechaUltimoMovimiento;
            $ProcesoJuridico->IdJuicio = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->IdJuicio;
            $ProcesoJuridico->InstanciaProceso = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->InstanciaProceso;
            $ProcesoJuridico->NitsActor = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->NitsActor;
            $ProcesoJuridico->NombresActor = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->NombresActor;
            $ProcesoJuridico->NitsDemandados = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->NitsDemandados;
            $ProcesoJuridico->NombresDemandado = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->NombresDemandado;
            $ProcesoJuridico->NumeroJuzgado = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->NumeroJuzgado;
            $ProcesoJuridico->RangoPretenciones = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->RangoPretenciones;
            $ProcesoJuridico->TieneGarantias = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->TieneGarantias;
            $ProcesoJuridico->TipoDeCausa = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->TipoDeCausa;
            $ProcesoJuridico->TipoJuzgado = $dataJuridico->JuiciosDemandado->JuicioResumen[$i]->TipoJuzgado;
            $ProcesoJuridico->save();
        }
        return $Procesos;
    }

    public function llamadoApi()
    {
        $token = new Token();
        $token->Token = str_shuffle("Xz013yV59073UIWK84oe".uniqid());
        $token->Estado = "1";
        $token->save();
        return view('')->with('token',$token->Token);
    }

    /*
    *Consulta los datos de una valoracion en BD y los convierte en Json
    */
    public function armarRespuesta($id)
    {
        //-> VARIABLES DE CONTEO.
        $NumCastigadas = 0;  $NumEnMora = 0;  $NumAlDia = 0;  $NumProcesos = 0;
        
        //-> VARIABLES SUMA DE VALORES.
        $sumaTotal["castigadas"] = 0;  $sumaTotal["enMora"] = 0;  $sumaTotal["alDia"] = 0;

        //-> ARREGLOS.
        $JsonEnMora = [];  $JsonCastigadas = [];  $JsonAlDia = [];  $JsonHuellaData = [];  $JsonHuellaCifin = [];  $JsonProcesosJuridicos = [];

        // ARREGLO PARA ALMACENAR TODA LA RESPUESTA
        $datos = [];

        $Valoracion = Valoracion::find($id);
        $datos["PuntajeData"] = $Valoracion->PuntajeData;
        $datos["PuntajeCifin"] = $Valoracion->PuntajeCifin;

        $Usuario = User::find($Valoracion->Usuario);
        $datos["NombreCompleto"] = $Usuario->nombre . " " . $Usuario->apellido;
        
        $Obligaciones = Obligacion::where('Valoracion',$Valoracion->id)->where("Estado", "Activo")->get();
        foreach ($Obligaciones as $Obligacion)
        {
            switch ($Obligacion->EstadoCuenta)
            {
                case 'En Mora':
                    $JsonEnMora[$NumEnMora]= $Obligacion;                    
                    $sumaTotal["enMora"] += $Obligacion->SaldoActual;
                    $NumEnMora += 1;
                break;
                case 'Castigada':
                    $JsonCastigadas[$NumCastigadas] = $Obligacion;
                    $sumaTotal["castigadas"] += $Obligacion->SaldoMora;
                    $NumCastigadas += 1;
                break;
                case 'Al Día':
                    $JsonAlDia[$NumAlDia]= $Obligacion;                    
                    $sumaTotal["alDia"] += $Obligacion->SaldoActual;
                    $NumAlDia += 1;
                break;
            }
        }
        $datos["NumEnMora"] = $NumEnMora;
        $datos["NumCastigadas"] = $NumCastigadas;
        $datos["NumAlDia"] = $NumAlDia;
        $datos["TotalEnMora"] = $sumaTotal["enMora"];
        $datos["TotalCastigadas"] = $sumaTotal["castigadas"];
        $datos["TotalAlDia"] = $sumaTotal["alDia"];
        $datos["tEnMora"] = json_decode(json_encode($JsonEnMora));
        $datos["tCastigadas"] = json_decode(json_encode($JsonCastigadas));
        $datos["tAlDia"] = json_decode(json_encode($JsonAlDia));
        $datos["allObligaciones"] = json_decode($Obligaciones);

        $Huellas = HuellaConsulta::where('Valoracion',$Valoracion->id)->get();
        $datos["TotalHuellas"] = count($Huellas);

        $NumHuellaData = 0;
        $NumHuellaCifin = 0;
        foreach ($Huellas as $Huella)
        {
            if($Huella->CentralInformacion == "Data Credito")
            {
                $JsonHuellaData[$NumHuellaData]["entidad"] = $Huella->Entidad;
                $JsonHuellaData[$NumHuellaData]["fecha"] = $Huella->Fecha;
                $NumHuellaData += 1;
            }
            else
            {
                $JsonHuellaCifin[$NumHuellaCifin]["entidad"] = $Huella->Entidad;
                $JsonHuellaCifin[$NumHuellaCifin]["fecha"] = $Huella->Fecha;
                $NumHuellaCifin += 1;
            }
        }
        $datos["huellaData"] = json_decode(json_encode($JsonHuellaData));
        $datos["huellaCifin"] = json_decode(json_encode($JsonHuellaCifin));

        /*$JsonProcesosJuridicos = ProcesoJuridico::where('Valoracion',$Valoracion->id)->get();
        $datos["NumProcesos"] = count($JsonProcesosJuridicos);
        $datos["tJuridico"] = json_decode($JsonProcesosJuridicos);*/

        return $datos;
    }

    public function desplegarVistaAdjuntosAnterior($id){
        $informacion;

        $tiposAdjunto = TipoAdjunto::all();
        foreach($tiposAdjunto as $tipo){
            $adjuntos = Adjunto::where("TipoAdjunto", $tipo->Codigo)->where("Tabla", "AdjuntosValoracion")->where("Modulo", "VALO")->where("idPadre", $id)->get();
            
            $adjuntosTMP = (count($adjuntos) > 0)? $adjuntos : false;
            $informacion[] = [
                "infoTipo" => [
                    "Codigo" => $tipo->Codigo,
                    "Descripcion" => $tipo->Descripcion
                ],
                "adjuntos" => $adjuntosTMP
            ];
        }       
        
        $valoracion = Valoracion::find($id);
        $usuario = User::find($valoracion->Usuario);
        return view('pages.Valoraciones.adjuntos')->with('informacion',$informacion)
                                                  ->with('idValoracion',$id)
                                                  ->with('usuario',$usuario);
    }

    public function desplegarVistaAdjuntos($id){
        $informacion;
        
        $tiposAdjunto = TipoAdjunto::find("AUT");
        
        $adjuntos = Adjunto::where("TipoAdjunto", "AUT")->where("Tabla", "autorizacionValoracion")->where("Modulo", "VALO")->where("idPadre", $id)->get();
        $adjuntosTMP = (count($adjuntos) > 0)? $adjuntos : false;
        
        $valoracion = Valoracion::find($id);
        $usuario = User::find($valoracion->Usuario);
        return view('pages.Valoraciones.adjuntos')
                                                    ->with('infoTipoAdjunto', $tiposAdjunto )
                                                    ->with('idValoracion', $id)                                                    
                                                    ->with('archivos', $adjuntosTMP)
                                                    ->with('usuario', $usuario);
    }
    
    function updateEstudio(Request $request){
        
        $datos = json_decode($request->parameters);
        $infoValoracion = Valoracion::find($request->idValoracion);
        
        $estudio = new Estudio;            
        $estudio->Estado = config('constantes.ESTUDIO_INGRESADO');            
        $estudio->VlrCuotaCompras = 0;
        $estudio->AntiguedadMeses = 0;            
        $estudio->TipoContrato = "";
        $estudio->Pagaduria = $infoValoracion->Pagaduria;
        $estudio->RamaJudicial = "";
        $estudio->Sector = "";
        $estudio->IngresoBase = str_replace(".", "", $request->EstudioIngreso);
        $estudio->TotalEgresos = str_replace(".", "", $request->EstudioEgreso);
        $estudio->ValorCompras = str_replace(".", "", $request->EstudioCompras);
        $estudio->Cupo = str_replace(".", "", $request->vlrCupo);
        $estudio->Disponible = (str_replace(".", "", $request->vlrCupo)) - (str_replace(".", "", $request->EstudioCompras));
        $estudio->Tasa = $request->tasa;
        $estudio->ValorCredito = str_replace(".", "", $request->vlrCredito);        
        $estudio->Plazo = $request->Plazo;        
        $estudio->Cuota = str_replace(".", "", $request->vlrCuota);
        $estudio->Desembolso = str_replace(".", "", $request->vlrDesembolso);
        $estudio->Valoracion = $request->idValoracion;                        
        $estudio->save();
        
        $idEstudio = $estudio->id;
        
        //Actualizamos el comercial de la valoracion        
        $infoValoracion->Comercial = $request->comercialAsignado;
        $infoValoracion->save();
                
        //Actualizamos la fecha de nacimiento del usuario dueño de la valoracion
        $user = User::find($infoValoracion->Usuario);
        $user->fecha_nacimiento = $request->FechaNacimiento;
        $user->save();
        
        $solicitud = SolicitudConsulta::where('valoracion_id',$request->idValoracion)
                                        ->get();
        if(count($solicitud)>0){
            $solicitud = $solicitud[0];
            $objtSolicitud = SolicitudConsulta::find($solicitud->id);
            $objtSolicitud->user_id;
            $objtSolicitud->save();
        }
        
        if(Auth::user()->perfil != config('constantes.ID_PERFIL_CLIENTE') && Auth::user()->perfil != config('constantes.PERFIL_COMERCIAL')){
            $url = config('constantes.RUTA')."GestionObligaciones/".$idEstudio;
        }else{
            $url = "";
        }
        
         echo json_encode(["MENSAJE" => "Información guardada satisfactoriamente", "URI" => $url]);       
       
    }
    
    function ConsultaXML($cedula){
        $envio = "";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,config('constantes.URL_REPORTE_CENTRAL'));
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "mt=obtenerLista&cc=$cedula");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $valores = curl_exec ($ch);
        
        $Respuesta = json_decode($valores);        
        
        if(!$Respuesta->STATUS){
            return view('errors.101')->with("mensaje", $Respuesta->MENSAJE);         
        }
        $data = json_decode($Respuesta->data);        
         return view('pages.reporteCliente.archivos')->with('data', $data);
    }
    function consultarData(){
        
    }

    function deleteValoracion(Request $request){

        $data = $request->all();
        $errores = array();
        $giroscliente = array();
        $obligacionids = array();
        $huellasids = array();
        $procesosids = array();
        $codigoids = array();

        //Valoracion
        $valoracion = Valoracion::find($data["Valoracion"]);

        //Obligaciones
        $Obligaciones = Obligacion::where('Valoracion',$valoracion->id)->get();
        foreach($Obligaciones as $obligacion){
            $obligacionids[]=$obligacion->id;
        }
        //Huellas Consulta
        $huellasConsulta = HuellaConsulta::where('Valoracion',$valoracion->id)->get();
        foreach($huellasConsulta as $huella){
            $huellasids[]=$huella->id;
        }
        //Procesos Juridicos
        $procesosJuridicos = ProcesoJuridico::where('Valoracion',$valoracion->id)->get();
        foreach($procesosJuridicos as $procesos){
            $procesosids[]=$procesos->id;
        }
        //User
        $user = User::find($valoracion->Usuario);

        //Codigos promicionales
        $codigos = CodigoPromocional::where('Usuario',$user->id)->get();
        foreach($codigos as $codigo){
            $codigoids[]=$codigo->id;
        }

        //Estudio
        $estudio = Estudio::where('Valoracion',$valoracion->id)->get();

        //Giros clientes
        if(count($estudio) > 0){
            $giroscliente = GiroCliente::where('Estudio',$estudio[0]->id)->get();
        }

        //Solicitud
        $solicitud = SolicitudConsulta::where('valoracion_id',$valoracion->id)->get();
        $solic = SolicitudConsulta::find($solicitud[0]->id);
        $solic->valoracion_id = NULL;

        if(count($Obligaciones) > 0 && Obligacion::destroy($obligacionids) == false){
            $errores[]="Error al eliminar obligaciones";
        }elseif(count($huellasConsulta) > 0 && HuellaConsulta::destroy($huellasids) == false){
            $errores[]="Error al eliminar huellas";
        }elseif(count($procesosJuridicos) > 0 && ProcesoJuridico::destroy($procesosids) == false){
            $errores[]="Error al eliminar procesos juridicos";
        }elseif(count($giroscliente) > 0 && GiroCliente::where('Estudio',$estudio->id)->delete() == false){
            $errores[]="Error al eliminar los giros del estudio";
        }elseif(count($estudio) > 0 && Estudio::where('Valoracion',$valoracion->id)->delete() == false){
            $errores[]="Error al eliminar el estudio";
        }elseif (count($valoracion) > 0 && $valoracion->delete() == false){
            $errores[]="Error al eliminar la valoracion";
        }elseif (count($codigos) > 0 && CodigoPromocional::destroy($codigoids) == false){
            $errores[]="Error al eliminar los codigos promocionales";
        }elseif (count($user) > 0 && $user->delete() == false){
            $errores[]="Error al eliminar el usuario";
        }elseif (count($solicitud) > 0 && $solic->save() == false){
            $errores[]="Error al eliminar la relación entre valoraciòn y solicitud";
        }

        if(count($errores)){
            $mensaje = "Se ha producido un error";
        }else{
            $mensaje = "La valoración y el estudio se han borrado con exito";
        }

        $user = Auth::user();
        $Valoraciones = DB::select("SELECT VALORACIONES.id, VALORACIONES.Filtro, cedula, nombre, apellido, VALORACIONES.Pagaduria,VALORACIONES.created_at,
                                           IFNULL((SELECT nombre
                                                     FROM USERS
                                                    WHERE id = Comercial),'N/A') Comercial
                                      FROM VALORACIONES,USERS                                      
                                     WHERE VALORACIONES.Usuario = USERS.id
                                       AND ((:perfilUsuario = :comercial AND Comercial = :usuario) OR :perfilUsuario1 != :comercial1)
                                     ORDER BY VALORACIONES.created_at DESC",[
            'perfilUsuario' => Auth::user()->perfil,
            'comercial' => config('constantes.PERFIL_COMERCIAL'),
            'usuario' => Auth::user()->id,
            'perfilUsuario1' => Auth::user()->perfil,
            'comercial1' => config('constantes.PERFIL_COMERCIAL')
        ]);

        $html = view('pages.Valoraciones.fragmentos.tablaValoracion')
            ->with('Valoraciones',$Valoraciones)
            ->with('forma', $this->forma)
            ->with('user', $user)
            ->render();

        echo json_encode([
            "mensaje"=>$mensaje,
            "errores"=>$errores,
            "tabla" => $html
        ]);


    }

}
