<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Policy;
use App\Models\LoanAgunan;
use App\Models\SavingType;
use App\Models\LoanPayment;
use App\Models\AgunanPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::with('member')->all();
        return view('loans.index', compact('loans'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $success = 0;
        $failed = [];
        $template_title = "";
        foreach ($rows as $index => $row) {
            // cek template
            if ($index == 0) $template_title = strtoupper($row[0]);
            if ($template_title !== "TEMPLATE INJECT PINJAMAN") {
                $failed[] = ['row' => $index + 1, 'errors' => ["Template tidak valid"]];
                break;
            } 
            if ($index <= 2) continue; // skip header and info 

            $data = [
                'nip' => $row[0] ?? null,
                'loan_date' => $row[1] ?? null,
                'loan_tenor' => $row[2] ?? null,
                'loan_value' => $row[3] ?? null,
                'interest_percent' => $row[4] ?? null,
                'loan_payment' => $row[5] ?? null,
                'with_agunan' => $row[6] ?? null,
                'agunan_type' => $row[7] ?? null,
                'agunan_docYear' => $row[8] ?? null,
                'agunan_docNumber' => $row[9] ?? null,
                'agunan_docDetail' => $row[10] ?? null,
            ];

            $arr_time = explode('.', $data['loan_date']);

            if (count($arr_time) !== 3) {
                $failed[] = ['row' => $index + 1, 'errors' => ["Format tanggal tidak valid!"]];
                continue;
            }

            $timeStr = $arr_time[2] . '-' . $arr_time[1] . '-' . $arr_time[0];
            
            $loan_date = date('Ymd', strtotime($timeStr));
            $member = Member::where('nip', $data['nip'])->first();

            if ($member == null) {
                $failed[] = ['row' => $index + 1, 'errors' => ["NIP tidak ditemukan"]];
                continue;
            }
            // cek pinjaman(uang) exists
            $loan_exists = Loan::where('member_id', $member->id)
            ->where('loan_state', 2)->where('loan_type', 'UANG')->count();

            if ($loan_exists > 0) {
                $failed[] = ['row' => $index + 1, 'errors' => ["Pengguna memiliki pinjaman aktif yang belum diselesaikan"]];
                continue;
            }

            DB::beginTransaction();
            try {
                if ($member) {
                    $loan_code = Loan::generateCode();
                    $loan_value = Loan::formatIdrToNumeric($data['loan_value']);
                    
                    $cutOff = Policy::where('doc_type', 'GENERAL')
                    ->where('pl_name', 'cut_off_bulanan')->first();

                    $firstAngsuran = Loan::hitungAngsuranPertama($loan_date, $cutOff->pl_value)->format('Ymd');

                    // check due date
                    $date = new DateTime($firstAngsuran);
                    $date->add(new DateInterval('P' . $data['loan_tenor'] . 'M'));
                    $dueDate = $date->format('Ymd');

                     
                    $is_agunan = strtoupper($data['with_agunan']) == "YA" ? true : false;
                    
                    if ($is_agunan) { 

                        $typeAgunan = strtoupper($data['agunan_type']);
                        $yearAgunan = (int) $data['agunan_docYear'];
                        $numberAgunan = strtoupper($data['agunan_docNumber']);
                        $detailAgunan = $data['agunan_docDetail'];
                     
                    }
   
                    //insert loan
                    $loan = Loan::create([
                        'member_id' => $member->id,
                        'loan_type' => "UANG",
                        'loan_code' => $loan_code,
                        'loan_date' => $loan_date,
                        'loan_tenor' => $data['loan_tenor']*1,
                        'loan_value' => $loan_value,
                        'cut_off_record' => $cutOff->pl_value,
                        'interest_percent' => $data['interest_percent'],
                        'due_date' => $dueDate,
                        'loan_state' => 2,
                        'created_by' => auth()->id() ?? 1,
                        'updated_by' => auth()->id() ?? 1,
                    ]);

                    if ($loan && $is_agunan) {
                        $lag = LoanAgunan::create([
                            'loan_id' => $loan->id,
                            'agunan_type' => $typeAgunan,
                            'doc_year' => $yearAgunan,
                            'doc_number' => $numberAgunan,
                            'doc_detail' => $detailAgunan,
                            'updated_by' => auth()->id() ?? 1,
                            'created_by' => auth()->id() ?? 1,
                        ]);

                        $loan->ref_doc_id = $lag->id;
                        $loan->save();
                    }
            
                    // insert loan payment
                    $pay_finish = $data['loan_payment']*1;

                    $loan_total = $loan->loan_value;
                    $ln_date = $firstAngsuran;
                    for ($i=1; $i <= ($data['loan_tenor']*1) ; $i++) { 
                        $lp_val = round($loan->loan_value / $data['loan_tenor'], 0);
                        $lp_intr = round(($loan->loan_value*$loan->interest_percent)/100, 0);
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
                            'lp_state' => $i <= $pay_finish ? 2 : 1,
                            'remark' => '',
                            'proof_of_payment' => '',
                            'lp_forfeit' => 0,
                            'created_by' => auth()->id() ?? 1,
                            'updated_by' => auth()->id() ?? 1,
                
                        ]);
                        $loan_total -= $lp_val;
                        $ln_date = $lp_date;
                        
                    }
                        
                } else {
                    $failed[] = ['row' => $index + 1, 'errors' => ["NIP tidak ditemukan"]];
                    continue;
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $failed[] = ['row' => $index + 1, 'errors' => [" catch error: ".$th->getMessage()]];
                continue;
            }

            $success++;
        }

        return redirect()->back()->with('success', "$success data berhasil diimport")->with('failed', $failed);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            "loan_policies" => Policy::getLoanPolicies(),
            "loan_code" => Loan::generateCode(),
            "cut_off_day" => Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value')
        ];

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
        
        // cek pinjaman(uang) exists
        $loan_exists = Loan::where('member_id', $request->member_id)
        ->where('loan_state', 2)->where('loan_type', 'UANG')->count();
        if ($loan_exists > 0)
            return redirect()->back()->with('error', 'Terdapat pinjaman aktif yang belum diselesaikan.');

        // cek cut off
        $cutOff = Policy::where('doc_type', 'GENERAL')
        ->where('pl_name', 'cut_off_bulanan')->first();

        $firstAngsuran = Loan::hitungAngsuranPertama($request->loan_date, $cutOff->pl_value)->format('Ymd');

        // check due date
        $date = new DateTime($firstAngsuran);
        $date->add(new DateInterval('P' . $request->loan_tenor . 'M'));
        $dueDate = $date->format('Ymd');

        // get anggota
        $member = Member::findOrFail($request->member_id);
        $maxLoan = $member->maxLoanAmount();
        $tenor = $member->tenorAmount($loan_value);
        $currentLoan = $member->getTotalLoan();
        $is_agunan = isset($request->cbAgunan) ? true : false;
        // check policy max loan non agunan
        if ($loan_value > $maxLoan && $is_agunan === false)
            return redirect()->back()->with('error', 'Plafon pinjaman melebihi batas maksimal sebesar Rp ' . number_format($maxLoan, 0, ',', '.'));

        if ($request->loan_tenor > $tenor['tenorMax'] && $is_agunan === false)
            return redirect()->back()->with('error', 'Tenor pinjaman melebihi batas maksimal '.(int) $tenor['tenorMax'].' bulan, gunakan agunan untuk tenor yang lebih lama');
       
        if ($is_agunan) {
            $request->validate([
                'ln_agunan' => 'required|string',
                'ln_docYear' => 'required|integer',
                'ln_docNumber' => 'required|string',
                'ln_docDetail' => 'required|string',
            ], [
                'ln_agunan.required' => 'Jaminan diperlukan untuk pinjaman ini',
                'ln_docYear.required' => 'Tahun Jaminan diperlukan untuk pinjaman ini',
                'ln_docNumber.required' => 'Nomor Jaminan diperlukan untuk pinjaman ini',
                'ln_docDetail.required' => 'Data Jaminan diperlukan untuk pinjaman ini',
            ]);

            $typeAgunan = $request->ln_agunan;
            $agunan_policy = AgunanPolicy::getAgunanPolicy($typeAgunan, $request->ln_docYear);
            $maxTenorAgunan = 0;
            $validAgunan = LoanAgunan::checkAgunan($member->id, $typeAgunan, $request->ln_docNumber);
            
            switch ($typeAgunan) {
                case 'SERTIFIKAT':
                    $maxTenorAgunan = 48;
                    break;
                default:
                    $maxTenorAgunan = 36;
                    break;
            }

            if ($request->loan_tenor > $maxTenorAgunan)
                return redirect()->back()->with('error', 'Tenor pinjaman dengan agunan melebihi batas maksimal '.$maxTenorAgunan.' bulan');
            
            if (!$agunan_policy && $agunan_policy->agp_value > $loan_value)
                return redirect()->back()->with('error', 'Pinjaman dengan agunan melebihi batas maksimal '.$agunan_policy->agp_value.' bulan');
            
            if ($validAgunan['agn_valid'] !== true) {
                if (isset($validAgunan['member_share'][$member->no_kk])) {
                    $pass = false;
                    foreach ($validAgunan['member_share'][$member->no_kk] as $key => $msh) {
                        if ($member->id == $msh['id']) {
                            if (!$validAgunan['exists_on_member']) {
                                $pass = true;
                                break;
                            }
                        }
                    }
                    if (!$pass) return redirect()->back()->with('error', 'Agunan sudah pernah dibuat pengajuan pinjaman');
                } else {
                    return redirect()->back()->with('error', 'Agunan sudah pernah dibuat pengajuan pinjaman');
                }
            }

        }

        $monthlySaving = SavingType::getMonthlySaving();
        $totalBayar = ($currentLoan['total_bayar']*1) + ($loan_value / $request->loan_tenor*1) + ($monthlySaving);

        if ($currentLoan['total_pokok'] > $currentLoan['maxPokok'])
            return redirect()->back()->with('error', 'Angsuran Pokok melebihi batas maksimal '.number_format((int) $currentLoan['maxPokok'],0).' per anggota');

        if ($totalBayar > $currentLoan['maxBayar'])
            return redirect()->back()->with('error', 'Pembayaran Angsuran melebihi batas maksimal '.(int) $currentLoan['maxBayar'].'');

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
                'cut_off_record' => $cutOff->pl_value,
                'interest_percent' => $request->interest_percent,
                'due_date' => $dueDate,
                'loan_state' => $request->loan_status,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            if ($loan && $is_agunan) {
                $lag = LoanAgunan::create([
                    'loan_id' => $loan->id,
                    'agunan_type' => $request->ln_agunan,
                    'doc_year' => $request->ln_docYear,
                    'doc_number' => $request->ln_docNumber,
                    'doc_detail' => $request->ln_docDetail,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $loan->ref_doc_id = $lag->id;
                $loan->save();
            }
    
            // insert loan payment
            $loan_total = $loan->loan_value;
            $ln_date = $firstAngsuran;
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
            "loan" => Loan::with('member','payments', 'loanAgunan')->findOrFail($id),
        ];

        return view('loans.view', $data);
    }

    public function edit(string $id)
    {
        $loan = Loan::with('member', 'loanAgunan')->findOrFail($id);
        // dd($loan);
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
