<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudMasiva extends Model
{
    protected $table = "solicitudes_masivas";

    protected $fillable =
        [
            'id', 'nombre', 'archivo', 'comentario', 'user_id', 'estado'
        ];

    public function usuario()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function archivo(){
        return Adjunto::where('idPadre', '=', $this->id)
                ->where('Tabla', '=', 'solicitudes_consulta')
                ->first();
    }
}
