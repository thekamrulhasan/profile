<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('users.view') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    public function update(User $user, User $model): bool
    {
        // Super admin can edit anyone, others can only edit themselves unless they have permission
        return $user->isSuperAdmin() || 
               $user->hasPermission('users.edit') || 
               $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself or other super admins
        return $user->hasPermission('users.delete') && 
               $user->id !== $model->id && 
               !$model->isSuperAdmin();
    }

    public function assignRole(User $user): bool
    {
        return $user->hasPermission('roles.manage');
    }
}
