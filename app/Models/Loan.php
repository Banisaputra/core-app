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

}
