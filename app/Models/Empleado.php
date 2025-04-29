<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    //
    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'email',
        'direccion',
        'cargo_id',
        'image',
    ];
    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
