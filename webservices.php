<?php

$objFunciones = new funcionesWebservices(); 
$objFunciones->pruebaReemplazar();die;
//$objFunciones->pruebaData();
//$objFunciones->pruebaConsultaSifin();
//$objFunciones->consultaCliente("66677347", "PINZON");

//$objFunciones->consularWsDataJuridico();
//$objFunciones->consularWsEvidente();

$xml = $objFunciones->consultaWsSifin("66677347");
$data = $objFunciones->replaceElementsSifin(simplexml_load_string($xml));
//echo '<h1>Cifin</h1><pre>';
print_r(simplexml_load_string($xml));
//print_r(($data));
die;



$xmlData = $objFunciones->consularWs("66677347","HC2", "ALARCON");
//echo '<pre>';
$dataData = $objFunciones->replaceElements(simplexml_load_string($xmlData));
//echo '<h1>Data</h1><pre>';
//print_r(simplexml_load_string($xml));
//print_r($data);

$objFunciones->unionDataSifin($dataData, $data);
die;


//print_r($xml->Tercero->SectorFinancieroAlDia);  

//$objFunciones->replaceElementsSifin($xml);

class funcionesWebservices {

    function pruebaConsultaSifin(){
        $cedulas = [
            "1038798653",
            "1039455467",
            "1039456093",
            "1039691242",
            "1040036874",
            "1040040064",
            "1040322217",
            "1040324499",
            "1040495866",
            "1040496713",
            "1040735087",
            "1040735095",
            "1042064258",
            "1042431748",
            "1042769420",
            "1043003442",
            "1044120221",
            "1044121351"
        ];        
        foreach ($cedulas as $identificacion){
            echo '<h1 style="text-align: center">Consulta SIFIN Para la cedula '.$identificacion.'</h1>';
            echo '<pre>';
//            $xml = $this->consultaWsSifin($identificacion);            
            $xml = $this->validarCache($identificacion,"Sifin-InfoComercial");            
            $data = $this->replaceElementsSifin($xml);
            print_r($data);
            echo '</pre><br><br>';
        }

    }
    
    function pruebaData(){
        $identificacion = "66677347";
        echo '<h1 style="text-align: center">Consulta DATA Para la cedula '.$identificacion.'</h1>';
        echo '<pre>';
        $xml = $this->validarCache($identificacion, "HC2");
        $data = $this->replaceElements($xml);
        print_r($data);
        echo '</pre><br><br>';
        
    }
    
    
    function consultaCliente($identificacion,$apellido){
        $xmlData = $this->validarCache($identificacion, "HC2",$apellido);
        $arrayData = $this->replaceElements($xmlData);
        
        $xmlSifin = $this->validarCache("1040496713", "Sifin-InfoComercial",$apellido);
        $arraySifin = $this->replaceElementsSifin($xmlSifin);
        
        $this->unionDataSifin($arrayData, $arraySifin);
    }
    var $carpetaPrincipal = "archivosXML";

    function crearArchivo($identificacion, $respuesta, $nombreWS) {
        try {
            $mediaXML = (file_exists("$this->carpetaPrincipal/")) ? true : mkdir("$this->carpetaPrincipal/", 0755);
            $carpetaCliente = (file_exists("$this->carpetaPrincipal/$identificacion/")) ? true : mkdir("$this->carpetaPrincipal/$identificacion/", 0755);

            if ($mediaXML && $carpetaCliente) {
                $nuevoarchivo = fopen("$this->carpetaPrincipal/$identificacion/$nombreWS.fff", "w+");
                fwrite($nuevoarchivo, base64_encode($respuesta));
                fclose($nuevoarchivo);
            } else {
                echo 'Ocurrio un problema al tratar de crear la carpeta del cliente. ';
                die;
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            die;
        }
        return true;
    }

    function validarCache($identificacion, $webservice, $primerApellido) {
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
                    $result = $this->consultaWs($identificacion, $webservice, $primerApellido);
                    $this->crearArchivo($identificacion, $result, $webservice);
                    $contenido = simplexml_load_string($result);
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
                    return $contenido;
                }else{
                    $result = $this->consultaWsSifin($identificacion, $webservice);
                    $this->crearArchivo($identificacion, $result, $webservice);
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
        $client = new SoapClient($WSDL, $SOAP_OPTS);
//        $client->__setLocation('http://cifinpruebas.asobancaria.com/ws/UbicaPlusWebService/services/UbicaPlusseg');
        $client->__setLocation('http://cifinpruebas.asobancaria.com/InformacionComercialWS/services/InformacionComercialseg');
        $parametros = [
            "parametrosConsulta" => [
                "codigoInformacion" => "154",
//                "codigoInformacion" => "5632",
                "motivoConsulta" => "23",
                "numeroIdentificacion" => $identificacion,
                "tipoIdentificacion" => "1"
            ]
        ];


        try {
            $result = $client->__soapCall("consultaXml", $parametros);
//            $result = $client->__soapCall("consultaUbicaPlus", $parametros);
            
        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception->getMessage());
            die("ERROR");
        }
        return $result;
    }

    function consularWs($identificacion, $nombreWS, $primerApellido) {

        set_time_limit(0);

        $WSDL = 'http://172.24.14.29:8080/dhws3/services/DH2PNClientesService_v1-5?wsdl';

        $client = new SoapClient($WSDL, array("trace" => true));

        $TRAMA = "<?xml version='1.0' encoding='UTF-8' ?>
        <Solicitudes>
            <Solicitud clave='84MJW' identificacion='$identificacion' primerApellido='$primerApellido' producto='64' tipoIdentificacion='1' usuario='1144176698' />
        </Solicitudes>";

        try {
            $result = $client->__soapCall("consultarHC2", array("xmlConsulta" => $TRAMA));
//            $this->crearArchivo($identificacion, $result, $nombreWS);
            /* Convierte el string que retorna soap a un objeto
              $xmlp = simplexml_load_string($result);
             */
            return $result;
        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception->getMessage());
            die("ERROR");
        }
    }
    function consularWsDataJuridico() {

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
            "nroDocConsultado" => "39025870",
            "Pagina" => "1",
            "Clave" => "60KUT"
        ];
        
        try {            
            $result = $client->__soapCall("buscarPaginaPN", $envio);
//            $this->crearArchivo($identificacion, $result, $nombreWS);
            /* Convierte el string que retorna soap a un objeto
              $xmlp = simplexml_load_string($result);
             */
            echo '<pre>';
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";  
            echo $result;
            
        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception->getMessage());
            die("ERROR");
        }
    }

    function consularWsEvidente() {

        set_time_limit(0);

        $WSDL = 'http://172.24.14.29:8080/idws2/services/ServicioIdentificacion?wsdl';
        
        $client = new SoapClient($WSDL, array(
            "trace" => true            
//            'usuario' => base64_encode("1144176698")
        ));
//        print_r($client->__getFunctions());die;
        
        $auth = array(
             'usuario' => base64_encode("1144176698")
        );

        //1144176698, 900661159
//        $header = new SoapHeader('http://www.datacredito.com.co/services/ServicioIdentificacion"', 'usuario', "MTE0NDE3NjY5ODo=");
//        $header = new SoapHeader('http://200.74.146.84/services/ServicioIdentificacion', 'Authorization', base64_encode("1144176698"));
//        $client->__setSoapHeaders($header);

        $datosValidacion = '<?xml version="1.0" encoding="UTF-8"?>
                            <DatosValidacion> 
                                <Identificacion numero="32715969" tipo="1" /> 
                                <PrimerApellido>martinez</PrimerApellido> 
                                <Nombres>monica</Nombres> 
                                <FechaExpedicion timestamp="503017122714" />
                            </DatosValidacion>';

        /* $datosValidacion = '<![CDATA[<?xml version="1.0" encoding="UTF-8"?><datosValidacion><Identificacion numero="32715969" tipo="1" /><PrimerApellido>martinez</PrimerApellido><SegundoApellido>martinez</SegundoApellido><Nombres>monica</Nombres><FechaExpedicion timestamp="503017122714" /></datosValidacion>]]>'; */

        $xml = new SimpleXMLElement($datosValidacion);

        $TRAMA = [
            "paramProducto" => "2637",
            "producto" => "007",
            "canal" => "001",
            "datosValidacion" => $datosValidacion
        ];

        try {
//            $result = $client->__soapCall("validar", $TRAMA);
            $result = $client->__soapCall("consultarParametrizacion",array(
                "producto" => "",
                "consecutivo" => "",
                "nit" => ""                
            ));
            print_r($result);
//            $this->crearArchivo($identificacion ,$result, $nombreWS);
            /* Convierte el string que retorna soap a un objeto
              $xmlp = simplexml_load_string($result);
             */
        } catch (SoapFault $exception) {
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
            var_dump($exception);
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
         * Logica para recorrer las cuentas cartera
         */
        if ((is_array($object->Informe->CuentaCartera) && count($object->Informe->CuentaCartera) > 0)
            || is_object($object->Informe->CuentaCartera)) {
            
            foreach ($object->Informe->CuentaCartera as $cuentaCartera) {            
                
                $credito = [
                    "nombreEntidad" => $cuentaCartera["entidad"],
                    "numeroObligacion" => $cuentaCartera["numero"],
                    "fechaApertura" => $cuentaCartera["fechaApertura"],
                    "fechaVencimiento" => $cuentaCartera["fechaVencimiento"],
                    "lineaCredito" => $cuentaCartera->Caracteristicas["tipoCuenta"],
                    "saldoObligacion" => $cuentaCartera->Valores->Valor["saldoActual"],
                    "valorInicial" => $cuentaCartera->Valores->Valor["valorInicial"],
                    "totalCuotas" => $cuentaCartera->Valores->Valor["totalCuotas"],
                    "valorCuota" => $cuentaCartera->Valores->Valor["cuota"],
                    "valorMora" => $cuentaCartera->Valores->Valor["saldoMora"],
                    "numeroCuotasMora" => $cuentaCartera->Valores->Valor["cuotasMora"],
                    "calidadDeudor" => $equivalencias->tabla6->{$cuentaCartera->Caracteristicas["calidadDeudor"]}
                ];                

                $estadoCuentaCod = $cuentaCartera->Estados->EstadoCuenta["codigo"];
                $estadoPagoCod = $cuentaCartera->Estados->EstadoPago["codigo"];
                $formaPago = $cuentaCartera["formaPago"];

                if ($estadoCuentaCod == 10 || ($estadoPagoCod == 43 && $formaPago == 3) || ($estadoPagoCod == 43 && $formaPago != 3)) {
                    continue;
                } elseif ($estadoPagoCod == 45 || $estadoPagoCod == 47) {
                    //castigadas  
                    $credito["estadoCuenta"] = "Castigada";
                    $data["obligaciones"]["castigadas"][] = $credito;
                } elseif ($estadoPagoCod >= 13 && $estadoPagoCod <= 41) {
                    //mora
                    $credito["estadoCuenta"] = "En mora";
                    $data["obligaciones"]["enMora"][] = $credito;
                } elseif ($estadoPagoCod == 1) {
                    //Al dia
                    $credito["estadoCuenta"] = "Al dia";
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
                    "nombreEntidad" => $tarjetaCredito["entidad"],
                    "numeroObligacion" => $tarjetaCredito["numero"],
                    "fechaApertura" => $tarjetaCredito["fechaApertura"],
                    "fechaVencimiento" => $tarjetaCredito["fechaVencimiento"],
                    "lineaCredito" => $tarjetaCredito->Caracteristicas["tipoCuenta"],
                    "saldoObligacion" => $tarjetaCredito->Valores->Valor["saldoActual"],
                    "valorInicial" => $tarjetaCredito->Valores->Valor["valorInicial"],
                    "totalCuotas" => $tarjetaCredito->Valores->Valor["totalCuotas"],
                    "valorCuota" => $tarjetaCredito->Valores->Valor["cuota"],
                    "valorMora" => $tarjetaCredito->Valores->Valor["saldoMora"],
                    "numeroCuotasMora" => $tarjetaCredito->Valores->Valor["cuotasMora"],
                    "calidadDeudor" => ($tarjetaCredito->Caracteristicas["amparada"] == "true") ? "Amparada" : "Principal"
                ];

                $estadoCuentaCod = $tarjetaCredito->Estados->EstadoCuenta["codigo"];
                $estadoPagoCod = $tarjetaCredito->Estados->EstadoPago["codigo"];
                $formaPago = $tarjetaCredito["formaPago"];

                if ($estadoCuentaCod == 10 || ($estadoPagoCod == 43 && $formaPago == 3) || ($estadoPagoCod == 43 && $formaPago != 3)) {
                    continue;
                } elseif ($estadoPagoCod == 45 || $estadoPagoCod == 47) {
                    //castigadas  
                    $credito["estadoCuenta"] = "Castigada";
                    $data["obligaciones"]["castigadas"][] = $credito;
                } elseif ($estadoPagoCod >= 13 && $estadoPagoCod <= 41) {
                    //mora
                    $credito["estadoCuenta"] = "En mora";
                    $data["obligaciones"]["enMora"][] = $credito;
                } elseif ($estadoPagoCod == 1) {
                    //al dia
                    $credito["estadoCuenta"] = "Al dia";
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
                "nombreEntidad" => $item->NombreEntidad,
                "numeroObligacion" => $item->NumeroObligacion,
                "fechaApertura" => $item->FechaApertura,
                "fechaVencimiento" => $item->FechaCorte,
                "lineaCredito" => (isset($item->LineaCredito) && !empty($item->LineaCredito))? $equivalencias->tabla5->$sector->{$item->LineaCredito} : "",
                "saldoObligacion" => $item->SaldoObligacion,
                "valorInicial" => $item->ValorInicial,
                "totalCuotas" => $item->NumeroCuotasPactadas,
                "valorCuota" => $item->ValorCuota,
                "valorMora" => $item->ValorMora,
                "numeroCuotasMora" => $item->NumeroCuotasMora,
                "calidadDeudor" => (isset($item->Calidad) && !empty($item->Calidad))? $equivalencias->tabla2->{$item->Calidad} : ""
            ];        
        } 
        return $array;
    }
    function replaceElementsSifin($xml){               
        
        $equivalencias = json_decode(utf8_encode($this->getEquivalenciasSifin()));        
        if(!isset($xml->Tercero->SectorRealAlDia->Obligacion) && !isset($xml->Tercero->SectorFinancieroAlDia->Obligacion)){
            return $xml->Tercero->Mensajes;
        }
        $data = array(); 
        /*
         * Inicio obligaciones al dia
         */
        $aldiaFinanciero = $this->getArrayCifin($xml->Tercero->SectorFinancieroAlDia->Obligacion, $equivalencias, "sectorFinanciero");
        $aldiaReal = $this->getArrayCifin($xml->Tercero->SectorRealAlDia->Obligacion, $equivalencias, "sectorReal");
        $data["obligaciones"]["alDia"]  = array_merge($aldiaFinanciero, $aldiaReal);
        /*
         * Fin obligaciones al dia
         * Inicio Obligaciones en mora y castigadas
         */        
        foreach ($xml->Tercero->SectorFinancieroEnMora->Obligacion as $sectorFinanciero){
            $tempData = [
                "nombreEntidad" => $sectorFinanciero->NombreEntidad,
                "numeroObligacion" => $sectorFinanciero->NumeroObligacion,
                "fechaApertura" => $sectorFinanciero->FechaApertura,
                "fechaVencimiento" => $sectorFinanciero->FechaCorte,
                "lineaCredito" => (isset($sectorFinanciero->LineaCredito) && !empty($sectorFinanciero->LineaCredito))? $equivalencias->tabla5->sectorFinanciero->{$sectorFinanciero->LineaCredito} : "",
                "saldoObligacion" => $sectorFinanciero->SaldoObligacion,
                "valorInicial" => $sectorFinanciero->ValorInicial,
                "totalCuotas" => $sectorFinanciero->NumeroCuotasPactadas,
                "valorCuota" => $sectorFinanciero->ValorCuota,
                "valorMora" => $sectorFinanciero->ValorMora,
                "numeroCuotasMora" => $sectorFinanciero->NumeroCuotasMora,
                "calidadDeudor" => (isset($sectorFinanciero->Calidad) && !empty($sectorFinanciero->Calidad))? $equivalencias->tabla2->{$sectorFinanciero->Calidad} : ""                
            ];
            if($sectorFinanciero->EstadoObligacion == "CAST"){
                $data["obligaciones"]["castigadas"][] = $tempData;
            }else{
                $data["obligaciones"]["enMora"][] = $tempData;
            }
            
        }        
        foreach ($xml->Tercero->SectorRealEnMora->Obligacion as $sectorReal){
            $tempData = [
                "nombreEntidad" => $sectorReal->NombreEntidad,
                "numeroObligacion" => $sectorReal->NumeroObligacion,
                "fechaVencimiento" => $sectorReal->FechaApertura,
                "fechaCorte" => $sectorReal->FechaCorte,
                "lineaCredito" => (isset($sectorReal->LineaCredito) && !empty($sectorReal->LineaCredito))? $equivalencias->tabla5->sectorReal->{$sectorReal->LineaCredito} : "",
                "saldoObligacion" => $sectorReal->SaldoObligacion,
                "valorInicial" => $sectorReal->ValorInicial,
                "totalCuotas" => $sectorReal->NumeroCuotasPactadas,
                "valorCuota" => $sectorReal->ValorCuota,
                "valorMora" => $sectorReal->ValorMora,
                "numeroCuotasMora" => $sectorReal->NumeroCuotasMora,
                "calidadDeudor" => (isset($sectorReal->Calidad) && !empty($sectorReal->Calidad))? $equivalencias->tabla2->{$sectorReal->Calidad} : ""
            ];
            
            if($sectorReal->EstadoObligacion == "CAST"){
                $data["obligaciones"]["castigadas"][] = $tempData;
            }else{
                $data["obligaciones"]["enMora"][] = $tempData;
            }
        }
        /*
         * Fin obligaciones en mora
         */
        return $data;
        
    }
    
    function pruebaReemplazar(){
        $Sifin["obligaciones"]["enMora"][]= [
                "nombreEntidad" => "COPROCENVA - COOPERATIVA DE AH",
                "numeroObligacion" => "004500",
                "fechaApertura" => "23/11/2011",
                "fechaVencimiento" => "33/11/2019",
                "lineaCredito" => "Libranza",
                "saldoObligacion" => "l28179",
                "valorInicial" => "j51000",
                "totalCuotas" => "96",
                "valorCuota" => "p960",
                "valorMora" => "3378",
                "numeroCuotasMora" => "3",
                "calidadDeudor" => "Principal"
            ];
        $data["obligaciones"]["enMora"][]= [
                "nombreEntidad" => "COPROCENVA     LIBRANZA",
                "numeroObligacion" => "002050045",
                "fechaApertura" => "2011-11-22",
                "fechaVencimiento" => "2019-11-30",
                "lineaCredito" => "CAC",
                "saldoObligacion" => "28179000.0",
                "valorInicial" => "51000000.0",
                "totalCuotas" => "96",
                "valorCuota" => "960000.0",
                "valorMora" => "485000.0",
                "numeroCuotasMora" => "1",
                "calidadDeudor" => "Deudor Principal"
            ];
        $this->unionDataSifin($data, $Sifin);
    }
    
    function reemplazar($dataSifin, $infoData){
            
        $infoData["numeroObligacion"] = str_replace(".", "", $infoData["numeroObligacion"]);
        $numeroCuenta = substr($infoData["numeroObligacion"], (strlen($infoData["numeroObligacion"])-4), (strlen($infoData["numeroObligacion"])-1));
        
        $array = array();
        
        foreach ($dataSifin as $infoSifin){
                if(strpos($infoSifin["numeroObligacion"], $numeroCuenta) !== false){
                    $tmpFecha = explode("/", $infoSifin["fechaApertura"]);
                    $fechaAperturaSifin = "{$tmpFecha["2"]}-${tmpFecha["1"]}-{$tmpFecha["0"]}";
                    
                    if($fechaAperturaSifin === $infoData["fechaApertura"]){
                        echo "es el mismo y a reemplazar 1";
                    }else{
                        unset($tmpFecha);
                        $tmpFecha = explode("/", $infoSifin["fechaVencimiento"]);
                        $fechaVencimientoSifin = "{$tmpFecha["2"]}-${tmpFecha["1"]}-{$tmpFecha["0"]}";
                        if($fechaVencimientoSifin === $infoData["fechaVencimiento"]){
                            echo "es el mismo y a reemplazar 2";
                        }else{
                            $saldoObligacionData = ((float)$infoData["saldoObligacion"]) / 1000;
                            if($saldoObligacionData == $infoSifin["saldoObligacion"]){
                                echo "es el mismo y a reemplazar 3";
                            }else{
                                $valorInicialData = ((float)$infoData["valorInicial"]) / 1000;
                                if($valorInicialData == $infoSifin["valorInicial"]){
                                    echo "es el mismo y a reemplazar 4";
                                }else{
                                    $valorCuotaData = ((float)$infoData["valorCuota"]) / 1000;
                                    if($valorCuotaData == $infoSifin["valorCuota"]){
                                        echo "es el mismo y a reemplazar 5";
                                    }else{
                                        $valorMoraData = ((float)$infoData["valorMora"]) / 1000;
                                        if($valorMoraData == $infoSifin["valorMora"]){
                                            echo "es el mismo y a reemplazar 6";
                                        }else{
                                            echo "Es diferente y debe adicionarse como nuevo";
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    
                }else{
                    var_dump($infoSifin["numeroObligacion"], $numeroCuenta, strpos($infoSifin["numeroObligacion"], $numeroCuenta));
                    echo 'No entro';
                }
        }        
        return $array;
    }
    function unionDataSifin($obligacionesData, $obligacionesSifin){        
        /*
         * Proceso unificacion para las obligaciones alDia
         */        
        $array = array();        
        
        foreach ($obligacionesData["obligaciones"]["enMora"] as $infoData){            
            $this->reemplazar($obligacionesSifin["obligaciones"]["enMora"], $infoData);            
        }       
        die();
        foreach ($infoData["obligaciones"]["castigadas"]as $data){
            $data["numeroCuenta"] = str_replace(".", "", $data["numeroCuenta"]);
            $numeroCuenta = substr($infoData["numeroCuenta"], (strlen($infoData["numeroCuenta"])-5), (strlen($infoData["numeroCuenta"])-1));
            $array["obligaciones"]["castigadas"] = $this->reemplazar($obligacionesSifin["obligaciones"]["castigadas"], $infoData, $numeroCuenta);                        
        }
        foreach ($infoData["obligaciones"]["enMora"]as $data){
            $data["numeroCuenta"] = str_replace(".", "", $data["numeroCuenta"]);
            $numeroCuenta = substr($infoData["numeroCuenta"], (strlen($infoData["numeroCuenta"])-5), (strlen($infoData["numeroCuenta"])-1));
            $array["obligaciones"]["enMora"] = $this->reemplazar($obligacionesSifin["obligaciones"]["enMora"], $infoData, $numeroCuenta);                        
        }

    }
}