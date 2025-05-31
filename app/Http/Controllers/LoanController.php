<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\Loan;
use Illuminate\Http\Request;

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
            'loan_value' => 'required|integer',
            'interest_percent' => 'required|integer',
        ]);

        $loan_code = Loan::generateCode();

        // check due date
        $date = new DateTime($request->loan_date);
        $date->add(new DateInterval('P' . $request->loan_tenor . 'M'));
        $dueDate = $date->format('Ymd');

        Loan::create([
            'member_id' => $request->member_id,
            'loan_code' => $loan_code,
            'loan_date' => date('Ymd', strtotime($request->loan_date)),
            'loan_tenor' => $request->loan_tenor,
            'loan_value' => $request->loan_value,
            'interest_percent' => $request->interest_percent,
            'due_date' => $dueDate,
            'loan_state' => 1,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data Pinjaman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            "loan" => Loan::with('member')->findOrFail($id),
        ];

        return view('loans.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $loan = Loan::with('member')->findOrFail($id);
        return view('loans.edit', compact('loan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_date' => 'required|date',
            'due_date' => 'required|date',
            'loan_tenor' => 'required|integer',
            'loan_value' => 'required|integer',
            'interest_percent' => 'required|integer',
        ]);

        $loan_code = Loan::generateCode();

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
            'loan_state' => 1,
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
