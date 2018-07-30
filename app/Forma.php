<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forma extends Model
{
  protected $primaryKey = "Codigo";
  public $incrementing = false;

  protected $fillable =
  [
    'Codigo', 'Descripcion','Modulo','Ruta','Visible','Icono','created_at','updated_at'
  ];
}
