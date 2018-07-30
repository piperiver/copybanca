<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormaDePago extends Model
{
    protected $table = "FormasDePago";
    protected $primaryKey = "Codigo"; // Se redefine la llave primaria
    public $incrementing = false;

    protected $fillable =
    [
        'Codigo', 'Descripcion','created_at','updated_at'
    ];
}