<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingresos_adicionales extends Model
{
    protected $table = "ingresos_adicionales";

    protected $fillable =
    [
        'id', 'id_estudio','tipo','valor'
    ];
}
