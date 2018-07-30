<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Juicios extends Model
{
        protected $table = "juicios";
        
        protected $fillable = [        
                                    "id",
                                    "idProcesoJuridico",
                                    "ciudad",                                            
                                    "departamento",
                                    "estadoProceso",
                                    "expediente",
                                    "fechaInicioProceso",
                                    "fechaUltimoMovimiento",
                                    "idJuicio",
                                    "instanciaProceso",
                                    "nitsActor",
                                    "nombresActor",
                                    "nitsDemandado",
                                    "nombresDemandado",
                                    "numeroJuzgado",
                                    "rangoPretenciones",
                                    "tieneGarantias",
                                    "tipoDeCausa",
                                    "tipoJuzgado"                                           
                                ];
}
