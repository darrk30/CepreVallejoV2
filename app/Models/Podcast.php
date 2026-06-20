<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Podcast extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'url_audio',
        'imagen_portada',
        'duracion_minutos',
        'orden',
        'estado',
    ];

    protected function streamUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->url_audio
                ? Storage::disk('public')->url($this->url_audio)
                : null
        );
    }

    public function getPodcastsProperty(): Collection
    {
        return Podcast::where('estado', true)
            ->orderBy('orden', 'asc') // <-- Ordenar por tu nueva columna
            ->get();
    }

    public function autor()
    {
        return $this->belongsTo(Autor::class, 'autor_id');
    }
}
