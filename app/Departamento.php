<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = "departamentos";

    protected $fillable = [
        "id_departamento",
        "departamento",
    ];
    
    public function municipios()
    {
        return $this->hasMany('App\Municipio','departamento_id','id_departamento');
    }
    

}
