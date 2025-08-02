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
        $policy =  DB::table('policies')
            ->join('policy_detail', 'policies.id', '=', 'policy_detail.pl_id')
            ->where('policies.doc_type', 'TERMS')
            ->get();
        $filePath = $policy[0]->file_path ?? 'empty';

        if (!Storage::disk('public')->exists($filePath)) {
            $data['pdfExists'] = false;
        } else {
            $fileUrl = asset('storage/' . $filePath);
            $data['pdfExists'] = true;
            $data['fileUrl'] = $fileUrl;
        }

        // get saving type
        $svTypes = SavingType::all();
        if ($svTypes) $data['svTypes'] = $svTypes;

        


        return view("policies.index", $data);
    }

    // term
    public function uploadTerms(Request $request)
    {
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
            $policy = Policy::where('doc_type', 'TERMS')->first();

            $exists = DB::table('policy_detail')->where('pl_id', $policy->id)->get();
            if (count($exists) > 0) {
                DB::table('policy_detail')->where('pl_id', $policy->id)->delete();
                Storage::disk('public')->delete($exists[0]->file_path);
            }

            DB::table('policy_detail')
            ->insert([
                'pl_id' => $policy->id,
                'file_path' => $photoPath,
                'sort' => 1
            ]);
                
            DB::commit();
            return redirect()->route('policy.index')->with('success', 'File syarat dan ketentuan berhasil diunggah.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pengaturan: Hubungi administrator' . $e->getMessage())->withInput();
        }

    }

    // saving


   
}
