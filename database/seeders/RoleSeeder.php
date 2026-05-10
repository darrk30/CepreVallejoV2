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
        $admin = Role::create(['name' => 'Administrador']);
        // El admin recibe TODOS los permisos creados hasta el momento
        $admin->givePermissionTo(Permission::all());

        // 2. ROL PROFESOR
        $profesor = Role::create(['name' => 'Profesor']);
        $profesor->givePermissionTo([
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
        $alumno = Role::create(['name' => 'Alumno']);
        $alumno->givePermissionTo([
            'view_videoteca',
            'view_biblioteca',
            'view_aula_virtual',
            'access_student_panel',
        ]);
    }
}