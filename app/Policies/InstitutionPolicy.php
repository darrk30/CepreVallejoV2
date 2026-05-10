<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InstitutionPolicy
{
    /**
     * Determina si el usuario puede ver la información de la institución.
     */
    public function viewAny(User $user): bool
    {
        // Usamos el permiso personalizado para ver y editar
        return $user->can('update_institution');
    }

    /**
     * Determina si el usuario puede ver el detalle.
     */
    public function view(User $user, Institution $institution): bool
    {
        return $user->can('update_institution');
    }

    /**
     * Determina si el usuario puede crear una institución.
     * Generalmente retornamos false porque ya existe un registro único.
     */
    public function create(User $user): bool
    {
        return false; 
    }

    /**
     * Determina si el usuario puede actualizar los datos.
     */
    public function update(User $user, Institution $institution): bool
    {
        return $user->can('update_institution');
    }

    /**
     * Determina si el usuario puede eliminar la institución.
     * Retornamos false por seguridad institucional.
     */
    public function delete(User $user, Institution $institution): bool
    {
        return false;
    }
}