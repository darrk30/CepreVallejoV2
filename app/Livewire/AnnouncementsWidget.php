<?php

namespace App\Livewire;

use App\Models\Anuncio;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AnnouncementsWidget extends Widget
{
    protected string $view = 'livewire.announcements-widget';

    // Ocupa todo el ancho del dashboard
    protected int | string | array $columnSpan = 'full';

    /**
     * Definimos qué panel debe filtrar este widget.
     * Puedes cambiarlo manualmente o dejarlo en 'todos' por defecto.
     */
    public string $panelType = 'alumno';

    public static function canView(): bool
    {
        $currentPanelId = Filament::getCurrentPanel()?->getId();

        if (!$currentPanelId) return false;

        // Comprobamos si existe al menos un anuncio activo para este panel
        return Anuncio::query()
            ->where('estado', 'activo')
            ->whereIn('panel', [$currentPanelId, 'todos'])
            ->where(function ($query) {
                $query->whereNull('fecha_inicio')
                    ->orWhere('fecha_inicio', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            })
            ->exists(); // Usamos exists() que es mucho más rápido que get()
    }

    public function getAnuncios()
    {
        $currentPanelId = Filament::getCurrentPanel()->getId();

        return Anuncio::query()
            ->where('estado', 'activo')
            ->whereIn('panel', [$currentPanelId, 'todos'])
            ->where(function ($query) {
                $query->whereNull('fecha_inicio')
                    ->orWhere('fecha_inicio', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            })
            ->latest()
            ->get();
    }
}
