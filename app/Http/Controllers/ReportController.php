<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\pdf as PDF;

class ReportController extends Controller
{
    public function deduction(Request $request) {
        $periode = $request->get('periode') ?? date('Ym');//2505
        $month = 06;
        $year = 25;

        $lp_date = 2507;
        $members = Member::with('user')->get();

        $data = [];

        foreach ($members as $member) {
            $m_id = $member->id;
            $loanDetails = Loan::with(['member', 'payments' => function($query) use ($lp_date) {
                $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date]);
            }])
            ->where('member_id', $m_id)
            ->whereHas('payments', function($query) use ($lp_date) {
                $query->whereRaw("DATE_FORMAT(lp_date, '%y%m') = ?", [$lp_date]);
            })
            ->orderBy('id')
            ->get();

            if($member->id == 8) {
                dd($loanDetails);

            }

            for ($i=0; $i < count($loanDetails) ; $i++) { 
                $loan = $loanDetails[$i];
                
            }



            $simpananWajib = 50000;

            $angsuranPinjaman = 125000;

            $cicilanBarang = 35000;

            $data[] = [
                'name' => $member->user->name ?? '-',
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
}
