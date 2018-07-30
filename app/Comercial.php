<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

    class Comercial extends Model
{
    protected $table = "Comerciales";

    protected $fillable =
    [
        'id', 'Nombre','Email','Telefono','created_at','updated_at'
    ];
}
