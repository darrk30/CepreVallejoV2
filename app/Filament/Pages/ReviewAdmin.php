<?php

namespace App\Filament\Pages;

use App\Models\Review;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use UnitEnum;

class ReviewAdmin extends Page
{
    protected string $view = 'filament.pages.review-admin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Comentarios';

    protected static ?string $navigationLabel = 'Comentarios';
    protected static ?int $navigationSort = 16;

    #[Url]
    public string $search = '';

    public static function canAccess(): bool
    {
        return Auth::user()->can('view_all_review');
    }

    public function getReviews(): LengthAwarePaginator
    {
        return Review::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where('mensaje', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            })
            ->latest('fecha_hora')
            ->paginate(15);
    }
}