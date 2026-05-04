<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Convention extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_path',
        'periodo',
        'representante',
        'estado_convenio',
        'estado',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($agreement) {
            if (Auth::check()) {
                $agreement->user_create_id = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}
