<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BalanceSinkronSeeder extends Seeder
{
    public function run(): void
    {
        // get saving value
        $savings = \App\Models\Saving::all();
    }
}
