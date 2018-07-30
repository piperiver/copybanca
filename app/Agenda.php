<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{   
    protected $table = "agenda";
    protected $fillable =
    [
        'id', 'titulo','inicio','fin','usuario', 'descripcion', 'lugar', 'created_at','updated_at'
    ];
}
