<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Devision extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'remark', 'is_transactional'];

    public function members() {
        return $this->hasMany(Member::class);
    }
}
