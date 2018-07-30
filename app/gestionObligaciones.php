<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class gestionObligaciones extends Model
{
    protected $table = "gestionObligaciones";

    protected $fillable =
    [
        'id','id_obligacion', 'id_adjunto','id_adjuntoSolicitud', 'fechaSolicitud','fechaEntrega','fechaRadicacion','fechaVencimiento','estado','created_at','updated_at'
    ];
}