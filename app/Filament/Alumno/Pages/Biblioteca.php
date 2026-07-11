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
    protected static ?int $navigationSort = 9;

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

    public function loadMoreLibros()
    {
        $this->perPage += $this->perPageStep;
    }

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
     * Query base de libros filtrados, EXCLUYENDO los que ya están en favoritos.
     * Sin memoizar: se reconstruye cada vez para no arrastrar el ->take()
     * del listado hacia el conteo total (o viceversa).
     */
    protected function librosBaseQuery()
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
            ->orderBy('orden');
    }

    /**
     * Total de libros que coinciden con el filtro actual (para saber si
     * mostrar el botón "Cargar más" y para el contador visible).
     */
    #[Computed]
    public function librosTotal()
    {
        return $this->librosBaseQuery()->count();
    }

    /**
     * Libros filtrados, limitados al lote actualmente cargado.
     */
    #[Computed]
    public function libros()
    {
        return $this->librosBaseQuery()->take($this->perPage)->get();
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
        unset($this->librosTotal);
    }

    public function getHeading(): string
    {
        return '';
    }
}
