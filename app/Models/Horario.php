<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    //
    protected $fillable = [
        'accion',
        'desde',
        'hasta'
    ];
}
