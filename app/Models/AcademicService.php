<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AcademicService extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen_path',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            // Asigna automáticamente al usuario creador
            if (Auth::check()) {
                $service->user_create_id = Auth::id();
            }
        });

        static::saved(fn () => Cache::forget('home.page.data'));
        static::deleted(fn () => Cache::forget('home.page.data'));
    }

    // Relación para saber qué usuario del sistema registró este servicio
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}