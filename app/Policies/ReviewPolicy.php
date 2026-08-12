<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determina si el usuario puede ver la lista de comentarios en el panel.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_review');
    }

    /**
     * Determina si el usuario puede ver un comentario específico.
     */
    public function view(User $user, Review $review): bool
    {
        return $user->can('view_review');
    }

    /**
     * Determina si el usuario puede crear comentarios.
     */
    public function create(User $user): bool
    {
        return $user->can('create_review');
    }

    /**
     * Determina si el usuario puede actualizar un comentario.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->can('update_review');
    }

    /**
     * Determina si el usuario puede eliminar un comentario.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->can('delete_review');
    }

    /**
     * Métodos para borrado lógico (opcional)
     */
    public function restore(User $user, Review $review): bool
    {
        return $user->can('restore_review');
    }

    public function forceDelete(User $user, Review $review): bool
    {
        return $user->can('force_delete_review');
    }
}