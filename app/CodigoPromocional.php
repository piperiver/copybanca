<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoPromocional extends Model
{
    protected $table = "CodigosPromocionales";

    protected $fillable =
    [
        'id', 'Codigo','Usuario','Cliente','created_at','updated_at'
    ];
}
