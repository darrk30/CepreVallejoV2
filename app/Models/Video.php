<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Video extends Model
{
    use Favoritable;
    use HasFactory;

    protected $fillable = [
        'titulo',
        'slug',
        'codigo',
        'descripcion',
        'url',
        'image_path',
        'area_id',
        'estado',
        'orden',
        'user_create_id',
    ];

    protected static function booted()
    {
        static::creating(function ($video) {
            if (Auth::check()) {
                $video->user_create_id = Auth::id();
            }
            $video->slug = Str::slug($video->nombre . '-' . $video->codigo);
        });

        static::updating(function ($video) {
            if ($video->isDirty('nombre') || $video->isDirty('codigo')) {
                $video->slug = Str::slug($video->nombre . '-' . $video->codigo);
            }
        });
    }

    /**
     * Relación con el Área académica.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con el Usuario que registró el video.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}