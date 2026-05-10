<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ejecutar primero la estructura de seguridad
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // 2. Crear el usuario y guardarlo en una variable
        $admin = User::create([
            'name' => 'Kevin Daniel',
            'email' => 'kevin@gmail.com',
            'password' => Hash::make('123123123')
        ]);

        // 3. Asignar el rol de Administrador
        // Asegúrate de que el nombre sea exactamente como lo pusiste en RoleSeeder
        $admin->assignRole('Administrador');
    }
}