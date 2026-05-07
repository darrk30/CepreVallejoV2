<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Anuncio extends Model
{
    protected $fillable = [
        'titulo',
        'contenido',
        'image_path',
        'url',
        'tipo',
        'estado',
        'user_create_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    /**
     * Relación con el Usuario que creó el anuncio.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    protected static function booted()
    {
        static::creating(function ($anuncio) {
            if (Auth::check()) {
                $anuncio->user_create_id = Auth::id();
            }
        });
    }

    /**
     * Filtra anuncios que están dentro de su rango de fecha.
     */
    public function scopeVigentes($query)
    {
        $now = now();
        return $query->where('estado', 'activo')
            ->where(function ($q) use ($now) {
                $q->whereNull('fecha_inicio')
                    ->orWhere('fecha_inicio', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', $now);
            });
    }
}
