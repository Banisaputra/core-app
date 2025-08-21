<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetail extends Model
{
    use HasFactory;
    protected $table = "sale_detail";
    protected $guarded = [];

    public function sale() 
    {
        return $this->belongsTo(Sale::class);
    }

    public function item() {
        return $this->belongsTo(MasterItem::class);
    }
}
