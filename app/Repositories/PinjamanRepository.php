<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Services\PeriodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PinjamanRepository
{
    public function getPinjamanPerPeriode($tahun, $cutOffDay): Collection
    {
        $periode = PeriodeService::generatePeriodeTahun($tahun, $cutOffDay);
        
        return $periode->map(function ($periode) {
            $data = Loan::whereBetween('loan_date', [
               $periode['start_date']->format('Ymd'),
               $periode['end_date']->format('Ymd')
            ])
            ->select(
               DB::raw('SUM(loan_value) as total_loan'),
               DB::raw('COUNT(*) as total_transaksi')
            )
            ->first();
            
            return [
               'periode' => $periode['nama_periode'],
               'start_date' => $periode['start_date']->format('d-m-Y'),
               'end_date' => $periode['end_date']->format('d-m-Y'),
               'total_jumlah' => $data->total_loan ?? 0,
               'total_transaksi' => $data->total_transaksi ?? 0
            ];
        });
    }

    public function getPinjamanDetailPerPeriode($tahun): Collection
    {
        return Loan::with(['payments'])
            ->whereYear('loan_date', $tahun)
            ->get()
            ->groupBy(function ($item) {
               $periode = PeriodeService::getPeriodeByDate($item->tanggal);
               return $periode['nama_periode'];
            });
    }
}