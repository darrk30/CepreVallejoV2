<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Video;
use App\Models\Area;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Videos extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;
    protected string $view = 'filament.alumno.pages.videos';
    protected static ?string $title = 'Videoteca';
    protected static ?int $navigationSort = 11;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $areaId = null;

    protected int $perPageStep = 24;

    public int $perPage = 24;

    public function updatedSearch()
    {
        $this->perPage = $this->perPageStep;
    }

    public function updatedAreaId()
    {
        $this->perPage = $this->perPageStep;
    }

    public function loadMoreVideos()
    {
        $this->perPage += $this->perPageStep;
    }

    /**
     * Obtiene solo los videos que el usuario ha marcado como favoritos.
     */
    #[Computed]
    public function favoritos()
    {
        return Video::whereHas('favoritos', fn($q) => $q->where('user_id', auth()->id()))
            ->get();
    }

    /**
     * Query base de videos activos, excluyendo los que ya están en
     * favoritos. Sin memoizar: se reconstruye cada vez para no arrastrar el
     * ->take() del listado hacia el conteo total (o viceversa).
     */
    protected function videosBaseQuery()
    {
        return Video::query()
            ->where('estado', 'activo')
            ->when($this->search, fn($q) => $q->where('titulo', 'like', "%{$this->search}%"))
            ->when($this->areaId, fn($q) => $q->where('area_id', $this->areaId))
            // FIX: Excluir videos que ya pertenecen a los favoritos del usuario
            ->whereDoesntHave('favoritos', fn($q) => $q->where('user_id', auth()->id()));
    }

    /**
     * Total de videos que coinciden con el filtro actual (para saber si
     * mostrar el botón "Cargar más" y para el contador visible).
     */
    #[Computed]
    public function videosTotal()
    {
        return $this->videosBaseQuery()->count();
    }

    /**
     * Videos filtrados, limitados al lote actualmente cargado.
     */
    #[Computed]
    public function videos()
    {
        return $this->videosBaseQuery()->take($this->perPage)->get();
    }

    #[Computed]
    public function areas() 
    {
        return Area::all();
    }

    public function toggleFavorite($videoId) 
    {
        $video = Video::findOrFail($videoId);
        $user = auth()->user();

        if ($video->isFavoritedBy($user)) {
            $video->favoritos()->where('user_id', $user->id)->delete();
        } else {
            $video->favoritos()->create(['user_id' => $user->id]);
        }

        // Refrescar las propiedades computadas
        unset($this->favoritos);
        unset($this->videos);
        unset($this->videosTotal);
    }

    public function getHeading(): string
    {
        return '';
    }
}