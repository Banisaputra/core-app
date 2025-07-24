<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function savings() {
        return $this->hasMany(Saving::class);
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function withdrawals() {
        return $this->hasMany(Withdrawal::class);
    }

    public function maxLoanAmount(): int
    {
        $selisihTahun = Carbon::parse($this->date_joined)->diffInYears(Carbon::now());

        if ($selisihTahun < 1) {
            return 2000000;
        } elseif ($selisihTahun < 5) {
            return 3500000;
        } else {
            return 5500000;
        }
         
    }

}
