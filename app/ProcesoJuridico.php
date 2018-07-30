<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcesoJuridico extends Model
{
    protected $table = "ProcesosJuridicos";

    protected $fillable =
    [
        'id', 'Valoracion','Ciudad','Departamento','EstadoProceso','Expediente','FechaInicioProceso',
        'FechaUltimoMovimiento','IdJuicio','InstanciaProceso','NitsActor','NombresActor','NitsDemandados','NombresDemandado','NumeroJuzgado',
        'RangoPretenciones','TieneGarantias','TipoDeCausa','TipoJuzgado','created_at','updated_at'
    ];
}
