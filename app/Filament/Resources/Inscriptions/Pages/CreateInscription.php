<?php

namespace App\Filament\Resources\Inscriptions\Pages;

use App\Filament\Resources\Inscriptions\InscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInscription extends CreateRecord
{
    protected static string $resource = InscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_create_id'] = Auth::id();
        return $data;
    }
}
