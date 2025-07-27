<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'inv_date', 'type', 'remark', 'created_by', 'updated_by'];

    public function invDetails() {
        return $this->hasMany(InventoryDetail::class, 'inv_id');
    }

    public static function generateCode($periode = null)
    {
        $prefix = 'ADJ-';
        $dateCode = $periode ?? date('ym'); // ex: 2505

        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(inv_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('id')
                    ->first();
        $counter = 1;
        if ($last && preg_match('/\d{4}$/', $last->code, $match)) {
            $counter = intval($match[0]) + 1;
        }

        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
}
