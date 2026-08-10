<?php

namespace App\Filament\Alumno\Resources\Reviews\Pages;

use App\Filament\Alumno\Resources\Reviews\ReviewResource;
use App\Models\Review;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected string $view = 'filament.alumno.pages.reviews-list';

    public function getTitle(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getReviews(): LengthAwarePaginator
    {
        return Review::query()
            ->where('user_id', auth()->id())
            ->where('estado', 'activo')
            ->orderBy('id')
            ->paginate(12);
    }

    public function deleteReviewAction(): Action
    {
        return Action::make('deleteReview')
            ->label('Eliminar')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Borrar Comentario')
            ->modalDescription('¿Está seguro/a de hacer esto?')
            ->modalSubmitActionLabel('Borrar')
            ->action(function (array $arguments) {
                Review::query()
                    ->where('user_id', auth()->id())
                    ->findOrFail($arguments['record'])
                    ->update(['estado' => 'eliminado']);

                Notification::make()
                    ->title('Comentario eliminado')
                    ->success()
                    ->send();
            });
    }
}