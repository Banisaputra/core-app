<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr_permission = [
            [ 'name' => 'dashboard'],
            [ 'name' => 'master'],
            [ 'name' => 'masterSettings'],
            [ 'name' => 'backup_database'],
            [ 'name' => 'role_info'],
            [ 'name' => 'position'],
            [ 'name' => 'member'],
            [ 'name' => 'devision'],
            [ 'name' => 'supplier'],
            [ 'name' => 'category'],
            [ 'name' => 'master_item'],
            [ 'name' => 'setting_policy'],
            [ 'name' => 'usaha'],
            [ 'name' => 'sales'],
            [ 'name' => 'purchase'],
            [ 'name' => 'inventory'],
            [ 'name' => 'koperasi'],
            [ 'name' => 'saving'],
            [ 'name' => 'loan'],
            [ 'name' => 'withdrawal'],
            [ 'name' => 'repayment'],
            [ 'name' => 'laporan'],
            [ 'name' => 'report_deduction'],
            [ 'name' => 'report_general'],
            [ 'name' => 'report_member']
        ];
        foreach ($arr_permission as $type) {
            Permission::create($type);
        }
    }
}
