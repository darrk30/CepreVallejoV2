<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TeacherCourseContent extends Model
{
    protected $fillable = [
        'ciclo_course_teacher_id',
        'titulo', // Ej: "Semana 01" o "Introducción"
        'descripcion',
        'orden', // Para que el profe pueda organizar sus semanas
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($content) {
            if (Auth::check()) {
                $content->user_create_id = Auth::id();
            }
        });
    }

    public function asignacionDocente(): BelongsTo
    {
        return $this->belongsTo(CicloCourseTeacher::class, 'ciclo_course_teacher_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'teacher_course_content_id');
    }

    // Relación con los temas específicos (Detalles)
    public function details()
    {
        return $this->hasMany(TeacherCourseContentDetail::class, 'teacher_course_content_id');
    }

    public function cicloCourseTeacher()
    {
        return $this->belongsTo(CicloCourseTeacher::class, 'ciclo_course_teacher_id');
    }
}
