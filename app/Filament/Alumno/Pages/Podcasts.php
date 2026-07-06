<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Autor;
use App\Models\Podcast;
use App\Models\PodcastProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Podcasts extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMicrophone;

    protected static ?string $navigationLabel = 'Podcasts';
    protected static ?string $title = 'Podcasts Académicos';
    protected string $view = 'filament.alumno.pages.podcasts';
    protected static ?int $navigationSort = 13;

    protected int $perPageStep = 20;

    public int $perPage = 20;

    public function loadMorePodcasts()
    {
        $this->perPage += $this->perPageStep;
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getAutoresProperty(): Collection
    {
        return Autor::where('estado', true)
            ->orderBy('orden', 'asc')
            ->get();
    }

    /**
     * Query base de podcasts activos. Sin memoizar: se reconstruye cada vez
     * para no arrastrar el ->take() del listado hacia el conteo total (o
     * viceversa).
     */
    protected function podcastsBaseQuery()
    {
        // Usamos 'with('autor')' para traer la relación en la misma consulta
        return Podcast::query()
            ->where('estado', true)
            ->with('autor')
            ->orderBy('orden', 'asc'); // Opcional: si quieres respetar el orden que configuramos
    }

    /**
     * Total de podcasts activos (para saber si mostrar "Cargar más").
     */
    public function getPodcastsTotalProperty(): int
    {
        return $this->podcastsBaseQuery()->count();
    }

    public function getPodcastsProperty(): Collection
    {
        return $this->podcastsBaseQuery()->take($this->perPage)->get();
    }
}