<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = User::all();
        foreach ($users as $user) {
            if ($user->id >1) {
                RoleUser::create([
                    'role_id' => 4,
                    'user_id' => $user->id,
                ]);

            }
        }
    }
}
