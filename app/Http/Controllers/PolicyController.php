<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PolicyController extends Controller
{
    public function index() {
        $data = [];

        // get file terms pdf
        $policy = Policy::where('doc_type', 'TERMS')
        ->where('pl_name', 'policy_information')->first();

        $filePath = $policy->pl_value ?? 'empty';
        if (!Storage::disk('public')->exists($filePath)) {
            $data['pdfExists'] = false;
        } else {
            $fileUrl = asset('storage/' . $filePath);
            $data['pdfExists'] = true;
            $data['fileUrl'] = $fileUrl;
        }

        // get saving type
        $svTypes = SavingType::all();
        $data['svTypes'] = [];
        if ($svTypes) $data['svTypes'] = $svTypes;

        // get loan
        $loanPolicies = Policy::where('doc_type', 'LOAN')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->pl_name => [
                    'id' => $policy->id,
                    'name' => $policy->pl_name,
                    'value' => $policy->pl_value
                ]
            ];
        })
        ->all();
        $data['loanPolicies'] = [];
        if ($loanPolicies) $data['loanPolicies'] = $loanPolicies;


        return view("policies.index", $data);
    }

    // term
    public function uploadTerms(Request $request) {
        // untuk TERMS -> file_path
        $request->validate([
            'fileTerms' => 'required|mimes:pdf|max:10240', //10124
        ]);

        // Simpan file ke storage
        $photoPath = '';
        if ($request->hasFile('fileTerms')) {
            $photoPath = $request->file('fileTerms')->store('terms', 'public');
        }
        DB::beginTransaction();
        try {
            $policy = Policy::where('doc_type', 'TERMS')
            ->where('pl_name', 'policy_information')->first();

            if ($policy) {
                if ($policy->pl_value != '') {
                    Storage::disk('public')->delete($policy->pl_value);
                }
               $policy->pl_value = $photoPath;
               $policy->save();

            } else {
                Policy::create([
                    'pl_name' => 'policy_information',
                    'doc_type' => 'TERMS',
                    'description' => 'Syarat dan ketentuan dalam bentuk PDF',
                    'pl_value' => $photoPath
                ]);
            }

            DB::commit();
            return redirect()->route('policy.index')->with('success', 'File syarat dan ketentuan berhasil diunggah.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }

    }

    // loan
    public function loanUmum(Request $request) {
        $bungaPinjam = str_replace(',', '.', $request->bunga ?? 0);
        $maxPkAngsur = str_replace('.', '', $request->maxAngsuranPokok ?? 0);
        $minPkAngsur = str_replace('.', '', $request->minAngsuranPokok ?? 0);
        $maxPtStaff = str_replace('.', '', $request->maxPotongStaff ?? 0);
        $maxPtOperator = str_replace('.', '', $request->maxPotongOperator ?? 0);
        
        DB::beginTransaction();
        try {
            // bunga
            $bunga = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'bunga_pinjaman')->first();
            if ($bunga) {
                if ($bungaPinjam != '') {
                    DB::table('policies')->where('id',$bunga->id)
                    ->update([
                        'pl_value' => $bungaPinjam
                    ]);
                }
            } else {
                DB::table('policies')->insert([
                    'pl_name' => 'bunga_pinjaman',
                    'doc_type' => 'LOAN',
                    'description' => 'bunga pinjaman',
                    'pl_value' => $bungaPinjam
                ]);
            }
            // max angsur pokok
            $mxpa = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_pokok_angsuran')->first();
            if ($mxpa) {
                if ($maxPkAngsur != '') {
                    DB::table('policies')->where('id', $mxpa->id)
                    ->update([
                        'pl_value' => $maxPkAngsur
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_pokok_angsuran',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal nominal angsuran pokok (diluar bunga)',
                    'pl_value' => $maxPkAngsur
                ]);
            }
            // min angsur pokok
            $mnpa = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'min_pokok_angsuran')->first();
            if ($mnpa) {
                if ($minPkAngsur != '') {
                    DB::table('policies')->where('id', $mnpa->id)
                    ->update([
                        'pl_value' => $minPkAngsur
                    ]);
                }
            } else {
                DB::table('policies')->insert([
                    'pl_name' => 'min_pokok_angsuran',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai minimal nominal angsuran pokok (diluar bunga)',
                    'pl_value' => $maxPkAngsur
                ]);
            }
            // max potong gaji staf
            $mxpgs = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_potong_gaji_staff')->first();
            if ($mxpgs) {
                if ($maxPtStaff != '') {
                    $mxpgs->pl_value = $maxPtStaff;
                    $mxpgs->save();
                }
            } else {
                Policy::create([
                    'pl_name' => 'max_potong_gaji_staff',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal nominal potongan gaji untuk jabatan staff',
                    'pl_value' => $maxPtStaff
                ]);
            }
            // max potong gaji operator
            $mxpgo = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_potong_gaji_operator')->first();
            if ($mxpgo) {
                if ($maxPtOperator != '') {
                    $mxpgo->pl_value = $maxPtOperator;
                    $mxpgo->save();
                }
            } else {
                Policy::create([
                    'pl_name' => 'max_potong_gaji_operator',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal nominal potongan gaji untuk jabatan operator',
                    'pl_value' => $maxPtOperator
                ]);
            }


            DB::commit();
            return redirect()->route('policy.index')->with('success', 'File syarat dan ketentuan berhasil diunggah.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }


    }

    public function loanKhusus(Request $request) {
        dd($request->all());

    }

    public function loanAgunan(Request $request) {
        dd($request->all());

    }


   
}
