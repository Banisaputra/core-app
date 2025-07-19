<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function prDetails() {
        return $this->hasMany(PurchaseDetail::class, "pr_id");
    }

    public static function generateCode($periode = null)
    {
        $prefix = 'PRC-';
        $dateCode = $periode ?? date('ym'); // ex: 2505

        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(pr_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('id')
                    ->first();
        $counter = 1;
        
        if ($last && preg_match('/\d{4}$/', $last->pr_code, $match)) {
            $counter = intval($match[0]) + 1;
        }

        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
 
}
