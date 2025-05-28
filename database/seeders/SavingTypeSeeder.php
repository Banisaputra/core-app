<?php

namespace Database\Seeders;

use App\Models\SavingType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SavingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr_svType = [
            [
                'name' => 'Wajib',
                'description' => 'Simpanan yang wajib dibayar',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Pokok',
                'description' => 'Simpanan pokok dengan nominal flexible',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Sukarela',
                'description' => 'Simpanan yang tidak mengikat, boleh tidak dilakukan',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];
        foreach ($arr_svType as $type) {
            SavingType::create($type);
        }
    }
}
