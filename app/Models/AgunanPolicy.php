<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgunanPolicy extends Model
{
    use HasFactory;
    protected $table = 'agunan_policies';
    protected $guarded = ['id'];
}
