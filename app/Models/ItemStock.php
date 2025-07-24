<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemStock extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'batch', 'stock', 'price', 'ref_doc_id', 'ref_doc_type'];


}
