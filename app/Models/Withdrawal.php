<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member() 
    {
        return $this->belongsTo(Member::class);
    }

    public static function generateCode()
    {
        $prefix = 'WD-';
        $dateCode = date('ym'); // ex: 2505

        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(wd_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('id')
                    ->first();
        $counter = 1;
        
        if ($last && preg_match('/\d{4}$/', $last->wd_code, $match)) {
            $counter = intval($match[0]) + 1;
        }

        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
}
