<?php

namespace App\Repositories;

use App\Models\Saving;
use App\Services\PeriodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SimpananRepository
{
    public function getSimpananPerPeriode($tahun, $cutOffDay): Collection
    {
        $periode = PeriodeService::generatePeriodeTahun($tahun, $cutOffDay);
        
        return $periode->map(function ($periode) {
            $data = Saving::whereBetween('sv_date', [
               $periode['start_date']->format('Ymd'),
               $periode['end_date']->format('Ymd')
            ])
            ->select(
               DB::raw('SUM(sv_value) as total_saving'),
               DB::raw('COUNT(*) as total_transaksi')
            )
            ->first();
            
            return [
               'periode' => $periode['nama_periode'],
               'start_date' => $periode['start_date']->format('d-m-Y'),
               'end_date' => $periode['end_date']->format('d-m-Y'),
               'total_jumlah' => $data->total_saving ?? 0,
               'total_transaksi' => $data->total_transaksi ?? 0
            ];
        });
    }

    public function getSimpananDetailPerPeriode($tahun): Collection
    {
        return Saving::with(['saDetail'])
            ->whereYear('sv_date', $tahun)
            ->get()
            ->groupBy(function ($item) {
               $periode = PeriodeService::getPeriodeByDate($item->tanggal);
               return $periode['nama_periode'];
            });
    }
}