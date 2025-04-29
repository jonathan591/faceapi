<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    //
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
