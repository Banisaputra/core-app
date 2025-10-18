<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisions = Permission::all();
        foreach ($permisions as $key => $value) {
            PermissionRole::create([
                'role_id' => 1,
                'permission_id' => $value->id
            ]);
        }

        // for user
        PermissionRole::create([
            'role_id' => 2,
            'permission_id' => 1 // dashboard
        ]);
    }
}
