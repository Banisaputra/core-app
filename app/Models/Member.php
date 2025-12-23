<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\LoanPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function savings() {
        return $this->hasMany(Saving::class);
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function position() {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function devision() {
        return $this->belongsTo(Devision::class, 'devision_id');
    }

    public function withdrawals() {
        return $this->hasMany(Withdrawal::class);
    }

    public function maxLoanAmount(): int
    {
        $selisihTahun = Carbon::parse($this->date_joined)->diffInYears(Carbon::now());
        $loanPolicy = Policy::getLoanPolicies();

        if ($selisihTahun < 1) {
            return $loanPolicy['max_agunan_0_1']['value'];
        } elseif ($selisihTahun < 5 && $selisihTahun > 1) {
            return $loanPolicy['max_agunan_1_5']['value'];
        } else {
            return $loanPolicy['max_agunan_5_0']['value'];
        }
        
    }

    public function tenorAmount($loan): array
    {
        $selisihTahun = Carbon::parse($this->date_joined)->diffInYears(Carbon::now());
        $loanPolicy = Policy::getLoanPolicies();
        $min_angsur = $loanPolicy['min_pokok_angsuran']['value'] * 1;
        $max_angsur = $loanPolicy['max_pokok_angsuran']['value'] * 1;
        $valid = true;
        
        
        if ($selisihTahun < 1) {
            $max_tenor = round((($loanPolicy['max_agunan_0_1']['value'] * 1) / $min_angsur)??0 ,0);
            $tenor = round($loan / $min_angsur, 0);
            if($tenor > $max_tenor) $valid = false;
            $angsuran = $loan / $tenor;
            return [
                'tenorIdeal' => $tenor,
                'angsuran' => $angsuran,
                'tenorMax' => $max_tenor,
                'pass' => $valid
            ];
        } elseif ($selisihTahun < 5 && $selisihTahun > 1) {
            $max_tenor = round((($loanPolicy['max_agunan_1_5']['value'] * 1) / $min_angsur)??0 ,0);
            $tenor = round($loan / $min_angsur, 0);
            if($tenor > $max_tenor) $valid = false;
            $angsuran = $loan / $tenor;
            return [
                'tenorIdeal' => $tenor,
                'angsuran' => $angsuran,
                'tenorMax' => $max_tenor,
                'pass' => $valid
            ];
        } else {
            $max_tenor = round((($loanPolicy['max_agunan_5_0']['value'] * 1) / $min_angsur)??0 ,0);
            $tenor = round($loan / $min_angsur, 0);
            if($tenor > $max_tenor) $valid = false;
            $angsuran = $loan / $tenor;
            return [
                'tenorIdeal' => $tenor,
                'angsuran' => $angsuran,
                'tenorMax' => $max_tenor,
                'pass' => $valid
            ];
        }

    }

    public function getTotalLoan(): array
    {
        $loanPolicy = Policy::getLoanPolicies();
        $YM = Carbon::now()->addMonth()->format('Ym');
        $jabatan = Position::where('id', $this->position_id)->first();
        $totalPokok = Loan::join('loan_payments', 'loan_payments.loan_id', '=', 'loans.id')
            ->where('loans.member_id', $this->id)
            ->where('loans.loan_state', 2)
            ->whereRaw('LEFT(loan_payments.lp_date, 6) = ?', [$YM])
            ->sum('loan_payments.lp_value');

        // include bunga
        $totalBayar = Loan::join('loan_payments', 'loan_payments.loan_id', '=', 'loans.id')
            ->where('loans.member_id', $this->id)
            ->where('loans.loan_state', 2)
            ->whereRaw('LEFT(loan_payments.lp_date, 6) = ?', [$YM])
            ->sum('loan_payments.lp_total');

        // 
        $maxBayar = 0;
        if (strtoupper($jabatan->name) == "STAFF") {
            $maxBayar = $loanPolicy['max_potong_gaji_staff']['value'];
        } else if (strtoupper($jabatan->name) == "PRODUKSI") {
            $maxBayar = $loanPolicy['max_potong_gaji_operator']['value'];
        }
        
        return [
            'total_pokok' => $totalPokok,
            'maxPokok' => $loanPolicy['max_pokok_angsuran']['value'],
            'total_bayar' => $totalBayar,
            'maxBayar' => $maxBayar
        ];
    }

}
