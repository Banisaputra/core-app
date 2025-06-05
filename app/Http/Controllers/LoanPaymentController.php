<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoanPaymentController extends Controller
{
    public function create() 
    {
        return view('loan_payments.create');
    }
}
