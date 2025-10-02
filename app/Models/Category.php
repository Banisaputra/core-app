<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'ppn_percent', 'margin_percent', 'margin_price', 'parent_id', 'is_parent', 'is_active', 'created_by', 'updated_by'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    protected function items() 
    {
        return $this->hasMany(MasterItem::class, 'ct_id');
    }

    public function getMargin() 
    {
        // Jika kategori ini child (punya parent), ambil margin dari parent
        if ($this->is_parent == 0 && $this->parent_id) {
            $parent = Category::find($this->parent_id);
            if ($parent) {
                // jika nilai child kosong
                // if ($this->margin_percent==0 && $this->margin_price==0) {
                    return [
                        'margin_percent' => ($parent->margin_percent*1) + ($this->margin_percent*1),
                        'margin_price' => ($parent->margin_price*1) + ($this->margin_price*1),
                        'ppn_percent' => $parent->ppn_percent,
                        'source' => 'parent+self' // Untuk debug/tracking
                    ];
                // }
            }
        }
        
        // Default: kembalikan margin kategori itu sendiri
        return [
            'margin_percent' => $this->margin_percent,
            'margin_price' => $this->margin_price,
            'ppn_percent' => $this->ppn_percent,
            'source' => 'self' // Untuk debug/tracking
        ];
    }
}
