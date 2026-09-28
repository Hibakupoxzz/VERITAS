<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManage
{
    /**
     * Handle an incoming request.
     * Mencegah role 'walas' mengakses menu manajemen (Data Siswa, Data Pelanggaran BK/PDS, Prestasi, dll).
     * KECUALI route read-only tertentu yang diizinkan (lihat pelanggaran & prestasi kelasnya).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $routeName = $request->route()->getName();

        // 1. Walas: Hanya read-only Pelanggaran & Prestasi + Leaderboard
        if ($user->isWalas()) {
            $allowedRoutes = [
                'pelanggaran.index',
                'pelanggaran.show',
                'prestasi.index',
                'prestasi.show',
                'leaderboard',
            ];

            if (in_array($routeName, $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('lapor.index')
                ->with('error', 'Akses dibatasi. Guru Walas hanya memiliki akses untuk pelaporan dan melihat data kelasnya.');
        }

        // 2. Guru Khusus / Piket: Read-only Siswa, Pelanggaran, Prestasi, Leaderboard
        if ($user->isGuru()) {
            $allowedRoutes = [
                'siswa.index',
                'siswa.search',
                'pelanggaran.index',
                'pelanggaran.show',
                'prestasi.index',
                'prestasi.show',
                'leaderboard',
            ];

            if (in_array($routeName, $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('lapor.index')
                ->with('error', 'Akses dibatasi. Guru hanya memiliki akses untuk pelaporan dan melihat direktori siswa/kelas.');
        }

        // 2. BK: Bisa nambah prestasi & report, tapi TIDAK BISA verify/acc pelanggaran
        // Jadi akses ke pelanggaran.pending, approve, reject ditolak.
        // Juga ditolak dari pelanggaran.create/store/edit/update/destroy (harus report via Lapor)
        if ($user->isBk()) {
            $forbiddenBkRoutes = [
                'pelanggaran.pending',
                'pelanggaran.approve',
                'pelanggaran.reject',
                'pelanggaran.create',
                'pelanggaran.store',
                'pelanggaran.edit',
                'pelanggaran.update',
                'pelanggaran.destroy',
            ];

            if (in_array($routeName, $forbiddenBkRoutes)) {
                return redirect()->route('pelanggaran.index')
                    ->with('error', 'Akses dibatasi. Guru BK hanya dapat melaporkan pelanggaran (via menu Lapor) dan mengelola prestasi.');
            }
        }

        // 3. PDS: Bisa acc pelanggaran & report, tapi TIDAK BISA mengelola prestasi
        // Jadi akses ke prestasi.create/store/edit/update/destroy ditolak.
        if ($user->isPds()) {
            $forbiddenPdsRoutes = [
                'prestasi.create',
                'prestasi.store',
                'prestasi.edit',
                'prestasi.update',
                'prestasi.destroy',
                // PDS hanya report pelanggaran (bisa via Lapor atau langsung),
                // Kita biarkan PDS akses pelanggaran.* karena PDS bisa verifikasi
            ];

            if (in_array($routeName, $forbiddenPdsRoutes)) {
                return redirect()->route('prestasi.index')
                    ->with('error', 'Akses dibatasi. Guru PDS tidak dapat mengelola prestasi.');
            }
        }

        return $next($request);
    }
}
