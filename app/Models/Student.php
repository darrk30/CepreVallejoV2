<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Student extends Model
{
    protected $fillable = [
        'apellidos',
        'dni',
        'telefono',
        'direccion',
        'estado',
        'user_id',
        'user_create_id',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscription::class);
    }

    protected static function booted()
    {
        static::creating(function ($content) {
            if (Auth::check()) {
                $content->user_create_id = Auth::id();
            }
        });
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}
