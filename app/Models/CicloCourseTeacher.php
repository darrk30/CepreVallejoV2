<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CicloCourseTeacher extends Model
{
    protected $table = 'ciclo_course_teacher';

    protected $fillable = ['ciclo_course_id', 'teacher_id', 'rol', 'estado', 'user_create_id'];

    public function teacher(): BelongsTo
    {
        // Asegúrate de que apunte a Teacher::class
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function cicloCourse(): BelongsTo
    {
        return $this->belongsTo(CicloCourse::class, 'ciclo_course_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(TeacherCourseContent::class, 'ciclo_course_teacher_id');
    }
}
