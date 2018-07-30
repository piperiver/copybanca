<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Causacion extends Model
{
    protected $table = "causacion";     
    
    protected $fillable = [        
                                            "id",
                                            "idEstudio",
                                            "fechaCausacion",
                                            "seguro",
                                            "interesMora",
                                            "interesCorriente",
                                            "abonoCapital",
                                            "created_at",
                                            "updated_at"
                                        ];
}
