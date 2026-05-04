<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Institution extends Model
{
    protected $fillable = [
        'razon_social',
        'ruc',
        'whatsapp',
        'correo',
        'direccion',
        'nosotros',
        'logo_path',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'estado',
    ];
}