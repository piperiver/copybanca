<?php

namespace App\Librerias;

use DB;
use App\Adjunto;
use App\TipoAdjunto;
use App\Librerias\UtilidadesClass;
use Illuminate\Support\Facades\Auth;


class ComponentAdjuntos{

    /*
     * Funcion para desplegar el componente de adjuntos, es recomendable que si se desea que el componente muestre el select de tipos de adjuntos, 
     * se llame dentro de un div con col-md-12 para que ocupe todo el ancho. Si ya se le asocia el tipo de adjunto por debajo el componente no mostrara el select de tipos de adjunto
     * en ese caso el componente es recomendable desplegarlo en un col-md-8 con un col-md-offset-2 para que quede centrado
     * Parametros:
     * @parametro $idPadre = identificador del elemento con el cual quedara asociado el adjunto que se cargue
     * @parametro $tabla = mas que el nombre de la tabla donde pertenece el identificados ($idPadre), este campo funciona como key o palabra reservada para diferenciar los adjuntos. por ejemplo
                            * si tengo dos tipos de adjuntos, uno para estudio y otro para valoracion. lo ideal es que el key para los adjuntos de valoracion sea diferente al de los adjuntos de estudio, para que de esta manera
                            * al tratar de consultar los adjuntos de cualquiera de los grupos se busque por el key que se le definio (solo con el key se trae todo ese grupo de adjuntos) y por el identificador($idPadre) del adjunto (en caso de traer un adjunto en especial)
     * @parametro $tipoAdjunto = puede recibir 3 cosas:
     *                                                  1. un string con el codigo del tipo de adjunto. cuando se utiliza de esta manera, graficamente el componente ignora el select con el tipo de adjuntos y no lo despliega
     *                                                  todos los adjuntos que se carguen quedaran asociados a el tipo de adjunto enviado
     *                                                  2. Un array con el codigo de los tipos de adjuntos que desea mostrar. Esta opcion muestra el select con los tipos de adjuntos que especifico. Nota. Si ud especifica
     *                                                      un codigo que no existe en la base de datos, el componente lo ignorara y solamente mostrara en el select los tipos de adjunto que existan en la base de datos
     *                                                  3. un booleano en falso. De esta manera el componente mostrara en el select todos los tipos de adjuntos que existan en a tabla tiposAdjuntos
     * @parametro $modulo = es un string con el codigo del modulo del que van a hacer parte los adjuntos, Estos para mantener un orden y asi facilitar la busqueda de los adjuntos
     * @parametro $tipoAdjuntoIgnore = Array con los codigos de tipos de adjunto que desea ignorar o que no se muestren en el select
     * @parametro $accionAlFinalizar =  string con el nombre de la funcionalidad que desea utilizar. se utiliza para decirle al componente que hacer cuando termine de subir el archivo. En el momento tiene 3 opciones
     *                                                          1. refresh = Lo que hace es actualizar la pagina donde este
     *                                                          2. locked = lo que hace el blouear el boton inputFile para que no suba mas archivos
     *                                                          3. clear= lo que hace volver al estado normal el input file
     *                                                          4. function = si necesita realizar un proceso especifico puede crear una funcion en javascript y pasarle el nombre en el parametro $function. Esa funcion que ud cree
     *                                                              recibira por defecto tres parametros:
     *                                                                  A. input = es un objeto con el inputFile para cambiar al boton inputFile o hacer lo que desee con el 
     *                                                                  B. datos = es un objeto con la informacion que ud le envio al componente y tiene esta estructura:
                        *                                                                   $otrosDatos = [
                                                                                                   "idPadre" => $idPadre,
                                                                                                   "tabla" => $tabla,
                                                                                                   "modulo" => $modulo,            
                                                                                                   "tipoAdjunto" => $opcionAdjunto,
                                                                                                   "accionAlFinalizar" => $accionAlFinalizar,
                                                                                                   "function" => $function            
                                                                                               ]; 
     *                                                                  C. El returno de la funcion en php que ejecute, en caso de que lo haga.  
     * @parametro $dspTabla = funciona para mostrar una tabla con los adjuntos ya cargados que cumplan con los criterios enviados al componente. La tabla podra visualizarce siempre y 
     *                                              cuando exista un div con el id=container_cargaTablaAdjuntosCargados el cual sera donde se desplegara la tabla. Esta tabla se actualiza automaticamente
     *                                              cada vez que se cargue un nuevo archivo  
     * @parametro $nombreAdjunto = recibe un String con el nombre que tendra ese archivo
     * @parametro $contenedorListAdjuntos = identificador del contenedor de la tabla de adjuntos, para poder reemplazar los datos automaticamente
     * @parametro $functionPHP = nombre de la funcion de php que desea ejecutar cuando se suba el adjunto. Nota: la funcion debe estar en: librerias/FuncionesComonente.php y recibe el array otrosDatos como objeto.
     */
    function dspFormulario($idPadre = false, $tabla = false, $tipoAdjunto = false, $modulo = false, $tipoAdjuntoIgnore = false, $accionAlFinalizar = "locked", $function = false, $dspTabla = false, $nombreAdjunto = false, $contenedorListAdjuntos = false, $functionPHP = false){
        
        $text = "";        
        if(!$idPadre){
            $text .= "<li>El campo idPadre es necesario para el funcionamiento del componente</li>";
        }
        if(!$tabla){
            $text.= "<li>El campo tabla es necesario para el funcionamiento del componente</li>";
        }        
        if(!$modulo){
            $text.= "<li>El campo modulo es necesario para el funcionamiento del componente</li>";
        }        

        $resultAdjunto = false;
        
        if($tipoAdjunto == false){
            $tiposAdjunto = TipoAdjunto::all()->toArray();
            $opcionAdjunto = false;
        }elseif(is_array ($tipoAdjunto)){                
            $tiposAdjunto = TipoAdjunto::find($tipoAdjunto)->toArray();            
            if(count($tiposAdjunto) == 0){
                $text.= "<li>Los Codigos de tipo adjunto definidos al componente  no existen en la tabla tipo_adjuntos.</li>";
            }else{
                $opcionAdjunto = false;            
            }
        }else{
            $resultAdjunto = TipoAdjunto::find($tipoAdjunto);
            if(is_null($resultAdjunto)){
                $text.= "<li>El Codigo de tipo adjunto definido no existe en la tabla tipo_adjuntos.</li>";
            }else{
                $opcionAdjunto = $tipoAdjunto;
                $tiposAdjunto = false;
            }            
        }
        
        if(!empty($text)){
            echo '<div class="alert alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>ERROR!!</strong>
                    <p>No se podra visualizar el componente ya que se presentaron los siguiente errores:</p>
                    <ul>'.$text.'</ul>
                  </div>';
            return;
        }
                
        $otrosDatos = [
            "idPadre" => $idPadre,
            "tabla" => $tabla,
            "modulo" => $modulo,            
            "tipoAdjunto" => $opcionAdjunto,
            "accionAlFinalizar" => $accionAlFinalizar,
            "function" => $function,
            "dspTabla" => $dspTabla,
            "contenedorListAdjuntos" => $contenedorListAdjuntos,
            "nombreAdjunto"=> $nombreAdjunto,
            "functionPHP" => $functionPHP
        ];
        
        echo view('componentes.adjuntos.dspFormulario')->with('tiposAdjunto', $tiposAdjunto)
                                                                                        ->with('otrosDatos', encrypt(json_encode($otrosDatos)))
                                                                                        ->with('resultAdjunto', $resultAdjunto)
                                                                                        ->with('nombreAdjunto', $nombreAdjunto)
                                                                                        ->with('tipoAdjuntoIgnore', $tipoAdjuntoIgnore)
                                                                                        ->with('idObjectAdjunto', bin2hex(random_bytes(7)))
                                                                                        ->with("idElements", $idPadre);
        
    }

    function save($idTabla,$tabla,$NombreArchivo,$extension,$tipoAdjunto,$modulo, $objArchivo){
        
        if(empty($idTabla)){
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [identificador de la tabla vacio]"]);
            die();
        }else if(empty($tabla)){
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [Nombre de la tabla vacia]"]);
           die();
        }else if(empty($NombreArchivo)){
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [Nombre del archivo vacio]"]);
           die();
        }else if(empty($extension)){
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [extension del archivo vacio]"]);
           die();
        }else if(empty($tipoAdjunto)){
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [tipo de adjunto vacio]"]);
           die();
        }else if(empty($modulo)){
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [modulo vacio]"]);
           die();        
        }
        
        $resultAdjunto = TipoAdjunto::find($tipoAdjunto);        
        
        if(is_null($resultAdjunto)){
            echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [codigo tipo adjunto invalido]"]);
            die();
            $text.= "<li></li>";
        }
         

        DB::beginTransaction();

        $Adjunto = new Adjunto();
        $Adjunto->idPadre = $idTabla;
        $Adjunto->Tabla = $tabla;
        $Adjunto->NombreArchivo = $NombreArchivo;
        $Adjunto->Extension = $extension;
        $Adjunto->TipoAdjunto = $tipoAdjunto;
        $Adjunto->Modulo = $modulo;
        $Adjunto->Usuario = Auth::id();

        $Adjunto->save();
        
        if($Adjunto->id > 0 && isset($objArchivo) && $objArchivo != false){
            $existCartepa = (file_exists(storage_path("adjuntos"))) ? true : mkdir(storage_path("adjuntos"), 0755);
            if($existCartepa){
                $subio = \Storage::disk('adjuntos')->put($Adjunto->id, \File::get($objArchivo));
                //$objArchivo->storeAs("Adjuntos", $Adjunto->id);
                if($subio){
                    DB::commit();
                    return $Adjunto->id;
                }else{
                    DB::rollBack();
                    echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [Error Storage::disk]"]);
                    die();                    
                }
            }else{
                DB::rollBack();                
                echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar cargar el archivo. Por favor comuníquese con soporte [La carpeta adjuntos no existe]"]);
                die();
            }            
        }else{
           echo json_encode(["STATUS" => false, "MENSAJE" => "Ocurio un problema al intentar almacenar el archivo en la base de datos. Por favor recargue la pagina e intente de nuevo y si el problema persiste, comuníquese con soporte [Error insert]"]);
           die();
        }

        
    }
    
    
    function getUrlViewAdjunto($idPadre, $modulo = false, $tabla = false, $tipoAdjunto = false){
        $adjunto = $this->adjunto_exist($idPadre, $modulo, $tabla, $tipoAdjunto);
        echo '<a class="color-negro" title="Visualizar" href="'.  config("constantes.RUTA").'/visualizar/'.$adjunto[0]->id.'" target="_blank"><span class="fa fa-paperclip  color-negro" style="font-size:15px"></span></a>';
    }
    function getUrlDownloadAdjunto($idPadre, $modulo = false, $tabla = false, $tipoAdjunto = false){
        $adjunto = $this->adjunto_exist($idPadre, $modulo, $tabla, $tipoAdjunto);
        echo '<a class="color-negro" title="Visualizar" href="'.  config("constantes.RUTA").'/descargar/'.$adjunto[0]->id.'" target="_blank"><span class="fa fa-paperclip color-negro" style="font-size:15px"></span></a>';
    }
    
     /*
     * funcion para validar si existe o no el adjunto
     */
    function adjunto_exist($idPadre, $modulo = false, $tabla = false, $tipoAdjunto = false){
        
        $where[] = ["idPadre", "=", $idPadre];
        if($modulo != false){
            $where[] = ["Modulo", "=", $modulo];
        }
        if($tabla != false){
            $where[] = ["Tabla", "=", $tabla];
        }
        if($tipoAdjunto != false){
            $where[] = ["TipoAdjunto", "=", $tipoAdjunto];
        }
        
        return DB::table('adjuntos')->where($where)->orderBy("TipoAdjunto")->get();
        }
    
     /*
     * funcion para construir una tabla con los adjuntos ya existentes
     */
    
    function createTableOfAdjuntos($idPadre, $modulo = false, $tabla = false, $tipoAdjunto = false, $nombreFuncion = false, $functionPHP = false){
        $adjuntos = $this->adjunto_exist($idPadre, $modulo, $tabla, $tipoAdjunto);
        $nombreFuncion = ($nombreFuncion != false)? "delete_".$nombreFuncion : false;
        if(count($adjuntos) > 0){
            $html = '
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>                            
                            <th>Ver</th>    
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($adjuntos as $adjunto){
                $idUnicoElements = uniqid();
                $html .= '
                     <tr id="'.$idUnicoElements.'">
                            <td>'.$adjunto->NombreArchivo.'</td>
                            <td>'.$adjunto->TipoAdjunto.'</td>
                            <td><a class="color-negro" title="Visualizar" href="'.config('constantes.RUTA').'visualizar/'.$adjunto->id.'" target="_blank"><span class="fa fa-paperclip fa-1x color-negro"></span></a></td>                                                        
                            <td class="text-center"><a title="Eliminar" style="cursor: pointer" data-funcionphp="'.$functionPHP.'" class="deleteAdjunto color-redA margin-left-5" data-nombrefuncion="'.$nombreFuncion .'" data-infoadjunto=\''.json_encode($adjunto).'\' data-delparent="#'.$idUnicoElements.'" data-adjunto='.$adjunto->id.' data-url="'.config('constantes.RUTA').'EliminarAdjunto">
                                        <span class="fa fa-remove"></span>
                                    </a></td>
                        </tr>';
            }
            $html .= '
                    </tbody>
                </table>';            
        }else{
            $html =  '<div class="alert alert-warning" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Mensaje:</strong>
                    <p>No hay adjuntos cargados hasta el momento</p>                    
                  </div>';
        }
        
        $UtilidadesClass = new UtilidadesClass();
        if($UtilidadesClass->isXmlHttpRequest()){
                return $html;
            }else{
                echo $html;
            }
    }

}