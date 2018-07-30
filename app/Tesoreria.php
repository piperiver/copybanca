<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tesoreria extends Model
{
    protected $table = "Tesoreria";

    protected $fillable =
    [
        'id', 'Descripcion','created_at','updated_at'
    ];
}
