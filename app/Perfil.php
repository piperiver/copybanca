<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
  protected $table = "Perfiles";
  protected $primaryKey = "Codigo"; // Se redefine la llave primaria
  public $incrementing = false;

  protected $fillable =
  [
    'Codigo', 'Descripcion','Estado','url_redireccionamiento','created_at','updated_at'
  ];
}
