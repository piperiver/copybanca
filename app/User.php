<?php

namespace App;

use Actuallymab\LaravelComment\CanComment;
use Actuallymab\LaravelComment\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use CanComment;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nombre', 'primerApellido', 'apellido', 'cedula', 'fecha_expedicion', 'sexo', 'fecha_nacimiento', 'email',
        'telefono', 'estado', 'perfil', 'password', 'pagaduria',
        'ciudad',
        'departamento',
        'municipio',
        'tipo_de_vivienda',
        'direccion',
        'estrato',
        'telefono_fijo',
        'nivel_de_estudios',
        'estado_civil',
        'personas_a_cargo',
        'rp1_nombre',
        'rp1_direccion',
        'rp1_telefono_fijo',
        'rp1_celular',
        'rp2_nombre',
        'rp2_direccion',
        'rp2_telefono_fijo',
        'rp2_celular',
        'rfa1_nombre',
        'rfa1_direccion',
        'rfa1_telefono_fijo',
        'rfa1_celular',
        'rfa1_parentesco',
        'rfa2_nombre',
        'rfa2_direccion',
        'rfa2_telefono_fijo',
        'rfa2_celular',
        'rfa2_parentesco',
        'otros_ingresos',
        'total_activos',
        'gastos_familiares',
        'total_pasivos',
        'fecha_ingreso',
        'tipo_pension',
        'nombre_titular',
        'cedula_titular',
        'estatura',
        'peso',
        'estado_salud',
        'numero_identificacion_bn1',
        'nombre_bn1',
        'primer_apellido_bn1',
        'segundo_apellido_bn1',
        'parentesco_bn1',
        'porcentaje_participacion_bn1',
        'numero_identificacion_bn2',
        'nombre_bn2',
        'primer_apellido_bn2',
        'segundo_apellido_bn2',
        'parentesco_bn2',
        'porcentaje_participacion_bn2',
        'numero_identificacion_bn3',
        'nombre_bn3',
        'primer_apellido_bn3',
        'segundo_apellido_bn3',
        'parentesco_bn3',
        'porcentaje_participacion_bn3',
        'numero_identificacion_bn4',
        'nombre_bn4',
        'primer_apellido_bn4',
        'segundo_apellido_bn4',
        'parentesco_bn4',
        'porcentaje_participacion_bn4',
        'numero_identificacion_conyuge',
        'nombre_conyuge',
        'celular_conyuge',
        'numero_de_cuenta',
        'tipo_cuenta',
        'banco',
        'comentario',
        'deportes',
        'deportes_explicacion',
        'fuma',
        'fuma_explicacion',
        'estudios',
        'estudios_explicacion',
        'deficiencias',
        'deficiencias_explicacion',
        'estado_salud',
        'estado_salud_explicacion',
        'representante_legal',
        'documento_representante_legal',
        'tipo_de_persona'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin(){
        return True;
    }

    public function solicitud()
    {
        return $this->hasOne('App\SolicitudConsulta');
    }

    public function aprobacion()
    {
        return $this->hasOne('App\LogAprobacion');
    }

    public function nombres()
    {
        return $this->nombre . " " . $this->primerApellido . " " . $this->apellido;
    }

    public function toJsonData()
    {
        $pagaduria = Pagaduria::where('nombre', $this->pagaduria)->first();
        $this->pagaduria = $pagaduria->nombre;
        $this->nombre_de_la_empresa = $pagaduria->nombre;
        $this->direccion_de_la_empresa = $pagaduria->direccion;
        $this->telefono_de_la_empresa = $pagaduria->telefono;
        if ($pagaduria->tipo == 'Pensionados') {
            $this->ocupacion_pensionado = "X";
        } else {
            $this->ocupacion_empleado = "X";
        }
        $this->full_name = $this->nombre . " " . $this->primerApellido . " " . $this->apellido;
        if ($this->sexo == "M") {
            $this->genero_masculino = "X";
        } else {
            $this->genero_femenino = "X";
        }
        $apellidos = explode(' ', $this->apellido);
        if (isset($apellidos[0])) {
            $this->primer_apellido = $apellidos[0];
            if (isset($apellidos[1])) {
                $this->segundo_apellido = $apellidos[1];
            }
        }
        $this->nombres = $this->nombre;
        $this->cell_phone = $this->telefono;
        $this->security_code = hash('sha256', $this->cedula . date('Ymd') . '00');
        return $this;
    }
}
