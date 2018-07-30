<?php

namespace App;

use Actuallymab\LaravelComment\Commentable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $nombre
 * @property string $tipo
 * @property string $sector
 * @property string $clasificacion
 * @property string $sitio_web
 * @property string $nit
 * @property string $domicilio
 * @property string $cuenta
 * @property string $tipo_cuenta
 * @property string $entidad_desembolso
 * @property Contacto[] $contactos
 */
class Acreedor extends Model
{
    use Commentable;
    public function mustBeApproved(){
        return false;
    }
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'acreedores';

    /**
     * @var array
     */
    protected $fillable = ['created_at', 'updated_at', 'nombre', 'tipo', 'sector', 'clasificacion', 'sitio_web', 'nit', 'domicilio', 'cuenta', 'tipo_cuenta', 'entidad_desembolso', 'ciudad', 'departamento'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactos()
    {
        return $this->hasMany('App\Contacto', 'acreedor_id');
    }
}
