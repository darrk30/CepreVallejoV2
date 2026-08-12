<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'fecha_hora',
        'estado',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    // Constantes para los estados (evita "magic strings" en el código)
    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_ELIMINADO = 'eliminado';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope útil para filtrar solo reseñas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function getMensajeLimpioAttribute(): string
    {
        return strip_tags($this->mensaje);
    }

    

    
}