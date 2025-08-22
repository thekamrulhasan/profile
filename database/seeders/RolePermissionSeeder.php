<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'Can view user list and details'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Can create new users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Can edit user information'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Can delete users'],
            
            // Role management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'Can view roles and permissions'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles', 'description' => 'Can create, edit, and delete roles'],
            
            // Content management
            ['name' => 'content.view', 'display_name' => 'View Content', 'description' => 'Can view all content'],
            ['name' => 'content.create', 'display_name' => 'Create Content', 'description' => 'Can create new content'],
            ['name' => 'content.edit', 'display_name' => 'Edit Content', 'description' => 'Can edit existing content'],
            ['name' => 'content.delete', 'display_name' => 'Delete Content', 'description' => 'Can delete content'],
            ['name' => 'content.publish', 'display_name' => 'Publish Content', 'description' => 'Can publish/unpublish content'],
            
            // Blog management
            ['name' => 'blog.view', 'display_name' => 'View Blog Posts', 'description' => 'Can view blog posts'],
            ['name' => 'blog.create', 'display_name' => 'Create Blog Posts', 'description' => 'Can create blog posts'],
            ['name' => 'blog.edit', 'display_name' => 'Edit Blog Posts', 'description' => 'Can edit blog posts'],
            ['name' => 'blog.delete', 'display_name' => 'Delete Blog Posts', 'description' => 'Can delete blog posts'],
            
            // Media management
            ['name' => 'media.view', 'display_name' => 'View Media', 'description' => 'Can view media library'],
            ['name' => 'media.upload', 'display_name' => 'Upload Media', 'description' => 'Can upload media files'],
            ['name' => 'media.delete', 'display_name' => 'Delete Media', 'description' => 'Can delete media files'],
            
            // Settings management
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Can view system settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Can modify system settings'],
            
            // Analytics
            ['name' => 'analytics.view', 'display_name' => 'View Analytics', 'description' => 'Can view analytics and reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => Permission::all()->pluck('name')->toArray(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access with most permissions',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit',
                    'content.view', 'content.create', 'content.edit', 'content.delete', 'content.publish',
                    'blog.view', 'blog.create', 'blog.edit', 'blog.delete',
                    'media.view', 'media.upload', 'media.delete',
                    'settings.view', 'settings.edit',
                    'analytics.view',
                ],
            ],
            [
                'name' => 'editor',
                'display_name' => 'Content Editor',
                'description' => 'Can manage content and blog posts',
                'permissions' => [
                    'content.view', 'content.create', 'content.edit', 'content.publish',
                    'blog.view', 'blog.create', 'blog.edit',
                    'media.view', 'media.upload',
                ],
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to content',
                'permissions' => [
                    'content.view',
                    'blog.view',
                    'media.view',
                    'analytics.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                ]
            );

            // Assign permissions to role
            $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
            $role->permissions()->sync($permissions->pluck('id'));
        }

        // Create default super admin user
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        User::firstOrCreate(
            ['email' => 'admin@devops-portfolio.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
