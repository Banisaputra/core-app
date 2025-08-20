<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgunanPolicy extends Model
{
    use HasFactory;
    protected $table = 'agunan_policies';
    protected $guarded = ['id'];

    public static function getAgunanPolicy($type, $year) 
    {
        return self::where('doc_type', $type)
        ->where('start_year', '<=', $year)
        ->where('end_year', '>=', $year)
        ->first();
    }

}
