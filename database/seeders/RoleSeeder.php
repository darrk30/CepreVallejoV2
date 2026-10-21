<?php

namespace Database\Seeders;

use App\Support\PanelPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ROL ADMINISTRADOR
        $admin = Role::firstOrCreate(
            ['name' => 'Administrador', 'guard_name' => 'web']
        );
        // El admin recibe TODOS los permisos creados hasta el momento
        // syncPermissions es más seguro en seeders porque evita duplicados
        $admin->syncPermissions(Permission::all());

        // 2. ROL PROFESOR
        $profesor = Role::firstOrCreate(
            ['name' => 'Profesor', 'guard_name' => 'web']
        );
        $profesor->syncPermissions(PanelPermissions::profesor());

        // 3. ROL ALUMNO
        $alumno = Role::firstOrCreate(
            ['name' => 'Alumno', 'guard_name' => 'web']
        );
        $alumno->syncPermissions(PanelPermissions::alumno());
    }
}
