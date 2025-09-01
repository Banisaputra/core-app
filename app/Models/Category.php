<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'ppn_percent', 'margin_percent', 'margin_price', 'parent_id', 'is_parent', 'is_active', 'created_by', 'updated_by'];

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
                if ($this->margin_percent==0 && $this->margin_price==0) {
                    return [
                        'margin_percent' => $parent->margin_percent,
                        'margin_price' => $parent->margin_price,
                        'source' => 'parent' // Untuk debug/tracking
                    ];
                }
            }
        }
        
        // Default: kembalikan margin kategori itu sendiri
        return [
            'margin_percent' => $this->margin_percent,
            'margin_price' => $this->margin_price,
            'source' => 'self' // Untuk debug/tracking
        ];
    }
}
