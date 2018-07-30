<?php

namespace App;

use Actuallymab\LaravelComment\Commentable;
use Illuminate\Database\Eloquent\Model;

class SolicitudConsulta extends Model
{
    use Commentable;

    protected $table = "solicitudes_consulta";

    protected $fillable =
        [
            'id', 
            'cedula', 
            'apellido', 
            'nombre', 
            'telefono', 
            'email', 
            'departamento', 
            'municipio', 
            'pagaduria_id', 
            'clave_desprendible', 
            'user_id',
            'estado',
            'descripcion_devolucion',
            'valoracion_id',
            'banco',
            'updated_at',
            'created_at'
        ];

    
    /**
     * Retorna el estado de la solicitud
     *
     * @author Vanessa Quintero
     * @param  string  $value
     * @return string
     */
    public function getEstadoAttribute($value){
        
        return $this->estadosLst()[$value];

    }

    /**
     * Retorna el listado de estados que pueden tener la solicitud
     *
     * @author Vanessa Quintero 
     * @return array
     */

    public function mustBeApproved(){
        return false;
    }

    public function estadosLst(){
        
        return array(
            0 => 'Pendiente',
            1 => 'Completa',
            2 => 'Devuelta',
        );

    }
    
    
    public function pagaduria()
    {
        return $this->belongsTo('App\Pagaduria');
    }

    public function valoracion(){
        return $this->belongsTo('App\Valoracion', 'valoracion_id');
    }

    public function usuario(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
