<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    public function index() 
    {
        $lp_date = 2508; //date('ym');
        $loanDetails = Loan::with(['member', 'payments' => function($query) use ($lp_date) {
            $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date]);
        }])
        ->whereHas('payments', function($query) use ($lp_date) {
            $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date]);
        })
        ->orderBy('id')
        ->get();

        // return view('repayments.index', compact('loanDetails'));
        return view('layouts.maintenance', compact('loanDetails'));
    }   
}
