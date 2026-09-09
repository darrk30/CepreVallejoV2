<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;

class SpecialtyPolicy
{
    /**
     * Ver la lista de categorías (y el ítem en el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_specialty');
    }

    /**
     * Ver el detalle de una categoría.
     */
    public function view(User $user, Specialty $specialty): bool
    {
        return $user->can('view_specialty');
    }

    public function create(User $user): bool
    {
        return $user->can('create_specialty');
    }

    public function update(User $user, Specialty $specialty): bool
    {
        return $user->can('update_specialty');
    }

    public function delete(User $user, Specialty $specialty): bool
    {
        return $user->can('delete_specialty');
    }

    /**
     * Eliminación en lote (bulk). Se apoya en el mismo permiso de eliminar.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_specialty');
    }

    public function restore(User $user, Specialty $specialty): bool
    {
        return $user->can('restore_specialty');
    }

    public function forceDelete(User $user, Specialty $specialty): bool
    {
        return $user->can('force_delete_specialty');
    }
}
