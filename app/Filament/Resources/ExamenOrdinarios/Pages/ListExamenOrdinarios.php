<?php

namespace App\Filament\Resources\ExamenOrdinarios\Pages;

use App\Filament\Resources\ExamenOrdinarios\ExamenOrdinarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamenOrdinarios extends ListRecords
{
    protected static string $resource = ExamenOrdinarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
