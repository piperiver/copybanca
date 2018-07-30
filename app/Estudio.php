<?php

namespace App;

use Actuallymab\LaravelComment\Commentable;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Estudio extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use Commentable;

    protected $fillable =
    [
        'id', 'Valoracion','Tasa','Plazo','Cuota','ValorCredito', 'IngresoBase','TotalEgresos','ValorCompras','Disponible',
        'VlrCuotaCompras','TipoContrato','AntiguedadMeses','Pagaduria','EmbargoActual', 'RamaJudicial','Sector','Estado', 
        'Cupo', 'MesesRetiroForzoso', 'Edad', 'FechaInicioContrato', 'FechaFinContrato', 'MesesFinContrato', 'Seguro', 
        'MesesVigenciaSeguro', 'PlazoMaximo', 'CuotaMaxima', 'CapDescuentoDesprendible', 'GastoFijo', 'Capacidad', 'DatosBanco', 
        'Saldo', 'Desembolso', 'Ley1527', 'Garantia', 'DatosCostos', 'DatosBeneficios', 'cuotaVisado', 'viabilizado','valorXmillon', 
        'costoSeguro', 'cargo', 'ComercialCartera', 'BancoFinal', 'ValorAprobadoBanco', 'EstadoCartera', 'created_at','updated_at',
        'ajusteCostos','aprobado'
    ];
    public function mustBeApproved(){
        return false;
    }

    protected $auditInclude = [
        'Estado',
    ];

    public function valoracion(){
        return $this->hasOne('App\Valoracion', 'id');
    }

    public function aprobacion(){
        return $this->hasOne('App\LogAprobacion');
    }
}