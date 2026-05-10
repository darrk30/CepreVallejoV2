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

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $areaId = null;

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
     * Obtiene los videos activos, excluyendo los que ya están en favoritos.
     */
    #[Computed]
    public function videos() 
    {
        return Video::query()
            ->where('estado', 'activo')
            ->when($this->search, fn($q) => $q->where('titulo', 'like', "%{$this->search}%"))
            ->when($this->areaId, fn($q) => $q->where('area_id', $this->areaId))
            // FIX: Excluir videos que ya pertenecen a los favoritos del usuario
            ->whereDoesntHave('favoritos', fn($q) => $q->where('user_id', auth()->id()))
            ->get();
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
    }

    public function getHeading(): string
    {
        return '';
    }
}