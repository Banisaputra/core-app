<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanAgunan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::with('member')->latest()->paginate();
        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            "loan_code" => Loan::generateCode()
        ];
        return view('loans.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date',
            'loan_tenor' => 'required|integer',
            'loan_value' => ['required','regex:/^\s?(\d{1,3}(\.\d{3})*(,\d{1,2})?|\d+)$/'],
            'interest_percent' => 'required|decimal:2',
        ]);

        $loan_code = Loan::generateCode();
        $loan_value = Loan::formatIdrToNumeric($request->loan_value);

        // check due date
        $date = new DateTime($request->loan_date);
        $date->add(new DateInterval('P' . $request->loan_tenor . 'M'));
        $dueDate = $date->format('Ymd');

        // get anggota
        $member = Member::findOrFail($request->member_id);
        $maxLoan = $member->maxLoanAmount();
        $is_agunan = isset($request->cbAgunan) ? true : false;

        // check policy max loan non agunan
        if ($loan_value > $maxLoan && $is_agunan === false)
            return redirect()->back()->with('error', 'Plafon pinjaman melebihi batas maksimal sebesar Rp ' . number_format($maxLoan, 0, ',', '.'));
        if ($request->loan_tenor > 12 && $is_agunan === false)
            return redirect()->back()->with('error', 'Tenor pinjaman melebihi batas maksimal 12 bulan, gunakan agunan untuk tenor yang lebih lama');
        if ($request->loan_tenor > 36 && $is_agunan === true)
            return redirect()->back()->with('error', 'Tenor pinjaman dengan agunan melebihi batas maksimal 36 bulan');

        if ($loan_value > 3000000) {
            $request->validate([
                'ln_agunan' => 'required|string',
                'ln_docNumber' => 'required|string',
                'ln_docDetail' => 'required|string',
            ], [
                'ln_agunan.required' => 'Jaminan diperlukan untuk pinjaman ini',
                'ln_docNumber.required' => 'Nomor Jaminan diperlukan untuk pinjaman ini',
                'ln_docDetail.required' => 'Data Jaminan diperlukan untuk pinjaman ini',
            ]);
 
        }

        DB::beginTransaction();
        try {
            //insert loan
            $loan = Loan::create([
                'member_id' => $request->member_id,
                'loan_type' => "UANG",
                'loan_code' => $loan_code,
                'loan_date' => date('Ymd', strtotime($request->loan_date)),
                'loan_tenor' => $request->loan_tenor,
                'loan_value' => $loan_value,
                'interest_percent' => $request->interest_percent,
                'due_date' => $dueDate,
                'loan_state' => $request->loan_status,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
    
            if ($loan && $request->loan_value > 3000000) {
                LoanAgunan::create([
                    'loan_id' => $loan->id,
                    'agunan_type' => $request->ln_agunan,
                    'doc_number' => $request->ln_docNumber,
                    'doc_detail' => $request->ln_docDetail,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
    
            // insert loan payment
            $loan_total = $loan->loan_value;
            $ln_date = $loan->loan_date;
            for ($i=1; $i <= $request->loan_tenor ; $i++) { 
                $lp_val = round($loan->loan_value / $loan->loan_tenor, 0);
                $lp_intr = round(($lp_val*$loan->interest_percent)/100, 0);
                $ln_remain = round($loan_total - $lp_val);
                $pay_date = new DateTime($ln_date);
                $lp_date = $pay_date->add(new DateInterval('P1M'))->format('Ymd');
                LoanPayment::create([
                    'lp_code' => LoanPayment::generateCode($lp_date),
                    'loan_id' => $loan->id,
                    'lp_date' => $lp_date,
                    'lp_value' => $lp_val,
                    'loan_interest' => $lp_intr,
                    'loan_remaining' => $ln_remain,
                    'lp_total' => ($lp_val+$lp_intr),
                    'tenor_month' => $i,
                    'lp_state' => 1,
                    'remark' => '',
                    'proof_of_payment' => '',
                    'lp_forfeit' => 0,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
        
                ]);
                $loan_total -= $lp_val;
                $ln_date = $lp_date;
                
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data Pinjaman berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return redirect()->back()->withInput()->with('error', $e->getMessage());
            // return redirect()->back()->withInput()->with('error', 'Data Pinjaman gagal ditambahkan.');
        }

    }

    public function show(string $id)
    {
        $data = [
            "loan" => Loan::with('member','payments')->findOrFail($id),
        ];

        return view('loans.view', $data);
    }

    public function edit(string $id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        return view('loans.edit', compact('loan'));
    }

    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date',
            'loan_tenor' => 'required|integer',
            'loan_value' => ['required','regex:/^\s?(\d{1,3}(\.\d{3})*(,\d{1,2})?|\d+)$/'],
            'interest_percent' => 'required|decimal:2',
            'loan_status' => 'required'
        ], [
            'loan_value' => 'Jumlah pinjaman tidak sesuai format penulisan.'
        ]);

        $loan_code = Loan::generateCode();
        $loan_value = Loan::formatIdrToNumeric($request->loan_value);

        // check due date
        $date = new DateTime($request->loan_date);
        $date->add(new DateInterval('P' . $request->loan_tenor . 'M'));
        $dueDate = $date->format('Ymd');

        $loan->update([
            'member_id' => $request->member_id,
            'loan_code' => $loan_code,
            'loan_date' => date('Ymd', strtotime($request->loan_date)),
            'loan_tenor' => $request->loan_tenor,
            'loan_value' => $request->loan_value,
            'interest_percent' => $request->interest_percent,
            'due_date' => $dueDate,
            'loan_state' => $request->loan_status * 1,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data Pinjaman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);
        if($loan) {
            $loan->delete();
        }

        return redirect()->back()->with('success', "Data pinjaman anggota berhasil dihapus");
    }
}
