<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Podcast;
use App\Models\Autor;
use Filament\Pages\Page;
use Livewire\Attributes\Url;
use Illuminate\Support\Collection;

class PodcastsAutor extends Page
{
    protected string $view = 'filament.alumno.pages.podcasts-autor';

    protected static bool $shouldRegisterNavigation = false;

    // Captura automáticamente ?autor_id=X desde la URL
    #[Url(as: 'autor_id')]
    public ?int $autor_id = null;

    /**
     * Obtiene la información del autor seleccionado
     */
    public function getAutorProperty(): ?Autor
    {
        // Faltaba el $this-> antes de autor_id
        return Autor::find($this->autor_id); 
    }

    public function getHeading(): string
    {
        return '';
    }

    /**
     * Obtiene solo los podcasts que pertenecen al autor recibido
     */
    public function getPodcastsProperty(): Collection
    {
        return Podcast::query()
            ->where('estado', true)
            ->where('autor_id', $this->autor_id) // Filtramos por el ID recibido
            ->get();
    }
}