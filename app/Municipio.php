<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = "municipios";

    protected $fillable = [
        "id_municipio",
        "municipio",
        "estado",
        "departamento_id",
    ];
    
    public function departamento(){
        return $this->belongsTo('App\Departamento','departamento_id','id_municipio');
    }
}
