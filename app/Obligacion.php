<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    protected $table = "Obligaciones";    
    public $incrementing = false; // No incrementable, tiene que ser public

    protected $fillable =
    [
        'id','NumeroObligacion', 'Valoracion','Entidad','Naturaleza','Calidad','SaldoMora','SaldoActual', 'SaldoActualOriginal',
        'CuotaTotal','ValorPagar','FechaApertura','FechaVencimiento','ValorInicial','ValorCuota','NumeroCuotasMora',
        'Compra','EstadoCuenta','Nit', 'Desprendible', 'Estado', 'calificacion', 'comportamiento', 'oficina', 'tipoCuenta', 
        'fechaActualizacion', 'cuotasVigencia', 'marca', 'CuotasProyectadas', 'FechaSolicitud', 'FechaEntrega',
        'FechaRadicacion', 'FechaVencimientoOb', 'EstadoCuentaCodigo', 'EstadoPlasticoCodigo', 'EstadoOrigenCodigo', 'EstadoPagoCodigo', 'FormaPagoCodigo',  'EstadoObligacion', 'created_at','updated_at' 
    ];
        
}