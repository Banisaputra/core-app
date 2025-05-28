<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
