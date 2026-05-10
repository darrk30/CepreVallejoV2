<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determina si el usuario puede ver la lista de cursos en el panel administrativo.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course');
    }

    /**
     * Determina si el usuario puede ver el detalle de un curso.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->can('view_course');
    }

    /**
     * Determina si el usuario puede registrar nuevos cursos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_course');
    }

    /**
     * Determina si el usuario puede actualizar la información de un curso.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->can('update_course');
    }

    /**
     * Determina si el usuario puede eliminar un curso.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->can('delete_course');
    }

    /**
     * Métodos para Soft Deletes (opcional)
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->can('restore_course');
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can('force_delete_course');
    }
}