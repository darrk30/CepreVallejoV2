<?php

namespace App\Filament\Resources\AcademicCycles\Pages;

use App\Filament\Resources\AcademicCycles\AcademicCycleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAcademicCycle extends CreateRecord
{
    protected static string $resource = AcademicCycleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_create_id'] = Auth::id();
        return $data;
    }
}
