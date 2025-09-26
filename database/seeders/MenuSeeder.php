<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fe-home',
                'order' => 1, 'permission' => 'dashboard_show', 'parent_id' => null
            ],
            [
                'name' => 'user_management', 'route' => null, 'icon' => 'fe-shield',
                'order' => 2, 'permission' => 'manage_access', 'parent_id' => null
            ],
            [
                'name' => 'Users', 'route' => 'users.index', 'icon' => 'fe-users',
                'order' => 1, 'permission' => 'manage_users', 'parent_id' => 2
            ],
            [
                'name' => 'Roles', 'route' => 'roles.index', 'icon' => 'fe-key',
                'order' => 2, 'permission' => 'manage_roles', 'parent_id' => 2
            ],
            [
                'name' => 'Permissions', 'route' => 'permissions.index', 'icon' => 'fe-lock',
                'order' => 3, 'permission' => 'manage_permissions', 'parent_id' => 2
            ],
            [
                'name' => 'Menus', 'route' => 'menus.index', 'icon' => 'fe-list',
                'order' => 4, 'permission' => 'manage_menus', 'parent_id' => 2
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
