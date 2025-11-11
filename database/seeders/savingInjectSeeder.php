<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class savingInjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //get member
        // $members = \App\Models\Member::all();
        // foreach ($members as $member) {
        //     // get saving
        //     $savings = \App\Models\Saving::where('member_id', $member->id)->get();
        //     foreach ($savings as $saving) {
        //         // inject saving
        //         if ($saving->sv_state == 1) {
        //             $saving->update([
        //                 'sv_state' => 2,
        //                 'updated_by' => 1,
        //             ]);
        //             \App\Models\Member::where('id', $saving->member_id)->increment('balance', $saving->sv_value);
        //         }
        //     }
        // }
    }
}
