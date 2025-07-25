<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterItem extends Model
{
    use HasFactory;
    protected $table = 'master_items';
    protected $guarded = ['id'];

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
