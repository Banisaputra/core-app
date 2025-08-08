<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Support\Facades\DB;

class AutoGenerateSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:auto-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automatic savings for active members based on saving type settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $todayDay = $today->day;
        $periode = $today->format('Ym'); // e.g., 202508

        $savingTypes = SavingType::where('is_auto', 1)->get();

        foreach ($savingTypes as $type) {
            if ((int)$type->auto_date !== $todayDay) {
                $this->info("⏩ Skip {$type->name} (auto_day: {$type->auto_day}, today: $todayDay)");
                continue;
            }

            $this->info("Processing auto savings for type: {$type->name}");
            $members = Member::where('is_transactional', 1)->get();
            foreach ($members as $member) {

                // Cek apakah sudah ada untuk bulan ini
                $exists = Saving::where('member_id', $member->id)
                    ->where('sv_type_id', $type->id)
                    ->whereRaw("DATE_FORMAT(STR_TO_DATE(sv_date, '%Y%m%d'), '%Y%m') = ?", [$periode])
                    ->exists();

                if (!$exists) {
                    $svn_code = Saving::generateCode();

                    Saving::create([
                        'sv_code' => $svn_code,
                        'sv_date' => $today->format('Ymd'),
                        'member_id' => $member->id,
                        'sv_type_id' => $type->id,
                        'sv_value' => $type->value,
                        'sv_state' => 1,
                        'remark' => 'Auto-generated on ' . $today->format('Y-m-d'),
                        'created_by' => 0,
                        'updated_by' => 0,
                    ]);
                    $this->info("  ✔ Added for member: {$member->name}");
                } else {
                    $this->info(" >> Skiped for member: {$member->name}");
                }

            }
        }

        $this->info('✅ Auto savings generation completed.');
    }
}
