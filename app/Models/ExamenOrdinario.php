<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExamenOrdinario extends Model
{
    protected $fillable = [
        'titulo',
        'pdf_path',
        'duracion_minutos',
        'estado',
        'user_create_id'
    ];

    // El usuario (admin) que creó el examen
    public function creador()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    // Las 120 respuestas correctas
    public function respuestasCorrectas()
    {
        return $this->hasMany(RespuestasCorrecta::class);
    }

    // Todos los intentos de los alumnos en este examen
    public function intentos()
    {
        return $this->hasMany(Intento::class);
    }

    protected static function booted()
    {
        static::creating(function ($exam) {
            if (Auth::check()) {
                $exam->user_create_id = Auth::id();
            }
        });
    }
}
