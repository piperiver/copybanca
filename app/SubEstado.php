<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubEstado extends Model
{
  protected $table = "SubEstados";
  protected $primaryKey = "Codigo"; // Se redefine la llave primaria
  public $incrementing = false; // No incrementable, tiene que ser public

  protected $fillable =
  [
    'Codigo', 'Descripcion','Decision','created_at','updated_at'
  ];
}
