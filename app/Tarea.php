<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'id_estudio', 'concepto', 'created_at','updated_at'
    ];

}
