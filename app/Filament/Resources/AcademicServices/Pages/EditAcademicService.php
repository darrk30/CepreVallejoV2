<?php

namespace App\Filament\Resources\AcademicServices\Pages;

use App\Filament\Resources\AcademicServices\AcademicServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicService extends EditRecord
{
    protected static string $resource = AcademicServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
