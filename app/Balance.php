<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = "balance";
    
    protected $fillable = [        
                                            "id",
                                            "idEstudio",
                                            "idPago",
                                            "idCausacion",
                                            "seguro",
                                            "interesMora",
                                            "interesCorriente",
                                            "abonoCapital",
                                            "balance",
                                            "saldoCapital",
                                            "saldoTotal"
                                        ];
}
