<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgunanPolicy extends Model
{
    use HasFactory;
    protected $table = 'agunan_policies';
    protected $guarded = ['id'];

    public static function getAgunanPolicies($type) {
        return self::where('doc_type', $type)
            ->get()
            ->mapWithKeys(function ($agunan) {
                return [
                    $agunan->agp_name => [
                        'id' => $agunan->id,
                        'start_year' => $agunan->start_year,
                        'end_year' => $agunan->end_year,
                        'agp_value' => $agunan->pl_value
                    ]
                ];
            })
            ->all();
    }
}
