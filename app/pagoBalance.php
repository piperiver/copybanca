<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagoBalance extends Model
{
    protected $table = "pago_balance";
    
    protected $fillable = [        
                                            "id",
                                            "idPago",
                                            "idBalance",                                            
                                            "seguro",
                                            "interesMora",
                                            "interesCorriente",
                                            "abonoCapital"                                            
                                        ];
}
