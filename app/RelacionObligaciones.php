<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    protected $fillable =
    [
        'id', 'idObligacion','SaldoActual','Pago','Indice'
    ];
}
