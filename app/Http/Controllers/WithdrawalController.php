<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $withdrawals = Withdrawal::with('member')->latest()->get();
        return view('withdrawals.index', compact('withdrawals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wd_code = Withdrawal::generateCode();
        return view('withdrawals.create', compact('wd_code'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'wd_value' => preg_replace('/[^\d]/', '', $request->wd_value)
        ]);
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'wd_date' => 'required|date',
            'wd_value' => ['required','numeric'],
            'remark' => 'required|string',
            'proof_of_withdrawal' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $wd_code = Withdrawal::generateCode();

        $photoPath = null;
        if($request->hasFile('proof_of_withdrawal')) {
            $photoPath = $request->file('proof_of_withdrawal')->store('proof_of_withdrawal', 'public_direct');
        }

        $member = Member::findOrFail($request->member_id);
        $totalLoan = 0;
        $loans = Loan::with(['member', 'payments' => function($query) {
            $query->where('lp_state', '=', 1);
        }])
        ->where('member_id', $member->id)
        ->withSum(['payments' => function($query) {
            $query->where('lp_state', '=', 1);
        }], 'lp_total')
        ->get();

        foreach ($loans as $key => $loan) {
            $totalLoan += $loan->payments_sum_lp_total*1;
        }

        if ($member->balance < $totalLoan) return redirect()->back()->with('error', 'Jumlah Simpanan tidak cukup!');
        $finalAmount = $member->balance - $totalLoan;

        if ($finalAmount < $request->wd_value) return redirect()->back()->with('error', 'Sisa Simpanan tidak cukup! - sisa '.number_format($finalAmount, 0));

        Withdrawal::create([
            'wd_code' => $wd_code,
            'member_id' => $request->member_id,
            'wd_date' => date('Ymd', strtotime($request->wd_date)),
            'wd_value' => $request->wd_value,
            'loan_remaining' => $totalLoan,
            'remark' => $request->remark,
            'proof_of_withdrawal' => $photoPath,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data penarikan berhasil ditambakan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $withdrawal = Withdrawal::with('member')->findOrFail($id);
        return view('withdrawals.view', compact('withdrawal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $withdrawal = Withdrawal::with('member')->findOrFail($id);
        return view('withdrawals.edit', compact('withdrawal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'wd_date' => 'required|date',
            'wd_value' => 'required|integer',
            'remark' => 'required|string',
            'proof_of_withdrawal' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file baru
        if ($request->hasFile('proof_of_withdrawal')) {
            // Hapus foto lama jika ada
            if ($withdrawal->proof_of_withdrawal && Storage::disk('public')->exists($withdrawal->proof_of_withdrawal)) {
                Storage::disk('public')->delete($withdrawal->proof_of_withdrawal);
            }

            // Simpan foto baru
            $newPhoto = $request->file('proof_of_withdrawal')->store('proof_of_withdrawal', 'public_direct');
            $withdrawal->proof_of_withdrawal = $newPhoto;
        } 

        // Update data
        $withdrawal->member_id = $request->member_id;
        $withdrawal->wd_date = date('Ymd', strtotime($request->wd_date));
        $withdrawal->wd_value = $request->wd_value;
        $withdrawal->remark = $request->remark;
        $withdrawal->updated_by = auth()->id();
        $withdrawal->save();

        return redirect()->route('withdrawals.edit', $withdrawal->id)->with('success', 'Data penarikan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if($withdrawal) {
            // delete picture
            // if ($withdrawal->proof_of_payment && Storage::disk('public')->exists($withdrawal->proof_of_payment)) {
            //     Storage::disk('public')->delete($withdrawal->proof_of_payment);
            // }
            $withdrawal->update([
                'wd_state' => 99,
                'updated_by' => auth()->id()
            ]);
        }

        return redirect()->back()->with('success', "Data Penarikan anggota berhasil dibatalkan");
        
    }

    public function confirmation(Request $request) 
    {
        $request->validate([
            'wd_id' => 'required|exists:withdrawals,id'
        ]);

        $withdrawal = Withdrawal::with(['member'])->findOrFail($request->wd_id);
        if ($withdrawal->wd_state != 1) return redirect()->back()->with('error', 'Dokumen Penarikan tidak valid, atau sudah pernah dikonfrimasi.');
        
        $loan = Loan::with(['payments' => function ($query) {
            $query->where('lp_state', 1);
        }])
        ->where('member_id', $withdrawal->member_id)
        ->get();

        foreach ($loan as $key => $pay) {
            foreach ($pay->payments as $key => $settle) {
                $settle->lp_state = 2;
                $settle->remark = 'Pelunasan dari Penarikan';
                $settle->save();
                Member::where('id', $pay->member_id)->decrement('balance', $settle->lp_total*1);
            }
        }
        $member = Member::findOrFail($withdrawal->member_id);
        if ($member->balance < $withdrawal->wd_value) return redirect()->back()->with('error', 'Sisa Saldo Simpanan tidak cukup');

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'wd_state' => 2,
                'updated_by' => auth()->id()
            ]);
            Member::where('id', $withdrawal->member_id)->decrement('balance', $withdrawal->wd_value);
            
            DB::commit();
            return redirect()->back()->with('success', 'Penarikan berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan penarikan! Hubungi Administrator.')->withInput();
        }
    }
       
}
