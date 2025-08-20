<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanAgunan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function loan() 
    {
        return $this->belongsTo(Loan::class);
    }

    public static function checkAgunan($memberID, $type, $number) 
    {
        $valid = true;
        $member_share = [];
        $exits = self::with(['loan'])
        ->where('agunan_type', $type)
        ->where('doc_number', $number)
        ->first();

        if ($exits) {
            $mID = $exits->loan->member_id;
            $valid = false;
            if ($type == 'SERTIFIKAT') {
               $member_share = Member::where('no_kk', function ($q) use ($mID) {
                    $q->select('no_kk')
                    ->from('members')
                    ->where('id', $mID);
                })
                ->get(['id', 'no_kk', 'name'])
                ->groupBy('no_kk') // Group by no_kk untuk mengelompokkan anggota keluarga
                ->map(function ($members) {
                    return $members->map(function ($member) {
                        return [
                            'id'    => $member->id,
                            'name'  => $member->name,
                        ];
                    });
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
