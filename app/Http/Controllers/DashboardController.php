<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isWalas()) {
            return redirect()->route('lapor.index');
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalSiswa = Siswa::count();

        $totalPelanggaran = Pelanggaran::where('status', 'diverifikasi')->count();

        $totalPrestasi = Prestasi::count();

        /*
        |--------------------------------------------------------------------------
        | Top Prestasi
        |--------------------------------------------------------------------------
        */

        $topPrestasi = Siswa::query()
            ->withCount('prestasis')
            ->withSum('prestasis', 'poin')
            ->has('prestasis')
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
            ->has('pelanggarans')
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
            ->withSum(['pelanggarans as pelanggarans_sum_poin' => function ($q) {
                $q->where('status', 'diverifikasi');
            }], 'poin')
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
