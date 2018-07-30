<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogAprobacion extends Model
{
    protected $table = "log_aprobacion";

    protected $fillable = [
        "id",
        "estudio_id",
        "user_id",
        "aprobacion",
        "created_at",
        "updated_at"
    ];

    public function usuario(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function estudio(){
        return $this->belongsTo('App\Estudio', 'estudio_id');
    }
}
