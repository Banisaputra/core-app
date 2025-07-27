<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemStock extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'batch', 'stock', 'price', 'ref_doc_id', 'ref_doc_type', 'remaining_stock'];

    public static function getFifoBatch($itemID, $order = 'asc') {
        return self::where('item_id', $itemID)
        ->where('remaining_stock', '>', '0')
        ->orderBy('batch', $order)
        ->orderBy('id')
        ->get();
    } 

    public function pushFifoBatch($qty) {
        $this->remaining_stock = $qty;
        $this->save();
    }


}
