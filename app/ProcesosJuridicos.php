<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcesosJuridicos extends Model
{
        protected $table = "procesosJuridicos";

        protected $fillable = [
                                    "id",
                                    "idValoracion",
                                    "fechaConsulta",
                                    "respuestaWs",
                                    "usuario",
                                    "status",
                                    "mensajeError",
                                    "descripcionMensajeError"
                                ];

       public function juicios(){
           return $this->hasMany('App\Juicios', 'idProcesoJuridico', 'id');
       } 
}
