<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanPayment;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    public function index() 
    {
        // $lp_date = date('ym');
        $lp_date = 2509;
        $loanDetails = LoanPayment::with(['loan', 'loan.member'])
            ->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date])
            ->whereHas('loan', function($query) {
                $query->where('loan_state', 2);
            })
            ->get();

        return view('repayments.index', compact('loanDetails'));
    }
    
    public function create() 
    {
        $loans = session('loans', []);
        $member_id = session('member_id', 0);
        
        return view('repayments.fast-settle', compact('loans','member_id'));
    }

    public function generate()
    {
        return view('repayments.generate');
    }

    public function settle(Request $request) 
    {
        $loanPay = LoanPayment::findOrFail($request->lp_id);
        if ($loanPay->lp_state == 2) return redirect()->back()->with('error', 'Pembayaran sudah pernah dilunasi');
        $loanPay->lp_state = 2;
        $loanPay->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil dilunasi');
    }

    public function generated(Request $request) 
    {
        $request->validate([
            'periode' => 'required',
            'member_id' => 'nullable|array',
            'member_id.*' => 'exists:members,id',
        ]);

        // loop member
        $members = Member::pluck('id');
        $month = date('m', strtotime($request->periode));
        $year = date('Y', strtotime($request->periode));

        $startOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01');
        $endOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '31');

        foreach ($members as $key => $id) {
            if (!in_array($id, $request->member_id ?? [])) {

                try {
                    $loanDetails = LoanPayment::select('loan_payments.*')
                    ->join('loans', 'loans.id', '=', 'loan_payments.loan_id')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->whereRaw("DATE_FORMAT(loan_payments.lp_date, '%Y%m') = ?", [$year.$month])
                    ->where('members.id', $id)
                    ->get();

                    foreach ($loanDetails as $key => $loan) {
                        $loan->lp_state = 2;
                        $loan->remark = "pelunasan dari generate pelunasan";
                        $loan->updated_by = auth()->id();
                        $loan->save();
                    }
                } catch (\Throwable $th) {
                    // Log the full error for debugging
                    \Log::error('Error fetching record: ' . $th->getMessage(), [
                        'exception' => $th,
                    ]);
                    return redirect()->back()->with('error', 'Anggota sudah melakukan pelunasan bulan ini.');
                } 
            }
        }
        return redirect()->back()->with('success', 'Data pelunasan berhasil digenerate.');
    }

    public function getSettle (Request $request) 
    {
        $member = Member::findOrFail($request->member_id);

        // get loan
        $loans = Loan::with(['member','payments'])->where('member_id', $member->id)
        ->where('loan_state', 2)
        ->get();

        return redirect()->route('repayments.create')->with(['loans' => $loans, 'member_id' => $member->id]);

    }

    public function settleConfirm (Request $request) 
    {
        $member = Member::findOrFail($request->member_id);

        // get loan
        $loans = Loan::with(['member','payments'])->where('member_id', $member->id)
        ->where('loan_state', 2)
        ->get();

        foreach ($loans as $keyL => $loan) {
            foreach ($loan->payments as $keyP => $pay) {
                if ($pay->lp_state == 1) {
                    $pay->lp_state = 2;
                    $pay->remark = "Pelunasan cepat, tutup pinjaman";
                    $pay->updated_by = auth()->id();
                    $pay->save();
                }
            }
            $loan->loan_state = 3;
            $loan->save();
        }

        return redirect()->route('repayments.index')->with('success', 'Pelunasan cepat berhasil dilakukan');

    }

}
