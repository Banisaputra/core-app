<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Policy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    public static function getLoanPolicies() {
        return self::where('doc_type', 'LOAN')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->pl_name => [
                    'id' => $policy->id,
                    'name' => $policy->pl_name,
                    'value' => $policy->pl_value
                ]
            ];
        })
        ->all();
    }

    public static function getSavingPolicies() {
        return self::where('doc_type', 'SAVING')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->pl_name => [
                    'id' => $policy->id,
                    'name' => $policy->pl_name,
                    'value' => $policy->pl_value
                ]
            ];
        })
        ->all();
    }


}
