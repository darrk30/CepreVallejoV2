<?php

namespace App\Policies;

use App\Models\Anuncio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnuncioPolicy
{
    /**
     * Determina si el usuario puede ver la lista de anuncios en el panel.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_anuncio');
    }

    /**
     * Determina si el usuario puede ver un anuncio específico.
     */
    public function view(User $user, Anuncio $anuncio): bool
    {
        return $user->can('view_anuncio');
    }

    /**
     * Determina si el usuario puede crear anuncios.
     */
    public function create(User $user): bool
    {
        return $user->can('create_anuncio');
    }

    /**
     * Determina si el usuario puede actualizar un anuncio.
     */
    public function update(User $user, Anuncio $anuncio): bool
    {
        return $user->can('update_anuncio');
    }

    /**
     * Determina si el usuario puede eliminar un anuncio.
     */
    public function delete(User $user, Anuncio $anuncio): bool
    {
        return $user->can('delete_anuncio');
    }

    /**
     * Métodos para borrado lógico (opcional)
     */
    public function restore(User $user, Anuncio $anuncio): bool
    {
        return $user->can('restore_anuncio');
    }

    public function forceDelete(User $user, Anuncio $anuncio): bool
    {
        return $user->can('force_delete_anuncio');
    }
}