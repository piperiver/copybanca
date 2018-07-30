<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    protected $table = "Valoraciones";
    
    protected $fillable =
    [
        'id', 'Usuario','PuntajeData','PuntajeCifin','Pagaduria','pagaduria_id','Comercial','created_at','updated_at','UsuarioCreacion', 'codSegData', 'numInformeCifin', 'codigoNombreArchivos', 'infoCentrales', 'Filtro'
    ];
    
    public function pagaduria_related()
    {
        return $this->belongsTo('App\Pagaduria','pagaduria_id');
    }

}