<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BancoEntidades extends Model
{
    protected $table = "bancoentidades";
    protected $fillable =
    [
        'id', 'nombre','created_at','updated_at'
    ];
}
