<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intento extends Model
{
    protected $fillable = [
        'user_id', 
        'examen_ordinario_id',
        'carrera_id', 
        'puntaje_obtenido', 
        'es_aprobado', 
        'fecha_inicio', 
        'fecha_fin', 
        'tiempo_utilizado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'es_aprobado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function examen()
    {
        return $this->belongsTo(ExamenOrdinario::class, 'examen_ordinario_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    // Las opciones que marcó el alumno en esta sesión
    public function respuestasAlumno()
    {
        return $this->hasMany(RespuestasAlumno::class);
    }


public function user()
{
    return $this->belongsTo(User::class);
}
}
