<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'texto_opcion',
        'es_correcta',
        'user_create_id',
        'imagen_path',
        'tiene_imagen_opcion',
    ];

    protected $casts = [
        'tiene_imagen_opcion' => 'boolean',
        'es_correcta' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(fn($model) => $model->user_create_id = Auth::id());
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
