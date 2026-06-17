<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestasCorrecta extends Model
{
    protected $fillable = [
        'examen_ordinario_id', 
        'numero_pregunta', 
        'opcion_correcta',
        'asignatura',
        'bloque'
    ];

    public function examen()
    {
        return $this->belongsTo(ExamenOrdinario::class, 'examen_ordinario_id');
    }
}
