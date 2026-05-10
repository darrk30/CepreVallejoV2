<?php

namespace App\Policies;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InscriptionPolicy
{
    /**
     * Determina si el usuario puede ver el listado de inscripciones.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inscription');
    }

    /**
     * Determina si el usuario puede ver una inscripción específica.
     */
    public function view(User $user, Inscription $inscription): bool
    {
        return $user->can('view_inscription');
    }

    /**
     * Determina si el usuario puede crear nuevas inscripciones (matricular).
     */
    public function create(User $user): bool
    {
        return $user->can('create_inscription');
    }

    /**
     * Determina si el usuario puede editar una inscripción.
     */
    public function update(User $user, Inscription $inscription): bool
    {
        return $user->can('update_inscription');
    }

    /**
     * Determina si el usuario puede eliminar una inscripción.
     */
    public function delete(User $user, Inscription $inscription): bool
    {
        return $user->can('delete_inscription');
    }

    /**
     * Métodos para Soft Deletes (Solo Admin)
     */
    public function restore(User $user, Inscription $inscription): bool
    {
        return $user->can('restore_inscription');
    }

    public function forceDelete(User $user, Inscription $inscription): bool
    {
        return $user->can('force_delete_inscription');
    }
}