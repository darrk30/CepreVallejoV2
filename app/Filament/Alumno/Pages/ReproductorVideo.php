<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Video;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;

class ReproductorVideo extends Page
{
    protected string $view = 'filament.alumno.pages.reproductor-video';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'ver-video/{videoSlug}';

    // 1. Esto hace que "Videoteca" se quede marcado en el menú lateral
    protected static ?string $navigationParentItem = 'Videoteca'; 

    public $video;

    public function mount($videoSlug)
    {
        $this->video = Video::where('slug', $videoSlug)->firstOrFail();
    }

    // 2. Generar las Migas de Pan (Breadcrumbs)
    public function getBreadcrumbs(): array
    {
        return [
            // Ruta a la lista de videos
            Videos::getUrl() => 'Videoteca', 
            // El video actual (sin URL para que no sea clickeable)
            $this->video->titulo, 
        ];
    }

    public function getRecommendedVideosProperty()
    {
        return Video::where('id', '!=', $this->video->id)
            ->where('area_id', $this->video->area_id)
            ->limit(10)
            ->get();
    }

    public function getEmbedUrl()
    {
        $url = $this->video->url;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]+)/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?autoplay=1";
        }
        return $url;
    }

    public function getHeading(): string
    {
        return ''; // Mantener vacío si no quieres título gigante arriba del reproductor
    }
}
