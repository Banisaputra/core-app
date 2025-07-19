<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = "purchase_detail";
    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function item() 
    {
        return $this->belongsTo(MasterItem::class);
    }

}
