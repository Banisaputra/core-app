<?php

namespace App\Http\Controllers;

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
        if ($loanPay->lp_state == 2) return redirect()->route('loans.show', $loanPay->loan_id)->with('error', 'Pembayaran sudah pernah dilunasi');
        $loanPay->lp_state = 2;
        $loanPay->save();

        return redirect()->route('loans.show', $loanPay->loan_id)->with('success', 'Pembayaran berhasil dilunasi');
    }
}
