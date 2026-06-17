<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Enums\AreaAcademica;

class Carrera extends Model
{
    protected $fillable = [
        'area',
        'nombre', 
        'estado', 
        'user_create_id'
    ];

    // Magia de Laravel: Convierte el string de la BD al objeto Enum automáticamente
    protected $casts = [
        'area' => AreaAcademica::class,
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function intentos()
    {
        return $this->hasMany(Intento::class);
    }

    protected static function booted()
    {
        static::creating(function ($carrera) {
            if (Auth::check()) {
                $carrera->user_create_id = Auth::id();
            }
        });
    }
}