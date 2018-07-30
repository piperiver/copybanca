<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
return '

{
    "tabla1": {
        "1": "C.C. Cédula de ciudadanía",
        "2": "NIT Número de identificación tributaria",
        "3": "N.E. Nit de extranjería",
        "4": "C.E. Cédula de Extranjería"
    },
    "tabla3": {
        "CCB": "Cuentas corrientes Bancarias",
        "TDC": "Tarjeta de crédito",
        "CBR": "Cartera Bancaria Rotativa",
        "CAU": "Cartera Automotriz",
        "CAB": "Cartera Bancaria",
        "CAC": "Cartera Cooperativas de ahorro y crédito",
        "COF": "Cartera corporaciones financieras",
        "CFE": "Cartera fondo de empleados",
        "CVE": "Cartera vestuario",
        "CLB": "Cartera editorial",
        "COC": "Cartera otros créditos",
        "CTU": "Cartera turismo",
        "CAV": "Cartera de ahorro y vivienda",
        "CCL": "Cartera de compañías de Leasing",
        "CCC": "Cartera de crédito de construcción",
        "CFR": "Cartera Finca Raíz",
        "EST": "Estatal",
        "CCF": "Cartera compañías de financiamiento comercial",
        "CMU": "Cartera Muebles",
        "CCS": "Cartera Compañías de Seguros",
        "CBM": "Créditos Bajo Monto",
        "CEL": "Cartera de Electrodomésticos",
        "CTC": "Cartera de telefonía celular",
        "CDC": "Cartera de comunicaciones",
        "DIC": "Departamento de información comercial",
        "CDB": "Corredores de Bolsa",
        "CSP": "Empresas de Servicios Públicos",
        "AGR": "Cartera Agroindustrial",
        "ALI": "Cartera de Alimentos",
        "CMZ": "Cartera Comercializadoras",
        "CSA": "Carteras caja de compensación y salud",
        "COM": "Cartera Computadores",
        "FER": "Cartera Ferreterías",
        "FUN": "Cartera Fundaciones",
        "GRM": "Cartera Gremios",
        "IND": "Cartera Industrial",
        "LAB": "Cartera Laboratorios",
        "LBZ": "Cartera Libranza",
        "SEG": "Cartera Seguridad",
        "TRT": "Cartera Transporte",
        "EDU": "Cartera Educación",
        "SFI": "Servicios Financieros",
        "CAU": "Cartera Automotriz",
        "CON": "Créditos de consumo",
        "APD": "Almacén por departamentos",
        "AHO": "Cuentas de ahorro y bancarias",
        "CAS": "Para el elemento consulta, sí tiene tipo de cuenta CAS quiere decir que fue consultado por el Centro de Atención y Servicio.",
        "SBG": "Cartera de Sobregiro",
        "MCR": "Cartera Microcrédito"
    },
    "tabla4": {
        "01": {
            "nombre": "AL DIA",
            "comportamiento": "N",
            "vigenteCerrada": "Vigente",
            "descripcion": "Este código de novedad se aplica a todas aquellas obligaciones que se encontraban al día en el momento de extractar la información que fue enviada a DataCrédito(fecha de corte)."
        },
        "02": {
            "nombre": "T. NO ENTREGADA",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando una tarjeta no ha sido entregada por el banco o no ha sido reclamada por el usuario."
        },
        "03": {
            "nombre": "CANCEL. MM",
            "comportamiento": "",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando el cliente hace mal uso de la tarjeta y el suscriptor toma la decisión de cancelarla."
        },
        "04": {
            "nombre": "T. ROBADA",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando el usuario de la tarjeta de crédito reporta a la entidad emisora, que el plástico ha sido robado."
        },
        "05": {
            "nombre": "CANCELADA VOL",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando el cliente cancela por voluntad propia el derecho a usar la tarjeta."
        },
        "06": {
            "nombre": "CANCELADA MX",
            "comportamiento": "",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando la tarjeta fue retirada del archivo maestro del suscriptor, sin especificar el motivo. Adicionalmente muestra la mora máxima en que incurrió la obligación."
        },
        "07": {
            "nombre": "T. EXTRAVIADA.",
            "comportamiento": "",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando el usuario de la tarjeta de crédito reporta a la entidad emisora que el plástico se ha extraviado."
        },
        "08": {
            "nombre": "PAGO VOL",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Pago total de la Deuda (cancelación final de la obligación). Esta novedad se presenta cuando la obligación ha llegado a su final su saldo es igual a cero y no presenta ninguna mora en el vector de comportamiento."
        },
        "09": {
            "nombre": "PAGO VOL MX 30",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Pago total de la Deuda (cancelación final de la obligación). Esta novedad se presenta cuando la obligación ha llegado a su final, su saldo es igual a cero y en el vector de comportamiento presenta una mora máxima de 30 días."
        },
        "10": {
            "nombre": "PAGO VOL MX 60",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Pago total de la Deuda (cancelación final de la obligación). Esta novedad se presenta cuando la obligación ha llegado a su final, su saldo es igual a cero y en el vector de comportamiento presenta una mora máxima de 60 días"
        },
        "11": {
            "nombre": "PAGO VOL MX 90",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Pago total de la Deuda (cancelación final de la obligación).. Esta novedad se presenta cuando la obligación ha llegado a su final, su saldo es igual a cero y en el vector de comportamiento presenta una mora máxima de 90 días"
        },
        "12": {
            "nombre": "PAGO VOL MX 120",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Pago total de la Deuda (cancelación final de la obligación).. Esta novedad se presenta cuando la obligación ha llegado a su final, su saldo es igual a cero y en el vector de comportamiento presenta una mora máxima de 120 días"
        },
        "13": {
            "nombre": "AL DIA MORA 30",
            "comportamiento": "N",
            "vigenteCerrada": "Vigente",
            "descripcion": "En el vector de comportamiento presenta una mora máxima de 30 y actualmente se encuentra al día."
        },
        "14": {
            "nombre": "AL DIA MORA 60",
            "comportamiento": "N",
            "vigenteCerrada": "Vigente",
            "descripcion": "En el vector de comportamiento presenta una mora máxima de 60 y actualmente se encuentra al día."
        },
        "15": {
            "nombre": "AL DIA MORA 90",
            "comportamiento": "N",
            "vigenteCerrada": "Vigente",
            "descripcion": "En el vector de comportamiento presenta una mora máxima de 90 y actualmente se encuentra al día."
        },
        "16": {
            "nombre": "AL DIA MORA 120",
            "comportamiento": "N",
            "vigenteCerrada": "Vigente",
            "descripcion": "En el vector de comportamiento presenta una mora máxima de 120 y actualmente se encuentra al día.."
        },
        "17": {
            "nombre": "ESTA EN MORA 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación se encuentra morosa entre 30 y 59 días."
        },
        "18": {
            "nombre": "ESTA EN MORA 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación se encuentra morosa entre 60 y 89 días."
        },
        "19": {
            "nombre": "ESTA EN MORA 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación se encuentra morosa entre 90 y 119 días."
        },
        "20": {
            "nombre": "ESTA EN MORA 120",
            "comportamiento": "4",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación se encuentra morosa entre 120 días o más."
        },
        "21": {
            "nombre": "FM 60 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 60 días y actualmente está en mora de 30 días. Fue mora 60 y pasó a mora 30."
        },
        "22": {
            "nombre": "FM 90 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 90 días y actualmente está en mora de 30 días. Fue mora 90 y pasó a mora 30. Fue mora 90 y pasó a mora 30."
        },
        "23": {
            "nombre": "FM 90 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 90 días y actualmente está en mora de 60 días. Fue mora 90 y pasó a mora 60"
        },
        "24": {
            "nombre": "FM 120 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 120 días y actualmente está en mora de 30 días. Fue mora 120 y pasó a mora 30"
        },
        "25": {
            "nombre": "FM 120 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 120 días y actualmente está en mora de 60 días. Fue mora 120 y pasó a mora 60."
        },
        "26": {
            "nombre": "FM 120 ESTA M 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta en el vector de comportamiento una mora máxima de 120 días y actualmente está en mora de 90 días. Fue mora 120 y pasó a mora 90."
        },
        "27": {
            "nombre": "RM 30 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 30 y actualmente se encuentra en mora 60."
        },
        "28": {
            "nombre": "RM 30 ESTA M 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 30 y actualmente se encuentra en mora 90."
        },
        "29": {
            "nombre": "RM 30 ESTA M 120",
            "comportamiento": "4",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 30 y actualmente se encuentra en mora 120."
        },
        "30": {
            "nombre": "RM 60 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 60 y actualmente se encuentra en mora 30."
        },
        "31": {
            "nombre": "RM 60 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 60 y actualmente se encuentra en mora 60."
        },
        "32": {
            "nombre": "RM 60 ESTA M 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 60 y actualmente se encuentra en mora 90."
        },
        "33": {
            "nombre": "RM 60 ESTA M 120",
            "comportamiento": "4",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 60 y actualmente se encuentra en mora 120."
        },
        "34": {
            "nombre": "RM 90 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 90 y actualmente se encuentra en mora 30."
        },
        "35": {
            "nombre": "RM 90 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 90 y actualmente se encuentra en mora 60."
        },
        "36": {
            "nombre": "RM 90 ESTA M 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 90 y actualmente se encuentra en mora 90."
        },
        "37": {
            "nombre": "RM 90 ESTA M 120",
            "comportamiento": "4",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 90 y actualmente se encuentra en mora 120."
        },
        "38": {
            "nombre": "RM 120 ESTA M 30",
            "comportamiento": "1",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 120 y actualmente se encuentra en mora 30."
        },
        "39": {
            "nombre": "RM 120 ESTA M 60",
            "comportamiento": "2",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 120 y actualmente se encuentra en mora 60."
        },
        "40": {
            "nombre": "RM 120 ESTA M 90",
            "comportamiento": "3",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 120 y actualmente se encuentra en mora 90."
        },
        "41": {
            "nombre": "RM 120 ESTA M 120",
            "comportamiento": "4",
            "vigenteCerrada": "Vigente",
            "descripcion": "La obligación presenta reincidencia en mora 120 y actualmente se encuentra en mora 120."
        },
        "45": {
            "nombre": "CART. CASTIGADA",
            "comportamiento": "C",
            "vigenteCerrada": "Vigente",
            "descripcion": "La entidad que reporta la obligación considera que la deuda en la actualidad es incobrable"
        },
        "46": {
            "nombre": "CART. RECUPERADA",
            "comportamiento": "D",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando la cancelación final de la deuda se logró por vías anormales como cobro judicial, embargo, arreglo entre deudor y acreedor, etc. Normalmente se trata de deudas que han pasado por dudoso recaudo o cartera castigada."
        },
        "47": {
            "nombre": "DUDOSO RECAUDO",
            "comportamiento": "D",
            "vigenteCerrada": "Vigente",
            "descripcion": "Cuando a juicio de la entidad, el tiempo de morosidad de la deuda pasó a ser considerada una deuda cuya probabilidad de recaudo es totalmente dudosa."
        },
        "49": {
            "nombre": "TARJETA NO RENOVADA",
            "comportamiento": "N",
            "vigenteCerrada": "Cerrada",
            "descripcion": "Cuando la entidad emisora de la tarjeta decide no renovarla para un próximo periodo."
        }
    },
    "tabla5": {
        "N": "Al día",
        "1": "Mora de 30 días",
        "2": "Mora de 60 días",
        "3": "Mora de 90 días",
        "4": "Mora de 120 días",
        "5": "Mora de 150 días",
        "6": "Mora de 180 días",
        "D": "Dudoso recaudo (Solo se presenta cuando la consulta se hace con formato nuevo \'M\').",
        "C": "Cartera castigada (Solo se presenta cuando la consulta se hace con formato nuevo \'M\')."
    },
    "tabla6": {
        "00": {
            "descripcionCartera": "Deudor Principal",
            "descripcionTarjeta": "Deudor Principal"
        },
        "01": {
            "descripcionCartera": "Codeudor",
            "descripcionTarjeta": "Amparada"
        },
        "02": {
            "descripcionCartera": "Codeudor",
            "descripcionTarjeta": "Amparada"
        },
        "03": {
            "descripcionCartera": "Codeudor",
            "descripcionTarjeta": "Amparada"
        },
        "04": {
            "descripcionCartera": "Avalista",
            "descripcionTarjeta": "Amparada"
        },
        "05": {
            "descripcionCartera": "Deudor Solitario",
            "descripcionTarjeta": "Amparada"
        },
        "06": {
            "descripcionCartera": "Coarrendatario",
            "descripcionTarjeta": "Amparada"
        },
        "07": {
            "descripcionCartera": "Otros garantes",
            "descripcionTarjeta": "Amparada"
        },
        "08": {
            "descripcionCartera": "Fiador",
            "descripcionTarjeta": "Amparada"
        },
        "09-95": {
            "descripcionCartera": "No Aplica",
            "descripcionTarjeta": "Amparada"
        },
        "96": {
            "descripcionCartera": "Cotitular",
            "descripcionTarjeta": "Cotitular"
        },
        "97": {
            "descripcionCartera": "Comunal(solo para cuentas Microcredito-MCR)",
            "descripcionTarjeta": "Amparada"
        },
        "98-99": {
            "descripcionCartera": "No Aplica",
            "descripcionTarjeta": "Amparada"
        }
    },
    "tabla7": {
        "0": "No informó",
        "1": "Mensual",
        "2": "Bimensual",
        "3": "Trimestral",
        "4": "Semestral",
        "5": "Anual",
        "6": "Al vencimiento",
        "9": "Otro"
    },
    "tabla8": {
        "0": {
            "adjetivoTarjeta": "No hay adjetivo",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "1": {
            "adjetivoTarjeta": "Fallecido",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "2": {
            "adjetivoTarjeta": "Cuenta en cobrador",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "3": {
            "adjetivoTarjeta": "Deudor no localizado",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "4": {
            "adjetivoTarjeta": "Línea suspendida",
            "cuentaCartera": "Sólo para cartera de telecomunicaciones"
        },
        "5": {
            "adjetivoTarjeta": "Incapacidad total ó permanente",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "6": {
            "adjetivoTarjeta": "Cobro pre-jurídico",
            "cuentaCartera": "Todos los tipos de cuenta"
        },
        "7": {
            "adjetivoTarjeta": "Cobro jurídico",
            "cuentaCartera": "Todos los tipos de cuenta"
        }
    },
    "tabla9": {
        "0": "No se encuentra información en este campo",
        "1": "Comerciales",
        "2": "Consumo",
        "3": "Hipotecario",
        "4": "Otro",
        "5": "Microcrédito",
        "6": "Libranza"
    },
    "tabla10": {
        "0": "No reportado",
        "1": "Moneda legal",
        "2": "Moneda Extranjera"
    },
    "tabla11": {
        "0": {
            "codigo": "SIN GAR",
            "descripcion": "Sin garantía"
        },
        "1": {
            "codigo": "ADMIS",
            "descripcion": "admisible"
        },
        "2": {
            "codigo": "OTR GAR",
            "descripcion": "Otras garantías idóneas"
        },
        "A": {
            "codigo": "NO IDON",
            "descripcion": "No idónea"
        },
        "B": {
            "codigo": "BIEN RAICES",
            "descripcion": "Bienes raíces comerciales y residenciales, fiducias hipotecarias."
        },
        "C": {
            "codigo": "OTR PREND",
            "descripcion": "Otras prendas."
        },
        "D": {
            "codigo": "OTR GAR",
            "descripcion": "Otras garantías idóneas"
        },
        "D": {
            "codigo": "PIGN RENTA",
            "descripcion": "Pignoración de rentas de entidades territoriales y descentralizadas de todos los órdenes."
        },
        "E": {
            "codigo": "GAR SOBER NACION",
            "descripcion": "Garantía Soberana de la Nación"
        },
        "F": {
            "codigo": "CONT IRREV FIDUC",
            "descripcion": "Contratos irrevocables de fiducia mercantil de garantía, inclusive aquellos que versen sobre rentas derivadas de contratos de concesión."
        },
        "G": {
            "codigo": "FNG",
            "descripcion": "Garantías otorgadas por el Fondo Nacional de Garantías S.A"
        },
        "H": {
            "codigo": "CARTA CRÉD",
            "descripcion": "Cartas de crédito"
        },
        "I": {
            "codigo": "FAG",
            "descripcion": "FAG (Fondo Agropecuario de Garantías)"
        },
        "J": {
            "codigo": "PERSONAL",
            "descripcion": "Personal"
        },
        "K": {
            "codigo": "BIEN LEASI NO INMOB",
            "descripcion": "Bienes dados en Leasing diferente a inmobiliario"
        },
        "L": {
            "codigo": "BIEN LEASI INMOB",
            "descripcion": "Bienes dados en Leasing inmobiliario"
        },
        "M": {
            "codigo": "PRENDA TITULO",
            "descripcion": "Prenda sobre títulos valores emitidos por instituciones financieras"
        },
        "N": {
            "codigo": "DEPOSITOS",
            "descripcion": "Depósitos de dinero en garantía colateral"
        },
        "O": {
            "codigo": "SEG CREDITO",
            "descripcion": "Seguros de crédito"
        }
    },
    "tabla12": {
        "ML": "Moneda legal",
        "ME": "Moneda Extranjera"
    },
    "tabla13": {
        "01": "Código de suscriptor no existe. ",
        "02": "Clave errada ",
        "03": "Numero de terminal no existe ",
        "04": "Tipo de documento errado ",
        "05": "Numero de documento errado ",
        "06": "Primer apellido errado ",
        "07": "Fin-consulta tipo 2 ",
        "08": "Fin-consulta tipo 4 ",
        "09": "Fin-consulta tipo 7. NO existe este número de identificación en los archivos de validación de la base de datos. ",
        "10": "Fin-consulta tipo 6. El apellido NO coincide con el registrado en la Registraduría Nacional del Estado Civil. ",
        "11": "Terminal desactivada ",
        "12": "Clave de consulta bloqueada ",
        "13": "Fin-consulta tipo 1. La consulta fue efectiva. ",
        "14": "Fin-consulta tipo 5. El apellido digitado si coincide con el registrado en la base de datos para ese número de cédula, pero la persona NO tiene información comercial en la base de datos de DataCrédito. ",
        "15": "Ingreso correcto ",
        "16": "Su prepago ya está agotado ",
        "17": "Clave vencida ",
        "18": "Clave no habilitada ",
        "19": "Modalidad de prepagos bloqueada ",
        "20": "Modalidad de prepagos aún no habilitada ",
        "21": "Su prepago ya fue vencido ",
        "23": "No se pudo realizar la consulta, vuelva a intentar ",
        "24": "Clave de Dcifra sin tabla"
    },
    "tabla14": {
        "1": "A ",
        "2": "B ",
        "3": "C ",
        "4": "D ",
        "5": "E ",
        "6": "AA ",
        "7": "BB ",
        "8": "CC ",
        "9": "K"
    },
    "tabla16": {
        "01": {
            "estado": "Activa ",
            "descripcion": "Este código de novedad se aplica a las cuentas que presentan un manejo normal durante el mes de corte. "
        },
        "02": {
            "estado": "Cancelada Mal manejo ",
            "descripcion": "Esta novedad se codifica cuando la entidad toma la decisión de cancelar la cuenta por mal uso por parte del titular. "
        },
        "05": {
            "estado": "Saldada ",
            "descripcion": "Esta novedad se codifica cuando la cuenta se encuentra saldada. "
        },
        "06": {
            "estado": "Embargada ",
            "descripcion": "Esta novedad se codifica cuando la cuenta se encuentra embargada. "
        },
        "07": {
            "estado": "Embargada-Activa ",
            "descripcion": "Esta novedad se codifica cuando la cuenta estuvo embargada pero ya se encuentra de nuevo activa. "
        },
        "09": {
            "estado": "Inactiva ",
            "descripcion": "Esta novedad se codifica cuando por decisión interna de la entidad una cuenta no ha tenido movimiento alguno durante un lapso de tiempo."
        }
    },
    "tabla18": {
        "0": "Vigente ",
        "1": "Pago Voluntario ",
        "2": "Proceso ejecutivo ",
        "3": "Mandamiento de Pago ",
        "4": "Reestructuración ",
        "5": "Dación en pago",
        "6": "Cesión ",
        "7": "Donación"
    },
    "tabla19": {
        "01": "Predicta ",
        "02": "Dcifra ",
        "04": "Acierta ",
        "05": "Por definir ",
        "06": "Por definir ",
        "07": "1nicia ",
        "08": "Acierta M ",
        "09": "Por definir ",
        "10": "Score a la medida ",
        "41": "Acierta A vehículo e hipotecario línea ",
        "45": "Acierta A cooperativas línea ",
        "47": "Acierta A financiero línea ",
        "48": "Acierta A tarjeta de crédito línea ",
        "49": "Acierta A telecomunicaciones línea ",
        "67": "Acierta+ ",
        "95": "Acierta A instalamentos línea ",
        "E1": "Modelo Stability ",
        "E2": "Modelo Never Pay ",
        "E6": "Pronostico ",
        "E3": "Acierta 360 Línea",
        "A3": "Acierta Microcrédito solo Buró ",
        "A4": "Acierta Microcrédito Cliente y Buró ",
        "A8": "Otorga"
    },
    "tabla20": {
        "1": {
            "estadoCivil": "Casada ",
            "genero": "Mujer "
        },
        "2": {
            "estadoCivil": "Viuda ",
            "genero": "Mujer "
        },
        "3": {
            "estadoCivil": "Mujer ",
            "genero": "Mujer "
        },
        "4": {
            "estadoCivil": "Hombre ",
            "genero": "Hombre"
        }
    },
    "tabla21": {
        "1": "18 a 21 años ",
        "2": "22 a 28 años ",
        "3": "29 a 35 años ",
        "4": "36 a 45 años ",
        "5": "46 a 55 años ",
        "6": "56 a 65 años ",
        "7": "66 años o más"
    },
    "tabla23": {
        "00": "Razón desconocida ",
        "01": "Solicitud de producto ",
        "02": "Revisión del portafolio por parte de la Entidad ",
        "03": "Revisión del portafolio por parte del ciudadano",
        "04": "Solicitud de producto ",
        "05": "Consulta del ciudadano por Internet ",
        "06": "Consulta del ciudadano por CAS Virtual ",
        "07": "Por definir ",
        "08": "Por definir"
    },
    "tabla24": {
        "01": "Comentario al Informe ",
        "02": "Comentario de Tutela ",
        "03": "Reclamo de Habito de Pago ",
        "04": "Reclamo de Endeudamiento Global ",
        "05": "Reclamo a Información de Identificación ",
        "06": "Estado Ley 917 (Si en el texto del comentario viene la palabra positivo quiere decir que la historia tiene un reporte positivo de lo contrario negativo)."
    },
    "tabla25": {
        "02": "Actualizar información ",
        "03": "Rectificar la información ",
        "04": "Conocer la información ",
        "05": "Otros"
    },
    "tabla26": {
        "1": "Colocado ",
        "2": "Modificado ",
        "3": "Aplicado ",
        "4": "Devuelto"
    },
    "tabla27": {
        "02": {
            "subtipo": "01 ",
            "descripcion": "No Actualización de la información "
        },
        "02": {
            "subtipo": "02 ",
            "descripcion": "No reporte de información oportuno "
        },
        "02": {
            "subtipo": "03 ",
            "descripcion": "Reporte de información incompleta o parcial "
        },
        "02": {
            "subtipo": "04 ",
            "descripcion": "No inclusión de las respectivas leyendas "
        },
        "02": {
            "subtipo": "05 ",
            "descripcion": "Inconformidad con la permanencia de la información negativa "
        },
        "03": {
            "subtipo": "06 ",
            "descripcion": "No rectificación de información errónea "
        },
        "03": {
            "subtipo": "07 ",
            "descripcion": "Inexistencia de la obligación reportada o negación de la relación contractual "
        },
        "03": {
            "subtipo": "08 ",
            "descripcion": "No contar con los documentos soporte de la obligación "
        },
        "03": {
            "subtipo": "09 ",
            "descripcion": "No contar con la autorización previa y expresa del titular para reportar información "
        },
        "03": {
            "subtipo": "10 ",
            "descripcion": "No certificar semestralmente al operador que la información suministrada cuenta con la respectiva autorización del titular "
        },
        "03": {
            "subtipo": "11 ",
            "descripcion": "No remitir la comunicación previa al reporte "
        },
        "04": {
            "subtipo": "12 ",
            "descripcion": "Negación de acceso a la información - Fuente "
        },
        "04": {
            "subtipo": "13 ",
            "descripcion": "No atender las peticiones y reclamos presentados por los titulares de fondo y oportunamente - Fuente "
        },
        "04": {
            "subtipo": "14 ",
            "descripcion": "No adoptar las medidas de seguridad adecuadas sobre la información obtenida en las bases de datos de los operadores "
        },
        "04": {
            "subtipo": "15 ",
            "descripcion": "Utilizar la información para una finalidad diferente a aquella para la cual fue entregada "
        },
        "04": {
            "subtipo": "16 ",
            "descripcion": "Consulta de información no autorizada por el titular, cuando esta sea requerida "
        },
        "04": {
            "subtipo": "17 ",
            "descripcion": "No contar con medidas adecuadas de seguridad - Fuente "
        },
        "04": {
            "subtipo": "18 ",
            "descripcion": "No informar al titular sobre la utilización que se le está dando a su información "
        },
        "04": {
            "subtipo": "19 ",
            "descripcion": "No informar al titular la finalidad de la recolección de la información "
        },
        "04": {
            "subtipo": "20 ",
            "descripcion": "No guardar reserva sobre la información obtenida en las bases de datos de los operadores "
        },
        "04": {
            "subtipo": "22 ",
            "descripcion": "Negación de acceso a la información - Operador "
        },
        "04": {
            "subtipo": "23 ",
            "descripcion": "No atender las peticiones y reclamos presentados por los titulares de fondo y oportunamente - Operador "
        },
        "04": {
            "subtipo": "24 ",
            "descripcion": "No contar con medidas adecuadas de seguridad  Operador "
        },
        "05": {
            "subtipo": "21 ",
            "descripcion": "Marcación de leyenda \'reclamo en trámite\' - en lote ."
        }
    },
    "tabla28": {
        "990003**": {
            "subtipo": "001 ",
            "descripcion": "El documento de identidad fue extraviado "
        },
        "990003**": {
            "subtipo": "002 ",
            "descripcion": "El documento de identidad fue robado "
        },
        "990003**": {
            "subtipo": "003 ",
            "descripcion": "Actualmente reside fuera de Colombia "
        },
        "990003**": {
            "subtipo": "004 ",
            "descripcion": "Ha sido objeto de varios intentos de suplantación de identidad "
        },
        "990001*": {
            "subtipo": "004 ",
            "descripcion": "La cedula tiene historia de crédito como Nit "
        },
        "990001*": {
            "subtipo": "004 ",
            "descripcion": "El cliente tiene actividad registrada como titular con establecimiento de comercio "
        },
        "990001*": {
            "subtipo": "006 ",
            "descripcion": "Sin información en archivo de extranjeros "
        },
        "990001*": {
            "subtipo": "007 ",
            "descripcion": "El apellido con el que se vuelve a consultar es diferente "
        },
        "990001": {
            "subtipo": "008 ",
            "descripcion": "El cliente NO tiene actividad registrada como titular con establecimiento de comercio "
        },
        "990001": {
            "subtipo": "009 ",
            "descripcion": "Si se está solicitando un microcrédito, sus deudas registradas llegan al tope máximo, decreto 919/2008 "
        },
        "990001": {
            "subtipo": "010 ",
            "descripcion": "El registro consultado ha presentado 3 nuevas direcciones en el último año "
        },
        "990001": {
            "subtipo": "011 ",
            "descripcion": "El registro consultado ha presentado moras de más de 60 días o más en los últimos 6 meses "
        },
        "990001": {
            "subtipo": "012 ",
            "descripcion": "Más de 3 consultas de diferentes entidades en los últimos 60 días "
        },
        "990001": {
            "subtipo": "999 ",
            "descripcion": "Sin alertas en el grupo de servicio "
        },
        "990001": {
            "subtipo": "999 ",
            "descripcion": "Sin alertas en el grupo Buro "
        },
        "990001": {
            "subtipo": "999 ",
            "descripcion": "Sin alertas en el grupo demandas judiciales "
        },
        "990001": {
            "subtipo": "999 ",
            "descripcion": "Sin alertas en el grupo verticales "
        },
        "000003": {
            "subtipo": "301 ",
            "descripcion": "Coincidencia por nombre e identificación en lista .. al . "
        },
        "000003": {
            "subtipo": "302 ",
            "descripcion": "Coincidencia solo por identificación en lista .. al . "
        },
        "000003": {
            "subtipo": "303 ",
            "descripcion": "Coincidencia solo por nombre lista .. al . "
        },
        "000003": {
            "subtipo": "304 ",
            "descripcion": "No se encuentra coincidencia con listas .. al . "
        },
        "000004": {
            "subtipo": "201 ",
            "descripcion": "Coincidencia por nombre e identificación en lista .. al . "
        },
        "000004": {
            "subtipo": "202 ",
            "descripcion": "Coincidencia solo por identificación en lista .. al . "
        },
        "000004": {
            "subtipo": "203 ",
            "descripcion": "Coincidencia solo por nombre lista .. al . "
        },
        "000002": {
            "subtipo": "101 ",
            "descripcion": "Tiene . demandas cerradas y . abiertas la abierta mas reciente se instauro el . "
        },
        "000002": {
            "subtipo": "102 ",
            "descripcion": "Tiene .embargos cerrados y .abiertos el abierto mas reciente se coloco el ."
        }
    },
    "tabla28-1": {
        "990001": "Fuente Interna DataCrédito ",
        "990003": "Fuente Externa El Ciudadano ",
        "000001": "Fuente externa otras ",
        "000002": "C.S. de la J ",
        "000003": "Listas Restrictivas ",
        "000004": "P.E.P"
    },
    "tabla29": {
        "0": "Normal ",
        "1": "Concordato ",
        "2": "Liquidación Forzosa ",
        "3": "Liquidación Voluntaria ",
        "4": "Proceso de Reorganización ",
        "5": "Ley 550 ",
        "6": "Ley 1116 ",
        "7": "Otra"
    },
    "tabla30": {
        "1": "Termino Definido ",
        "2": "Termino Indefinido ",
        "3": "Prestación de Servicios ",
        "4": "Temporal ",
        "5": "Carrera Administrativa"
    },
    "tabla32": {
        "01": "Asalariado ",
        "02": "Independiente ",
        "03": "Pensionado ",
        "04": "Estudiante ",
        "05": "Ama de Casa ",
        "06": "Otro"
    },
    "tabla33": {
        "01": "Soltero ",
        "02": "Casado ",
        "03": "Divorciado ",
        "04": "Unión Libre"
    },
    "tabla34": {
        "01": "Primaria ",
        "02": "Secundaria ",
        "03": "Bachillerato ",
        "04": "Universitario ",
        "05": "Postgrado ",
        "06": "Maestría ",
        "07": "Doctorado ",
        "08": "Ninguno"
    },
    "tabla35": {
        "A": "Normal ",
        "B": "Sobregiro mayor a 31 días ",
        "C": "Sobregiro mayor a 61 días"
    },
    "tabla36": {
        "0": {
            "cuenta": "Normal ",
            "codCuenta": "NOR "
        },
        "1": {
            "cuenta": "Nomina ",
            "codCuenta": "NOM "
        },
        "2": {
            "cuenta": "GMF (Gravamen Movimiento Financiero) ",
            "codCuenta": "GMF "
        },
        "3": {
            "cuenta": "Nomina GMF ",
            "codCuenta": "NGM "
        },
        "4": {
            "cuenta": "Electrónica ",
            "codCuenta": "ELE "
        },
        "5": {
            "cuenta": "AFC ",
            "codCuenta": "AFC/ACF"
        }
    },
    "tabla37": {
        "1": "Reclamo en Trámite ",
        "2": "Reclamo en Discusión Judicial ",
        "3": "Suplantación de Identidad ",
        "4": "Investigación en Trámite o Actuación Administrativa ",
        "5": "Reclamo en Trámite, Investigación en Trámite o Actuación Administrativa ",
        "6": "Reclamo en Trámite, Suplantación de Identidad ",
        "7": "Reclamo en Trámite, Investigación en Trámite o Actuación Administrativa, Suplantación de Identidad ",
        "8": "Investigación en Trámite o Actuación Administrativa, Suplantación de Identidad ",
        "9": "Discusión Judicial, Investigación en Trámite o Actuación Administrativa ",
        "A": "Discusión Judicial, Suplantación de Identidad ",
        "B": "Discusión Judicial, Investigación en Trámite o Actuación Administrativa, Suplantación de Identidad"
    },
    "tabla38": {
        "0": "No hay adjetivo",
        "5": "Fallecido"
    },
    "tabla39": {
        "1": "American Express ",
        "2": "Visa ",
        "3": "Master Card ",
        "4": "Diners ",
        "5": "Privada"
    },
    "tabla40": {
        "1": "Clasica ",
        "2": "Gold ",
        "3": "Platinum ",
        "4": "Otra"
    },
    "tabla41": {
        "0": "No reportado ",
        "1": "Definido ",
        "2": "Indefinido"
    },
    "tabla42": {
        "1": "Entregado ",
        "2": "Renovado ",
        "3": "No Renovado ",
        "4": "Reexpedido ",
        "5": "Robado ",
        "6": "Extraviado ",
        "7": "No Entregado ",
        "8": "Devuelto"
    },
    "tabla43": {
        "00": "Entidad no reportó ",
        "01": "Al día ",
        "02": "En Mora ",
        "03": "Pago Total ",
        "04": "Pago Judicial ",
        "05": "Dudoso Recaudo ",
        "06": "Castigada ",
        "07": "Dación en Pago ",
        "08": "Cancelada Voluntariamente ",
        "09": "Cancelada por mal manejo ",
        "10": "Cancelada por prescripción ",
        "11": "Cancelada por la entidad ",
        "12": "Cancelada por reestructuración/refinanciación ",
        "13": "Cancelada por venta ",
        "14": "Insoluta ",
        "15": "Cancelada por siniestro"
    },
    "tabla44": {
        "0": "Normal. Creación por apertura. ",
        "1": "Reestructurada ",
        "2": "Refinanciada ",
        "3": "Transferida de otro producto ",
        "4": "Comprada"
    },
    "tabla45": {
        "CMR": "Créditos comerciales ",
        "HIP": "Créditos hipotecarios para vivienda ",
        "MIC": "Microcrédito ",
        "CNS": "Consumo"
    },
    "tabla46": {
        "S": "Superintendencia ",
        "DC": "Datacredito"
    },
    "tabla47": {
        "62": "Quanto"
    },
    "tabla48": {
        "1": "Financiero ",
        "2": "Cooperativo ",
        "3": "Real ",
        "4": "Telecomunicaciones"
    },
    "tabla49": {
        "0": {
            "subtipo": "SIN GAR ",
            "descripcion": "Sin garantía "
        },
        "1": {
            "subtipo": "NO IDON ",
            "descripcion": "No idónea "
        },
        "2": {
            "subtipo": "BIEN RAICES ",
            "descripcion": "Bienes raíces comerciales y residenciales, fiducias hipotecarias "
        },
        "3": {
            "subtipo": "OTR PREND ",
            "descripcion": "Otras prendas "
        },
        "4": {
            "subtipo": "PIGN RENTA",
            "descripcion": "Pignoración de rentas de entidades territoriales y descentralizadas de todos los órdenes "
        },
        "5": {
            "subtipo": "GAR SOBER NACION",
            "descripcion": "Garantía Soberana de la Nación. (Ley 617 de 2000) "
        },
        "6": {
            "subtipo": "CONT IRREV FIDUC ",
            "descripcion": "Contratos irrevocables de fiducia mercantil de garantía, inclusive aquellos que versen sobre rentas derivadas de contratos de concesión "
        },
        "7": {
            "subtipo": "FNG ",
            "descripcion": "Garantías otorgadas por el Fondo Nacional de Garantías S.A "
        },
        "8": {
            "subtipo": "CARTA CRÉD ",
            "descripcion": "Cartas de crédito Stand By idóneas conforme lo dispuesto en el literal d del subnumeral 1.3.2.3.1. del Capítulo II de la Circular Externa 100 de 1995 "
        },
        "9": {
            "subtipo": "OTR GAR ",
            "descripcion": "Otras garantías idóneas "
        },
        "10": {
            "subtipo": "FAG ",
            "descripcion": "FAG (Fondo Agropecuario de Garantías) "
        },
        "11": {
            "subtipo": "PERSONAL ",
            "descripcion": "Personal "
        },
        "12": {
            "subtipo": "BIEN LEASI NO INMOB ",
            "descripcion": "Bienes dados en Leasing diferente a inmobiliario "
        },
        "13": {
            "subtipo": "BIEN LEASI INMOB ",
            "descripcion": "Bienes dados en Leasing inmobiliario "
        },
        "14": {
            "subtipo": "PRENDA TITULO ",
            "descripcion": "Prenda sobre títulos valores emitidos por instituciones financieras "
        },
        "15": {
            "subtipo": "DEPOSITOS ",
            "descripcion": "Depósitos de dinero en garantía colateral "
        },
        "16": {
            "subtipo": "SEG CREDITO ",
            "descripcion": "Seguros de crédito"
        }
    }
}
    
    
    ';
