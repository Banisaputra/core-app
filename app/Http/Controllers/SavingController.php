<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Policy;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SavingController extends Controller
{ 
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = [
                'id',
                'sv_date',
                'member_id',
                'sv_type_id',
                'sv_value',
            ];

            $search = $request->input('search.value');
            $orderColumnIndex = $request->order[0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request->order[0]['dir'] ?? 'desc';

            $query = Saving::with('member', 'svType')
                ->select('savings.*');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sv_value', 'like', "%{$search}%")
                    ->orWhere('sv_code', 'like', "%{$search}%")
                    ->orWhere('sv_date', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $totalFiltered = $query->count();

            $data = $query
                ->orderBy($orderColumn, $orderDir)
                ->offset($request->start)
                ->limit($request->length)
                ->get();

            $start = $request->start;
            $formatted = [];
            foreach ($data as $index => $saving) {

                // state
                $stateText = match($saving->sv_state) {
                    99 => '<span class="text-danger">Dibatalkan</span>',
                    2  => '<span class="text-success">Dikonfirmasi</span>',
                    default => '<span class="text-info">Pengajuan</span>',
                };

                // Action button
                $action = '
                    <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-muted sr-only">Action</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="'.route('savings.show', $saving->id).'">View</a>
                        <a class="dropdown-item" href="'.route('savings.edit', $saving->id).'">Edit</a>
                        <form action="'.route('savings.destroy', $saving->id).'" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin batal?\')">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="dropdown-item">Batalkan</button>
                        </form>
                    </div>
                ';
                
                $formatted[] = [
                    'rownum' => $start + $index + 1,
                    'sv_code' => $saving->sv_code,
                    'member' => [
                        'nip' => $saving->member->nip ?? '-',
                        'name' => $saving->member->name ?? '-',
                    ],
                    'sv_date' => date('d M Y', strtotime($saving->sv_date)),
                    'type' => $saving->svType->name ?? '-',
                    'sv_value' => number_format($saving->sv_value),
                    'sv_state' => $stateText,
                    'action' => $action,
                ];
            }

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => Saving::count(),
                "recordsFiltered" => $totalFiltered,
                "data" => $formatted,
            ]);
        }

        return view('savings.index');
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
            $photoPath = $request->file('proof_of_payment')->store('proof_of_payment', 'public_direct');
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
        $sht = SavingType::where('name', 'like', 'SHT')->first();
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

        $s_exists = Saving::where('member_id', $request->member_id)
            ->where('sv_type_id', $sht->id)
            ->where('sv_state', '<>', 99)
            ->whereBetween('sv_date', [$startOfMonth, $endOfMonth])
            ->exists();

        if ($w_exists && $wajib->id == $request->sv_type_id) {
            return back()->with('error', 'Anggota sudah melakukan simpanan wajib pada bulan ini.')->withInput();
        } else if ($p_exists && $pokok->id == $request->sv_type_id) {
            return back()->with('error', 'Anggota sudah pernah melakukan simpanan pokok.')->withInput();
        } else if ($s_exists && $sht->id == $request->sv_type_id) {
            return back()->with('error', 'Anggota sudah pernah melakukan simpanan SHT pada bulan ini.')->withInput();
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
        $members = Member::where('is_transactional', 1)->pluck('id')->toArray();;
        $wajib = SavingType::where('name', 'like', 'Wajib')->first();
        $month = date('m', strtotime($request->periode));
        $year = date('Y', strtotime($request->periode));

        $startOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01');
        $endOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '31');

        // cek cut off
        $cutOff = Policy::where('doc_type', 'GENERAL')
        ->where('pl_name', 'cut_off_bulanan')->value('pl_value');

        $tglSaving = Loan::hitungAngsuranPertama(date('Y-m-d', strtotime($startOfMonth)), $cutOff)->format('Ymd');

        $periode_start = new DateTime("$year-$month-".($cutOff + 1)."");
        $periode_start->modify("-1 month");
        $periode_end = new DateTime("$year-$month-".($cutOff ?? 0)."");

        foreach ($members as $key => $id) {
            if (!in_array($id, $request->member_id ?? [])) {
                if ($wajib->id == $request->sv_type_id) {
                    $exists = Saving::where('member_id', $id)
                        ->where('sv_type_id', $wajib->id)
                        ->where('sv_state', '<>', 99)
                        ->whereBetween('sv_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
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
                        "remark" => 'Generate otomatis untuk periode '.date('F Y', strtotime($request->periode)),
                        "created_by" => auth()->id(),
                        "updated_by" => auth()->id(),
                    ]);
                } catch (\Throwable $th) {
                    // Log the full error for debugging
                    \Log::error('Error fetching record: ' . $th->getMessage(), [
                        'exception' => $th,
                    ]);
                    return redirect()->back()->with('error', 'Anggota sudah melakukan jenis simpanan pada bulan ini.');
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
            
            $newPhoto = $request->file('proof_of_payment')->store('proof_of_payment', 'public_direct');
            $saving->proof_of_payment = $newPhoto;
            
            // jika symlink tidak tersedia
            // // start
            // $sourcePath = storage_path('app/public/' . $newPhoto);
            // $destinationPath = public_path('storage/' . $newPhoto);
            
            // File::ensureDirectoryExists(dirname($destinationPath));
            
            // File::copy($sourcePath, $destinationPath);
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
            return back()->with('error', 'Gagal menyimpan simpanan! Hubungi Administrator.')->withInput();
        }
    }

}
