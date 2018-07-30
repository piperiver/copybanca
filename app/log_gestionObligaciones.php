<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class log_gestionObligaciones extends Model
{
    protected $table = "log_gestionObligaciones";

    protected $fillable =
    [
        'id', 'id_gestionObligacion','id_obligacion', 'id_adjunto','id_adjuntoSolicitud', ' fechaSolicitud','fechaEntrega','fechaRadicacion','fechaVencimiento','estado', 'usuario','created_at','updated_at'
    ];
}