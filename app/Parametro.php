<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
  protected $primaryKey = "Codigo"; // Se redefine la llave primaria
  public $incrementing = false; // No incrementable, tiene que ser public

  protected $fillable =
  [
    'Codigo', 'Descripcion','Valor','Tipo','Modulo','created_at','updated_at'
  ];
}
