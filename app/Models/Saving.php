<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saving extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function svType()
    {
        return $this->belongsTo(SavingType::class, 'sv_type_id');
    }

    public static function generateCode($periode = null)
    {
        $prefix = 'SVN-';
        $dateCode = $periode ?? date('ym'); // ex: 2505
        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(sv_date, '%y%m') = ?", [$dateCode])
        ->orderByDesc('sv_code')
        ->first();
        $counter = 1;
         
        if ($last && preg_match('/\d{4}$/', $last->sv_code, $match)) {
            $counter = intval($match[0]) + 1;
        }
        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
    
    public static function generateCodeRev($periode = null, $counter = null)
    {
        $prefix = 'SVN-';
        $dateCode = $periode ?? date('ym'); // ex: 2505

        if ($counter !== null) {
            return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        }

        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(sv_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('sv_code')
                    ->where('sv_code', $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT))
                    ->first();
        $counter = 1;
        
        if ($last && preg_match('/\d{4}$/', $last->sv_code, $match)) {
            $counter = intval($match[0]) + 1;
        }
        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }


}
