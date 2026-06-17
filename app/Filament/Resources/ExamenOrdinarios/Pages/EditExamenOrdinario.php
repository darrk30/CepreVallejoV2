<?php

namespace App\Filament\Resources\ExamenOrdinarios\Pages;

use App\Filament\Resources\ExamenOrdinarios\ExamenOrdinarioResource;
use App\Imports\ClavesExamenImport;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EditExamenOrdinario extends EditRecord
{
    protected static string $resource = ExamenOrdinarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $data = $this->data;

        if (!empty($data['usar_excel_claves']) && !empty($data['excel_claves'])) {
            
            // 1. Borramos la cartilla anterior
            $record->respuestasCorrectas()->delete();

            // 2. Extraemos la nueva ruta
            $rutaArchivo = is_array($data['excel_claves']) 
                ? array_values($data['excel_claves'])[0] 
                : $data['excel_claves'];

            $filePath = Storage::disk('public')->path($rutaArchivo);

            // 3. Importamos el nuevo Excel
            Excel::import(new ClavesExamenImport($record->id), $filePath);
            
            // 4. Borramos el archivo
            if (Storage::disk('public')->exists($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }
        }
    }
}