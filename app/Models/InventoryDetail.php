<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryDetail extends Model
{
    use HasFactory;
    protected $table = "inventory_detail";

    protected $fillable = ['inv_id', 'item_id', 'amount', 'batch'];

    public function inventory() {
        return $this->belongsTo(Inventories::class);
    }

    public function item() {
        return $this->belongsTo(MasterItem::class);
    }
}
