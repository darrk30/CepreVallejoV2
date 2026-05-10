<?php

namespace Database\Seeders;

use App\Models\Permission; // Tu modelo extendido
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de spatie para evitar conflictos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $actionLabels = [
            'view_any'     => 'Ver lista de',
            'view'         => 'Ver detalle de',
            'create'       => 'Crear',
            'update'       => 'Editar',
            'delete'       => 'Eliminar',
            'restore'      => 'Restaurar',
            'force_delete' => 'Borrar permanente',
        ];

        $modelLabels = [
            'video'          => 'Videos',
            'libro'          => 'Libros',
            'banner'         => 'Banners',
            'services'       => 'Servicios',
            'convenio'       => 'Convenios',
            'anuncio'        => 'Anuncios',
            'user'           => 'Usuarios',
            'student'        => 'Estudiantes',
            'teacher'        => 'Profesores',
            'exam'           => 'Exámenes',
            'academic_cycle' => 'Ciclos Académicos',
            'course'         => 'Cursos',
            'inscription'    => 'Inscripciones',
            'role'           => 'Roles',
            'permission'     => 'Permisos',
        ];

        $actions = array_keys($actionLabels);

        // Generación de permisos CRUD
        foreach ($modelLabels as $modelKey => $modelLabel) {
            foreach ($actions as $action) {
                $name = "{$action}_{$modelKey}";

                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'label'       => "{$actionLabels[$action]} {$modelLabel}",
                        'label_model' => $modelLabel,
                    ]
                );
            }
        }

        // Permisos Especiales
        $extraPermissions = [
            ['name' => 'access_admin_panel', 'label' => 'Acceso al Panel Administrativo', 'label_model' => 'Acceso'],
            ['name' => 'access_teacher_panel', 'label' => 'Acceso al Panel de Docentes', 'label_model' => 'Acceso'],
            ['name' => 'access_student_panel', 'label' => 'Acceso al Panel de Estudiantes', 'label_model' => 'Acceso'],
            ['name' => 'update_institution', 'label' => 'Actualizar datos de la Empresa', 'label_model' => 'Configuración'],
            ['name' => 'view_dashboard', 'label' => 'Acceder al Panel de Control', 'label_model' => 'Dashboard'],
            ['name' => 'view_aula_virtual', 'label' => 'Acceder al Aula Virtual', 'label_model' => 'Aula Virtual'],
            ['name' => 'create_section', 'label' => 'Crear Secciones de Curso', 'label_model' => 'Aula Virtual'],
            ['name' => 'update_section', 'label' => 'Editar Secciones de Curso', 'label_model' => 'Aula Virtual'],
            ['name' => 'delete_section', 'label' => 'Eliminar Secciones de Curso', 'label_model' => 'Aula Virtual'],
            ['name' => 'order_section', 'label' => 'Reordenar Secciones', 'label_model' => 'Aula Virtual'],
            ['name' => 'create_topic', 'label' => 'Subir Temas/Contenido', 'label_model' => 'Aula Virtual'],
            ['name' => 'update_topic', 'label' => 'Editar Temas/Contenido', 'label_model' => 'Aula Virtual'],
            ['name' => 'delete_topic', 'label' => 'Eliminar Temas/Contenido', 'label_model' => 'Aula Virtual'],
            ['name' => 'order_topic', 'label' => 'Reordenar Temas', 'label_model' => 'Aula Virtual'],
            ['name' => 'create_exam', 'label' => 'Crear Evaluaciones', 'label_model' => 'Exámenes'],
            ['name' => 'update_exam', 'label' => 'Editar Evaluaciones', 'label_model' => 'Exámenes'],
            ['name' => 'view_videoteca', 'label' => 'Ver Videoteca Académica', 'label_model' => 'Recursos'],
            ['name' => 'view_biblioteca', 'label' => 'Ver Biblioteca Digital', 'label_model' => 'Recursos'],
            ['name' => 'view_pagos_teacher', 'label' => 'Ver pagos del profesor', 'label_model' => 'Profesores'],
        ];

        foreach ($extraPermissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p['name'], 'guard_name' => 'web'],
                [
                    'label'       => $p['label'],
                    'label_model' => $p['label_model'],
                ]
            );
        }
    }
}
