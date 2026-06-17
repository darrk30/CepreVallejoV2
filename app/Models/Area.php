<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Area extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($area) {
            if (Auth::check()) {
                $area->user_create_id = Auth::id();
            }
        });
    }

    public function libros()
    {
        return $this->hasMany(Libro::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function carreras()
    {
        return $this->hasMany(Carrera::class);
    }

    public function examenes()
    {
        return $this->belongsToMany(ExamenOrdinario::class, 'area_examen_ordinario')
                    ->withPivot('puntaje_minimo')
                    ->withTimestamps();
    }
}
