<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TeacherCourseContent extends Model
{
    protected $fillable = [
        'ciclo_course_teacher_id',
        'titulo',
        'descripcion',
        'archivo_path',
        'url_video',
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
}