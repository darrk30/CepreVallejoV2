<?php

namespace App\Policies;

use App\Models\Podcast;
use App\Models\User;

class PodcastPolicy
{
    /**
     * Ver la lista de podcasts (y el ítem en el menú).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_podcast');
    }

    /**
     * Ver el detalle de un podcast.
     */
    public function view(User $user, Podcast $podcast): bool
    {
        return $user->can('view_podcast');
    }

    public function create(User $user): bool
    {
        return $user->can('create_podcast');
    }

    public function update(User $user, Podcast $podcast): bool
    {
        return $user->can('update_podcast');
    }

    public function delete(User $user, Podcast $podcast): bool
    {
        return $user->can('delete_podcast');
    }

    /**
     * Eliminación en lote (bulk). Se apoya en el mismo permiso de eliminar.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_podcast');
    }

    public function restore(User $user, Podcast $podcast): bool
    {
        return $user->can('restore_podcast');
    }

    public function forceDelete(User $user, Podcast $podcast): bool
    {
        return $user->can('force_delete_podcast');
    }
}
