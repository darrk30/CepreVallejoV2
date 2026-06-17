<?php

namespace App\Filament\Resources\ExamenOrdinarios\Pages;

use App\Filament\Resources\ExamenOrdinarios\ExamenOrdinarioResource;
use App\Imports\ClavesExamenImport;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CreateExamenOrdinario extends CreateRecord
{
    protected static string $resource = ExamenOrdinarioResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        // Buscar el archivo directamente en el disco
        $archivos = Storage::disk('public')->files('temp-excel-claves');

        \Log::info('Archivos en temp-excel-claves:', $archivos);

        if (empty($archivos)) {
            \Log::warning('No hay archivos en temp-excel-claves');
            return;
        }

        // Tomar el más reciente
        $rutaArchivo = collect($archivos)->sortByDesc(function ($file) {
            return Storage::disk('public')->lastModified($file);
        })->first();

        \Log::info('Archivo seleccionado:', [$rutaArchivo]);

        $filePath = Storage::disk('public')->path($rutaArchivo);

        $record->respuestasCorrectas()->delete();
        Excel::import(new ClavesExamenImport($record->id), $filePath);
        Storage::disk('public')->delete($rutaArchivo);

        \Log::info('Import completado. Total:', [$record->respuestasCorrectas()->count()]);
    }
}
