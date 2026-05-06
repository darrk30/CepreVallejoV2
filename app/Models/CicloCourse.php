<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class CicloCourse extends Pivot
{
    protected $fillable = [
        'ciclo_id',
        'course_id',
        'estado',
        'user_create_id',
    ];
    protected $table = 'ciclo_course';
    public $incrementing = true;

    // Un registro de "Curso en un Ciclo" tiene muchos docentes asignados
    public function cicloCourseTeachers(): HasMany
    {
        return $this->hasMany(CicloCourseTeacher::class, 'ciclo_course_id');
    }

    protected static function booted()
    {
        static::creating(function ($pivot) {
            // Esto se ejecutará cuando se cree la relación
            if (Auth::check()) {
                $pivot->user_create_id = Auth::id();
            }
        });
    }


    /**
     * RELACIÓN FALTANTE: De pivote a Curso
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * RELACIÓN FALTANTE: De pivote a Ciclo Académico
     */
    public function academicCycle()
    {
        return $this->belongsTo(AcademicCycle::class, 'ciclo_id');
    }

}
