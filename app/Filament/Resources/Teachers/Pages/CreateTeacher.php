<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Extraemos los datos del usuario que vienen del esquema 'user.name', etc.
        $userData = $data['user'];
        
        // 2. Creamos el usuario primero
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'], // Recuerda el cast 'hashed' en el modelo User
            'tipo_usuario' => 'docente', // Opcional: para identificar el rol
        ]);

        // 3. Quitamos 'user' del array original para que no ensucie la creación del Teacher
        unset($data['user']);

        // 4. Creamos el Teacher asignándole el ID del usuario recién creado
        return static::getModel()::create([
            ...$data,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Se ejecuta justo después de que el Docente y su Usuario 
     * relacionado han sido creados en la base de datos.
     */
    protected function afterCreate(): void
    {
        // Accedemos al registro recién creado (Teacher)
        $teacher = $this->record;
        if ($teacher->user) {
            $teacher->user->assignRole('Profesor');
        }
    }
    
    // Opcional: Redirigir al listado después de crear
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
