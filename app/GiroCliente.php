<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiroCliente extends Model
{
    protected $table = "GirosCliente";

    protected $fillable =
    [
        'id', 'Estudio','TipoGiro','Valor','created_at','updated_at'
    ];
}