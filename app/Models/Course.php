<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'slug',
        'nombre',
        'descripcion',
        'imagen_path',
        'horas_semanales',
        'area_id',
        'user_create_id',
        'estado',
    ];

    protected static function booted()
    {
        static::creating(function ($course) {
            if (Auth::check()) {
                $course->user_create_id = Auth::id();
            }
            $course->slug = Str::slug($course->nombre . '-' . $course->codigo);
        });

        static::updating(function ($course) {
            if ($course->isDirty('nombre') || $course->isDirty('codigo')) {
                $course->slug = Str::slug($course->nombre . '-' . $course->codigo);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function contents()
    {
        return $this->hasMany(CourseContent::class);
    }

    public function academicCycles()
    {
        return $this->belongsToMany(AcademicCycle::class, 'ciclo_course', 'course_id', 'ciclo_id')
            ->using(CicloCourse::class)
            ->withPivot('id', 'estado')
            ->withTimestamps();
    }
}
