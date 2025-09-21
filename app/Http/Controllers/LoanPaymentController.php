<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;

class LoanPaymentController extends Controller
{
    public function create() 
    {
        return view('loan_payments.create');
    }

    public function settle(Request $request) 
    {
        $loanPay = LoanPayment::findOrFail($request->lp_id);
        $loan_finish = true;
        
        if ($loanPay->lp_state == 2) return redirect()->route('loans.show', $loanPay->loan_id)->with('error', 'Pembayaran sudah pernah dilunasi');
        $loanPay->lp_state = 2;
        $loanPay->save();
        
        $loan = Loan::with(['payments'])->where('id', $loanPay->loan_id)->first();
        foreach ($loan->payments as $key => $lp) {
            if ($lp['lp_state'] == 1) {
                $loan_finish = false;
                break;
            }
        }
        if ($loan_finish) {
            $loan->loan_state = 3;
            $loan->save();
        }

        return redirect()->route('loans.show', $loanPay->loan_id)->with('success', 'Pembayaran berhasil dilunasi');
    }
}
