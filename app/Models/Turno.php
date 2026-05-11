<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $fillable = ['nombre', 'slug'];

    /**
     * Relación con las inscripciones (un turno tiene muchos alumnos)
     */
    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Relación con las asignaciones docente (un turno tiene muchos cursos/profesores)
     */
    public function cicloCourseTeachers(): HasMany
    {
        return $this->hasMany(CicloCourseTeacher::class);
    }
}
