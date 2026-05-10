<?php

namespace Database\Seeders;

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
        $profesor->syncPermissions([
            'view_aula_virtual',
            'create_section',
            'update_section',
            'order_section',
            'create_topic',
            'update_topic',
            'order_topic',
            'create_exam',
            'delete_topic',
            'delete_section',
            'view_pagos_teacher',
            'update_exam',
            'access_teacher_panel',
        ]);

        // 3. ROL ALUMNO
        $alumno = Role::firstOrCreate(
            ['name' => 'Alumno', 'guard_name' => 'web']
        );
        $alumno->syncPermissions([
            'view_videoteca',
            'view_biblioteca',
            'view_aula_virtual',
            'access_student_panel',
        ]);
    }
}
