<?php

namespace App\Policies;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperiencePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('content.view');
    }

    public function view(User $user, Experience $experience): bool
    {
        return $user->hasPermission('content.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('content.create');
    }

    public function update(User $user, Experience $experience): bool
    {
        return $user->hasPermission('content.edit');
    }

    public function delete(User $user, Experience $experience): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function restore(User $user, Experience $experience): bool
    {
        return $user->hasPermission('content.delete');
    }

    public function forceDelete(User $user, Experience $experience): bool
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
