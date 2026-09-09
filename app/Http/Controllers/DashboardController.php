<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Pelanggaran;
use App\Models\Prestasi;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalSiswa = Siswa::count();

        $totalPelanggaran = Pelanggaran::count();

        $totalPrestasi = Prestasi::count();


        /*
        |--------------------------------------------------------------------------
        | Top Prestasi
        |--------------------------------------------------------------------------
        */

        $topPrestasi = Siswa::query()
            ->withCount('prestasis')
            ->withSum('prestasis', 'poin')
            ->having('prestasis_count', '>', 0)
            ->orderByDesc('prestasis_count')
            ->orderByDesc('prestasis_sum_poin')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Top Pelanggaran
        |--------------------------------------------------------------------------
        */

        $topPelanggaran = Siswa::query()
            ->withCount('pelanggarans')
            ->withSum('pelanggarans', 'poin')
            ->having('pelanggarans_count', '>', 0)
            ->orderByDesc('pelanggarans_count')
            ->orderByDesc('pelanggarans_sum_poin')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Saldo Poin
        |--------------------------------------------------------------------------
        |
        | Rumus:
        |
        | 100 - total pelanggaran + total prestasi
        |
        */

        $saldoSiswa = Siswa::query()
            ->withSum('pelanggarans', 'poin')
            ->withSum('prestasis', 'poin')
            ->get()
            ->map(function ($siswa) {

                $totalPelanggaran =
                    (int) ($siswa->pelanggarans_sum_poin ?? 0);

                $totalPrestasi =
                    (int) ($siswa->prestasis_sum_poin ?? 0);

                $siswa->saldo_poin =
                    100
                    - $totalPelanggaran
                    + $totalPrestasi;

                return $siswa;
            })
            ->sortByDesc('saldo_poin')
            ->take(5)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Kirim ke Dashboard
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'totalSiswa',
            'totalPelanggaran',
            'totalPrestasi',
            'topPrestasi',
            'topPelanggaran',
            'saldoSiswa'
        ));
    }
}
