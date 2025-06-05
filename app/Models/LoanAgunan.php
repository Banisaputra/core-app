<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanAgunan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function loan() 
    {
        return $this->belongsTo(Loan::class);
    }
}
