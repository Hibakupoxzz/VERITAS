<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaPortalController extends Controller
{
    /**
     * Portal siswa: hanya menampilkan pelanggaran dan prestasi milik
     * siswa yang sedang login, tidak data siswa lain.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var Siswa|null $siswa */
        $siswa = $user->siswa;

        if (! $siswa) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with(
                'status',
                'Akun siswa belum terhubung dengan data siswa. Silakan hubungi administrator.'
            );
        }

        /*
         * Filter eksplisit berdasarkan siswa pemilik akun yang login.
         * Portal ini tidak boleh menampilkan pelanggaran/prestasi siswa lain,
         * termasuk lewat relasi yang kelak mungkin diubah.
         */
        $pelanggarans = Pelanggaran::query()
            ->where('siswa_id', $siswa->id)
            ->with(['aturanPelanggaran', 'verifikator'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $prestasis = Prestasi::query()
            ->where('siswa_id', $siswa->id)
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $pelanggaransDiverifikasi = $pelanggarans->where('status', 'diverifikasi');

        $totalPelanggaran = $pelanggaransDiverifikasi->count();
        $totalPelanggaranSemuaStatus = $pelanggarans->count();
        $totalPrestasi = $prestasis->count();
        $poinPelanggaran = (int) $pelanggaransDiverifikasi->sum('poin');
        $poinPrestasi = (int) $prestasis->sum('poin');
        $saldoPoin = 100 - $poinPelanggaran + $poinPrestasi;

        return view('siswa.portal', compact(
            'siswa',
            'pelanggarans',
            'prestasis',
            'totalPelanggaran',
            'totalPelanggaranSemuaStatus',
            'totalPrestasi',
            'poinPelanggaran',
            'poinPrestasi',
            'saldoPoin'
        ));
    }
}
