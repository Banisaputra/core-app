<?php

namespace App\Models;

use App\Models\Loan;
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
        $exists = self::with(['loan'])
        ->where('agunan_type', $type)
        ->where('doc_number', $number)
        ->where('is_transactional', 1)
        ->first();
        
        $agn_list = self::where('agunan_type', $type)
        ->where('doc_number', $number)
        ->where('is_transactional', 1)
        ->get();

        $loan_member = Loan::where('member_id', $memberID)
        ->pluck('member_id','id');

        $exists_with_same = false;
        foreach ($agn_list as $key => $loan_agn) {
            if (isset($loan_member[$loan_agn->loan_id])) {
                $exists_with_same = true;
                break;
            }
        }

        if ($exists) {
            $mID = $exists->loan->member_id;
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
            'exists_on_member' => $exists_with_same,
            'member_share' => $member_share
        ];
    }
}
