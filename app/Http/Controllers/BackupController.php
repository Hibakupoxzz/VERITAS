<?php

namespace App\Http\Controllers;

use App\Exports\BackupTahunanExport;
use App\Models\Pelanggaran;
use App\Models\Prestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Backup data tahunan.
 *
 * Mengunduh seluruh pelanggaran & prestasi dalam satu tahun kalender
 * sebagai satu file Excel berisi dua sheet (Pelanggaran dan Prestasi).
 * Dipakai untuk arsip akhir tahun ajaran.
 */
class BackupController extends Controller
{
    public function index()
    {
        /*
         * Kumpulkan tahun yang benar-benar punya data, dihitung di
         * sisi PHP (bukan fungsi YEAR()/strftime()) supaya tetap
         * jalan di MySQL maupun SQLite.
         */
        $years = Pelanggaran::whereNotNull('tanggal')->pluck('tanggal')
            ->merge(Prestasi::whereNotNull('tanggal')->pluck('tanggal'))
            ->map(fn ($tanggal) => (int) Carbon::parse($tanggal)->year)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('backup.index', [
            'years' => $years,
            'defaultYear' => $years->first() ?? (int) now()->year,
            'totalPelanggaran' => Pelanggaran::count(),
            'totalPrestasi' => Prestasi::count(),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $tahun = (int) $validated['tahun'];

        return Excel::download(
            new BackupTahunanExport($tahun),
            "backup-veritas-{$tahun}.xlsx"
        );
    }
}
