<?php

namespace App\Policies;

use App\Models\Autor;
use App\Models\User;

class AutorPolicy
{
    /**
     * Ver la lista de autores (y el ítem en el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_autor');
    }

    /**
     * Ver el detalle de un autor.
     */
    public function view(User $user, Autor $autor): bool
    {
        return $user->can('view_autor');
    }

    public function create(User $user): bool
    {
        return $user->can('create_autor');
    }

    public function update(User $user, Autor $autor): bool
    {
        return $user->can('update_autor');
    }

    public function delete(User $user, Autor $autor): bool
    {
        return $user->can('delete_autor');
    }

    /**
     * Eliminación en lote (bulk). Se apoya en el mismo permiso de eliminar.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_autor');
    }

    public function restore(User $user, Autor $autor): bool
    {
        return $user->can('restore_autor');
    }

    public function forceDelete(User $user, Autor $autor): bool
    {
        return $user->can('force_delete_autor');
    }
}
