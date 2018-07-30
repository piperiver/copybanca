<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogCertificaciones extends Model
{   
    protected $table = "log_certificaciones";
    protected $fillable =
    [
        'id', 'id_estudio','nombre','cedula','valorLetras', 'pagaduria', 'valorCuota', 'diaCorte','mesVigencia',
        'anioVigencia','cuotaLetras','created_at','updated_at'
    ];
}
