<?php

namespace App\Http\Controllers;

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

        $pelanggarans = $siswa->pelanggarans()
            ->with(['aturanPelanggaran', 'verifikator'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $prestasis = $siswa->prestasis()
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $pelanggaransDiverifikasi = $pelanggarans->where('status', 'diverifikasi');

        $totalPelanggaran = $pelanggaransDiverifikasi->count();
        $totalPrestasi = $prestasis->count();
        $poinPelanggaran = (int) $pelanggaransDiverifikasi->sum('poin');
        $poinPrestasi = (int) $prestasis->sum('poin');
        $saldoPoin = 100 - $poinPelanggaran + $poinPrestasi;

        return view('siswa.portal', compact(
            'siswa',
            'pelanggarans',
            'prestasis',
            'totalPelanggaran',
            'totalPrestasi',
            'poinPelanggaran',
            'poinPrestasi',
            'saldoPoin'
        ));
    }
}
