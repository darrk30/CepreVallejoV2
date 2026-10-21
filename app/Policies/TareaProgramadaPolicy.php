<?php

namespace App\Policies;

use App\Models\TareaProgramada;
use App\Models\User;

class TareaProgramadaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tarea_programada');
    }

    public function view(User $user, TareaProgramada $tareaProgramada): bool
    {
        return $user->can('view_tarea_programada');
    }

    public function create(User $user): bool
    {
        return $user->can('create_tarea_programada');
    }

    public function update(User $user, TareaProgramada $tareaProgramada): bool
    {
        return $user->can('update_tarea_programada');
    }

    public function delete(User $user, TareaProgramada $tareaProgramada): bool
    {
        return $user->can('delete_tarea_programada');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_tarea_programada');
    }
}
