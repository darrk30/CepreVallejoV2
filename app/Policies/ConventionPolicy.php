<?php

namespace App\Policies;

use App\Models\Convention;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConventionPolicy
{
    /**
     * Determina si el usuario puede ver la lista de convenios.
     */
    public function viewAny(User $user): bool
    {
        // En el seeder lo definimos como 'convenio'
        return $user->can('view_any_convenio');
    }

    /**
     * Determina si el usuario puede ver un convenio específico.
     */
    public function view(User $user, Convention $convention): bool
    {
        return $user->can('view_convenio');
    }

    /**
     * Determina si el usuario puede crear convenios.
     */
    public function create(User $user): bool
    {
        return $user->can('create_convenio');
    }

    /**
     * Determina si el usuario puede actualizar un convenio.
     */
    public function update(User $user, Convention $convention): bool
    {
        return $user->can('update_convenio');
    }

    /**
     * Determina si el usuario puede eliminar un convenio.
     */
    public function delete(User $user, Convention $convention): bool
    {
        return $user->can('delete_convenio');
    }

    /**
     * Métodos para borrado lógico (opcional)
     */
    public function restore(User $user, Convention $convention): bool
    {
        return $user->can('restore_convenio');
    }

    public function forceDelete(User $user, Convention $convention): bool
    {
        return $user->can('force_delete_convenio');
    }
}