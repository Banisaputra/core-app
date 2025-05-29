<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SavingController extends Controller
{
    public function index() 
    {
        $savings = Saving::with('member', 'svType')->latest()->paginate(10);
        return view('savings.index', compact('savings'));
    }

    public function create() 
    {
        $data = [
            "sv_types" => SavingType::all(),
            "sv_code" => Saving::generateCode()
        ];
        return view('savings.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'sv_type_id' => 'required|exists:saving_types,id',
            'sv_value' => 'required|integer',
            'sv_date' => 'required|date',
            'proof_of_payment' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $svn_code = Saving::generateCode();

        // image path
        $photoPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $photoPath = $request->file('proof_of_payment')->store('proof_of_payment', 'public');
        }
        
        Saving::create([
            "sv_code" => $svn_code,
            "sv_date" => date('Ymd', strtotime($request->sv_date)),
            "member_id" => $request->member_id,
            "sv_type_id" => $request->sv_type_id,
            "sv_value" => $request->sv_value,
            "proof_of_payment" => $photoPath,
            "created_by" => auth()->id(),
            "updated_by" => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data simpanan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $saving = Saving::with('member', 'svType')->findOrFail($id);
        return view('savings.view', compact('saving'));
    }

     public function edit(string $id)
    {
        $saving = Saving::with('member')->findOrFail($id);
        $sv_types = SavingType::all();
        return view('savings.edit', compact('saving', 'sv_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $saving = Saving::findOrFail($id);
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'sv_type_id' => 'required|exists:saving_types,id',
            'sv_value' => 'required|integer',
            'sv_date' => 'required|date',
            'proof_of_payment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file baru
        if ($request->hasFile('proof_of_payment')) {
            // Hapus foto lama jika ada
            if ($saving->proof_of_payment && Storage::disk('public')->exists($saving->proof_of_payment)) {
                Storage::disk('public')->delete($saving->proof_of_payment);
            }

            // Simpan foto baru
            $newPhoto = $request->file('proof_of_payment')->store('proof_of_payment', 'public');
            $saving->proof_of_payment = $newPhoto;
        } 

        // Update data
        $saving->member_id = $request->member_id;
        $saving->sv_type_id = $request->sv_type_id;
        $saving->sv_date = date('Ymd', strtotime($request->sv_date));
        $saving->sv_value = $request->sv_value;
        $saving->updated_by = auth()->id();
        $saving->save();

        return redirect()->route('savings.edit', $saving->id)->with('success', 'Data simpanan berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $saving = Saving::findOrFail($id);
        if($saving) {
            // delete picture
            if ($saving->proof_of_payment && Storage::disk('public')->exists($saving->proof_of_payment)) {
                Storage::disk('public')->delete($saving->proof_of_payment);
            }
            $saving->delete();
        }

        return redirect()->back()->with('success', "Data simpanan anggota berhasil dihapus");
        
    }
}
