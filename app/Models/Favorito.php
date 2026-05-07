<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorito extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_id',
        'favoritable_type',
    ];

    /**
     * Obtiene el modelo al que pertenece el favorito (Libro o Video).
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * El usuario que dio el "corazón".
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}