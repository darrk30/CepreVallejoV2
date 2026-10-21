<?php

namespace App\Support;

/**
 * Permisos que pertenecen a cada panel (rol). Es la fuente única: la usan
 * RoleSeeder (para asignar permisos a Profesor/Alumno) y RoleForm (para
 * separar los permisos en tabs dentro del formulario de roles).
 *
 * Todo lo que no esté en profesor() ni en alumno() se considera del panel
 * administrador.
 */
class PanelPermissions
{
    public static function profesor(): array
    {
        return [
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
            'view_videoteca',
            'view_biblioteca',
            'view_podcasts',
        ];
    }

    /**
     * Recursos de consumo (videoteca, biblioteca, podcasts). Se muestran en la
     * pestaña Alumno del formulario de roles, aunque también se pueden activar
     * para el rol Profesor.
     */
    public static function recursos(): array
    {
        return ['view_videoteca', 'view_biblioteca', 'view_podcasts'];
    }

    public static function alumno(): array
    {
        return [
            'view_videoteca',
            'view_biblioteca',
            'view_podcasts',
            'view_aula_virtual',
            'access_student_panel',
            'create_review',
            'view_any_review',
            'update_review',
            'delete_review',
        ];
    }
}
