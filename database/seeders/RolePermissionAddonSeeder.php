<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionAddonSeeder extends Seeder
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
            'supplier_create' => 'Dapat membuat data supplier baru',
            'supplier_edit' => 'Dapat mengedit data supplier',
            'supplier_delete' => 'Dapat menghapus data supplier',
            'supplier_show' => 'Dapat melihat detail data supplier',
            'category_create' => 'Dapat membuat data kategori baru',
            'category_edit' => 'Dapat mengedit data kategori',
            'category_delete' => 'Dapat menghapus data kategori',
            'category_show' => 'Dapat melihat detail data kategori',
            'devision_create' => 'Dapat membuat data divisi baru',
            'devision_edit' => 'Dapat mengedit data divisi',
            'devision_delete' => 'Dapat menghapus data divisi',
            'devision_show' => 'Dapat melihat detail data divisi',
            'position_create' => 'Dapat membuat data posisi baru',
            'position_edit' => 'Dapat mengedit data posisi',
            'position_delete' => 'Dapat menghapus data posisi',
            'position_show' => 'Dapat melihat detail data posisi',
            'member_create' => 'Dapat membuat data member baru',
            'member_edit' => 'Dapat mengedit data member',
            'member_delete' => 'Dapat menghapus data member',
            'member_show' => 'Dapat melihat detail data member',
            'item_create' => 'Dapat membuat data item baru',
            'item_edit' => 'Dapat mengedit data item',
            'item_delete' => 'Dapat menghapus data item',
            'item_show' => 'Dapat melihat detail data item',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['description' => $description]
            );
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'superuser']);
        $adminRole->givePermissionTo(Permission::all());
        
    }
}
