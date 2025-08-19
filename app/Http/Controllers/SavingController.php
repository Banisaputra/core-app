<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SavingController extends Controller
{
    public function index() 
    {
        $savings = Saving::with('member', 'svType')->latest()->get();
        return view('savings.index', compact('savings'));
    }

    public function create() 
    {
        $data = [
            "sv_types" => SavingType::where('is_transactional', 1)->get(),
            "sv_code" => Saving::generateCode()
        ];
        return view('savings.create', $data);
    }

    public function generate() 
    {
        $data = [
            "sv_types" => SavingType::all(),
        ];
        return view('savings.generate', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'sv_type_id' => 'required|exists:saving_types,id',
            'sv_value' => 'required|integer',
            'sv_date' => 'required|date',
            'proof_of_payment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $svn_code = Saving::generateCode();

        // image path
        $photoPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $photoPath = $request->file('proof_of_payment')->store('proof_of_payment', 'public');
            // jika symlink tidak tersedia
            // // start
            $sourcePath = storage_path('app/public/' . $photoPath);
            $destinationPath = public_path('storage/' . $photoPath);
             
            File::ensureDirectoryExists(dirname($destinationPath));
            
            File::copy($sourcePath, $destinationPath);
            // // end
        }
        

        $pokok = SavingType::where('name', 'like', 'Pokok')->first();
        $wajib = SavingType::where('name', 'like', 'Wajib')->first();
        $month = date('m', strtotime($request->sv_date));
        $year = date('Y', strtotime($request->sv_date));

        $startOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01');
        $endOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '31');

        $w_exists = Saving::where('member_id', $request->member_id)
            ->where('sv_type_id', $wajib->id)
            ->where('sv_state', '<>', 99)
            ->whereBetween('sv_date', [$startOfMonth, $endOfMonth])
            ->exists();

        $p_exists = Saving::where('member_id', $request->member_id)
            ->where('sv_type_id', $pokok->id)
            ->where('sv_state', '<>', 99)
            ->exists();

        if ($w_exists && $wajib->id == $request->sv_type_id) {
            return back()->with('error', 'Anggota sudah melakukan simpanan wajin pada bulan ini.')->withInput();
        } else if ($p_exists && $pokok->id == $request->sv_type_id) {
            return back()->with('error', 'Anggota sudah pernah melakukan simpanan pokok')->withInput();
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

    public function generated(Request $request) 
    {
        $request->validate([
            'periode' => 'required',
            'sv_type_id' => 'required',
            'member_id' => 'nullable|array',
            'member_id.*' => 'exists:members,id',
        ]);

        // loop member
        $members = Member::pluck('id');
        $wajib = SavingType::where('name', 'like', 'Wajib')->first();
        $month = date('m', strtotime($request->periode));
        $year = date('Y', strtotime($request->periode));

        $startOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01');
        $endOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '31');

        foreach ($members as $key => $id) {
            if (!in_array($id, $request->member_id ?? [])) {
                if ($wajib->id == $request->sv_type_id) {
                    $exists = Saving::where('member_id', $id)
                        ->where('sv_type_id', $wajib->id)
                        ->whereBetween('sv_date', [$startOfMonth, $endOfMonth])
                        ->exists();
    
                    if ($exists) {
                        return back()->with('error', 'Ada anggota yang sudah melakukan simpanan pada bulan ini untuk jenis simpanan tersebut.')->withInput();
                    }
                }

                try {
                    $svType=SavingType::findOrFail($request->sv_type_id);
                    Saving::create([
                        "sv_code" => Saving::generateCode(date('ym', strtotime($request->periode))),
                        "sv_date" => date('Ymd', strtotime($request->periode)),
                        "member_id" => $id,
                        "sv_type_id" => $request->sv_type_id,
                        "sv_value" => $svType->value ?? 0, // sesuikan settingan
                        "created_by" => auth()->id(),
                        "updated_by" => auth()->id(),
                    ]);
                } catch (\Throwable $th) {
                    // Log the full error for debugging
                    \Log::error('Error fetching record: ' . $th->getMessage(), [
                        'exception' => $th,
                    ]);
                    return redirect()->back()->with('error', 'Anggota sudah melakukan jenis simpanan bulan ini.');
                }    
            }
        }
        return redirect()->back()->with('success', 'Data simpanan berhasil digenerate.');
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
            if ($saving->proof_of_payment && Storage::disk('public')->exists($saving->proof_of_payment)) {
                Storage::disk('public')->delete($saving->proof_of_payment);
            }
            // jika symlink tidak tersedia
            if ($saving->proof_of_payment && File::exists(public_path('storage/' . $saving->proof_of_payment))) {
                File::delete(public_path('storage/' . $saving->proof_of_payment));
            }
            
            $newPhoto = $request->file('proof_of_payment')->store('proof_of_payment', 'public');
            $saving->proof_of_payment = $newPhoto;
            
            // jika symlink tidak tersedia
            // // start
            $sourcePath = storage_path('app/public/' . $newPhoto);
            $destinationPath = public_path('storage/' . $newPhoto);
            
            File::ensureDirectoryExists(dirname($destinationPath));
            
            File::copy($sourcePath, $destinationPath);
            // // end
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
            // if ($saving->proof_of_payment && Storage::disk('public')->exists($saving->proof_of_payment)) {
            //     Storage::disk('public')->delete($saving->proof_of_payment);
            // }
            $saving->update([
                'sv_state' => 99,
                'updated_by' => auth()->id()
            ]);
        }

        return redirect()->back()->with('success', "Data simpanan anggota berhasil dibatalkan");
        
    }

    public function confirmation(Request $request) 
    {
        $request->validate([
            'sv_id' => 'required|exists:savings,id'
        ]);

        $saving = Saving::with(['member'])->findOrFail($request->sv_id);
        if ($saving->sv_state != 1) return redirect()->back()->with('error', 'Dokumen Simpanan tidak valid, atau sudah pernah dikonfrimasi');

        DB::beginTransaction();
        try {
            $saving->update([
                'sv_state' => 2,
                'updated_by' => auth()->id()
            ]);
            Member::where('id', $saving->member_id)->increment('balance', $saving->sv_value);
            
            DB::commit();
            return redirect()->back()->with('success', 'Simpanan berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan simpanan: ' . $e->getMessage())->withInput();
        }
    }

}
