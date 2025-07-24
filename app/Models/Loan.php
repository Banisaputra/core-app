<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function loanAgunan()
    {
        return $this->hasOne(LoanAgunan::class);
    }

    public static function generateCode()
    {
        $prefix = 'LN-';
        $dateCode = date('ym'); // ex: 2505

        // Ambil entri terakhir di bulan ini
        $last = self::whereRaw("DATE_FORMAT(loan_date, '%y%m') = ?", [$dateCode])
                    ->orderByDesc('id')
                    ->first();
        $counter = 1;
        
        if ($last && preg_match('/\d{4}$/', $last->loan_code, $match)) {
            $counter = intval($match[0]) + 1;
        }

        return $prefix . $dateCode . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }

    public static function formatIdrToNumeric($idrValue)
    {
        // Remove all non-digit characters except comma
        $numericString = preg_replace('/[^0-9,]/', '', $idrValue);
        
        // Replace comma with dot for decimal
        $numericString = str_replace(',', '.', $numericString);
        
        // Remove thousand separators
        $numericString = str_replace('.', '', $numericString);
        
        return (int)$numericString;
    }

}
