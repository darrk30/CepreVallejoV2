<?php

namespace App\Filament\Alumno\Resources\Reviews\Pages;

use App\Filament\Alumno\Resources\Reviews\ReviewResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected string $view = 'filament.alumno.pages.review';

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Eliminar')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Borrar Comentario')
            ->modalDescription('¿Está seguro/a de hacer esto?')
            ->modalSubmitActionLabel('Borrar')
            ->action(function () {
                $this->record->update(['estado' => 'eliminado']);
                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('cancel')
                ->label('Cancelar')
                ->color('gray')
                ->url(ReviewResource::getUrl('index')),
        ];
    }
}