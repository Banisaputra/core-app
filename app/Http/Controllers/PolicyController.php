<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\SavingType;
use App\Models\AgunanPolicy;
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
        
        // get agunan
        $agunans = AgunanPolicy::orderBy('agp_value')->get();
        $data['agunans'] = [];
        if ($agunans) $data['agunans'] = $agunans;
        

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
                if ($bungaPinjam > 0) {
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
                if ($maxPkAngsur > 0) {
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
                if ($minPkAngsur > 0) {
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
                if ($maxPtStaff > 0) {
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
                if ($maxPtOperator > 0) {
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
            return redirect()->route('policy.index')->with('success', 'Syarat Ketentuan Umum berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }


    }
   
    public function loanKhusus(Request $request) {
        $agunan01 = str_replace('.', '', $request->maxAgunan01 ?? 0);
        $agunan15 = str_replace('.', '', $request->maxAgunan15 ?? 0);
        $agunan50 = str_replace('.', '', $request->maxAgunan50 ?? 0);
        $tenorTA = $request->maxTenorTA ?? 0;
        $tenorDA = $request->maxTenorDA ?? 0;
        
        DB::beginTransaction();
        try {
            // max angsur kurang dari 1 tahun
            $mxa01 = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_agunan_0_1')->first();
            if ($mxa01) {
                if ($agunan01 > 0) {
                    DB::table('policies')->where('id', $mxa01->id)
                    ->update([
                        'pl_value' => $agunan01
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_agunan_0_1',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal pinjaman kurang dari 1 tahun',
                    'pl_value' => $agunan01
                ]);
            }
            // max angsur kurang dari 5 tahun lebih dari 1 tahun
            $mxa15 = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_agunan_1_5')->first();
            if ($mxa15) {
                if ($agunan15 > 0) {
                    DB::table('policies')->where('id', $mxa15->id)
                    ->update([
                        'pl_value' => $agunan15
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_agunan_1_5',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal pinjaman kurang dari 5 tahun dan lebih dari 1 tahun',
                    'pl_value' => $agunan15
                ]);
            }
            // max angsur lebih dari 5 tahun
            $mxa50 = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_agunan_5_0')->first();
            if ($mxa50) {
                if ($agunan50 > 0) {
                    DB::table('policies')->where('id', $mxa50->id)
                    ->update([
                        'pl_value' => $agunan50
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_agunan_5_0',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal pinjaman lebih dari 5 tahun',
                    'pl_value' => $agunan50
                ]);
            }

            // tenor tanpa agunan
            $tna = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_tenor_tanpa_agunan')->first();
            if ($tna) {
                if ($tenorTA > 0) {
                    DB::table('policies')->where('id', $tna->id)
                    ->update([
                        'pl_value' => $tenorTA
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_tenor_tanpa_agunan',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal tenor pinjaman tanpa agunan',
                    'pl_value' => $tenorTA
                ]);
            }

            // tenor dengan agunan
            $tda = Policy::where('doc_type', 'LOAN')
            ->where('pl_name', 'max_tenor_dengan_agunan')->first();
            if ($tda) {
                if ($tenorDA > 0) {
                    DB::table('policies')->where('id', $tda->id)
                    ->update([
                        'pl_value' => $tenorDA
                    ]);
                }
            } else {
                DB::table('policies')
                ->insert([
                    'pl_name' => 'max_tenor_dengan_agunan',
                    'doc_type' => 'LOAN',
                    'description' => 'nilai maksimal tenor pinjaman dengan agunan',
                    'pl_value' => $tenorDA
                ]);
            }
             
            DB::commit();
            return redirect()->route('policy.index')->with('success', 'Syarat Ketentuan Khusus berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }


    }

    public function loanAgunan(Request $request) {
        $agMotor = str_replace('.', '', $request->bpkbMotor ?? 0);
        $agMotorStart = $request->startBPM;
        $agMotorEnd = $request->endBPM;
        $agMobil = str_replace('.', '', $request->bpkbMobil ?? 0);
        $agMobilStart = $request->startBPC;
        $agMobilEnd = $request->endBPC;
        $agSertify = str_replace('.', '', $request->sertify ?? 0);
        $agSertifyStart = $request->startSertify;
        $agSertifyEnd = $request->endSertify;
        
        DB::beginTransaction();
        try {
            // cek agunan type bpkb motor
            $agpMt = AgunanPolicy::where('doc_type', 'MOTOR')
            ->where(function($query) use ($agMotorStart, $agMotorEnd) {
                $query->where('start_year', '=', $agMotorStart)
                      ->orWhere('end_year', '=', $agMotorEnd);
            })->first();
            // dd($agpMt);
            if ($agpMt) {
                if ($agMotor > 0) {
                    DB::table('agunan_policies')->where('id', $agpMt->id)
                    ->update([
                        'start_year' => $agMotorStart,
                        'end_year' => $agMotorEnd,
                        'agp_value' => $agMotor
                    ]);
                }
            } else {
                if ($agMotor > 0 && $agMotorStart && $agMotorEnd) {
                    DB::table('agunan_policies')
                    ->insert([
                        'agp_name' => 'bpkb_motor',
                        'doc_type' => 'MOTOR',
                        'description' => 'nilai maksimal pinjaman dengan agunan bpkb motor',
                        'agp_value' => $agMotor,
                        'start_year' => $agMotorStart,
                        'end_year' => $agMotorEnd
                    ]);
                }
            }
            
            // cek agunan type bpkb mobil
            $agpMb = AgunanPolicy::where('doc_type', 'MOBIL')
            ->where(function($query) use ($agMobilStart, $agMobilEnd) {
                $query->where('start_year', '=', $agMobilStart)
                      ->orWhere('end_year', '=', $agMobilEnd);
            })->first();
            if ($agpMb) {
                if ($agMobil > 0) {
                    DB::table('agunan_policies')->where('id', $agpMb->id)
                    ->update([
                        'start_year' => $agMobilStart,
                        'end_year' => $agMobilEnd,
                        'agp_value' => $agMobil
                    ]);
                }
            } else {
                if ($agMobil > 0 && $agMobilStart && $agMobilEnd) {
                    DB::table('agunan_policies')
                    ->insert([
                        'agp_name' => 'bpkb_mobil',
                        'doc_type' => 'MOBIL',
                        'description' => 'nilai maksimal pinjaman dengan agunan bpkb mobil',
                        'agp_value' => $agMobil,
                        'start_year' => $agMobilStart,
                        'end_year' => $agMobilEnd
                    ]);
                }
            }
           
            // cek agunan type sertifikat
            $agpSf = AgunanPolicy::where('doc_type', 'SERTIFIKAT')
            ->where(function($query) use ($agSertifyStart, $agSertifyEnd) {
                $query->where('start_year', '=', $agSertifyStart)
                      ->orWhere('end_year', '=', $agSertifyEnd);
            })->first();
            if ($agpSf) {
                if ($agSertify > 0) {
                    DB::table('agunan_policies')->where('id', $agpSf->id)
                    ->update([
                        'start_year' => $agSertifyStart,
                        'end_year' => $agSertifyEnd,
                        'agp_value' => $agSertify
                    ]);
                }
            } else {
                if ($agSertify > 0 && $agSertifyStart && $agSertifyEnd) {
                    DB::table('agunan_policies')
                    ->insert([
                        'agp_name' => 'sertifikat_letter',
                        'doc_type' => 'SERTIFIKAT',
                        'description' => 'nilai maksimal pinjaman dengan agunan sertifikat',
                        'agp_value' => $agSertify,
                        'start_year' => $agSertifyStart,
                        'end_year' => $agSertifyEnd
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('policy.index')->with('success', 'Syarat Ketentuan Agunan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }
    }
   
    public function agDestroy(string $id)
    {
        $agp = AgunanPolicy::findOrFail($id);
        if ($agp) $agp->delete();

        return redirect()->back()->with('success', "Syarat Agunan berhasil dihapus");
        
    }

}
