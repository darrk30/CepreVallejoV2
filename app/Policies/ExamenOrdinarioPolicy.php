<?php

namespace App\Policies;

use App\Models\ExamenOrdinario;
use App\Models\User;

class ExamenOrdinarioPolicy
{
    /**
     * Ver la lista de exámenes ordinarios (y el ítem en el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_examen_ordinario');
    }

    /**
     * Ver el detalle de un examen ordinario.
     */
    public function view(User $user, ExamenOrdinario $examenOrdinario): bool
    {
        return $user->can('view_examen_ordinario');
    }

    public function create(User $user): bool
    {
        return $user->can('create_examen_ordinario');
    }

    public function update(User $user, ExamenOrdinario $examenOrdinario): bool
    {
        return $user->can('update_examen_ordinario');
    }

    public function delete(User $user, ExamenOrdinario $examenOrdinario): bool
    {
        return $user->can('delete_examen_ordinario');
    }

    /**
     * Eliminación en lote (bulk). Se apoya en el mismo permiso de eliminar.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_examen_ordinario');
    }

    public function restore(User $user, ExamenOrdinario $examenOrdinario): bool
    {
        return $user->can('restore_examen_ordinario');
    }

    public function forceDelete(User $user, ExamenOrdinario $examenOrdinario): bool
    {
        return $user->can('force_delete_examen_ordinario');
    }
}
