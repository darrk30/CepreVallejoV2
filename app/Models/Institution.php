<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Institution extends Model
{
    protected $fillable = [
        'razon_social',
        'ruc',
        'whatsapp',
        'correo',
        'direccion',
        'nosotros',
        'vision',
        'mision',
        'logo_path',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'estado',
    ];

    protected static function booted(): void
    {
        // Se ejecuta después de crear o actualizar un registro
        static::saved(function ($institution) {
            Cache::forget('institution_whatsapp');
        });

        // Se ejecuta si eliminas el registro (por seguridad)
        static::deleted(function ($institution) {
            Cache::forget('institution_whatsapp');
        });
    }
}