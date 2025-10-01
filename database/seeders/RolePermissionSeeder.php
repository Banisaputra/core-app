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
            'dashboard_show' => 'Dapat mengakses dan melihat dashboard',
            'manage_access' => 'Dapat mengelola menu hak akses sistem',
            'manage_categories' => 'Dapat mengelola menu kategori barang',
            'manage_databases' => 'Dapat mengelola menu database dan backup data',
            'manage_devisions' => 'Dapat mengelola menu divisi/departemen',
            'manage_inventories' => 'Dapat mengelola menu stok inventaris',
            'manage_items' => 'Dapat mengelola menu item/barang',
            'manage_koperasi' => 'Dapat mengelola menu data koperasi',
            'manage_loans' => 'Dapat mengelola menu pinjaman anggota',
            'manage_members' => 'Dapat mengelola menu data anggota',
            'manage_menus' => 'Dapat mengelola menu menu navigasi',
            'manage_permissions' => 'Dapat mengelola menu permissions/izin akses',
            'manage_policies' => 'Dapat mengelola menu pengaturan sistem',
            'manage_pos' => 'Dapat mengelola menu Point of Sale (POS)',
            'manage_positions' => 'Dapat mengelola menu jabatan/posisi',
            'manage_purchases' => 'Dapat mengelola menu pembelian barang',
            'manage_repayments' => 'Dapat mengelola menu angsuran pinjaman',
            'manage_report' => 'Dapat mengelola menu laporan',
            'manage_roles' => 'Dapat mengelola menu peran/roles',
            'manage_savings' => 'Dapat mengelola menu simpanan anggota',
            'manage_suppliers' => 'Dapat mengelola menu data supplier',
            'manage_usaha' => 'Dapat mengelola menu unit usaha',
            'manage_users' => 'Dapat mengelola menu pengguna sistem (users)',
            'manage_withdrawals' => 'Dapat mengelola menu penarikan simpanan',
            'report_deductions' => 'Dapat melihat laporan potongan',
            'report_generals' => 'Dapat melihat laporan umum',
            'report_members' => 'Dapat melihat laporan anggota',
            'user_management_access' => 'Dapat mengakses modul manajemen pengguna',
            'user_create' => 'Dapat membuat pengguna baru',
            'user_edit' => 'Dapat mengedit data pengguna',
            'user_delete' => 'Dapat menghapus pengguna',
            'user_show' => 'Dapat melihat detail pengguna',
            'role_management_access' => 'Dapat mengakses modul manajemen peran',
            'role_create' => 'Dapat membuat peran baru',
            'role_edit' => 'Dapat mengedit peran',
            'role_delete' => 'Dapat menghapus peran',
            'role_show' => 'Dapat melihat detail peran',
            'permission_management_access' => 'Dapat mengakses modul manajemen izin',
            'permission_create' => 'Dapat membuat izin baru',
            'permission_edit' => 'Dapat mengedit izin',
            'permission_delete' => 'Dapat menghapus izin',
            'permission_show' => 'Dapat melihat detail izin',
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

        $userRole = Role::firstOrCreate(['name' => 'member']);
        $userRole->givePermissionTo([
            'dashboard_show',
            'user_show'
        ]);

        // Create admin user
        $admin = \App\Models\User::where('email', 'admin@dev.com')->first();
        if (!$admin) {
            $admin = \App\Models\User::factory()->create([
                'name' => 'Admin Development',
                'email' => 'admin@dev.com',
            ]);
        }
        $admin->assignRole('superuser');

        // Create regular user
        $user = \App\Models\User::where('email', 'user@example.com')->first();
        if (!$user) {
            $user = \App\Models\User::factory()->create([
                'name' => 'User',
                'email' => 'user@example.com',
            ]);
        }
        $user->assignRole('member');
    }
}
