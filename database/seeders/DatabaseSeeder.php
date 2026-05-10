<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'kevin@gmail.com'],
            [
                'name' => 'Kevin Daniel',
                'password' => Hash::make('123123123'),
            ]
        );

        $admin->assignRole('Administrador');
    }
}