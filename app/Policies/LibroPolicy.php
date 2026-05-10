<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Libro;

class LibroPolicy
{
    /**
     * Determina si el usuario puede ver la lista de libros (y el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_libro');
    }

    /**
     * Determina si puede ver el detalle de un libro.
     */
    public function view(User $user, Libro $libro): bool
    {
        return $user->can('view_libro');
    }

    public function create(User $user): bool
    {
        return $user->can('create_libro');
    }

    public function update(User $user, Libro $libro): bool
    {
        return $user->can('update_libro');
    }

    public function delete(User $user, Libro $libro): bool
    {
        return $user->can('delete_libro');
    }

    // Si usas SoftDeletes, añade estos también:
    public function restore(User $user, Libro $libro): bool
    {
        return $user->can('restore_libro');
    }

    public function forceDelete(User $user, Libro $libro): bool
    {
        return $user->can('force_delete_libro');
    }
}