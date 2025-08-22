<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.manage')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::withCount('users')->orderBy('created_at')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions', 'users');
        
        return view('admin.roles.show', compact('role'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'regex:/^[a-z_]+$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        // Log role creation
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'new_values' => array_merge($role->toArray(), [
                'permissions' => $role->permissions->pluck('name')->toArray()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $oldValues = array_merge($role->toArray(), [
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);

        $role->update([
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->sync([]);
        }

        // Log role update
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => $oldValues,
            'new_values' => array_merge($role->fresh()->toArray(), [
                'permissions' => $role->fresh()->permissions->pluck('name')->toArray()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $oldValues = array_merge($role->toArray(), [
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);

        $role->delete();

        // Log role deletion
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.roles.index')
                        ->with('success', 'Role deleted successfully.');
    }
}
