<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index() 
    {
        return view('reports.index');
    }

    // for PDF report type
    public function deduction(Request $request) 
    {
        $periode = 2508; // date('ym');
        $members = Member::with('user')->get();

        $data = [];

        foreach ($members as $member) {
            $m_id = $member->id;
            $loanDetails = Loan::with(['member', 'payments' => function($query) use ($periode) {
                $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$periode]);
                }])
                ->where('member_id', $m_id)
                ->whereHas('payments', function($query) use ($periode) {
                    $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$periode]);
                })
                ->orderBy('id')
                ->get();

            $savingDetails = Saving::with(['member', 'svType'])
                ->whereRaw("DATE_FORMAT(sv_date, '%y%m') = ?", [$periode])
                ->where('member_id', $m_id)
                ->orderBy('id')
                ->get();

            $simpananWajib = 0;
            $angsuranPinjaman = 0;
            $cicilanBarang = 0;

            for ($i=0; $i < count($savingDetails); $i++) { 
                $saving = $savingDetails[$i];
                $simpananWajib += $saving->sv_value;
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
                'name' => $member->name ?? '-',
                'potongan_wajib' => $simpananWajib,
                'potongan_pinjaman' => $angsuranPinjaman + $cicilanBarang,
                'total' => $simpananWajib + $angsuranPinjaman + $cicilanBarang,
            ];
        }

        $pdf = PDF::loadView('reports.deduction-salary', [
            'data' => $data,
            'periode' => $periode
        ]);

        return $pdf->stream('Laporan-Potongan-Gaji-' . $periode . '.pdf');
    }
    // -----------
    // for excel report type
    public function deductionXlsx(Request $request)
    {
        $periode = $request->get('periode') ?? date('Y-m');
        $month = substr($periode, 5, 2);
        $year = substr($periode, 0, 4);

        $members = Member::with('user')->get();
        $data = [];

        foreach ($members as $member) {
            $simpananWajib = $member->savings()
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
                'Potongan Wajib' => $simpananWajib,
                'Potongan Pinjaman' => $angsuranPinjaman + $cicilanBarang,
                'Total Potongan' => $simpananWajib + $angsuranPinjaman + $cicilanBarang,
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
