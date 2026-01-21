<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use Throwable;
use App\Models\Loan;
use App\Models\Sale;
use App\Models\Member;
use App\Models\Policy;
use App\Models\Saving;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\MasterItem;
use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\Snappy\Facades\SnappyPdf as SnappyPDF;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index() 
    {
        $categories = Category::with(['parent'])->get();
        return view('reports.index', compact('categories'));
    }

    public function index2()
    {
        return view('reports.index2');
    }

    public function getReport(Request $request) 
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        $request->validate([
            "typeReport" => "required",
            "dateStart" => "nullable|date",
            "dateEnd" => "nullable|date",
        ]);
        $type = strtoupper($request->typeReport);
        $startDate = $request->dateStart ?? now();
        $endDate = $request->dateEnd ?? now();
        $data = [];
        $header = [];
        $filter = [];
        $file = 'reports';

        $filter['Tgl. Mulai'] = date('d-m-Y', strtotime($startDate));
        $filter['Tgl. Batas'] = date('d-m-Y', strtotime($endDate));
        
        switch ($type) {
            case 'SVSUMMARY':
                try {
                    $data = Member::select(
                        'members.id',
                        'members.nip',
                        'members.name',
                        'positions.name as position',
                        'devisions.name as devision',

                        DB::raw("
                            SUM(CASE 
                                WHEN saving_types.name = 'Dana Cadangan' THEN savings.sv_value 
                                ELSE 0 
                            END) AS simpanan_cadangan
                        "),

                        DB::raw("
                            SUM(CASE 
                                WHEN saving_types.name = 'Pokok' THEN savings.sv_value 
                                ELSE 0 
                            END) AS simpanan_pokok
                        "),

                        DB::raw("
                            SUM(CASE 
                                WHEN saving_types.name = 'Wajib' THEN savings.sv_value 
                                ELSE 0 
                            END) AS simpanan_wajib
                        "),

                        DB::raw("
                            SUM(CASE 
                                WHEN saving_types.name = 'SHT' THEN savings.sv_value 
                                ELSE 0 
                            END) AS simpanan_sht
                        "),

                        DB::raw("
                            SUM(sv_value) AS total
                        ")
                    )
                    ->join('savings', function ($join) use ($startDate, $endDate) {
                        $join->on('savings.member_id', '=', 'members.id')
                            ->where('savings.sv_state', 2)
                            ->whereBetween('savings.sv_date', [date('Ymd', strtotime($startDate)), date('Ymd', strtotime($endDate))]);
                    })
                    ->leftJoin('saving_types', 'saving_types.id', '=', 'savings.sv_type_id')
                    ->leftJoin('positions', 'positions.id', '=', 'members.position_id')
                    ->leftJoin('devisions', 'devision.id', '=', 'members.devision_id')
                    ->groupBy(
                        'members.id',
                        'members.nip',
                        'members.name',
                        'positions.name'
                    )
                    ->orderBy('members.id', 'ASC') 
                    ->get();

                    // header info
                    $header = DB::table('savings as s')
                        ->select(
                            DB::raw("
                                SUM(CASE WHEN st.name = 'Dana Cadangan' THEN s.sv_value ELSE 0 END) AS total_cadangan
                            "),
                            DB::raw("
                                SUM(CASE WHEN st.name = 'Pokok' THEN s.sv_value ELSE 0 END) AS total_pokok
                            "),
                            DB::raw("
                                SUM(CASE WHEN st.name = 'Wajib' THEN s.sv_value ELSE 0 END) AS total_wajib
                            "),
                            DB::raw("
                                SUM(CASE WHEN st.name = 'SHT' THEN s.sv_value ELSE 0 END) AS total_sht
                            "),
                            DB::raw("SUM(s.sv_value) AS grand_total")
                        )
                        ->leftJoin('saving_types as st', 'st.id', '=', 's.sv_type_id')
                        ->where('s.sv_state', 2)
                        ->whereBetween('s.sv_date', [date('Ymd', strtotime($startDate)), date('Ymd', strtotime($endDate))])
                        ->first();


                } catch (\Throwable $e) {

                    // \Log::error('PDF REAL ERROR', [
                    //     'msg'  => $e->getMessage(),
                    //     'file' => $e->getFile(),
                    //     'line' => $e->getLine(),
                    //     'trace' => $e->getTraceAsString(),
                    // ]);

                    // // 🔴 PENTING: KIRIM ERROR ASLI KE RESPONSE
                    // return response()->json([
                    //     'real_error' => $e->getMessage(),
                    //     'file' => $e->getFile(),
                    //     'line' => $e->getLine(),
                    // ], 500);

                    abort(500, 'Terjadi kesalahan saat generate laporan. Silakan hubungi administrator.');

                }

                $file = 'reports.summary-saving';
                break;

            case 'LNSUMMARY':
                try {
                    $data = DB::table('members as m')
                    ->select(
                        'm.id',
                        'm.nip',
                        'm.name',
                        'p.name as position',
                        'd.name as devision',
                        'lt.jenis_pinjaman',

                        DB::raw('COALESCE(l.loan_value, 0) as jumlah_pinjaman'),
                        DB::raw('COALESCE(l.loan_tenor, 0) as loan_tenor'),

                        DB::raw('COALESCE(lp.lp_value, 0) as pokok'),
                        DB::raw('COALESCE(lp.loan_interest, 0) as bunga'),
                        DB::raw('COALESCE(lp.tenor_month, 0) as angsuran_ke'),

                        DB::raw('COALESCE(lp.lp_total, 0) as total_tagihan'),
                        DB::raw('COALESCE(lp.loan_remaining, 0) as sisa_pinjaman')
                    )

                    /* MASTER JENIS PINJAMAN */
                    ->crossJoin(DB::raw("
                        (
                            SELECT 'UANG' AS jenis_pinjaman
                            UNION ALL
                            SELECT 'BARANG'
                        ) lt
                    "))

                    /* LOAN (FILTER TANGGAL PINJAM) */
                    ->join('loans as l', function ($join) use ($startDate, $endDate) {
                        $join->on('l.member_id', '=', 'm.id')
                            ->whereColumn('l.loan_type', 'lt.jenis_pinjaman')
                            ->where('l.loan_state', 2)
                            ->whereBetween('l.loan_date', [
                                date('Ymd', strtotime($startDate)),
                                date('Ymd', strtotime($endDate))
                            ]);
                    })

                    /* TAGIHAN TERAKHIR */
                    ->leftJoin('loan_payments as lp', function ($join) {
                        $join->on('lp.loan_id', '=', 'l.id')
                            ->whereRaw('lp.tenor_month = (
                                COALESCE((
                                    SELECT MAX(li.tenor_month)
                                    FROM loan_payments li
                                    WHERE li.loan_id = l.id
                                    AND li.lp_state = 2
                                ), 0) + 1
                            )');
                    })

                    ->leftJoin('positions as p', 'p.id', '=', 'm.position_id')
                    ->leftJoin('devisions as d', 'd.id', '=', 'm.devision_id')
                    ->orderBy('m.id', 'ASC')
                    ->orderByRaw("FIELD(lt.jenis_pinjaman, 'UANG', 'BARANG')")
                    ->get();

                        
                    // header info
                    $header = DB::table('loans as l')
                        ->select(
                            DB::raw("
                                SUM(CASE WHEN l.loan_type = 'UANG' THEN lp.lp_value ELSE 0 END) AS total_pinjaman_uang
                            "),
                            DB::raw("
                                SUM(CASE WHEN l.loan_type = 'BARANG' THEN lp.lp_value ELSE 0 END) AS total_pinjaman_barang
                            "),
                            
                            DB::raw("SUM(lp.lp_value) AS grand_total")
                        )
                        ->leftJoin('loan_payments as lp', 'lp.loan_id', '=', 'l.id')
                        ->where('l.loan_state', 2)
                        ->whereBetween('l.loan_date', [date('Ymd', strtotime($startDate)), date('Ymd', strtotime($endDate))])
                        ->first();


                } catch (\Throwable $e) {

                    // \Log::error('PDF REAL ERROR', [
                    //     'msg'  => $e->getMessage(),
                    //     'file' => $e->getFile(),
                    //     'line' => $e->getLine(),
                    //     'trace' => $e->getTraceAsString(),
                    // ]);

                    // // 🔴 PENTING: KIRIM ERROR ASLI KE RESPONSE
                    // return response()->json([
                    //     'real_error' => $e->getMessage(),
                    //     'file' => $e->getFile(),
                    //     'line' => $e->getLine(),
                    // ], 500);

                    abort(500, 'Terjadi kesalahan saat generate laporan. Silakan hubungi administrator.');

                }

                $file = 'reports.summary-loan';
                break;
            case 'SAVING':
                $savings = Saving::with(['member','svType'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

                foreach ($savings as $key => $sv) {
                    $data[] = [
                        'sv_code' => $sv->sv_code,
                        'sv_date' => $sv->sv_date,
                        'sv_type' => $sv->svType->name,
                        'sv_value' => $sv->sv_value,
                    ];
                }
                
                $file = 'reports.saving';
                break;

            case 'LOAN':
                $loans = Loan::with(['member','payments'])
                ->whereBetween('loan_date', [date('Ymd', strtotime($startDate)), date('Ymd', strtotime($endDate))])
                ->get();

                foreach ($loans as $key => $loan) {
                    $data[] = [
                        'loan_code' => $loan->loan_code,
                        'loan_date' => $loan->loan_date,
                        'loan_type' => $loan->loan_type,
                        'loan_value' => $loan->loan_value,
                    ];
                }
                $file = 'reports.loan';
                break;
            case 'PURCHASE':
                $purchases = Purchase::with(['supplier','prDetails'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

                foreach ($purchases as $key => $pr) {
                    $data[] = [
                        'pr_code' => $pr->pr_code,
                        'pr_date' => $pr->pr_date,
                        'pr_ref_doc' => $pr->ref_doc,
                        'pr_value' => $pr->total,
                    ];
                }
                $file = 'reports.purchase';
                break;
            
            case 'SALES':
                $pay_type = $request->typeSales ?? "all";
                $where = "1=1";
                if ($pay_type != "all") {
                    $where = "payment_type='".strtoupper($pay_type)."'";
                }
                $sales = Sale::with(['saDetail'])
                ->whereBetween('created_at', [$startDate, $endDate." 23:59:59"])
                ->whereRaw($where)
                ->get();

                foreach ($sales as $key => $sa) {
                    $data[] = [
                        'sa_code' => $sa->sa_code,
                        'sa_date' => $sa->sa_date,
                        'sa_payment' => $sa->payment_type,
                        'sa_value' => $sa->sub_total,
                    ];
                }
                $file = 'reports.sales';
                break;
            
            case 'PROFITNLOSE':
                $totalPurchase = 0;
                $totalSales = 0;

                $purchases = Purchase::with(['supplier','prDetails'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

                foreach ($purchases as $key => $pr) {
                    $totalPurchase += $pr['total'];
                }

                $sales = Sale::with(['saDetail'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

                foreach ($sales as $key => $sa) {
                    $totalSales += $sa['sub_total'];
                }

                $data['totalPr'] = $totalPurchase;
                $data['totalSl'] = $totalSales;
                
                $file = 'reports.profit-lose';
                break;
            
            case 'INVENTORY':
                $inventories = Inventory::with(['invDetails'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

                foreach ($inventories as $key => $inv) {
                    $data[] = [
                        'inv_code' => $inv->code,
                        'inv_date' => $inv->inv_date,
                        'inv_type' => $inv->type,
                        'inv_remark' => $inv->remark,
                        'inv_state' => $inv->inv_state,
                    ];
                }
                $file = 'reports.inventories';
                break;
            
            case 'ITEMSTOCK':
                $stock_type = $request->typeStock ?? "all";
                $where = "1=1";
                if ($stock_type != "all") {
                    $stock_type = $stock_type*1;
                    if ($stock_type == 10) {
                        $where = "stock<=".$stock_type."";
                    } else if ($stock_type == 0) {
                        $where = "stock =".$stock_type."";
                    }
                }

                $item_stock = MasterItem::with(['category'])
                ->whereRaw($where)
                ->get();

                foreach ($item_stock as $key => $sa) {
                    $data[] = [
                        'item_code' => $sa->item_code,
                        'item_name' => $sa->item_name,
                        'item_hpp' => $sa->hpp,
                        'item_price' => $sa->sales_price,
                        'item_stock' => $sa->stock,
                    ];
                }
                $file = 'reports.item-stock';
                break;
            
            default:
                // invalid report type

                break;
        }

        if ($type == 'SVSUMMARY' || $type == 'LNSUMMARY') {
            $pdf = PDF::loadView($file, [
                'data' => $data,
                'filter' => $filter,
                'header' => $header
            ])->setPaper('A4', 'landscape');
        } else {
            $pdf = PDF::loadView($file, [
                'data' => $data,
                'filter' => $filter,
            ]);
        }
        
        $filename = 'Laporan-'.ucwords(strtolower($type)).'-' . now()->format('Ymd') . '.pdf';
        if ($request->has('preview')) {
            // return $pdf->stream($filename);
            return response()->make($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } else {
            return $pdf->download($filename);
        }
    

    }

    public function getMemberList(Request $request) 
    {
        ini_set('memory_limit', '1024M'); // 1GB
        ini_set('max_execution_time', 300);

        $request->validate([
            "typeReport" => "required",
            "activate" => "required"
        ]);

        $type = strtoupper($request->typeReport);
        $data = [];
        $filter = [];
        $file = 'reports';

        switch ($type) {
            case 'MEMBER':

                $query = "
                    SELECT m.nip, m.name mb_name, p.name ps_name, d.name dv_name, m.is_transactional mb_active
                    FROM members m
                    JOIN users u ON m.user_id = u.id 
                    JOIN positions p ON m.position_id = p.id 
                    JOIN devisions d ON m.devision_id = d.id 
                    WHERE 1=1
                ";

                if ($request->activate == 2) {
                    $filter['Status'] = "SEMUA"; 
                    $query .= " AND m.is_transactional < 2";
                } else {
                    $filter['Status'] = $request->activate == 1 ? "AKTIF" : "NONAKTIF"; 
                    $query .= " AND m.is_transactional =". $request->activate ."";
                }
                
                if ($request->startJoined) {
                    $filter['Tgl. Bergabung'] = $request->startJoined;
                    $query .= " AND m.date_joined >='".$request->startJoined."'";
                }
                if ($request->endJoined) {
                    $filter['Tgl. Batas Bergabung'] = $request->endJoined;
                    $query .= " AND m.date_joined <='".$request->endJoined."'";
                }

                $members = DB::select($query);

                foreach ($members as $key => $mb) {
                    $data[] = [
                        'nip' => $mb->nip,
                        'name' => $mb->mb_name,
                        'position' => $mb->ps_name,
                        'devision' => $mb->dv_name,
                        'status' => $mb->mb_active,
                    ];
                }

                $file = 'reports.member';
                break;

            default:
                # code...
                break;
        }

        $pdf = PDF::loadView($file, [
                'data' => $data,
                'filter' => $filter,
            ]);
        
        $filename = 'Laporan-'.ucwords(strtolower($type)).'-' . now()->format('Ymd') . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }
    
        return $pdf->download($filename); 

    }

    public function getMemberDetail(Request $request) 
    {
        $request->validate([
            "typeReport" => "required",
            "member_id" => "required"
        ]);

        $type = strtoupper($request->typeReport);
        $data = [];
        $filter = [];
        $file = 'reports';

        $member = Member::findOrFail($request->member_id);
        $filter['Anggota'] = $member->nip . " - " . $member->name;

        switch ($type) {
            case 'DETAIL_SAVING':
                $query = "
                    SELECT sv_code, sv_date, svt.name svt_name, sv_value, sv_state 
                    FROM savings sv JOIN saving_types svt ON (sv.sv_type_id=svt.id)
                    WHERE sv.member_id=".$request->member_id."
                ";

                if ($request->activate == 2) {
                    $filter['Status'] = "SEMUA"; 
                    $query .= " AND sv.is_transactional < 2";
                } else {
                    $filter['Status'] = $request->activate == 1 ? "AKTIF" : "NONAKTIF"; 
                    $query .= " AND sv.is_transactional =". $request->activate ."";
                }

                if ($request->startJoined) {
                    $filter['Tgl. Simpanan'] = date('d-m-Y', strtotime($request->startJoined));
                    $query .= " AND sv.sv_date >='".date('Ymd', strtotime($request->startJoined))."'";
                }
                if ($request->endJoined) {
                    $filter['Tgl. Batas Simpanan'] = date('d-m-Y', strtotime($request->endJoined));
                    $query .= " AND sv.sv_date <='".date('Ymd', strtotime($request->endJoined))."'";
                }

                $savings = DB::select($query);

                foreach ($savings as $key => $sv) {
                    $data[] = [
                        'sv_code' => $sv->sv_code,
                        'sv_date' => $sv->sv_date,
                        'sv_type' => $sv->svt_name,
                        'sv_value' => $sv->sv_value,
                        'status' => $sv->sv_state,
                    ];
                }

                $file = 'reports.member-saving';
                break;
            case 'DETAIL_LOANS':
                $query = "1=1";

                if ($request->activate == 2) {
                    $filter['Status'] = "SEMUA"; 
                    $query .= " AND loans.loan_state <> 0";
                } else { 
                    $filter['Status'] = $request->activate == 1 ? "AKTIF" : "NONAKTIF";
                    $query .= " AND loans.loan_state ";
                    if ($request->activate == 1) $query .= "< 3";
                    if ($request->activate == 99) $query .= "> 2";
                }

                if ($request->startJoined) {
                    $filter['Tgl. Pinjaman'] = date('d-m-Y', strtotime($request->startJoined));
                    $query .= " AND loans.loan_date >='".date('Ymd', strtotime($request->startJoined))."'";
                }
                if ($request->endJoined) {
                    $filter['Tgl. Batas Pinjaman'] = date('d-m-Y', strtotime($request->endJoined));
                    $query .= " AND loans.loan_date <='".date('Ymd', strtotime($request->endJoined))."'";
                }

                $loans = Loan::with(['member', 'payments'])
                ->where('member_id', $member->id)
                ->whereRaw($query)
                ->get();

                foreach ($loans as $key => $ln) {
                    $data[] = [
                        'ln_code' => $ln->loan_code,
                        'ln_date' => $ln->loan_date,
                        'ln_type' => $ln->loan_type,
                        'ln_value' => $ln->loan_value,
                        'status' => $ln->loan_state,
                        'payments' => $ln->payments
                    ];
                }

                $file = 'reports.member-loans';
                break;

            default:
                # code...
                break;
        }

        $pdf = PDF::loadView($file, [
                'data' => $data,
                'filter' => $filter,
            ]);
        
        $filename = 'Laporan-'.ucwords(strtolower($type)).'-' . now()->format('Ymd') . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }
    
        return $pdf->download($filename); 

    }

    public function loanInfo(Request $request) 
    {
        $loan = Loan::with('member','payments')->findOrFail($request->loan_id);

        $pdf = PDF::loadView('reports.loan-info', [
            'loan' => $loan,
        ])->setPaper([0,0,164.36,600], 'portrait');

        return $pdf->stream('Bukti-stuk-pinjaman-'. date('dmY', strtotime($loan->created_at)).'.pdf');
    } 
 
    // for PDF report type modified
    public function deduction(Request $request) 
    {
        ini_set('memory_limit', '1024M'); // 1GB
        ini_set('max_execution_time', '300'); // 5 menit

        $cut_off_day = Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value');
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');

        $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
        $periode_start->modify("-1 month");
        $periode_end = new DateTime("$current_year-$current_month-".($cut_off_day ?? 0)."");

        // $members = Member::with(['position','devision','user'])->get();
        
        $data = [];

        Member::with(['position','devision','user'])
        ->chunk(500, function($members) use (&$data, $periode_start, $periode_end) {
            $memberIds = $members->pluck('id');
            
            // Ambil savings per batch
            $savingsAll = Saving::whereIn('member_id', $memberIds)
            ->whereBetween('sv_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
            ->whereIn('sv_state', [1])
            ->get()
            ->groupBy('member_id');
            
            
            // Ambil loan & payments per batch
            $loansAll = Loan::with(['payments' => function($q) use ($periode_start, $periode_end) {
                    $q->whereBetween('lp_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                    ->where('lp_state', 1);
                }])
                ->whereIn('member_id', $memberIds)
                ->whereIn('loan_state', [2])
                ->get()
                ->groupBy('member_id');

            foreach ($members as $member) {
                if ($member->is_transactional != 1) {
                    continue; // Skip non-transactional members
                }
                $m_id = $member->id;
                $savingDetails = $savingsAll->get($m_id) ?? collect();
                $loanDetails = $loansAll->get($m_id) ?? collect();
                
                $simpananBulanan = $savingDetails->sum('sv_value');

                $angsuranPinjaman = 0;
                $cicilanBarang = 0;
                $tenor_month = 0;

                foreach ($loanDetails as $loan) {
                    if ($loan->payments->isNotEmpty()) {
                        $firstPay = $loan->payments->first();
                        if (!$firstPay) continue;
                        $tenor_month = $firstPay->tenor_month;
                        if (strtoupper($loan->type) === "BARANG") {
                            $cicilanBarang += $firstPay->lp_total;
                        } else {
                            $angsuranPinjaman += $firstPay->lp_total;
                        }
                    }
                }
                $data[] = [
                    'nip' => $member->nip ?? '-',
                    'name' => $member->name ?? '-',
                    'position' => $member->devision->name ?? '-',
                    'potongan_simpanan' => $simpananBulanan,
                    'potongan_pinjaman_uang' => $angsuranPinjaman,
                    'angsuran_ke' => $tenor_month,
                    'potongan_pinjaman_barang' => $cicilanBarang,
                    'total' => $simpananBulanan + $angsuranPinjaman + $cicilanBarang,
                ];
                
            }
            // Bersihkan memory tiap chunk
            unset($savingsAll, $loansAll);
        });

        $pdf = PDF::loadView('reports.deduction-salary', [
            'data' => $data,
            'periode_start' => $periode_start->format('Ymd'),
            'periode_end' => $periode_end->format('Ymd'),
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Potongan-Gaji-' . $periode_start->format('Ymd') . "-" . $periode_end->format('Ymd') . '.pdf');
    }

    // for excel report type
    public function deductionXlsx(Request $request)
    {
        $periode = $request->get('periode') ?? date('Y-m');
        $month = substr($periode, 5, 2);
        $year = substr($periode, 0, 4);

        $members = Member::with('user')->get();
        $data = [];

        foreach ($members as $member) {
            $simpananBulanan = $member->savings()
                ->where('type', 'wajib')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $angsuranPinjaman = $member->loanInstallments()
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $cicilanBarang = $member->installments()
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $data[] = [
                'Nama' => $member->user->name ?? '-',
                'Potongan Wajib' => $simpananBulanan,
                'Potongan Pinjaman' => $angsuranPinjaman + $cicilanBarang,
                'Total Potongan' => $simpananBulanan + $angsuranPinjaman + $cicilanBarang,
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nama Anggota');
        $sheet->setCellValue('B1', 'Potongan Wajib');
        $sheet->setCellValue('C1', 'Potongan Pinjaman');
        $sheet->setCellValue('D1', 'Total Potongan');

        // Data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item['Nama']);
            $sheet->setCellValue("B$row", $item['Potongan Wajib']);
            $sheet->setCellValue("C$row", $item['Potongan Pinjaman']);
            $sheet->setCellValue("D$row", $item['Total Potongan']);
            $row++;
        }

        // Response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan-Potongan-Gaji-' . $periode . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }


}

