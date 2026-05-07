<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'puntaje_obtenido',
        'respuestas_enviadas',
        'fecha_inicio',
        'fecha_fin',
        'duracion_minutos_restantes',
        'estado',
    ];

    protected $casts = [
        'respuestas_enviadas' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'puntaje_obtenido' => 'float',
    ];

    /**
     * El examen que se está rindiendo.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * El alumno que rinde el examen.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}