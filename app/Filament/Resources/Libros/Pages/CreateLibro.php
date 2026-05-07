<?php

namespace App\Filament\Resources\Libros\Pages;

use App\Filament\Resources\Libros\LibroResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibro extends CreateRecord
{
    protected static string $resource = LibroResource::class;
}
