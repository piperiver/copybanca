<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntidadBancaria extends Model
{
    protected $table = "EntidadesBancarias";
    protected $primaryKey = "Id"; // Se redefine la llave primaria
    public $incrementing = false;

    protected $fillable =
    [
        'Id', 'Descripcion', 'CastigoMora', 'Politica', 'PazSalvo','created_at','updated_at','Entidades','DtoInicial','PuntajeData','PuntajeCifin'
    ];
}
