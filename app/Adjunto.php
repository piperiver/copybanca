<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $fillable =
    [
        'id', 'idPadre','Tabla','NombreArchivo','Extension', 'TipoAdjunto', 'Modulo', 'Usuario', 'created_at','updated_at'
    ];
}
