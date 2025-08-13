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

    public static function checkAgunan($memberID, $type, $number) 
    {
        $valid = true;
        $member_share = [];
        $exits = self::where('agunan_type', $type)
        ->where('doc_number', $number)
        ->first();

        if ($exits) {
            $valid = false;
            if ($type == 'SERTIFIKAT') {
                $member_share = Member::where('no_kk', function ($q) use ($memberID) {
                    $q->select('no_kk')
                    ->from('members')
                    ->where('id', $memberID);
                })
                ->get(['id', 'no_kk', 'name'])
                ->keyBy('no_kk')
                ->map(function ($member) {
                    return [
                        'id'    => $member->id,
                        'name'  => $member->name,
                    ];
                })
                ->toArray();
            }
        }

        return [
            'agn_valid' => $valid,
            'member_share' => $member_share
        ];
    }
}
