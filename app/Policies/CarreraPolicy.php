<?php

namespace App\Policies;

use App\Models\Carrera;
use App\Models\User;

class CarreraPolicy
{
    /**
     * Ver la lista de carreras (y el ítem en el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_carrera');
    }

    /**
     * Ver el detalle de una carrera.
     */
    public function view(User $user, Carrera $carrera): bool
    {
        return $user->can('view_carrera');
    }

    public function create(User $user): bool
    {
        return $user->can('create_carrera');
    }

    public function update(User $user, Carrera $carrera): bool
    {
        return $user->can('update_carrera');
    }

    public function delete(User $user, Carrera $carrera): bool
    {
        return $user->can('delete_carrera');
    }

    /**
     * Eliminación en lote (bulk). Se apoya en el mismo permiso de eliminar.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_carrera');
    }

    public function restore(User $user, Carrera $carrera): bool
    {
        return $user->can('restore_carrera');
    }

    public function forceDelete(User $user, Carrera $carrera): bool
    {
        return $user->can('force_delete_carrera');
    }
}
