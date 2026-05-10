<?php

namespace App\Policies;

use App\Models\AcademicCycle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcademicCyclePolicy
{
    /**
     * Determina si el usuario puede ver la lista de ciclos académicos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_academic_cycle');
    }

    /**
     * Determina si el usuario puede ver un ciclo académico específico.
     */
    public function view(User $user, AcademicCycle $academicCycle): bool
    {
        return $user->can('view_academic_cycle');
    }

    /**
     * Determina si el usuario puede crear ciclos académicos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_academic_cycle');
    }

    /**
     * Determina si el usuario puede actualizar un ciclo académico.
     */
    public function update(User $user, AcademicCycle $academicCycle): bool
    {
        return $user->can('update_academic_cycle');
    }

    /**
     * Determina si el usuario puede eliminar un ciclo académico.
     */
    public function delete(User $user, AcademicCycle $academicCycle): bool
    {
        return $user->can('delete_academic_cycle');
    }

    /**
     * Métodos para borrado lógico (opcional)
     */
    public function restore(User $user, AcademicCycle $academicCycle): bool
    {
        return $user->can('restore_academic_cycle');
    }

    public function forceDelete(User $user, AcademicCycle $academicCycle): bool
    {
        return $user->can('force_delete_academic_cycle');
    }
}