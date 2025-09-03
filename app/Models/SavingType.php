<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'value', 'created_by', 'updated_by'];

    public function savings() 
    {
        return $this->hasMany(Saving::class);
    }

    public static function getMonthlySaving() {
        return self::where('is_auto', 1)->sum('value');
        
    }
}
