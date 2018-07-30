<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
  protected $primaryKey = "Codigo"; // Se redefine la llave primaria
  public $incrementing = false;

  protected $fillable =
  [
    'Codigo', 'Descripcion','idPadre','Forma','created_at','updated_at'
  ];
}
