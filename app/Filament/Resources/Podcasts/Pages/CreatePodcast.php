<?php

namespace App\Filament\Resources\Podcasts\Pages;

use App\Filament\Resources\Podcasts\PodcastResource;
use App\Models\Podcast;
use Filament\Resources\Pages\CreateRecord;

class CreatePodcast extends CreateRecord
{
    protected static string $resource = PodcastResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Obtenemos el valor máximo actual de la columna 'orden'. Si no hay registros, devuelve 0.
        $maxOrden = Podcast::max('orden') ?? 0;
        
        // Asignamos el nuevo orden sumando 1 al valor máximo
        $data['orden'] = $maxOrden + 1;

        return $data;
    }
}
