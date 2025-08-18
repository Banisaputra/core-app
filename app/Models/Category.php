<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'margin_percent', 'margin_price', 'parent_id', 'is_parent', 'is_active', 'created_by', 'updated_by'];
}
