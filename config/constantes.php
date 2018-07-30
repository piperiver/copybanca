<?php

return [
    /*
     * Estados Gestion Obligaciones
     */
    "GO_SOLICITADA" => "SOL",
    "GO_RADICADA" => "RAD",
    "GO_VENCIDA" => "VEN",
    "GO_CANCELADA" => "CAN",
    "GO_PAGADA" => "PAG",
    
    /*
     * Estados Estudio
     */
    "ESTUDIO_SAVE" => "SAV", // Estado para el estudio que este en radicado pero que solamente se haya guardado y aun no se ha enviado ha aprobacion
    "ESTUDIO_RADICADO" => "RAD",
    "ESTUDIO_INGRESADO" => "ING",
    "ESTUDIO_NOVIABLE" => "NVI",
    "ESTUDIO_VIABLE" => "VIA",
    "ESTUDIO_FIRMADO" => "FIR",
    "ESTUDIO_VISADO" => "VIS",
    "ESTUDIO_TESORERIA" => "TES",
    "ESTUDIO_PROCESO_TESORERIA" => "PRT", // Estado que indica que el estudio esta en proceso de tesoreria, es decir que ya se desembolso algo
    "ESTUDIO_CARTERA" => "CAR",
    "ESTUDIO_BANCO" => "BAN", //Estado bancos, es cuando el credito se ha pagado completamente por el banco
    "ESTUDIO_NEGADO" => "NEG",
    "ESTUDIO_APROBADO" => "APR", //Cuando el estudio es aprobado manualmente
    "ESTUDIO_PENDIENTE" => "PEN", //Cuando el estudio es pasado a pendiente manualmente
    "ESTUDIO_PRE_APROBADO" => "PRE", //Cuando el estudio es pasado a pre aprobado manualmente
    "ESTUDIO_COMITE" => "COM",
    "ESTUDIO_DESISTIO" => "DES",

    
    
    /*
     * TABLA ADJUNTOS
     */
    "KEY_ESTUDIO" => "adjuntosEstudio",
    "KEY_SOLICITUD" => "SolicitudConsulta",
    "KEY_OBLIGACION" => "obligaciones",
    "KEY_GIROS" => "girosCliente",
    "KEY_AUTORIZACION" => "autorizacionValoracion",
    "KEY_GENERAL" => "adjuntosGenerales",
    
    /*
     * TIPOS DE ADJUNTOS
     */
    "CERTIFICADO_LABORAL" => "CRL",
    "DESPRENDIBLE" => "DDN",
    "AUTORIZACION_DE_CONSULTA" => "AUT",
    "FORMATO_BANCO_SOLICITUD" => "FBA",
    "CEDULA_DE_CIUDADANIA" => "DID",
    "LIBRANZA_FIRMADA" => "LBZ",
    "SOLICITUD_VISADO" => "SVI",
    "VISADO" => "VIS",
    "INGRESOS_ADICIONALES" => "IAD",
    "SOPORTE_PAGO" => "SPA", 
    "SOPORTE_PAGO_CLIENTE" => "SPC", //Soporte de pago cliente. Cuando se pagan giros a los clientes
    
    "CERTIFICACIONES_DEUDA" => "CDD", 
    "SOL_CERTIFICACIONES_DEUDA" => "SCD",
    "AUT_CERTIFICACIONES_DEUDA" => "ACD", 
    
    "PAZ_SALVO" => "PYS",
    "SOL_PAZ_SALVO_CARTERA" => "SPT",
    "RAD_PAZ_SALVO_CARTERA" => "RPC",
    "PAZ_SALVO_CARTERA" => "PSC",
    "SOL_PAZ_SALVO" => "SPS",
    "SOPORTES_ADICIONALES" => "SAD", 
    "FOTO_PERFIL" => "FPE",
    "CERTIFICACION_VTM" => "CDV",
    "DESPRENDIBLECUOTA_VTM" => "DCV",
    "VISADO_BANCO" => "VSB",
    "LIBRANZA_BANCO" => "LZB",
    "SOPORTE_RECAUDO" => "SRE", //Soporte que se carga cuando se agrega un pago del recaudo del cliente
    "CARGUE_MASIVO_LIDER" => "CML",
    "PAGO_CERTIFICACIONDEUDA" => "PCD", //Soporte de pago de la certificacion expedida por la empresa
    "SEGURO_DE_VIDA" => "SGV", //SEGURO DE VIDA
    
    
    /*
     * MODULOS
     */
    "MDL_VALORACION" => "VALO",
    
    /*
        PERFILES
    */
    "ID_PERFIL_CLIENTE" => "CLI", //codigo del perfil que se asigna a las personas que se registran por primera vez en el portal
    "PERFIL_COMERCIAL" => "COM", //codigo del perfil que se asigna a algunos Usuarios del Sistema.    
    "PERFIL_LIDER_COMERCIAL" => "LID", //codigo del perfil que se asigna a algunos Usuarios del Sistema.
    "PERFIL_OFICINA" => "OFI", //codigo del perfil que se asigna a algunos Usuarios del Sistema.
    "PERFIL_ADMIN" => "ADM", //codigo del perfil que se asigna a algunos Usuarios del Sistema.
    "PERFIL_ROOT" => "ROT", //codigo del perfil que se asigna a algunos Usuarios del Sistema.
    "PERFIL_COORDINADOR" => "COOR", //codigo del perfil que se asigna a algunos Usuarios del Sistema.

    /*
        ESTADOS
    */
    "ACTIVO" => "act", //Codigo de estado de usuario "Activo"
    "NO_VALORADO" => "nov", //Codigo de estado de usuario "No Valorado"
    "INACTIVO" => "ina", //Codigo de estado de usuario "Inactivo"


    /*
        RUTAS
    */
    //"RUTA" => "http://localhost/VTM/vtmplatform/public/", //direccionamiento estatico del menu - http://localhost/VTM/vtmplatform/public/
    "RUTA" => "http://localhost:8888/bancarizate/public/", //direccionamiento estatico del menu - http://localhost/VTM/vtmplatform/public/
    //"RUTA" => "http://192.168.0.10/VTM/vtmplatform/public/", 

    "URL_REPORTE_CENTRAL" => "http://vtmsoluciones.com/webservices/lectura.php?", //direccionamiento de consumo de web service
    "URL_VALORACION" => "http://vtmsoluciones.com/webservices/data_consultarHC2.php?", //direccionamiento de consumo de web service
    "URL_EVIDENTE" => "http://vtmsoluciones.com/webservices/data_consultarHC2.php?evidente=true", //direccionamiento de consumo de web service
    "URL_DATAJURIDICO" => "http://vtmsoluciones.com/webservices/data_consultarHC2.php?juridico=true", //direccionamiento de consumo de web service

    /************************ Inicio Constantes para las valoraciones ************************/
    "VALORACION_TITLE" => "VALORACIÓN",
    "VALORACION_TITLE_PUNTAJE" => "PARA APROBACIÓN DE CRÉDITO",
    "VALORACION_DATA" => "DATACRÉDITO",
    "VALORACION_TUNION" => "TRANSUNION",
    "VALORACION_RANGE_BAD" => "0 - 399 (Poco Probable)",
    "VALORACION_RANGE_MEDIUM" => "400 - 599 (Probable)",
    "VALORACION_RANGE_GOOD" => "600 - 1000 (Muy Probable)",
    "VALORACION_DATA_HIST" => "DATOS HISTÓRICOS",
    "VALORACION_DATA_HIST_CENTER" => "CONSULTA CENTRALES",
    "VALORACION_DATA_HIST_JURI" => "PROCESOS JURÍDICOS",
    "VALORACION_OBL_TITLE" => "RESUMEN DE LAS OBLIGACIONES",
    "VALORACION_OBL_CAST" => "CASTIGADAS",
    "VALORACION_OBL_MORA" => "EN MORA",
    "VALORACION_OBL_DIA" => "AL DÍA",
    "VALORACION_PLAN_ACCION" => "PLAN DE ACCIÓN",
    "VALORACION_PLAN_ACCION_DESC" => "Aquí encontrarás un plan para administrar y pagar tus obligaciones, transformando tu reporte en un buen ",
    "VALORACION_PUNTAJE" => "PUNTAJE",
    "VALORACION_HISTORICOS" => "HISTÓRICOS DATACRÉDITO",
    "VALORACION_TBL_HISTORICOS_FECHA" => "Fecha",
    "VALORACION_TBL_HISTORICOS_NOMBRE" => "Nombre",
    "VALORACION_HISTORICOS_TRANSUNION" => "HISTÓRICOS TRANSUNION",
    "VALORACION_CLOSE" => "Cerrar",
    "VALORACION_MODAL_CASTIGADAS_ENTIDAD" => "Entidad",
    "VALORACION_MODAL_CASTIGADAS_VLR" => "Valor Mora",
    "VALORACION_MODAL_CASTIGADAS_TITU" => "Tipo Titularidad",
    "VALORACION_MODAL_CASTIGADAS_OBLIGA" => "Tipo Obligación",
    "VALORACION_MODAL_MORA_ENTIDAD" => "Entidad",
    "VALORACION_MODAL_MORA_SALDO" => "Saldo Obligación",
    "VALORACION_MODAL_NUMERO_OBLIGACION" => "Numero Obligación",
    "VALORACION_MODAL_MORA_TITU" => "Tipo Titularidad",
    "VALORACION_MODAL_MORA_OBL" => "Tipo Obligación",
    "VALORACION_MODAL_PUNTAJE_NPAGAS1" => "El motivo más frecuente para explicar un bajo puntaje en las centrales de información financiera y la no aprobación de una solicitud de crédito es el no pago de sus obligaciones financieras.",
    "VALORACION_MODAL_PUNTAJE_NPAGAS2" => "Las obligaciones financieras no pagas se clasifican en dos grupos, obligaciones castigadas y obligaciones en mora, con éstas se debe actuar de la siguiente manera:",
    "VALORACION_MODAL_PUNTAJE_C1" => "Debe comunicarse o acercarse a una oficina de su acreedor y confirmar el valor actual de la obligación.",
    "VALORACION_MODAL_PUNTAJE_C2" => "Es posible que el valor actual de su obligación sea sujeto de descuentos y beneficios si ofrece su pago inmediato y completo, incluso pueden llegar hasta un 40% de su valor.",
    "VALORACION_MODAL_PUNTAJE_C3" => "Sus acreedores después de confirmar e incluso de negociar el valor de la obligación, le entregan una certificación de deuda con lo que se puede realizar el pago.",
    "VALORACION_MODAL_PUNTAJE_C4" => "Una vez realizados los pagos, es necesario confirmar la efectividad de ellos solicitando a cada uno de los acreedores un paz y salvo por cada obligación pagada.",
    "VALORACION_MODAL_PUNTAJE_PAGAS_DES" => "Para éste tipo de obligaciones es importante tener en cuenta que si se trata de obligaciones con saldos variables, como los cupos rotativos y las tarjetas de crédito, se recomienda no mantener sus saldos al limite, es decir sin cupo, lo mas recomendable es usarlas siempre hasta un 80% del valor aprobado.",
    "VALORACION_MODAL_PUNTAJE_REC_HUE" => "Es importante tener un control de las entidades que consultan y verifican su información financiera, pues no se recomienda tener mas de 5 cosultas en un periodo inferior a 3 meses, esto podria afectar su puntaje o el resultado de sus solicitudes.",
    "VALORACION_MODAL_PUNTAJE_INFO_CONTACTO" => "INFORMACIÓN DE CONTACTO",
    "VALORACION_MODAL_PUNTAJE_INFO_CONTACTO_DESC" => "El cambio constante en la información de contacto laboral o de residencia, teléfono y dirección, podría indicar cierta inestabilidad y afectar su puntaje o aprobación",
    /**********************Fin constantes para las valoraciones ***********************************/
    
    /******* Inicio Constantes para estudio ******************/
    "EST_CONT2_TIPO" => "TIPO",
    "EST_CONT1_TITLE" => "ANÁLISIS DE LA CAPACIDAD",
    "EST_CONT1_ING" => "INGRESO",
    "EST_CONT1_EGR" => "EGRESO",
    "EST_CONT1_DIS" => "DISPONIBLE",
    "EST_CONT1_CONSUMO_GASTOS" => "GASTO FIJO",
    "EST_CONT1_OTHERS_INGRESOS" => "INGRESOS",
    "EST_CONT1_CAPACIDAD_DE_PAGO" => "CAPACIDAD",
    "EST_CONT1_CAPACIDAD" => "DESCUENTO",
    "EST_CONT1_CAPACIDAD_PAGO" => "CUOTA MÁXIMA",
    "EST_CONT1_COMPR" => "COMPRAS DE CARTERA",
    "EST_CONT1_ENT" => "ENTIDAD",
    "EST_CONT1_CUO" => "CUOTA",
    "EST_CONT1_DISP" => "COMPRAS",
    "EST_CONT1_NACI" => "FECHA DE NACIMIENTO",
    "EST_CONT1_EDAD" => "EDAD",
    "EST_CONT1_PMAX" => "PLAZO MAX",        
    "EST_CONT1_TVEHI" => "TIPO DE VÍNCULO LABORAL",
    "EST_CONT1_HUELLA" => "HUELLAS DE CONSULTA",
    "EST_CONT1_FECHA" => "FECHA",
    "EST_CONT1_PROCE_JURI" => "PROCESOS JURÍDICOS",
    "EST_CONT1_DEMANDANTE" => "DEMANDANTE",
    "EST_CONT1_PROCESO" => "# PROCESO",
    "EST_CONT2_TITLE" => "RESUMEN DE OBLIGACIONES",
    "EST_CONT2_ENTIDAD" => "ENTIDAD",
    "EST_CONT2_ESTADO" => "TIPO",
    "EST_CONT2_SALDO" => "SALDO",
    "EST_CONT2_PAGO" => "PAGO",
    "EST_CONT2_CERTI" => "CERTIFICADO",
    "EST_INFO_NUMERO" => "NÚMERO OBLIGACIÓN",
    "EST_INFO_NOMBRE" => "Entidad",
    "EST_INFO_TP" => "Tipo de cuenta",
    "EST_INFO_CALIF" => "Calificación",
    "EST_INFO_ESTOBL" => "Estado de la obligación",
    "EST_INFO_FA" => "Fecha de actualización",
    "EST_INFO_FAP" => "Fecha de apertura",
    "EST_INFO_FV" => "Fecha de vencimiento",
    "EST_INFO_COMPOR" => "Comportamiento",
    "EST_INFO_VLRCUP" => "Valor cupo inicial",
    "EST_INFO_SALACT" => "Saldo actual",
    "EST_INFO_SALMOR" => "Saldo mora",
    "EST_INFO_VLRCUO" => "Valor cuota",
    "EST_INFO_CTAS" => "Cuotas",
    "EST_INFO_PORDEU" => "Porcentaje deuda",
    "EST_INFO_OFI" => "Oficina",
    "EST_INFO_TITU" => "Titularidad",
    "EST_BANC_CALC" => "Cálculo Banco",
    "EST_MOD_VALORACION" => "VALORACION",
    /******* Fin Constantes para estudio ******************/
    
    
    /******* Variables globales de Cartera*****************/
    "CAR_FECHA_CORTE" => "15",
    
    /******* Fin Constantes Cartera **********/
    
    /************* Globales *******************************/
    "GBL_RAZON_SOCIAL" => "VTM VALORES TECNOLOGIA Y MERCADO S.A.S",
    "GBL_EMAIL" => "info@vtmsoluciones.com",
    "GBL_TELEFONO" => "305 9701",
    "GBL_SITIO_WEB" => "www.vtmsoluciones.com",
    "GBL_CELULAR" => "311 6709642",
    "GBL_CIUDAD" => "Cali",
    "GBL_PAIS" => "Colombia",
    "GBL_DIRECCION" => "Cll 12 # 83-65 Multicentro",
    "GBL_BANCO" => "Banco Davivienda",
    "GBL_CUENTA_BANCO" => "016969998513",
    "GBL_NIT_EMPRESA" => "900661159-2",
    
    /**Estados de la solicitud**/
    
    'SOLICITUD_PENDIENTE' => 0,
    'SOLICITUD_COMPLETA' => 1,
    'SOLICITUD_DEVUELTA' => 2
];



