<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class Pagaduria extends Model
{

    protected $table = 'pagadurias';
    protected $fillable = ['id',
            'nombre', 'tipo',
        'telefono', 'orden',
        'tipo_de_descuento',
        'tipo_empresa', 'descuentos_permitidos',
        'operadores', 'direccion',
        'contacto', 'email',
        'nit', 'dia_reporte',
        'tipo_visacion', 'ciudad',
        'created_at', 'updated_at',
        'provectus','codigo'
        ];

    public function parameters()
    {
        return array(
            'nomina' => ['Activos', 'Pensionados'],
            'descuentos_permitidos' => [1, 2, 3, 4],
            'dia_reporte' => ["Primer a tercer día", "Cuarto al sexto día", "Septimo al octavo día" , "Noveno día", "Decimo día", 'Onceavo día', 'Doceavo día', 'Treceavo día', 'Catorceavo día'],
            'tipo_visacion' => ["Oficio", "Mail", "Tercero"],
            'tipo_empresa' => ["Publica", "Privada"],
            'tipos_de_descuentos' => ['NORMAL', 'PROTECCIONISTA', 'AGRESIVA']
        );
    }

    public function solicitudes()
    {
        return $this->hasMany('App/SolicitudConsulta');
    }

    public function valoraciones()
    {
        return $this->hasMany('App/Valoracion');
    }

    public function calcularCupo($ingreso, $egreso, $reg_especial)
    {
        $calculo = new stdClass();
        $calculo->ingreso = $ingreso;
        $calculo->egreso = $egreso;
        $ingreso = intval(str_replace('.', '', $ingreso));
        $retes = intval(str_replace('.', '', $egreso));
        $calculo_cupo = 0;
        $ftr_soli = 1;
        $ftr_ley = 50;
        $salario_minimo = 781242;
        $descuento_de_ley = 0;
        $fondo_solidaridad = False;
        if ($ingreso > ($salario_minimo * 4) && $reg_especial == "off") {
            if ($this->tipo != "Pensionados") {
                $retes += (($ingreso * $ftr_soli) / 100);
            }
            $fondo_solidaridad = True;
        }
        if ($this->tipo == "Pensionados") {
            $ftr_sal = 12;
            $salud_pension = (($ingreso * $ftr_sal) / 100);
            if ($reg_especial == "on") {
                $descuento_de_ley = $retes;
                $salud_pension = 0;
            } else {
                $descuento_de_ley = (($ingreso * $ftr_sal) / 100) + $retes;
            }
        } else if ($this->tipo == "Activos") {
            $ftr_pensal = 8;
            $salud_pension = (($ingreso * $ftr_pensal) / 100);
            $descuento_de_ley = (($ingreso * $ftr_pensal) / 100) + $retes;
            $calculo_cupo = 1;
            if($this->tipo_de_descuento == "NORMAL"){
                $calculo_cupo = 0;
            } else if ($this->tipo_de_descuento == "PROTECCIONISTA") {
                if ($ingreso > ($salario_minimo * 2)) {
                    $calculo_cupo = 0;
                } else {
                    $descuento_de_ley = (($ingreso * $ftr_pensal) / 100);
                }
            } else if ($this->tipo_de_descuento == "AGRESIVA") {
                if ($ingreso > ($salario_minimo * 2)) {
                    $descuento_de_ley = (($ingreso * $ftr_pensal) / 100) + $retes;
                    $calculo_cupo = 2;
                } else {
                    $descuento_de_ley = (($ingreso * $ftr_pensal) / 100);
                }
            }
        }
        if ($calculo_cupo == 0) {
            $cupo = (($ingreso - $descuento_de_ley) * $ftr_ley) / 100;
            $calculo->cupo = floor($cupo);
        } else if ($calculo_cupo == 2) {
            $cupo = (($ingreso - $descuento_de_ley) * $ftr_ley) / 100;
            $cupo_2 = $ingreso - $descuento_de_ley - $salario_minimo;
            $calculo->cupo = floor($cupo);
            $calculo->cupo_2 = floor($cupo_2);
        } else {
            $cupo = $ingreso - $descuento_de_ley - $salario_minimo;
            $calculo->cupo = floor($cupo);
        }
        $calculo->pagaduria_datos = $this;
        $calculo->descuentos_ley = intval($descuento_de_ley);
        $calculo->reg_especial = $reg_especial;
        $calculo->salud_pension = intval($salud_pension);
        if($fondo_solidaridad){
            $calculo->salud_pension += intval(($ingreso * $ftr_soli) / 100);
        }
        return $calculo;
    }
}