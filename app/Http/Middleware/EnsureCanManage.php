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

        // Guru walas boleh akses route read-only berikut
        if (auth()->user()->isWalas()) {
            $allowedRoutes = [
                'pelanggaran.index',
                'pelanggaran.show',
                'prestasi.index',
                'prestasi.show',
                'leaderboard',
            ];

            if (in_array($request->route()->getName(), $allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('lapor.index')
                ->with('error', 'Akses dibatasi. Guru Walas hanya memiliki akses untuk pelaporan dan melihat data kelasnya.');
        }

        return $next($request);
    }
}
