<?php

namespace App\Filament\Alumno\Resources\Reviews\Pages;

use App\Filament\Alumno\Resources\Reviews\ReviewResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['fecha_hora'] = now();
        $data['estado'] = 'activo';
        $data['titulo'] = 'Comentario';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Action::make('cancel')
                ->label('Cancelar')
                ->color('gray')
                ->url(ReviewResource::getUrl('index')),
        ];
    }
}