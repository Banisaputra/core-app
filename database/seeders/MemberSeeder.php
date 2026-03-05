<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 1025; $i++) {

            $userId = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // default login
                'remember_token' => Str::random(10),
                'is_transactional' => $faker->randomElement([0,1]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('members')->insert([
                'user_id' => $userId,
                'nip' => $faker->unique()->numerify('##########'),
                'position_id' => 1,
                'devision_id' => 1,
                'name' => $faker->name,
                'telphone' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['PRIA', 'WANITA']),
                'no_kk' => $faker->numerify('################'),
                'no_ktp' => $faker->numerify('################'),
                'address' => $faker->address,
                'image' => 'default.png',
                'balance' => 0,
                'date_joined' => $faker->date(),
                'is_transactional' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}