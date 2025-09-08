<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    public static function getLoanPolicies() {
        return self::where('doc_type', 'LOAN')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->pl_name => [
                    'id' => $policy->id,
                    'name' => $policy->pl_name,
                    'value' => $policy->pl_value
                ]
            ];
        })
        ->all();
    }

    public static function getSavingPolicies() {
        return self::where('doc_type', 'SAVING')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->pl_name => [
                    'id' => $policy->id,
                    'name' => $policy->pl_name,
                    'value' => $policy->pl_value
                ]
            ];
        })
        ->all();
    }

    public static function getPeriodeActive($cut_off_day = 1) {
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');
        
        // Tentukan periode aktif berdasarkan cut-off
        if ($current_day <= $cut_off_day) {
            // Jika hari ini <= cut-off, periode aktif adalah bulan sebelumnya
            $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
            $periode_start->modify("-1 month");
            $periode_end = new DateTime("$current_year-$current_month-$cut_off_day");
        } else {
            // Jika hari ini > cut-off, periode aktif adalah bulan ini
            $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
            $periode_end = new DateTime("$current_year-$current_month-$cut_off_day");
            $periode_end->modify("+1 month");
        }
        
        return [
            'start' => $periode_start->format('Y-m-d'),
            'end' => $periode_end->format('Y-m-d'),
            'periode_code' => $periode_start->format('Ym')
        ];
    }


}
