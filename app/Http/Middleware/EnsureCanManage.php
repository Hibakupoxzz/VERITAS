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

        /*
         * 0. Akses penuh (mis. Wakil Kepala Kesiswaan).
         *
         *    Diperlakukan setara Super Admin: seluruh rute manajemen,
         *    termasuk Manajemen Akun, dibuka.
         */
        if ($user->hasFullAccess()) {
            return $next($request);
        }

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

        // 2. Guru & pimpinan (role "guru" / "pimpinan")
        //
        // Ada dua kelompok:
        //
        // a) Guru biasa — hanya pelaporan + direktori siswa + prestasi.
        //    TIDAK boleh melihat data pribadi siswa berupa daftar
        //    pelanggaran (pelanggaran.index / pelanggaran.show).
        //    Leaderboard tetap boleh, tapi bagian "Top Pelanggaran"
        //    disembunyikan di view.
        //
        // b) Unsur pimpinan (kepsek + waka, role "pimpinan") —
        //    monitoring seluruh sekolah, jadi hak aksesnya sama seperti
        //    guru biasa PLUS daftar pelanggaran.
        //
        // Penting: cabangnya memakai isGuru(), yang juga mencakup role
        // "pimpinan". Kalau tidak, role pimpinan akan jatuh ke
        // return $next() di akhir dan otomatis mendapat akses penuh —
        // termasuk hapus data.
        if ($user->isGuru()) {
            $allowedRoutes = [
                'siswa.index',
                'siswa.search',
                'prestasi.index',
                'prestasi.show',
                'leaderboard',
            ];

            if ($user->isPimpinan()) {
                $allowedRoutes[] = 'pelanggaran.index';
                $allowedRoutes[] = 'pelanggaran.show';
            }

            /*
             * Pimpinan tertentu (mis. Waka Humas) boleh menambah
             * pelanggaran langsung lewat menu Tambah Pelanggaran.
             * Selainnya tetap read-only.
             */
            if ($user->canDirectAddPelanggaran()) {
                $allowedRoutes[] = 'pelanggaran.create';
                $allowedRoutes[] = 'pelanggaran.store';
            }

            /*
             * Pimpinan tertentu (mis. Waka Humas) juga boleh
             * memverifikasi laporan layaknya Guru PDS.
             */
            if ($user->canVerify()) {
                $allowedRoutes[] = 'pelanggaran.pending';
                $allowedRoutes[] = 'pelanggaran.approve';
                $allowedRoutes[] = 'pelanggaran.reject';
            }

            if (in_array($routeName, $allowedRoutes)) {
                return $next($request);
            }

            // Pesan khusus untuk dua halaman yang paling sering ditanyakan.
            $pesan = in_array($routeName, ['pelanggaran.index', 'pelanggaran.show'], true)
                ? 'Akses dibatasi. Daftar pelanggaran siswa hanya dapat dilihat oleh Kepala Sekolah, Wakil Kepala Sekolah, Guru PDS, dan Guru BK.'
                : 'Akses dibatasi. Guru hanya memiliki akses untuk pelaporan, melihat direktori siswa, dan mengelola prestasi.';

            return redirect()->route('lapor.index')->with('error', $pesan);
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
