<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
        ];
    }

    // ESTO CARGA LOS DATOS AL ABRIR EL FORMULARIO
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Traemos el nombre y email del usuario vinculado
        $data['user']['name'] = $this->record->user?->name;
        $data['user']['email'] = $this->record->user?->email;

        return $data;
    }

    // ESTO GUARDA LOS CAMBIOS EN AMBAS TABLAS
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $student = $this->getRecord();

        // Actualizamos los datos en la tabla 'users'
        $userData = [
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
        ];

        // Solo actualizamos password si se llenó el campo
        if (!empty($data['user']['password'])) {
            $userData['password'] = Hash::make($data['user']['password']);
        }

        $student->user->update($userData);

        return $data;
    }
}
