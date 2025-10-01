<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\Loan;
use App\Models\Sale;
use App\Models\Member;
use App\Models\Policy;
use App\Models\Saving;
use App\Models\Purchase;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        $request->validate([
            "typeReport" => "required",
            "dateStart" => "nullable|date",
            "dateEnd" => "nullable|date",
        ]);
        $type = strtoupper($request->typeReport);
        $startDate = $request->dateStart ?? now();
        $endDate = $request->dateEnd ?? now();
        $data = [];
        $filter = [];
        $file = 'reports';

        $filter['Tgl. Mulai'] = date('d-m-Y', strtotime($startDate));
        $filter['Tgl. Batas'] = date('d-m-Y', strtotime($endDate));
        
        switch ($type) {
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
                ->whereBetween('created_at', [$startDate, $endDate])
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
                ->whereBetween('created_at', [$startDate, $endDate])
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
                # code...
                break;
        }

        $pdf = PDF::loadView($file, [
                'data' => $data,
                'filter' => $filter,
            ]);
        
        $filename = 'Laporan-'.ucwords(strtolower($type)).'-' . now()->format('Ymd') . '.pdf';
        // dd($filename);
        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }
    
        return $pdf->download($filename);

        // old
        //  $pdf = PDF::loadView($file, [
        //         'data' => $data,
        //         'dateStart' => $startDate,
        //         'dateEnd' => $endDate,
        //     ]);

        // return $pdf->stream('Laporan-'.ucwords(strtolower($type)).'-' . now()->format('Ymd') . '.pdf');

    }

    public function getMemberList(Request $request) 
    {
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

    // for PDF report type
    public function deduction(Request $request) 
    {
        $cut_off_day = Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value');
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');

        $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
        $periode_start->modify("-1 month");
        $periode_end = new DateTime("$current_year-$current_month-".($cut_off_day ?? 0)."");

        $members = Member::with(['position','devision','user'])->get();
        $data = [];

        foreach ($members as $member) {
            $m_id = $member->id;
            $loanDetails = Loan::with(['member', 'payments' => function($query) use ($periode_start, $periode_end) {
                $query->whereRaw("DATE_FORMAT(lp_date, '%Y%m%d') BETWEEN ? AND ?", 
                           [$periode_start->format('Ymd'), $periode_end->format('Ymd')]);
                }])
                ->where('member_id', $m_id)
                ->whereHas('payments', function($query) use ($periode_start, $periode_end) {
                    $query->whereRaw("DATE_FORMAT(lp_date, '%Y%m%d') BETWEEN ? AND ?", 
                           [$periode_start->format('Ymd'), $periode_end->format('Ymd')]);
                })
                ->orderBy('id')
                ->get();

            $savingDetails = Saving::with(['member', 'svType'])
                ->whereBetween("sv_date", [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                ->where('member_id', $m_id)
                ->orderBy('id')
                ->get();

                // $sql = $loanDetails->toSql();
                // $bindings = $loanDetails->getBindings();
                // // Format query dengan binding
                // $fullQuery = vsprintf(str_replace('?', "'%s'", $sql), $bindings);
                // dd($fullQuery);

            $simpananBulanan = 0;
            $angsuranPinjaman = 0;
            $cicilanBarang = 0;

            for ($i=0; $i < count($savingDetails); $i++) { 
                $saving = $savingDetails[$i];
                $simpananBulanan += $saving->sv_value;
            }
            for ($i=0; $i < count($loanDetails) ; $i++) { 
                $loan = $loanDetails[$i];
                if(strtoupper($loan->type) == "BARANG") {
                    $cicilanBarang += $loan->payments[0]['lp_total']*1;
                } else {
                    $angsuranPinjaman += $loan->payments[0]['lp_total']*1;
                }
            }

            $data[] = [
                'nip' => $member->nip ?? '-',
                'name' => $member->name ?? '-',
                'position' => $member->position->name ?? '-',
                'potongan_simpanan' => $simpananBulanan,
                'potongan_pinjaman' => $angsuranPinjaman + $cicilanBarang,
                'total' => $simpananBulanan + $angsuranPinjaman + $cicilanBarang,
            ];
        }

        $pdf = PDF::loadView('reports.deduction-salary', [
            'data' => $data,
            'periode_start' => $periode_start->format('Ymd'),
            'periode_end' => $periode_end->format('Ymd'),
        ]);

        return $pdf->stream('Laporan-Potongan-Gaji-' . $periode_start->format('Ymd') . "-" . $periode_end->format('Ymd') . '.pdf');
    }
    // --------unset---
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
