<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademicCycle extends Model
{
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'año',
        'numero',
        'precio',
        'estado',
        'user_create_id'
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'estado' => 'boolean',
        ];
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'ciclo_course', 'ciclo_id', 'course_id')
            ->using(CicloCourse::class) // <--- Esto es lo que permite que Filament vea las relaciones del pivot
            ->withPivot('id', 'estado');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscription::class);
    }

    public function details()
    {
        return $this->hasMany(AcademicCycleDetail::class);
    }
}
