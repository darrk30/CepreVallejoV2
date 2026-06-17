<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestasAlumno extends Model
{
    protected $fillable = [
        'intento_id', 
        'numero_pregunta', 
        'puntos_obtenidos', 
        'opcion_seleccionada'
    ];

    public function intento()
    {
        return $this->belongsTo(Intento::class);
    }

    
}
