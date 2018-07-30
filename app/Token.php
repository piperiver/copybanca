<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable =
    [
        'id', 'Token','Estado','created_at','updated_at'
    ];
}
