<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Libro extends Model
{
    use Favoritable;
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'slug',
        'descripcion',
        'autor',
        'area_id',
        'image_path',
        'url',
        'estado',
        'orden',
        'user_create_id',
    ];

    /**
     * Relación con el Área a la que pertenece el libro.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con el Usuario que creó el registro.
     */
    public function creator(): BelongsTo
    {
        // Especificamos 'user_create_id' porque no sigue la convención 'user_id'
        return $this->belongsTo(User::class, 'user_create_id');
    }

    protected static function booted()
    {
        static::creating(function ($libro) {
            if (Auth::check()) {
                $libro->user_create_id = Auth::id();
            }
            $libro->slug = Str::slug($libro->nombre . '-' . $libro->codigo);
        });

        static::updating(function ($libro) {
            if ($libro->isDirty('nombre') || $libro->isDirty('codigo')) {
                $libro->slug = Str::slug($libro->nombre . '-' . $libro->codigo);
            }
        });
    }
}