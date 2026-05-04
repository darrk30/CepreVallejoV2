<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
