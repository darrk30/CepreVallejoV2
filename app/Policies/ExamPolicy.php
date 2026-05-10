<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    /**
     * ¿Quién puede ver la lista de exámenes en el panel?
     */
    public function viewAny(User $user): bool
    {
        // Admin (permiso CRUD) o Profesor (permiso específico)
        return $user->can('view_any_exam') || $user->can('create_exam');
    }

    /**
     * ¿Quién puede ver el detalle de un examen?
     */
    public function view(User $user, Exam $exam): bool
    {
        return $user->can('view_exam') || $user->can('update_exam');
    }

    /**
     * ¿Quién puede crear nuevos exámenes?
     */
    public function create(User $user): bool
    {
        // Tanto el Admin como el Profesor tienen este permiso según el seeder
        return $user->can('create_exam');
    }

    /**
     * ¿Quién puede editar un examen?
     */
    public function update(User $user, Exam $exam): bool
    {
        // El Admin siempre puede (vía Gate::before)
        // El Profesor puede si tiene el permiso Y es el dueño del examen (opcional)
        if ($user->can('update_exam')) {
            return true; 
            // Si quieres que solo edite sus propios exámenes, usarías:
            // return $user->id === $exam->user_id;
        }

        return false;
    }

    /**
     * ¿Quién puede eliminar un examen?
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Según tu requerimiento, el CRUD completo es del Admin
        return $user->can('delete_exam');
    }

    /**
     * Métodos para Soft Deletes (Solo Admin)
     */
    public function restore(User $user, Exam $exam): bool
    {
        return $user->can('restore_exam');
    }

    public function forceDelete(User $user, Exam $exam): bool
    {
        return $user->can('force_delete_exam');
    }
}