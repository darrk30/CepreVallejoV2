<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // Carga los datos del usuario en el formulario al abrirlo
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user']['name'] = $this->record->user->name;
        $data['user']['email'] = $this->record->user->email;

        return $data;
    }

    // Guarda los cambios tanto en Teacher como en User
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Actualizar datos del usuario vinculado
        $record->user->update($data['user']);

        // Limpiar y actualizar el docente
        unset($data['user']);
        $record->update($data);

        return $record;
    }
}
