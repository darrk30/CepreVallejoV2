<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\Response;

class VideoPolicy
{
    // ¿Puede ver el botón en el menú y entrar a la lista?
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_video');
    }

    // ¿Puede ver el detalle de un video específico?
    public function view(User $user, Video $video): bool
    {
        return $user->can('view_video');
    }

    public function create(User $user): bool
    {
        return $user->can('create_video');
    }

    public function update(User $user, Video $video): bool
    {
        return $user->can('update_video');
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->can('delete_video');
    }
}