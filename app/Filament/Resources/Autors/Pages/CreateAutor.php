<?php

namespace App\Filament\Resources\Autors\Pages;

use App\Filament\Resources\Autors\AutorResource;
use App\Models\Autor;
use Filament\Resources\Pages\CreateRecord;

class CreateAutor extends CreateRecord
{
    protected static string $resource = AutorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $maxOrden = Autor::max('orden') ?? 0;
        $data['orden'] = $maxOrden + 1;
        return $data;
    }
}
