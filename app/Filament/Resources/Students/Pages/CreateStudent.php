<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Crear el usuario con el 'name' (Nombres) proporcionado
        $user = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => Hash::make($data['user']['password']),
        ]);

        // 2. Vincular el ID del usuario al registro de Student
        $data['user_id'] = $user->id;

        // 3. Asignar el creador (Admin logueado)
        $data['user_create_id'] = Auth::id();

        return $data;
    }
}
