<?php
namespace App\Librerias;

use App\Parametro;
use App\Permiso;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use App\Mensaje;
use App\Adjunto;
use App\Estudio;
use App\Valoracion;

class UtilidadesClass
{
    
    var $extensionesPermitidas = [
            "pdf",
            "jpg",
            "jpeg",
            "png"
        ];
    
    var $cargos = [
                            "CEL" => "CELADOR",
                            "ADM" => "ADMINISTRATIVO",
                            "DOC" => "DOCENTE",
                            "PEN" => "PENSIONADO"                            
                            ];
    
    public function Ordenar($return)
    {
        if(count($return) <= 0){
            return array();
        }
        // ********** Nuevo Codigo 03/02/2017
        foreach ($return as $clave => $fila)
        {
            $cod[$clave] = $fila->Codigo;
        }        
        natsort($cod);
        $new_array = array();
        foreach ($cod as $clave => $fila)
        {
            $new_array[] = $return[$clave];
        }
        return $new_array;
    }

    public function obtenerValorParametro($Codigo)
    {
        $Parametro = Parametro::find($Codigo);        
        return (isset($Parametro->Valor))? $Parametro->Valor : false;
    }

    static function ValidarAcceso($forma, $gestion = false)
    {
        
        if(Auth::user()->perfil == config("constantes.PERFIL_ROOT")){
            return true;
        }
        
        $user= Auth::user();
        $permisos = Permiso::where("Perfil",$user->perfil)
                ->where("Forma",$forma)->first();
                
        if(isset($permisos) && !is_null($permisos)){
            
            if($gestion !== false){

                if(($gestion == "Insertar" && $permisos->Insertar !== "S")
                || ($gestion == "Actualizar" && $permisos->Actualizar !== "S")
                || ($gestion == "Eliminar" && $permisos->Eliminar !== "S")){                
                    return false;
                }
            }

        }else{        
            return false;
        }
        
        return true;
    }

    /*
    *Funcion para extraer el mensaje segun el id que se le pasa
    *@Param: $idMessage => Variable que contiene el id del mensaje a buscar
    *@Param: Type => tipo de mensaje: danger(rojo), success(verde), info(azul), warning(amarillo)
    *@Param: $replaceInMessage => Array con las palabras que se reemplazaran por los key 
    *que se encuentre ya sea en: Mensaje, causa o solucion.
    */   
    public function getMessage($idMessage, $type, $replaceInMessage = array(), $archivo, $linea)
    {
        $message = Mensaje::find($idMessage);
        if(is_null($message)){
            return ["No se pudo eliminar el registro. No existe el mensaje #$idMessage en la base de datos. By: ".$archivo.", Linea: ".$linea];
        }

        $mensaje = $message->Mensaje;
        $causa = $message->Causa;
        $solucion = $message->Solucion;

        for ($i=0; $i < count($replaceInMessage) ; $i++) {
            if(empty($replaceInMessage[$i])){
                continue;
            }
            $mensaje = str_replace("[key$i]", $replaceInMessage[$i], $mensaje);
            $causa = str_replace("[key$i]", $replaceInMessage[$i], $causa);
            $solucion = str_replace("[key$i]", $replaceInMessage[$i], $solucion);
        }
        
        $texto = [
            "<strong>Mensaje:</strong> ".$mensaje,
            "<strong>Causa:</strong> ".$causa,
            "<strong>Solución:</strong> ".$solucion
        ];
        return $texto;
    
    }
    /*
    #Funcion para crear el html del mensaje
    @param $message => Mensaje que se mostrara
    @Param: Type => tipo de mensaje: danger(rojo), success(verde), info(azul), warning(amarillo)
    */
    public function createMessage($message, $type){
        $texto = '<div class="alert alert-'.$type.'" style="display: block;">
                    <button class="close" data-close="alert"></button>
                    '.$message.'
                  </div>';
        return $texto;
    }

    public function registroAdjunto($id,$tabla,$archivo,$extension,$tipoAdjunto,$modulo)
    {
        $Adjunto = new Adjunto();
        $Adjunto->idPadre = $id;
        $Adjunto->Tabla = $tabla;
        $Adjunto->NombreArchivo = $archivo;
        $Adjunto->Extension = $extension;
        $Adjunto->TipoAdjunto = $tipoAdjunto;
        $Adjunto->Modulo = $modulo;
        $Adjunto->Usuario = Auth::user()->id;

        $Adjunto->save();
        
        return (isset($Adjunto->id))? $Adjunto->id : false;
    }

    public function validaAdjunto($id,$tabla,$tipo = null)
    {
        if(is_null($tipo))
        {
            $tipo = '%';
        }

        $adjunto = Adjunto::where('idPadre', '=', $id)
                            ->where('Tabla', '=', $tabla)
                            ->where('TipoAdjunto', 'LIKE', $tipo)
                            ->first();
        
        if(is_null($adjunto))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    static function validateList($Objeto, $array, $clave){
        $count = 0;
        
        foreach ($Objeto as $row){
            if(in_array($row->$clave, $array)){
                $count++;
            } 
        }        
        return $count;
    }
   // Funcion que recibe el idValoracion y busca si se encuentra creado en estudio con el estado ING
    
    
    static function validaGestionObligacionesValoracion($idValoracion){
        $infoValoracion = Valoracion::find($idValoracion);
        
        //Si no existe la valoracion sera redireccionado a la vista de valoraciones donde se desplegara el respectivo mensaje de error.
        // O si el filtro es verdadero, significa que ya se le realizo, por tal razon debe ser redireccionado a la vista de valoracion
        if(!$infoValoracion || $infoValoracion->Filtro){
            return redirect(config('constantes.RUTA')."Valoraciones/".$idValoracion);
        }
        
    }
    
    static function validaGestionObligaciones($id){
        
        $Estudio = Estudio::where('id','=',$id)
                           ->where('estado','=','ing')
                           ->first();
        
        if(is_null($Estudio)){
            return false;
        }else{
            return true;
        }
            
        
    }
    
      /*
     * Funcion para obtener toda la informacion de un usuario
     */
    function getInfoUser($id){        
        $User = User::find($id);        
        return (isset($User) && $User != false)? $User : false;
    }  
    
    /**
 * Indica si una peticiÃ³n es ajax
 * @return boolean
 */
function isXmlHttpRequest(){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		return true;
	}
	return false;
}//isXmlHttpRequest

function format_number($number){
    if(isset($number) && !empty($number) && is_numeric($number)){
        return number_format($number, 0, ",", ".");
    }else{
        return 0;
    }
}

    static function convert_number_to_words($xcifra)
    { 
        $xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", 
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", 
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
        //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false))
        {
            if ($xpos_punto == 0)
            {
                $xcifra = "0".$xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for($xz = 0; $xz < 3; $xz++)
        {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While    
            while ($xexit)
            {
                if ($xi == $xlimite) // si ya lleg� al l�mite m&aacute;ximo de enteros
                {
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
                for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
                {
                    switch ($xy) 
                    {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            {
                            }
                            else
                            {
                                $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                                if ($xseek)
                                {
                                    $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100) 
                                        $xcadena = " ".$xcadena." CIEN ".$xsub;
                                    else
                                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                {
                                    $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " ".$xcadena." ".$xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma l�gica que las centenas)
                            if (substr($xaux, 1, 2) < 10)
                            {
                            }
                            else
                            {
                                $xseek = $xarray[substr($xaux, 1, 2)];
                                if ($xseek)
                                {
                                    $xsub = subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " ".$xcadena." VEINTE ".$xsub;
                                    else
                                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                                    $xy = 3;
                                }
                                else
                                {
                                    $xseek = $xarray[substr($xaux, 1, 1) * 10];
                                    if (substr($xaux, 1, 1) * 10 == 20)
                                        $xcadena = " ".$xcadena." ".$xseek;
                                    else    
                                        $xcadena = " ".$xcadena." ".$xseek." Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                            {
                            }
                            else
                            {
                                $xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = subfijo($xaux);
                                $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
            if (trim($xaux) != "")
            {
                switch ($xz)
                {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1 )
                        {
                            $xcadena = "CERO PESOS ";
                        }
                        if ($xcifra >= 1 && $xcifra < 2)
                        {
                            $xcadena = "UN PESO ";
                        }
                        if ($xcifra >= 2)
                        {
                            $xcadena.= " PESOS "; // 
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR    ($xz)
        return trim($xcadena);
    } // END FUNCTION


    static function subfijo($xx)
    { // esta funci�n regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //    
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    } // END FUNCTION

    function prueba(){
        return "erere";
    }
    
    function listComerciles(){
        $comerciales = User::where('Perfil', config('constantes.PERFIL_COMERCIAL'))
                           ->orWhere('Perfil', config('constantes.PERFIL_LIDER_COMERCIAL'))
                           ->orderBy("nombre", "ASC")->get();
        
        return (count($comerciales) > 0)? $comerciales : false;
    }


}

