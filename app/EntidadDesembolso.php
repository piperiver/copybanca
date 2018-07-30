<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntidadDesembolso extends Model
{
    protected $table = "EntidadesDesembolso";
    protected $primaryKey = "Nit"; // Se redefine la llave primaria
    public $incrementing = false;

    protected $fillable =
    [
        'Nit', 'Descripcion','created_at','updated_at'
    ];
}
