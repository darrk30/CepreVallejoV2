<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Libro;
use App\Models\Area;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Biblioteca extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected string $view = 'filament.alumno.pages.biblioteca';
    protected static ?string $title = 'Mi Biblioteca Digital';
    protected static ?string $navigationLabel = 'Biblioteca';

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $areaId = null;

    /**
     * Obtener libros favoritos del usuario
     */
    #[Computed]
    public function favoritos()
    {
        return Libro::whereHas('favoritos', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('area')->get();
    }

    /**
     * Obtener libros filtrados, EXCLUYENDO los que ya están en favoritos
     */
    #[Computed]
    public function libros()
    {
        return Libro::query()
            ->with('area')
            ->where('estado', 'activo')
            // FIX: Excluir libros que ya son favoritos del usuario
            ->whereDoesntHave('favoritos', fn($q) => $q->where('user_id', auth()->id()))
            ->when($this->search, fn($q) => $q->where(
                fn($sub) =>
                $sub->where('nombre', 'like', "%{$this->search}%")
                    ->orWhere('autor', 'like', "%{$this->search}%")
            ))
            ->when($this->areaId, fn($q) => $q->where('area_id', $this->areaId))
            ->orderBy('orden')
            ->get();
    }

    #[Computed]
    public function areas()
    {
        return Area::all();
    }

    /**
     * Acción para agregar/quitar de favoritos con refresco de cache
     */
    public function toggleFavorite($libroId)
    {
        $libro = Libro::findOrFail($libroId);
        $user = auth()->user();

        if ($libro->isFavoritedBy($user)) {
            $libro->favoritos()->where('user_id', $user->id)->delete();
        } else {
            $libro->favoritos()->create([
                'user_id' => $user->id,
            ]);
        }

        // Forzamos a Livewire a recalcular las listas en el mismo request
        unset($this->favoritos);
        unset($this->libros);
    }

    public function getHeading(): string
    {
        return '';
    }
}
