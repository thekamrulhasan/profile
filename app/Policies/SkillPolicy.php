<?php

namespace App\Policies;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SkillPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('content.view');
    }

    public function view(User $user, Skill $skill): bool
    {
        return $user->hasPermission('content.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('content.create');
    }

    public function update(User $user, Skill $skill): bool
    {
        return $user->hasPermission('content.edit');
    }

    public function delete(User $user, Skill $skill): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function restore(User $user, Skill $skill): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function forceDelete(User $user, Skill $skill): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function updateAny(User $user): bool
    {
        return $user->hasPermission('content.edit');
    }
}
