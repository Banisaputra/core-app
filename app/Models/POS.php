<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class POS extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function generateSalesCode() 
    {
        $prefix = 'POS-';
        $dateCode = date('ym'); // ex: 2505

        // Ambil entri terakhir di bulan ini
        $last = DB::table('sales')->whereRaw("DATE_FORMAT(sa_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('id')
                    ->first();
        $counter = 1;
        
        if ($last && preg_match('/\d{4}$/', $last->sa_code, $match)) {
            $counter = intval($match[0]) + 1;
        }

        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
}
