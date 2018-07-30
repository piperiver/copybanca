<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
  protected $primaryKey = ['Perfil','Forma']; // Se redefine la llave primaria
  public $incrementing = false;

  protected $fillable =
  [
    'Perfil', 'Forma','Insertar','Actualizar','Eliminar','created_at','updated_at'
  ];
}
