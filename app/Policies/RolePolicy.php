<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * ¿Puede ver la lista de roles en el panel?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_role');
    }

    /**
     * ¿Puede ver el detalle de un rol?
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('view_role');
    }

    /**
     * ¿Puede crear nuevos roles?
     */
    public function create(User $user): bool
    {
        return $user->can('create_role');
    }

    /**
     * ¿Puede editar un rol existente?
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('update_role');
    }

    /**
     * ¿Puede eliminar un rol?
     */
    public function delete(User $user, Role $role): bool
    {
        // Regla de oro: No permitir borrar el rol Administrador de raíz
        if ($role->name === 'Administrador') {
            return false;
        }

        return $user->can('delete_role');
    }

    /**
     * Métodos para Soft Deletes (Opcional)
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->can('restore_role');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        // Protegemos el rol Admin incluso del borrado permanente
        if ($role->name === 'Administrador') {
            return false;
        }

        return $user->can('force_delete_role');
    }
}