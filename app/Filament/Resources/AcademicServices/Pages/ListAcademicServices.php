<?php

namespace App\Filament\Resources\AcademicServices\Pages;

use App\Filament\Resources\AcademicServices\AcademicServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcademicServices extends ListRecords
{
    protected static string $resource = AcademicServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
