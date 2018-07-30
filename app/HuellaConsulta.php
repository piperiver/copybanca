<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HuellaConsulta extends Model
{
    protected $table = "HuellasConsulta";

    protected $fillable =
    [
        'id', 'Valoracion','Entidad','Fecha','CentralInformacion','created_at','updated_at'
    ];
}