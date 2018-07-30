<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable =
    [
        'Nombre','Entidad','Cargo','Telefono','Correo','Area','created_at','updated_at'
    ];
    
    protected $hidden = [
        'id'
    ];
}
