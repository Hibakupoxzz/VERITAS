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

        // Statistik Umum

        $siswaQuery = Siswa::query();
        $totalSiswa = $siswaQuery->count();

        $pelanggaranQuery = Pelanggaran::where('status', 'diverifikasi');
        $totalPelanggaran = $pelanggaranQuery->count();

        $prestasiQuery = Prestasi::query();
        $totalPrestasi = $prestasiQuery->count();

        // Top Prestasi

        $topPrestasiQuery = Siswa::query()
            ->withCount('prestasis')
            ->withSum('prestasis', 'poin')
            ->has('prestasis')
            ->orderByDesc('prestasis_count')
            ->orderByDesc('prestasis_sum_poin')
            ->limit(5);

        $topPrestasi = $topPrestasiQuery->get();

        // Top Pelanggaran

        $topPelanggaranQuery = Siswa::query()
            ->withCount('pelanggarans')
            ->withSum('pelanggarans', 'poin')
            ->has('pelanggarans')
            ->orderByDesc('pelanggarans_count')
            ->orderByDesc('pelanggarans_sum_poin')
            ->limit(5);

        $topPelanggaran = $topPelanggaranQuery->get();

        // Saldo Poin (100 - total pelanggaran + total prestasi)

        $saldoQuery = Siswa::query()
            ->withSum(['pelanggarans as pelanggarans_sum_poin' => function ($q) {
                $q->where('status', 'diverifikasi');
            }], 'poin')
            ->withSum('prestasis', 'poin');

        $saldoSiswa = $saldoQuery->get()
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

        // Render View

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
