<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akun siswa punya portal sendiri. Semua halaman tenaga pendidik
 * (pelaporan, dashboard, manajemen, dll.) ditutup untuk mereka.
 */
class EnsureNotSiswa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSiswa()) {
            return redirect()->route('siswa.portal')
                ->with('error', 'Akses dibatasi. Halaman ini hanya untuk tenaga pendidik.');
        }

        return $next($request);
    }
}
