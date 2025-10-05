<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class PeriodeService
{
    public static function generatePeriodeTahun($tahun, $cutOffDay): Collection
    {
        $periode = collect();
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // start dibulan sebelum nya (21 desember 2024 - 20 januari 2025) periode Januari
            $startDate = Carbon::create($tahun, $bulan - 1, $cutOffDay +1);
            $endDate = Carbon::create($tahun, $bulan, $cutOffDay);
            
            // Handle first periode (December to January)
            if ($bulan == 1) {
                $startDate = Carbon::create($tahun - 1, 12, $cutOffDay +1);
            }
            
            $periode->push([
                'bulan' => $bulan,
                'nama_periode' => $startDate->format('d M Y') . ' s.d ' . $endDate->format('d M Y'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tahun' => $tahun
            ]);
        }
        
        return $periode;
    }

    public static function getPeriodeByDate(Carbon $date): array
    {
        $tahun = $date->year;
        $bulan = $date->month;
        
        // Jika tanggal <= 20, maka termasuk periode bulan sebelumnya
        if ($date->day <= 20) {
            $bulan--;
            if ($bulan == 0) {
                $bulan = 12;
                $tahun--;
            }
        }
        
        $startDate = Carbon::create($tahun, $bulan, 21);
        $endDate = Carbon::create($tahun, $bulan + 1, 20);
        
        if ($bulan == 12) {
            $endDate = Carbon::create($tahun + 1, 1, 20);
        }
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'nama_periode' => $startDate->format('d M Y') . ' s.d ' . $endDate->format('d M Y')
        ];
    }
}