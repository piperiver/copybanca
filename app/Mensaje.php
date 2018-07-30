<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
  public $incrementing = false;
  
  protected $fillable =
  [
    'id', 'Mensaje','Causa','Solucion','created_at','updated_at'
  ];
}
