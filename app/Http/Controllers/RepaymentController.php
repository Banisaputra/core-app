<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LoanPayment::with(['loan', 'loan.member']);

            // filter tanggal
            if ($request->filled('date_start') && $request->filled('date_end')) {
                $query->whereBetween('lp_date', [
                    date('Ymd', strtotime($request->date_start)),
                    date('Ymd', strtotime($request->date_end))
                ]);
            }
            // filter status
            if ($request->filled('status')) {
                $query->where('lp_state', $request->status);
            }
    
            // filter jenis
            if ($request->filled('type')) {
                $query->whereHas('loan', function ($q) use ($request) {
                    $q->where('loan_type', $request->type);
                });
            }

            $columns = [
                'id',
                'lp_date',
                'member_id',
                'loan_type',
                'lp_value',
            ];

            $search = $request->input('search.value');
            $orderColumnIndex = $request->order[0]['column'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request->order[0]['dir'] ?? 'desc';

            $all_count = $query->count();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('lp_value', 'like', "%{$search}%")
                    ->orWhere('lp_code', 'like', "%{$search}%")
                    ->orWhere('lp_date', 'like', "%{$search}%")
                    ->orWhereHas('loan.member', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('loan.member', function ($m) use ($search) {
                        $m->where('nip', 'like', "%{$search}%");
                    });
                });
            }

            $totalFiltered = $query->count();

            $data = $query
                ->orderBy($orderColumn, $orderDir)
                ->offset($request->start)
                ->limit($request->length == -1 ? $all_count : $request->length)
                ->get(); 
                
            $start = $request->start;
            $formatted = [];
            foreach ($data as $index => $loanPay) {

                // state
                $stateText = match($loanPay->lp_state) {
                    99  => '<span class="text-danger">Ditutup</span>',
                    2  => '<span class="text-success">Dibayarkan</span>',
                    default => '<span class="text-info">Pending</span>',
                };

                // Action button
                $action = '
                    <button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-muted sr-only">Action</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="'. route('repayments.settle') .'" method="POST" style="display: inline;">
                            '.csrf_field().'
                            <input type="hidden" name="lp_id" value="'.$loanPay->id.'">
                            <button type="submit" id="btnSettle" class="dropdown-item">Pelunasan</button>
                        </form>
                    </div>
                ';
                
                $formatted[] = [
                    'id' => $loanPay->id,
                    'rownum' => $start + $index + 1,
                    'loan_code' => $loanPay->loan->loan_code ?? '-',
                    'lp_code' => $loanPay->lp_code,
                    'member' => [
                        'nip' => $loanPay->loan->member->nip ?? '-',
                        'name' => $loanPay->loan->member->name ?? '-',
                    ],
                    'lp_date' => date('d M Y', strtotime($loanPay->lp_date)),
                    'lp_code' => $loanPay->lp_code,
                    'type' => $loanPay->loan->loan_type ?? '-',
                    'lp_value' => number_format($loanPay->lp_value),
                    'lp_state' => $stateText,
                    'action' => $action,
                ];
            }

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => LoanPayment::count(),
                "recordsFiltered" => $totalFiltered,
                "data" => $formatted,
            ]);
        }




        // $lp_date = date('ym');
        // $loanDetails = LoanPayment::with(['loan', 'loan.member'])
        //     ->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date])
        //     ->whereHas('loan', function($query) {
        //         $query->where('loan_state', 2);
        //     })
        //     ->get();

        return view('repayments.index');
    }
    
    public function create() 
    {
        $loans = session('loans', []);
        $member = session('member', []);
        $type = session('type', []);
        
        return view('repayments.fast-settle', compact('loans','member','type'));
    }

    public function generate()
    {
        return view('repayments.generate');
    }

    public function settle(Request $request) 
    {
        $loanPay = LoanPayment::findOrFail($request->lp_id);
        if ($loanPay->lp_state == 2) return redirect()->back()->with('error', 'Pembayaran sudah pernah dilunasi');
        $loanPay->lp_state = 2;
        $loanPay->save();

        // cek pinjaman
        $loan = Loan::findOrFail($loanPay->loan_id);
        $allPaid = LoanPayment::where('loan_id', $loan->id)
            ->where('lp_state', 2)
            ->count();
        $totalPayments = LoanPayment::where('loan_id', $loan->id)->count();
        if ($allPaid >= $totalPayments) {
            $loan->loan_state = 3; // lunas
            $loan->save();
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil dilunasi');
    }

    public function generated(Request $request) 
    {
        $request->validate([
            'periode' => 'required',
            'member_id' => 'nullable|array',
            'member_id.*' => 'exists:members,id',
        ]);

        // loop member
        $members = Member::pluck('id');
        $month = date('m', strtotime($request->periode));
        $year = date('Y', strtotime($request->periode));

        $startOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01');
        $endOfMonth = (int) ($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '31');

        foreach ($members as $key => $id) {
            if (!in_array($id, $request->member_id ?? [])) {

                try {
                    $loanDetails = LoanPayment::select('loan_payments.*')
                    ->join('loans', 'loans.id', '=', 'loan_payments.loan_id')
                    ->join('members', 'members.id', '=', 'loans.member_id')
                    ->whereRaw("DATE_FORMAT(loan_payments.lp_date, '%Y%m') = ?", [$year.$month])
                    ->where('members.id', $id)
                    ->get();

                    foreach ($loanDetails as $key => $loan) {
                        $loan->lp_state = 2;
                        $loan->remark = "pelunasan dari generate pelunasan";
                        $loan->updated_by = auth()->id();
                        $loan->save();
                    }
                } catch (\Throwable $th) {
                    // Log the full error for debugging
                    \Log::error('Error fetching record: ' . $th->getMessage(), [
                        'exception' => $th,
                    ]);
                    return redirect()->back()->with('error', 'Anggota sudah melakukan pelunasan bulan ini.');
                }
            }
        }
        return redirect()->back()->with('success', 'Data pelunasan berhasil digenerate.');
    }

    public function getSettle (Request $request) 
    {
        $member = Member::findOrFail($request->member_id)->toArray();
        $type = [$request->loan_type];
        if (!$request->loan_type) $type = ['UANG', 'BARANG'];

        $loans = Loan::with(['payments'])->where('member_id', $member['id'])
        ->where('loan_state', 2)
        ->whereIn('loan_type', $type)
        ->get(); 

        return redirect()->route('repayments.create')->with(['loans' => $loans, 'member' => $member, 'type' => json_encode($type)]);

    }

    public function settleConfirm (Request $request) 
    {
        $member = Member::findOrFail($request->member_id);
        $type = explode(',', $request->loan_type);
        // get loan  
        $loans = Loan::with(['member','payments'])->where('member_id', $member->id)
        ->where('loan_state', 2)
        ->whereIn('loan_type', $type)
        ->get();
        foreach ($loans as $keyL => $loan) {
            foreach ($loan->payments as $keyP => $pay) {
                if ($pay->lp_state == 1) {
                    $pay->lp_state = 2;
                    $pay->remark = "Pelunasan cepat, tutup pinjaman";
                    $pay->updated_by = auth()->id();
                    $pay->save();
                }
            }
            $loan->loan_state = 3;
            $loan->save();
        }

        return redirect()->route('repayments.index')->with('success', 'Pelunasan cepat berhasil dilakukan');

    }

    public function bulkConfirmation(Request $request) 
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $ids = array_map(
            'intval',
            json_decode($request->ids, true)
        );

        DB::beginTransaction();
        try {
            $count = 0;
            $loanArr = [];
            foreach ($ids as $id) {
                $payment = LoanPayment::with(['loan', 'loan.member'])->findOrFail($id);
                if ($payment->lp_state == 1) {
                    $payment->update([
                        'lp_state' => 2,
                        'updated_by' => auth()->id()
                    ]);
                    Member::where('id', $payment->loan->member_id)->increment('balance', $payment->lp_value);
                    $count++;
                    $loanArr[] = $payment->loan_id;
                }
            }

            // cek pinjaman
            $loanIds = array_unique($loanArr);
            foreach ($loanIds as $loanId) {
                $loan = Loan::findOrFail($loanId);
                $allPaid = LoanPayment::where('loan_id', $loan->id)
                    ->where('lp_state', 2)
                    ->count();
                $totalPayments = LoanPayment::where('loan_id', $loan->id)->count();
                if ($allPaid >= $totalPayments) {
                    $loan->loan_state = 3; // lunas
                    $loan->save();
                }
            }
            
            DB::commit();
            return redirect()->back()->with('success', $count . ' Pelunasan angsuran berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pelunasan angsuran! Hubungi Administrator.')->withInput();
        }
    }

}
