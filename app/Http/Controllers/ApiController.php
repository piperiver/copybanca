<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SoapClient;

class ApiController extends Controller
{
    var $carpetaPrincipal = "archivosXML";
    var $mensajeError = "";
    var $regValidacion = "";    
    
    function pruebaConsumo(){
        try {            
            
            set_time_limit(0);
            
            $WSDL = 'http://172.24.14.29:8080/idws2/services/ServicioIdentificacion?wsdl';
            
            $client = new SoapClient($WSDL, array(
                "trace" => true,           
                'login' => "1144176698"
            ));
            
            $datosValidacion = '<?xml version="1.0" encoding="UTF-8"?>
                                <DatosValidacion> 
                                    <Identificacion numero="'.$identificacion.'" tipo="1" /> 
                                    <PrimerApellido>'.$primerApellido.'</PrimerApellido>                                
                                    <Nombres>'.$nombre.'</Nombres> 
                                    <FechaExpedicion timestamp="'.$fechaExpedicion.'" />
                                </DatosValidacion>';
                    
    //        $xml = new SimpleXMLElement($datosValidacion);        
            $trama = [
                "paramProducto" => "3020",
                "producto" => "007",
                "canal" => "001",
                "datosValidacion" => $datosValidacion
            ];
                    
            $result = $client->__soapCall("validar", $trama);            
            return $result;

        } catch (SoapFault $exception) {
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservice evidente validar. mensaje: ".$exception->getMessage());
            die();
        }
    }
    
    
    function checkDataHC2($contenido){
       $respuesta = (string) $contenido->Informe["respuesta"];
       
        if(in_array($respuesta, ["01","14"])){
            return false;
        }elseif(!in_array($respuesta, ["13","15"])){            
            $equivalencias = json_decode($this->getEquivalencias());            
            $this->imprimir(false, $equivalencias->tabla13->{$respuesta});
            die();
        }
        return true;
    }
    
    function consultaClienteProduccion($identificacion,$apellido){
        $xmlData = $this->validarCache($identificacion, "HC2-P",$apellido);
//        print_r($xmlData);
        $responseValidation = $this->checkDataHC2($xmlData);
        if($responseValidation !== false){
            $arrayData = $this->replaceElements($xmlData);
        }        
        $xmlSifin = $this->validarCache($identificacion, "Sifin-InfoComercial-P",$apellido);        
//        print_r($xmlSifin);
        if($xmlSifin !== false){            
            $arraySifin = $this->replaceElementsSifin($xmlSifin);                    
        }        
        
        if($xmlData !== false && $xmlSifin !== false){
            $obligaciones = $this->unionDataSifin($arrayData, $arraySifin);
        }elseif($xmlData !== false){
            $obligaciones = $xmlData;
        }elseif($xmlSifin !== false){
            $obligaciones = $xmlSifin;
        }
        //echo '<pre>';
        $dataJuridico = $this->consularWsDataJuridico($identificacion);
        $obligaciones["dataJuridico"] = simplexml_load_string($dataJuridico);          
//        $obligaciones["dataJuridico"] = [];
        echo json_encode($obligaciones);
    }
    function consultaCliente($identificacion,$apellido){//Request $request
        //dd("Llega");//$request
        /*$identificacion = $request->numIdentificacion;
        $apellido = $request->primerApellido;*/
        $xmlData = $this->validarCache($identificacion, "HC2",$apellido);        
        $responseValidation = $this->checkDataHC2($xmlData);
        if($responseValidation !== false){
            $arrayData = $this->replaceElements($xmlData);
        }        
        $xmlSifin = $this->validarCache($identificacion, "Sifin-InfoComercial",$apellido);                
        if($xmlSifin !== false){            
            $arraySifin = $this->replaceElementsSifin($xmlSifin);                    
        }        
        
        if($xmlData !== false && $xmlSifin !== false){
            $obligaciones = $this->unionDataSifin($arrayData, $arraySifin);
        }elseif($xmlData !== false){
            $obligaciones = $xmlData;
        }elseif($xmlSifin !== false){
            $obligaciones = $xmlSifin;
        }
        //echo '<pre>';
        $dataJuridico = $this->consularWsDataJuridico($identificacion);        
        $obligaciones["dataJuridico"] = simplexml_load_string($dataJuridico);          
        echo json_encode($obligaciones);
    }
    
    function crearArchivo($identificacion, $respuesta, $nombreWS) {
        try {
            $mediaXML = (file_exists("$this->carpetaPrincipal/")) ? true : mkdir("$this->carpetaPrincipal/", 0755);
            $carpetaCliente = (file_exists("$this->carpetaPrincipal/$identificacion/")) ? true : mkdir("$this->carpetaPrincipal/$identificacion/", 0755);

            if ($mediaXML && $carpetaCliente) {
                $nuevoarchivo = fopen("$this->carpetaPrincipal/$identificacion/$nombreWS.fff", "w+");
                fwrite($nuevoarchivo, base64_encode($respuesta));
                fclose($nuevoarchivo);
                return true;
            } else {
                $nuevoarchivo = fopen("{$nombreWS}_{$identificacion}.fff", "w+");
                fwrite($nuevoarchivo, base64_encode($respuesta));
                fclose($nuevoarchivo);
//                $this->imprimir(false, "Ocurrio un problema en el proceso. Por favor intentelo de nuevo.");
                $this->setMessageLog(__LINE__.": Ocurrio un problema al tratar de crear la carpeta del cliente. El archivo bakup se creo temporalmente con este nombre: {$nombreWS}_{$identificacion}.fff");                
            }
        }catch (Exception $exc) {
//            $this->imprimir(false, "Ocurrio un problema en el proceso. Por favor intentelo de nuevo.");
            $this->setMessageLog(__LINE__.": Ocurrio un problema en al intentar crear el archivo de backup. Mensaje: ".$exc->getMessage());
        }
        return true;
    }

    function validarCache($identificacion, $webservice, $primerApellido, $nombre=null, $fechaExpedicion=null, $regValidacion = "") {
        switch ($webservice) {
            case "HC2":                
                if (file_exists("$this->carpetaPrincipal/$identificacion/HC2.fff")) {                    
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/HC2.fff";
                    $gestor = fopen($nombre_fichero, "r");
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                    
                    $contenido = base64_decode($contenido);
                    $contenido = simplexml_load_string($contenido);                    
                    return $contenido;
                }else{                    
                    $result = $this->consularWsData($identificacion, $primerApellido);                    
                    $contenido = simplexml_load_string($result);                    
                    $this->crearArchivo($identificacion, $result, $webservice);
                    return $contenido;
                }
                break;
            case "HC2-P":
                
                if (file_exists("$this->carpetaPrincipal/$identificacion/HC2-P.fff")) {
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/HC2-P.fff";
                    $gestor = fopen($nombre_fichero, "r");
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                    
                    $contenido = base64_decode($contenido);
                    $contenido = simplexml_load_string($contenido);
                    return $contenido;
                }else{
                    $result = $this->consularWsDataProduccion($identificacion, $primerApellido);                    
                    $contenido = simplexml_load_string($result);                    
                    $this->crearArchivo($identificacion, $result, $webservice);
                    return $contenido;
                }
                
                break;
            case "Sifin-InfoComercial":
                if (file_exists("$this->carpetaPrincipal/$identificacion/Sifin-InfoComercial.fff")) {
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/Sifin-InfoComercial.fff";
                    $gestor = fopen($nombre_fichero, "r"); 
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                    
                    $contenido = base64_decode($contenido);
                    $contenido = simplexml_load_string($contenido);
                    if($contenido->Tercero["RespuestaConsulta"] == "01"){                        
                        return false;
                    }
                    return $contenido;
                }else{
                    $result = $this->consultaWsSifin($identificacion, $webservice);
                    $contenido = simplexml_load_string($result);
                    if($contenido->Tercero["RespuestaConsulta"] == "01"){                        
                        return false;
                    }
                    $this->crearArchivo($identificacion, $result, $webservice);
                    return $contenido;
                    
                }

                break;
            case "Sifin-InfoComercial-P":
                if (file_exists("$this->carpetaPrincipal/$identificacion/Sifin-InfoComercial-P.fff")) {                   
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/Sifin-InfoComercial-P.fff";
                    $gestor = fopen($nombre_fichero, "r"); 
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                    
                    $contenido = base64_decode($contenido);
                    $contenido = simplexml_load_string($contenido);
                    if($contenido->Tercero["RespuestaConsulta"] == "01"){                        
                        return false;
                    }                    
                    return $contenido;
                }else{                    
                    $result = $this->consultaWsSifinProduccion($identificacion, $webservice);
                    $contenido = simplexml_load_string($result);
                    if($contenido->Tercero["RespuestaConsulta"] == "01"){                        
                        return false;
                    }
                    $this->crearArchivo($identificacion, $result, $webservice);
                    return $contenido;
                    
                }

                break;
            case "Evidente-Validar":
                if (file_exists("$this->carpetaPrincipal/$identificacion/$webservice.fff") && false) {
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/$webservice.fff";
                    $gestor = fopen($nombre_fichero, "r");
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                                        
                    $contenido = base64_decode($contenido);
                    $contenido = simplexml_load_string($contenido);                    
                    return $contenido;
                }else{                    
                    $result = $this->consultarWsEvidenteValidar($identificacion, $primerApellido, $nombre, $fechaExpedicion);
                    $contenido = simplexml_load_string($result);                   
//                    $this->crearArchivo($identificacion, $result, $webservice);
                    return $contenido;
                }
                break;
            case "Evidente-Preguntas":
                if (file_exists("$this->carpetaPrincipal/$identificacion/$webservice.fff") && false) {
                    $nombre_fichero = "$this->carpetaPrincipal/$identificacion/$webservice.fff";
                    $gestor = fopen($nombre_fichero, "r");
                    $contenido = fread($gestor, filesize($nombre_fichero));
                    fclose($gestor);                    
                    $contenido = base64_decode($contenido);
                    echo $contenido; die;
                    $contenido = simplexml_load_string($contenido);                    
                    return $contenido;
                }else{                    
                    $result = $this->consultarWsEvidentePreguntas($identificacion, $regValidacion);
//                    $this->crearArchivo($identificacion, $result, $webservice);
                    $contenido = simplexml_load_string($result);
                    return $contenido;
                }
                break;
            default:
                return "El nombre del archivo que desea consultar, no se encuentra parametrizado";
                break;
        }
    }

    function consultaWsSifin($identificacion) {
        
        set_time_limit(0);

        $certificado = '/root/CERT-2017/private_key.pem';
        $clave = 'vpvNjyAc742zpvTdJ';

        $WSDL = 'http://cifinpruebas.asobancaria.com/InformacionComercialWS/services/InformacionComercial?wsdl';
//        $WSDL = 'http://cifinpruebas.asobancaria.com/ws/UbicaPlusWebService/services/UbicaPlus?wsdl';
        $SOAP_OPTS = array(
            'login' => '689346',
            'password' => 'D7lo!p',
            'local_cert' => $certificado,
            'passphrase' => $clave,
            'trace' => true);
        
        try {
            
            $client = new SoapClient($WSDL, $SOAP_OPTS);            
    //        $client->__setLocation('http://cifinpruebas.asobancaria.com/ws/UbicaPlusWebService/services/UbicaPlusseg');
            $client->__setLocation('http://cifinpruebas.asobancaria.com/InformacionComercialWS/services/InformacionComercialseg');
            
            $parametros = [
                "parametrosConsulta" => [
                    "codigoInformacion" => "2042", //154 [Informacion comercial] - 2042 [Informacion comercial + score]
    //                "codigoInformacion" => "5632",
                    "motivoConsulta" => "23",
                    "numeroIdentificacion" => $identificacion,
                    "tipoIdentificacion" => "1"
                ]
            ];
        
            $result = $client->__soapCall("consultaXml", $parametros);
//            $result = $client->__soapCall("consultaUbicaPlus", $parametros);            
            return $result;
            
        } catch (SoapFault $exception) {
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservide se sifin. Mensaje: ".$exception->getMessage());
            die;
        }        
    }

    function consularWsData($identificacion, $primerApellido) {
        set_time_limit(0);

        $WSDL = 'http://172.24.14.29:8080/dhws3/services/DH2PNClientesService_v1-5?wsdl';

        try {
            $client = new SoapClient($WSDL, array("trace" => true));            
            
            $TRAMA = "<?xml version='1.0' encoding='UTF-8' ?>
            <Solicitudes>
                <Solicitud clave='84MJW' identificacion='$identificacion' primerApellido='$primerApellido' producto='64' tipoIdentificacion='1' usuario='1144176698' />
            </Solicitudes>";
            
            $result = $client->__soapCall("consultarHC2", array("xmlConsulta" => $TRAMA));            
            return $result;
            
        } catch (Exception $exc) {
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservice de datacredito. mensaje: ".$exc->getMessage());
            die();
        }       
    }
    function consularWsDataJuridico($identificacion) {
        
        set_time_limit(0);
        
        $WSDL = 'http://172.24.14.29:8080/ijws/services/ServicioIJWS?wsdl';
       
        $client = new SoapClient($WSDL, array("trace" => true));        
//        print_r($client->__getFunctions());die;
        $envio = [
            "tipoDocCliente" => "1",
            "nroDocCliente" => "1144176698",
            "tpoNitCliente" => "2",
            "nitCliente" => "900661159",
            "tpoDocConsultado" => "1",
            "nroDocConsultado" => "$identificacion",//39025870
            "Pagina" => "1",
            "Clave" => "60KUT"
        ];
        
        try {            
            $result = $client->__soapCall("buscarPaginaPN", $envio);
//            $this->crearArchivo($identificacion, $result, $nombreWS);
            /* Convierte el string que retorna soap a un objeto
              $xmlp = simplexml_load_string($result);
             */            
            return $result;
            
        } catch (SoapFault $exception) {            
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservice de datacredito datajuridico. mensaje: ".$exception->getMessage());
            die();
        }
    }

    function consultarWsEvidenteValidar($identificacion, $primerApellido, $nombre, $fechaExpedicion) {
        try {            
            
            set_time_limit(0);
            
            $WSDL = 'http://172.24.14.29:8080/idws2/services/ServicioIdentificacion?wsdl';
            
            $client = new SoapClient($WSDL, array(
                "trace" => true,           
                'login' => "1144176698"
            ));
            
            $datosValidacion = '<?xml version="1.0" encoding="UTF-8"?>
                                <DatosValidacion> 
                                    <Identificacion numero="'.$identificacion.'" tipo="1" /> 
                                    <PrimerApellido>'.$primerApellido.'</PrimerApellido>                                
                                    <Nombres>'.$nombre.'</Nombres> 
                                    <FechaExpedicion timestamp="'.$fechaExpedicion.'" />
                                </DatosValidacion>';
                    
    //        $xml = new SimpleXMLElement($datosValidacion);        
            $trama = [
                "paramProducto" => "3020",
                "producto" => "007",
                "canal" => "001",
                "datosValidacion" => $datosValidacion
            ];
                    
            $result = $client->__soapCall("validar", $trama);
            echo "REQUEST VALIDAR:\n" . $client->__getLastRequest() . "\n";
            echo "RESPONSE VALIDAR:\n" . $result . "\n";
            return $result;

        } catch (SoapFault $exception) {
//            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception);
            die("ERROR ".__LINE__);
        }
    }
    function consultarWsEvidentePreguntas($identificacion, $regValidacion) {

        set_time_limit(0);

        $WSDL = 'http://172.24.14.29:8080/idws2/services/ServicioIdentificacion?wsdl';
        
        $client = new SoapClient($WSDL, array(
            "trace" => true,           
            'login' => "1144176698"
        ));
        
        $datosValidacion = '<?xml version="1.0" encoding="UTF-8"?> <SolicitudCuestionario tipoId="1" identificacion="'.$identificacion.'" regValidacion="'.$regValidacion.'" />';
//        $xml = new SimpleXMLElement($datosValidacion);            
        $trama = [
            "paramProducto" => "3020",
            "producto" => "007",
            "canal" => "001",
            "solicitudCuestionario" => $datosValidacion
        ];        
        
        
        try {            
            $result = $client->__soapCall("preguntas", $trama);                    
            echo "REQUEST PEGUNTAS:\n" . $client->__getLastRequest() . "\n";
            echo "RESPONSE PREGUNTAS:\n" . $result . "\n";
            return $result;            

        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception->getMessage());
            die("ERROR");
        }
    }
    function consultarWsEvidenteVerificar() {

        set_time_limit(0);

        $WSDL = 'http://172.24.14.29:8080/idws2/services/ServicioIdentificacion?wsdl';
        
        $client = new SoapClient($WSDL, array(
            "trace" => true,           
            'login' => "1144176698"
        ));
        
        $datos = $this->getRespuestas();
        $idCuestionario = $datos["idCuestionario"];
        $regCuestionario = $datos["regCuestionario"];
        $identificacion = $datos["Identificacion"]["numero"];
        $tipo = $datos["Identificacion"]["tipo"];
        
        $respuestas ="";
        foreach ($datos["Respuestas"] as $idPregunta => $idRespuesta){
            $respuestas.= '<Respuesta idPregunta="'.$idPregunta.'" idRespuesta="'.$idRespuesta.'" />';
        }
       
        $datosValidacion = '
                    <?xml version="1.0" encoding="UTF-8"?>
                    <Respuestas idCuestionario="00011315" regCuestionario="4250864">
                        <Identificacion numero="1144176698" tipo="1" />
                        <Respuesta idPregunta="1" idRespuesta="01" />
                        <Respuesta idPregunta="2" idRespuesta="01" />
                        <Respuesta idPregunta="3" idRespuesta="03" />
                        <Respuesta idPregunta="4" idRespuesta="04" />
                    </Respuestas>';
//        $xml = new SimpleXMLElement($datosValidacion);            
        $trama = [
            "paramProducto" => "3020",
            "producto" => "007",            
            "canal" => "001",
            "respuestas" => $datosValidacion
        ];
        
        try {            
            $result = $client->__soapCall("verificar", $trama);                    
            echo "REQUEST VERIFICAR:\n" . $client->__getLastRequest() . "\n";
            echo "RESPONSE VERIFICAR:\n" . $result . "\n";
            return $result;            

        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception->getMessage());
            die("ERROR");
        }
    }

    function getEquivalencias() {
        $nombre_fichero = "parametros.json";
        if (file_exists($nombre_fichero)) {
            $gestor = fopen($nombre_fichero, "r");
            $contenido = fread($gestor, filesize($nombre_fichero));
            fclose($gestor);
            return $contenido;
        } else {
            return false;
        }
    }
    function getEquivalenciasSifin() {                           
        $nombre_fichero = "parametrosSifn.json";
        if (file_exists($nombre_fichero)) {
            $gestor = fopen($nombre_fichero, "r");
            $contenido = fread($gestor, filesize($nombre_fichero));
            fclose($gestor);
            return $contenido;
        } else {
            return false;
        }
    }

    function replaceElements($object) {
        
        $equivalencias = json_decode($this->getEquivalencias());
        
        $data = array();
        
        /*
         * Se carga la informacion principal
         */
        
        $data["infoUser"]["nombres"] = (string) $object->Informe->NaturalNacional["nombres"];
        $data["infoUser"]["primerApellido"] = (string) $object->Informe->NaturalNacional["primerApellido"];
        $data["infoUser"]["segundoApellido"] = (string) $object->Informe->NaturalNacional["segundoApellido"];
        $data["infoUser"]["nombreCompleto"] = (string) $object->Informe->NaturalNacional["nombreCompleto"];
        $data["infoUser"]["fechaConsulta"] = (string) $object->Informe["fechaConsulta"];
        $data["infoUser"]["identificacion"] = (string) $object->Informe->NaturalNacional->Identificacion["numero"];
        $data["infoUser"]["fechaExpedicion"] = (string) $object->Informe->NaturalNacional->Identificacion["fechaExpedicion"];
        $data["infoUser"]["sexo"] = ($object->Informe->NaturalNacional["genero"] == "4")? "Masculino" : "Femenino" ;
        $data["infoUser"]["edad"] = $object->Informe->NaturalNacional->Edad["min"]." - ".$object->Informe->NaturalNacional->Edad["max"];
                
        $data["score"]["puntaje"] = (string) $object->Informe->Score["puntaje"];
        $data["score"]["fecha"] = (string) $object->Informe->Score["fecha"];
        
        foreach ($object->Informe->Consulta as $huella){
            $data["huellaConsulta"][] = [
                                            "entidad" => (string) $huella["entidad"],
                                            "fecha" => (string) $huella["fecha"]
                                        ];            
        }
        /*
         * Logica para recorrer las cuentas cartera
         */
        if ((is_array($object->Informe->CuentaCartera) && count($object->Informe->CuentaCartera) > 0)
            || is_object($object->Informe->CuentaCartera)) {
            
            foreach ($object->Informe->CuentaCartera as $cuentaCartera) {            
                
                $credito = [
                    "nombreEntidad" => (string) $cuentaCartera["entidad"],
                    "numeroObligacion" => (string) $cuentaCartera["numero"],
                    "fechaApertura" => (string) $cuentaCartera["fechaApertura"],
                    "fechaVencimiento" => (string) $cuentaCartera["fechaVencimiento"],
                    "lineaCredito" => (isset($cuentaCartera->Caracteristicas["tipoCuenta"]) && !empty($cuentaCartera->Caracteristicas["tipoCuenta"]))? $equivalencias->tabla3->{$cuentaCartera->Caracteristicas["tipoCuenta"]} : "",
                    "saldoObligacion" => (string) $cuentaCartera->Valores->Valor["saldoActual"],
                    "valorInicial" => (string) $cuentaCartera->Valores->Valor["valorInicial"],
                    "totalCuotas" => (string) $cuentaCartera->Valores->Valor["totalCuotas"],
                    "valorCuota" => (string) $cuentaCartera->Valores->Valor["cuota"],
                    "valorMora" => (string) $cuentaCartera->Valores->Valor["saldoMora"],
                    "numeroCuotasMora" => (string) $cuentaCartera->Valores->Valor["cuotasMora"],
                    "calidadDeudor" => (isset($cuentaCartera->Caracteristicas["calidadDeudor"]) && !empty($cuentaCartera->Caracteristicas["calidadDeudor"]))? $equivalencias->tabla6->{$cuentaCartera->Caracteristicas["calidadDeudor"]}->descripcionCartera : ""
                ];                

                $estadoCuentaCod = $cuentaCartera->Estados->EstadoCuenta["codigo"];
                $estadoPagoCod = $cuentaCartera->Estados->EstadoPago["codigo"];
                $formaPago = $cuentaCartera["formaPago"];

                if ($estadoCuentaCod == 10 || ($estadoPagoCod == 43 && $formaPago == 3) || ($estadoPagoCod == 43 && $formaPago != 3)) {
                    continue;
                } elseif ($estadoPagoCod == 45 || $estadoPagoCod == 47) {
                    //castigadas                      
                    $data["obligaciones"]["castigadas"][] = $credito;
                } elseif ($estadoPagoCod >= 13 && $estadoPagoCod <= 41) {
                    //mora                    
                    $data["obligaciones"]["enMora"][] = $credito;
                } elseif ($estadoPagoCod == 1) {
                    //Al dia                    
                    $data["obligaciones"]["alDia"][] = $credito;
                }
                unset($credito);
            }            
        }        
        /*
         * Logica para recorrer las tarjetas de credito
         */
        if ((is_array($object->Informe->TarjetaCredito) && count($object->Informe->TarjetaCredito) > 0)
          || is_object($object->Informe->TarjetaCredito)){
            
            foreach ($object->Informe->TarjetaCredito as $tarjetaCredito) {
            
                $credito = [
                    "nombreEntidad" => (string) $tarjetaCredito["entidad"],
                    "numeroObligacion" => (string) $tarjetaCredito["numero"],
                    "fechaApertura" => (string) $tarjetaCredito["fechaApertura"],
                    "fechaVencimiento" => (string) $tarjetaCredito["fechaVencimiento"],
                    "lineaCredito" => (string) $tarjetaCredito->Caracteristicas["tipoCuenta"],
                    "saldoObligacion" => (string) $tarjetaCredito->Valores->Valor["saldoActual"],
                    "valorInicial" => (string) $tarjetaCredito->Valores->Valor["valorInicial"],
                    "totalCuotas" => (string) $tarjetaCredito->Valores->Valor["totalCuotas"],
                    "valorCuota" => (string) $tarjetaCredito->Valores->Valor["cuota"],
                    "valorMora" => (string) $tarjetaCredito->Valores->Valor["saldoMora"],
                    "numeroCuotasMora" => (string) $tarjetaCredito->Valores->Valor["cuotasMora"],
                    "calidadDeudor" => ($tarjetaCredito->Caracteristicas["amparada"] == "true") ? "Amparada" : "Principal"
                ];

                $estadoCuentaCod = $tarjetaCredito->Estados->EstadoCuenta["codigo"];
                $estadoPagoCod = $tarjetaCredito->Estados->EstadoPago["codigo"];
                $formaPago = $tarjetaCredito["formaPago"];

                if ($estadoCuentaCod == 10 || ($estadoPagoCod == 43 && $formaPago == 3) || ($estadoPagoCod == 43 && $formaPago != 3)) {
                    continue;
                } elseif ($estadoPagoCod == 45 || $estadoPagoCod == 47) {
                    //castigadas                      
                    $data["obligaciones"]["castigadas"][] = $credito;
                } elseif ($estadoPagoCod >= 13 && $estadoPagoCod <= 41) {
                    //mora                    
                    $data["obligaciones"]["enMora"][] = $credito;
                } elseif ($estadoPagoCod == 1) {
                    //al dia                    
                    $data["obligaciones"]["alDia"][] = $credito;
                }
                unset($credito);
            }
        }
        
        return $data;
    }
    
    function getArrayCifin($object, $equivalencias, $sector){
        $array = array();
        foreach ($object as $item){
            $array[] = [
                "nombreEntidad" => (string) $item->NombreEntidad,
                "numeroObligacion" => (string) $item->NumeroObligacion,
                "fechaApertura" => (string) $item->FechaApertura,
                "fechaVencimiento" => (string) $item->FechaCorte,
                "lineaCredito" => (isset($item->LineaCredito) && !empty($item->LineaCredito))? utf8_decode($equivalencias->tabla5->$sector->{$item->LineaCredito}) : "",
                "saldoObligacion" => (string) $item->SaldoObligacion,
                "valorInicial" => (string) $item->ValorInicial,
                "totalCuotas" => (string) $item->NumeroCuotasPactadas,
                "valorCuota" => (string) $item->ValorCuota,
                "valorMora" => (string) $item->ValorMora,
                "numeroCuotasMora" => (string) $item->NumeroCuotasMora,
                "calidadDeudor" => (isset($item->Calidad) && !empty($item->Calidad))? $equivalencias->tabla2->{$item->Calidad} : ""
            ];        
        } 
        return $array;
    }
    function replaceElementsSifin($xml){               
        
        $equivalencias = json_decode(utf8_encode($this->getEquivalenciasSifin()));        
//        if(!isset($xml->Tercero->SectorRealAlDia->Obligacion) && !isset($xml->Tercero->SectorFinancieroAlDia->Obligacion)){
//            return $xml->Tercero->Mensajes;
//        }
        $data = array(); 
        
        /*
         * Se carga la informacion principal
         */
        $data["score"]["puntaje"] = (string)$xml->Tercero->Score->Puntaje;                
        
        foreach ($xml->Tercero->HuellaConsulta->Consulta as $huella){
            $explode = explode("/", $huella->FechaConsulta);
            $data["huellaConsulta"][] = [
                                            "entidad" => (string) $huella->NombreEntidad,
                                            "fecha" => "{$explode[2]}-{$explode[1]}-{$explode[0]}"
                                        ];            
        }        
        
        /*
         * Inicio obligaciones al dia
         */
        $aldiaFinanciero = [];
        $aldiaReal = [];
        if(isset($xml->Tercero->SectorFinancieroAlDia->Obligacion) && count($xml->Tercero->SectorFinancieroAlDia->Obligacion) > 0){
            $aldiaFinanciero = $this->getArrayCifin($xml->Tercero->SectorFinancieroAlDia->Obligacion, $equivalencias, "sectorFinanciero");
        }
        if(isset($xml->Tercero->SectorRealAlDia->Obligacion) && count($xml->Tercero->SectorRealAlDia->Obligacion) > 0){
        $aldiaReal = $this->getArrayCifin($xml->Tercero->SectorRealAlDia->Obligacion, $equivalencias, "sectorReal");
        }
        
        $data["obligaciones"]["alDia"]  = array_merge($aldiaFinanciero, $aldiaReal);
        /*
         * Fin obligaciones al dia
         * Inicio Obligaciones en mora y castigadas
         */        
        if(isset($xml->Tercero->SectorFinancieroEnMora->Obligacion) && count($xml->Tercero->SectorFinancieroEnMora->Obligacion) > 0){
            foreach ($xml->Tercero->SectorFinancieroEnMora->Obligacion as $sectorFinanciero){
                $tempData = [
                    "nombreEntidad" => (string) $sectorFinanciero->NombreEntidad,
                    "numeroObligacion" => (string) $sectorFinanciero->NumeroObligacion,
                    "fechaApertura" => (string) $sectorFinanciero->FechaApertura,
                    "fechaVencimiento" => (string) $sectorFinanciero->FechaCorte,
                    "lineaCredito" => (isset($sectorFinanciero->LineaCredito) && !empty($sectorFinanciero->LineaCredito))? utf8_decode($equivalencias->tabla5->sectorFinanciero->{$sectorFinanciero->LineaCredito}) : "",
                    "saldoObligacion" => (string) $sectorFinanciero->SaldoObligacion,
                    "valorInicial" => (string) $sectorFinanciero->ValorInicial,
                    "totalCuotas" => (string) $sectorFinanciero->NumeroCuotasPactadas,
                    "valorCuota" => (string) $sectorFinanciero->ValorCuota,
                    "valorMora" => (string) $sectorFinanciero->ValorMora,
                    "numeroCuotasMora" => (string) $sectorFinanciero->NumeroCuotasMora,
                    "calidadDeudor" => (isset($sectorFinanciero->Calidad) && !empty($sectorFinanciero->Calidad))? $equivalencias->tabla2->{$sectorFinanciero->Calidad} : ""                
                ];
                if($sectorFinanciero->EstadoObligacion == "CAST"){
                    $data["obligaciones"]["castigadas"][] = $tempData;
                }else{
                    $data["obligaciones"]["enMora"][] = $tempData;
                }

            }        
        }
        
        if(isset($xml->Tercero->SectorRealEnMora->Obligacion) && count($xml->Tercero->SectorRealEnMora->Obligacion) > 0){
            foreach ($xml->Tercero->SectorRealEnMora->Obligacion as $sectorReal){
                $tempData = [
                    "nombreEntidad" => (string) $sectorReal->NombreEntidad,
                    "numeroObligacion" => (string) $sectorReal->NumeroObligacion,
                    "fechaApertura" => (string) $sectorReal->FechaApertura,
                    "fechaVencimiento" => (string) $sectorReal->FechaCorte,                    
                    "lineaCredito" => (isset($sectorReal->LineaCredito) && !empty($sectorReal->LineaCredito))? utf8_decode($equivalencias->tabla5->sectorReal->{$sectorReal->LineaCredito}) : "",
                    "saldoObligacion" => (string) $sectorReal->SaldoObligacion,
                    "valorInicial" => (string) $sectorReal->ValorInicial,
                    "totalCuotas" => (string) $sectorReal->NumeroCuotasPactadas,
                    "valorCuota" => (string) $sectorReal->ValorCuota,
                    "valorMora" => (string) $sectorReal->ValorMora,
                    "numeroCuotasMora" => (string) $sectorReal->NumeroCuotasMora,
                    "calidadDeudor" => (isset($sectorReal->Calidad) && !empty($sectorReal->Calidad))? $equivalencias->tabla2->{$sectorReal->Calidad} : ""
                ];

                if($sectorReal->EstadoObligacion == "CAST"){
                    $data["obligaciones"]["castigadas"][] = $tempData;
                }else{
                    $data["obligaciones"]["enMora"][] = $tempData;
                }
            }
        }
        /*
         * Fin obligaciones en mora
         */
        return $data;
        
    }
    
    function compararInfo($dataSifin, $infoData){
            
        $infoData["numeroObligacion"] = str_replace(".", "", $infoData["numeroObligacion"]);
        $numeroCuenta = substr($infoData["numeroObligacion"], (strlen($infoData["numeroObligacion"])-4), (strlen($infoData["numeroObligacion"])-1));
               
        foreach ($dataSifin as $infoSifin){
            $infoSifin["numeroObligacion"] = str_replace("-", "", $infoSifin["numeroObligacion"]);            
        
                if(strpos($infoSifin["numeroObligacion"], $numeroCuenta) !== false){                    
                    $tmpFecha = explode("/", $infoSifin["fechaApertura"]);
                    $fechaAperturaSifin = "{$tmpFecha["2"]}-{$tmpFecha["1"]}-{$tmpFecha["0"]}";
                    
                    if($fechaAperturaSifin === (string)$infoData["fechaApertura"]){
                        $infoData = $this->reemplazar($infoSifin, $infoData);                        
                        return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                    }else{
                        unset($tmpFecha);
                        $tmpFecha = explode("/", $infoSifin["fechaVencimiento"]);
                        $fechaVencimientoSifin = "{$tmpFecha["2"]}-{$tmpFecha["1"]}-{$tmpFecha["0"]}";                        
                        if($fechaVencimientoSifin === (string)$infoData["fechaVencimiento"]){
                            $infoData = $this->reemplazar($infoSifin, $infoData);
                            return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                        }else{
                            $saldoObligacionData = ((float)$infoData["saldoObligacion"]) / 1000;                            
                            if($saldoObligacionData == (float)$infoSifin["saldoObligacion"]){
                                $infoData = $this->reemplazar($infoSifin, $infoData);
                                return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                            }else{
                                $valorInicialData = ((float)$infoData["valorInicial"]) / 1000;                                
                                if($valorInicialData == (float)$infoSifin["valorInicial"]){
                                    $infoData = $this->reemplazar($infoSifin, $infoData);
                                    return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                                }else{
                                    $valorCuotaData = ((float)$infoData["valorCuota"]) / 1000;
                                    if($valorCuotaData == (float)$infoSifin["valorCuota"]){
                                        $infoData = $this->reemplazar($infoSifin, $infoData);
                                        return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                                    }else{
                                        $valorMoraData = ((float)$infoData["valorMora"]) / 1000;
                                        if($valorMoraData == (float)$infoSifin["valorMora"]){
                                            $infoData = $this->reemplazar($infoSifin, $infoData);
                                            return ["infoData" => $infoData, "obligacionEncontrada" => $infoSifin["numeroObligacion"]];
                                        }
                                    }
                                }
                            }
                        }
                    }                    
                }
        }
        return ["infoData" => $infoData, "obligacionEncontrada" => false];
    }
    
    function reemplazar($infoSifin, $infoData){        
        //se toma el nombre mas corto
        if(strlen($infoSifin["nombreEntidad"]) < strlen($infoData["nombreEntidad"]) || is_null($infoData["nombreEntidad"]) ){
            $infoData["nombreEntidad"] = $infoSifin["nombreEntidad"];
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["saldoObligacion"]) > (((float)$infoData["saldoObligacion"]) / 1000) || is_null($infoData["saldoObligacion"]) ){
            $infoData["saldoObligacion"] = ((float)$infoSifin["saldoObligacion"]) * 1000;
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["valorInicial"]) > (((float)$infoData["valorInicial"]) / 1000) || is_null($infoData["valorInicial"]) ){
            $infoData["valorInicial"] = ((float)$infoSifin["valorInicial"]) * 1000;
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["totalCuotas"]) > ((float)$infoData["totalCuotas"]) || is_null($infoData["totalCuotas"]) ){
            $infoData["totalCuotas"] = $infoSifin["totalCuotas"];
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["vsalorCuota"]) > (((float)$infoData["valorCuota"]) / 1000) || is_null($infoData["valorCuota"]) ){
            $infoData["valorCuota"] = ((float)$infoSifin["valorCuota"]) * 1000;
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["valorMora"]) > (((float)$infoData["valorMora"]) / 1000) || is_null($infoData["valorMora"]) ){
            $infoData["valorMora"] = ((float)$infoSifin["valorMora"]) * 1000;
        }
        //se toma el valor mayor
        if( ((float)$infoSifin["numeroCuotasMora"]) > ((float)$infoData["numeroCuotasMora"]) || is_null($infoData["numeroCuotasMora"])){
            $infoData["numeroCuotasMora"] = $infoSifin["numeroCuotasMora"];
        }
        // valores que probablemente lleguen vacios en data seran completados por Sifin
        if(is_null($infoData["lineaCredito"]) || empty($infoData["lineaCredito"])){
            $infoData["lineaCredito"] = $infoSifin["lineaCredito"];
        }
        $infoData["marca"] = "Reemplazado";
        return $infoData;
    }
    function conversorSifin($infoSifin){
        $tmpFecha = explode("/", $infoSifin["fechaApertura"]);
        $infoSifin["fechaApertura"] = "{$tmpFecha["2"]}-{$tmpFecha["1"]}-{$tmpFecha["0"]}";
        $tmpFechaV = explode("/", $infoSifin["fechaVencimiento"]);
        $infoSifin["fechaVencimiento"] = "{$tmpFechaV["2"]}-{$tmpFechaV["1"]}-{$tmpFechaV["0"]}";                        
        
        $infoSifin["saldoObligacion"] = ((float)$infoSifin["saldoObligacion"]) * 1000;
        $infoSifin["valorInicial"] = ((float)$infoSifin["valorInicial"]) * 1000;
        $infoSifin["valorCuota"] = ((float)$infoSifin["valorCuota"]) * 1000;
        $infoSifin["valorMora"] = ((float)$infoSifin["valorMora"]) * 1000;
        $infoSifin["marca"] = "Original Cifin";
        return $infoSifin;
        
    }
    function unionDataSifin($obligacionesData, $obligacionesSifin){        
        
        $obligacionesEncontradas = array();        
        $listaObligaciones = array();
        
        $listaObligaciones["infoUser"]= $obligacionesData["infoUser"];
        
        $listaObligaciones["infoData"]["score"] = $obligacionesData["score"];
        $listaObligaciones["infoData"]["HuellaConsulta"] = $obligacionesData["huellaConsulta"];
        $listaObligaciones["infoCifin"]["score"] = $obligacionesSifin["score"];
        $listaObligaciones["infoCifin"]["HuellaConsulta"] = $obligacionesSifin["huellaConsulta"];
        
        // Extraccion y union de todas la obligaciones en mora ------------------------------------------------------
        foreach ($obligacionesData["obligaciones"]["enMora"] as $infoData){            
            $response = $this->compararInfo($obligacionesSifin["obligaciones"]["enMora"], $infoData);            
            $listaObligaciones["obligaciones"]["enMora"][] = $response["infoData"];
            if($response["obligacionEncontrada"] !== false){
                $obligacionesEncontradas[] = $response["obligacionEncontrada"];
            }            
        }
        if(count($obligacionesEncontradas) < count($obligacionesSifin["obligaciones"]["enMora"])){
            foreach ($obligacionesSifin["obligaciones"]["enMora"] as $item){
                if(!in_array($item["numeroObligacion"], $obligacionesEncontradas)){
                    $listaObligaciones["obligaciones"]["enMora"][] = $this->conversorSifin($item);
                }
            }
        }
        
        unset($obligacionesEncontradas);
        // Extraccion y union de todas la obligaciones castigadas ------------------------------------------------------
        foreach ($obligacionesData["obligaciones"]["castigadas"]as $infoDataC){            
            $response = $this->compararInfo($obligacionesSifin["obligaciones"]["castigadas"], $infoDataC);            
            $listaObligaciones["obligaciones"]["castigadas"][] = $response["infoData"];
            if($response["obligacionEncontrada"] !== false){
                $obligacionesEncontradas[] = $response["obligacionEncontrada"];
            }            
        }
        
        if(count($obligacionesEncontradas) < count($obligacionesSifin["obligaciones"]["castigadas"])){
            foreach ($obligacionesSifin["obligaciones"]["castigadas"] as $item){
                if(!in_array($item["numeroObligacion"], $obligacionesEncontradas)){
                    $listaObligaciones["obligaciones"]["castigadas"][] = $this->conversorSifin($item);
                }
            }
        }
        
        unset($obligacionesEncontradas); 
        // Extraccion y union de todas la obligaciones al Dia ------------------------------------------------------
        foreach ($obligacionesData["obligaciones"]["alDia"]as $infoDataA){
            $response = $this->compararInfo($obligacionesSifin["obligaciones"]["alDia"], $infoDataA);            
            $listaObligaciones["obligaciones"]["alDia"][] = $response["infoData"];
            if($response["obligacionEncontrada"] !== false){
                $obligacionesEncontradas[] = $response["obligacionEncontrada"];
            }   
        }
        if(count($obligacionesEncontradas) < count($obligacionesSifin["obligaciones"]["alDia"])){
            foreach ($obligacionesSifin["obligaciones"]["alDia"] as $item){
                if(!in_array($item["numeroObligacion"], $obligacionesEncontradas)){
                    $listaObligaciones["obligaciones"]["alDia"][] = $this->conversorSifin($item);
                }
            }
        }
        
        return $listaObligaciones;

    }
    
    function getRespuestas(){
        return [
                        'idCuestionario' => '00011311',
                        'regCuestionario' => '4250859',
                        'Identificacion' => [
                                                'numero' => '1144176698',
                                                'tipo' => '1'
                                            ],
                        'Respuestas' => array(
                                            '03' => '01',
                                            '01' => '01',
                                            '03' => '01',
                                            '01' => '05'                                            
                                        )
                    ];
    }
    function checkEvidenteValidar($result){
        if($result["resultado"] != "01" && $result["resultado"] != "05"){            
            
            if($result["resultado"] == "06"){
                $errores = [];                    
                if($result["valApellido"] == "false"){
                    $errores[] = "El primer apellido ingresado es incorrecto";
                }
                if($result["valFechaExp"] == "false"){
                      $errores[] = "La fecha de expedicion ingresada no es correcta";
                }
                if($result["valNombre"] == "false"){
                      $errores[] = "El nombre ingresado es incorrecto";
                }
                if(count($errores) > 0){
                    $mensajeError = implode("\n", $errores);
                    $this->imprimir(false, $mensajeError);
                    die();
                }
            }
            
            if($result["resultado"] == "07"){
                $this->imprimir(false, "La identificaci&oacuten ingresada no cuenta con un historial crediticio, por tanto no es posible realizar la valoraci&oacute;n");
                die();
            }
            if($result["resultado"] == "08"){
                $this->imprimir(false, "El documento ingresado no esta vigente en el momento. Por favor vuelva a valorarse cuando este seguro de que el documento se encuentre vigente");
                die();
            }
            if($result["resultado"] == "09"){
                $this->imprimir(false, "Has superado el numero m&aacute;ximo de intentos permitidos.");
                die();
            }
            if($result["resultado"] == "10"){
                //mensaje real de la documentacion: Este Web Service no permite la utilizaci�n del producto evidente+, conectarse a idws2.
                $this->imprimir(false, "Ocurrio un problema en el proceso. por favor intentelo m&aacute;s tarde o comun&iacute;quese con servicio al cliente");
                die();
            }
            if($result["resultado"] == "11"){
                $this->imprimir(false, "No se tiene autorizaci&oacute;n para consultar la identificaci&oacute;n digitada. Por favor comuniquese con servicio al cliente.");
                die();
            }            
        }
    }
    function checkEvidentePreguntas($result){
        if($result["resultado"] != "01"){
            if($result["resultado"] == "00" || $result["resultado"] == "07"){
                $this->imprimir(false, "No hay suficientes preguntas para generar el cuestionario.");
                die();
            }
            if($result["resultado"] == "02"){
                $this->imprimir(false, "Ocurrio un error en el proceso. Por favor comuniquese con servicio al cliente");
                die();
            }
            if($result["resultado"] == "10"){
                $this->imprimir(false, "Ha superado la cantidad m&aacute;xima de intentos permitidos por d&iacute;a");
                die();
            }
            if($result["resultado"] == "11"){
                $this->imprimir(false, "Ha superado la cantidad m&aacute;xima de intentos permitidos por mes");
                die();
            }
            if($result["resultado"] == "12"){
                $this->imprimir(false, "Ha superado la cantidad m&aacute;xima de intentos permitidos por a&ntilde;o");                
                die();
            }
            if($result["resultado"] == "13"){
                $this->imprimir(false, "Excedi&oacute; el n�mero de ingresos permitidos para el producto por este d&aacute;a");
                die();
            }
            if($result["resultado"] == "14"){
                $this->imprimir(false, "Excedi&oacute; el n�mero de ingresos permitidos para el producto por este mes");
                die();
            }
            if($result["resultado"] == "15"){
                $this->imprimir(false, "Excedi&oacute; el n�mero de ingresos permitidos para el producto por este a&ntilde;o");
                die();
            }
            if($result["resultado"] == "17" || $result["resultado"] == "18" || $result["resultado"] == "19"){
                $this->imprimir(false, "Ocurrio un error en el proceso. Por favor comuniquese con servicio al cliente");
                $this->setMessageLog(__LINE__." Consulta no autorizada en el servicio de preguntas del webservice evidente");
                die();
            }            
        }
    }
    function evidenteController($identificacion, $primerApellido, $nombre, $fechaExpedicion){        
        $result = $this->validarCache($identificacion, "Evidente-Validar", $primerApellido, $nombre, $fechaExpedicion);        
        $this->checkEvidenteValidar($result);
        echo $regValidacion = (string) $result["regValidacion"];        
        $resultPreguntas = $this->validarCache($identificacion, "Evidente-Preguntas", $primerApellido, $nombre, $fechaExpedicion, $regValidacion);
        $this->checkEvidentePreguntas($resultPreguntas);        
        print_r($resultPreguntas);        
    }
    
    function imprimir($status, $mensaje){
        echo json_encode(["STATUS" => $status, "mensaje" => $mensaje]);
    }
    
    function setMessageLog($message){
        $nuevoarchivo = fopen("error_log_webservice.txt", "a");
        fwrite($nuevoarchivo, $message);
        fclose($nuevoarchivo);
    }
    
    
    
    /**************************************************** PRODUCCION ***********************************************************/
    function consularWsDataProduccion($identificacion, $primerApellido) {
        set_time_limit(0);
        
        $WSDL = 'http://172.24.14.7:8080/dhws3/services/DH2PNClientesService_v1-5?wsdl';

        try {
            $client = new SoapClient($WSDL, array("trace" => true));            
//            var_dump($client->__getFunctions());die;
            $TRAMA = "<?xml version='1.0' encoding='UTF-8' ?>
            <Solicitudes>
                <Solicitud clave='19PPY' identificacion='$identificacion' primerApellido='$primerApellido' producto='64' tipoIdentificacion='1' usuario='1144176698' />
            </Solicitudes>";
            
            $result = $client->__soapCall("consultarHC2", array("xmlConsulta" => $TRAMA));            
            return $result;
            
        } catch (Exception $exc) {
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservice de datacredito. mensaje: ".$exc->getMessage());
            die();
        }       
    }
    function consultaWsSifinProduccion($identificacion) {
        
        set_time_limit(0);

        $certificado = '/root/CERT-2017/private_key.pem';
        $clave = 'vpvNjyAc742zpvTdJ';

        $WSDL = 'http://cifin.asobancaria.com/InformacionComercialWS/services/InformacionComercial?wsdl';
//        $WSDL = 'http://cifinpruebas.asobancaria.com/ws/UbicaPlusWebService/services/UbicaPlus?wsdl';
        $SOAP_OPTS = array(
            'login' => '556605',
            'password' => 'D3vTm!',
            'local_cert' => $certificado,
            'passphrase' => $clave,
            'trace' => true);
        
        try {
            
            $client = new SoapClient($WSDL, $SOAP_OPTS);
    //        $client->__setLocation('http://cifinpruebas.asobancaria.com/ws/UbicaPlusWebService/services/UbicaPlusseg');
            $client->__setLocation('http://cifin.asobancaria.com/InformacionComercialWS/services/InformacionComercialseg');
            
            $parametros = [
                "parametrosConsulta" => [
                    "codigoInformacion" => "2042", //154 [Informacion comercial] - 2042 [Informacion comercial + score]
    //                "codigoInformacion" => "5632",
                    "motivoConsulta" => "23",
                    "numeroIdentificacion" => $identificacion,
                    "tipoIdentificacion" => "1"
                ]
            ];
        
            $result = $client->__soapCall("consultaXml", $parametros);
//            $result = $client->__soapCall("consultaUbicaPlus", $parametros);            
            return $result;
            
        } catch (SoapFault $exception) {
            $this->imprimir(false, "Ocurrio un problema de conexi&oacute;n, Por favor vuelva a intentarlo y si el problema persiste intentelo nuevamente en un rato.");
            $this->setMessageLog(__LINE__.": Error al tratar de conectarse al webservide se sifin. Mensaje: ".$exception->getMessage());
            die;
        }        
    }
}