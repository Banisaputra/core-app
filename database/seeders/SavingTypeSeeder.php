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
                'description' => 'Simpanan yang wajib yang dilakukan setiap bulan oleh setiap anggota.',
                'value' => 50000,
                'auto_day' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Pokok',
                'description' => 'Simpanan pokok dengan nominal yang ditentukan dan  dibayarkan satu kali saja.',
                'value' => 100000,
                'auto_day' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Sukarela',
                'description' => 'Simpanan yang tidak mengikat dan dapat dilakukan kapan saja oleh anggota.',
                'value' => 0,
                'auto_day' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];
        foreach ($arr_svType as $type) {
            SavingType::create($type);
        }
    }
}
