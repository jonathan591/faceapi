<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    //
    protected $fillable = [
        'empleado_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado'
    ];
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
