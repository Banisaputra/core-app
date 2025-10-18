<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Model
{
    use HasFactory;
    protected $table = 'role_user';
    protected $fillable = [
        'role_id',
        'user_id',
    ];
    public $timestamps = false;
}
