<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Pelanggaran;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Siswa::count();

        $pelanggaranHariIni = Pelanggaran::whereDate(
            'tanggal',
            today()
        )->count();

        $totalPoin = Pelanggaran::whereDate(
            'tanggal',
            today()
        )->sum('poin');

        $pelanggaranHariIniList = Pelanggaran::with('siswa')
            ->whereDate('tanggal', today())
            ->latest()
            ->get();

        $latestPelanggaran = Pelanggaran::with('siswa')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSiswa',
            'pelanggaranHariIni',
            'totalPoin',
            'pelanggaranHariIniList',
            'latestPelanggaran'
        ));
    }
}
