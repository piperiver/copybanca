<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    protected $table = "CuentasBancarias";
    protected $primaryKey = ['Banco','EntidadDesembolso','TipoCuenta']; // Se redefine la llave primaria
    public $incrementing = false;

  protected $fillable =
  [
      'Banco', 'EntidadDesembolso','TipoCuenta','Cuenta','created_at','updated_at'
  ];
}
