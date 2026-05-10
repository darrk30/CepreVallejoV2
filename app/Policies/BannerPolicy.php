<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BannerPolicy
{
    /**
     * Determina si el usuario puede ver la lista de banners.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_banner');
    }

    /**
     * Determina si el usuario puede ver un banner específico.
     */
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('view_banner');
    }

    /**
     * Determina si el usuario puede crear nuevos banners.
     */
    public function create(User $user): bool
    {
        return $user->can('create_banner');
    }

    /**
     * Determina si el usuario puede editar un banner.
     */
    public function update(User $user, Banner $banner): bool
    {
        return $user->can('update_banner');
    }

    /**
     * Determina si el usuario puede eliminar un banner.
     */
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('delete_banner');
    }

    /**
     * Métodos adicionales para Soft Deletes (opcional)
     */
    public function restore(User $user, Banner $banner): bool
    {
        return $user->can('restore_banner');
    }

    public function forceDelete(User $user, Banner $banner): bool
    {
        return $user->can('force_delete_banner');
    }
}