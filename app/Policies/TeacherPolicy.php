<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_teacher');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->can('view_teacher');
    }

    public function create(User $user): bool
    {
        return $user->can('create_teacher');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->can('update_teacher');
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->can('delete_teacher');
    }
}