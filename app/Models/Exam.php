<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Exam extends Model
{
    protected $fillable = [
        // 'teacher_course_content_id',
        'teacher_course_content_detail_id',
        'titulo',
        'descripcion',
        'duracion_minutos',
        'intentos_maximos',
        'puntaje_minimo',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($exam) {
            if (Auth::check()) {
                $exam->user_create_id = Auth::id();
            }
        });
    }
    

    public function participants()
    {
        return $this->belongsToMany(User::class, 'exam_attempts', 'exam_id', 'user_id')->distinct();
    }
    /**
     * Relación con el contenido (clase/tema) al que pertenece.
     */
    public function teacherCourseContent(): BelongsTo
    {
        return $this->belongsTo(TeacherCourseContent::class, 'teacher_course_content_id');
    }

    /**
     * Relación con el Usuario (Admin o Profe) que creó el examen.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

public function detail(): BelongsTo
{
    return $this->belongsTo(TeacherCourseContentDetail::class, 'teacher_course_content_detail_id');
}

    /**
     * Relación con las preguntas (lo crearemos en el siguiente paso).
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
