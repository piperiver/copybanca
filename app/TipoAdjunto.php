<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoAdjunto extends Model
{
    protected $table = "TiposAdjunto";
    protected $primaryKey = "Codigo"; // Se redefine la llave primaria
    public $incrementing = false;

    protected $fillable =
    [
        'Codigo', 'Descripcion','created_at','updated_at'
    ];
}
