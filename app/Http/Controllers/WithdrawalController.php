<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
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
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'wd_date' => 'required|date',
            'wd_value' => 'required|integer',
            'remark' => 'required|string',
            'proof_of_withdrawal' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $wd_code = Withdrawal::generateCode();

        $photoPath = null;
        if($request->hasFile('proof_of_withdrawal')) {
            $photoPath = $request->file('proof_of_withdrawal')->store('proof_of_withdrawal', 'public');
        }

        Withdrawal::create([
            'wd_code' => $wd_code,
            'member_id' => $request->member_id,
            'wd_date' => date('Ymd', strtotime($request->wd_date)),
            'wd_value' => $request->wd_value,
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
            $newPhoto = $request->file('proof_of_withdrawal')->store('proof_of_withdrawal', 'public');
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
            if ($withdrawal->proof_of_withdrawal && Storage::disk('public')->exists($withdrawal->proof_of_withdrawal)) {
                Storage::disk('public')->delete($withdrawal->proof_of_withdrawal);
            }
            $withdrawal->delete();
        }

        return redirect()->back()->with('success', "Data penarikan anggota berhasil dihapus");
    }
}
