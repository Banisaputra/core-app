<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Services\PeriodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PembelianRepository
{
    public function getPembelianPerPeriode($tahun, $cutOffDay): Collection
    {
        $periode = PeriodeService::generatePeriodeTahun($tahun, $cutOffDay);
        
        return $periode->map(function ($periode) {
            $data = Purchase::whereBetween('pr_date', [
               $periode['start_date']->format('Ymd'),
               $periode['end_date']->format('Ymd')
            ])
            ->select(
               DB::raw('SUM(total) as total_purchase'),
               DB::raw('COUNT(*) as total_transaksi')
            )
            ->first();
            
            return [
               'periode' => $periode['nama_periode'],
               'start_date' => $periode['start_date']->format('d-m-Y'),
               'end_date' => $periode['end_date']->format('d-m-Y'),
               'total_jumlah' => $data->total_purchase ?? 0,
               'total_transaksi' => $data->total_transaksi ?? 0
            ];
        });
    }

    public function getPenjualanDetailPerPeriode($tahun): Collection
    {
        return Sale::with('items')
            ->whereYear('sa_date', $tahun)
            ->get()
            ->groupBy(function ($item) {
               $periode = PeriodeService::getPeriodeByDate($item->tanggal);
               return $periode['nama_periode'];
            });
    }
}