<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'user_management_access',
            'user_create',
            'user_edit',
            'user_delete',
            'user_show',
            'role_management_access',
            'role_create',
            'role_edit',
            'role_delete',
            'role_show',
            'permission_management_access',
            'dashboard_show'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'user_show',
            // Permission untuk user biasa
        ]);

        // Create admin user
        $admin = \App\Models\User::factory()->firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Create regular user
        $user = \App\Models\User::factory()->firstOrCreate([
            'name' => 'User',
            'email' => 'user@example.com',
        ]);
        $user->assignRole('user');
    }
}
