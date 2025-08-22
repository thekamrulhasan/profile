<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('roles.manage');
    }

    public function update(User $user, Role $role): bool
    {
        // Cannot edit super_admin role unless you are super admin
        return $user->hasPermission('roles.manage') && 
               ($role->name !== 'super_admin' || $user->isSuperAdmin());
    }

    public function delete(User $user, Role $role): bool
    {
        // Cannot delete super_admin role or roles that have users
        return $user->hasPermission('roles.manage') && 
               $role->name !== 'super_admin' && 
               $role->users()->count() === 0;
    }
}
