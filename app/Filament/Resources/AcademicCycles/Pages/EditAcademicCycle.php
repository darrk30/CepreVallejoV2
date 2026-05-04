<?php

namespace App\Filament\Resources\AcademicCycles\Pages;

use App\Filament\Resources\AcademicCycles\AcademicCycleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicCycle extends EditRecord
{
    protected static string $resource = AcademicCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
