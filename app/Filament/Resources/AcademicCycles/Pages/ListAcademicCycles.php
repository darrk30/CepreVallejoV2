<?php

namespace App\Filament\Resources\AcademicCycles\Pages;

use App\Filament\Resources\AcademicCycles\AcademicCycleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcademicCycles extends ListRecords
{
    protected static string $resource = AcademicCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
