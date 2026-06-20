<?php

namespace App\Filament\Resources\Autors\Pages;

use App\Filament\Resources\Autors\AutorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutors extends ListRecords
{
    protected static string $resource = AutorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
