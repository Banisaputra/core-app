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
                'description' => 'Simpanan yang wajib dibayar setiap bulan.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Pokok',
                'description' => 'Simpanan pokok dengan nominal tertentu, dibayarkan sekali selama menjadi anggota',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'SHT',
                'description' => 'Simpanan Hari Tua, Tabungan wajib setiap bulan',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];
        foreach ($arr_svType as $type) {
            SavingType::create($type);
        }
    }
}
