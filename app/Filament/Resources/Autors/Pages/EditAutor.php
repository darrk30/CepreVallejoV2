<?php

namespace App\Filament\Resources\Autors\Pages;

use App\Filament\Resources\Autors\AutorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAutor extends EditRecord
{
    protected static string $resource = AutorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
