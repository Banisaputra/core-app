<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit_type extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'created_by', 'updated_by'];
}
