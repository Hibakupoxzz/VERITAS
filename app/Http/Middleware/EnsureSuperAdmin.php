<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi rute tertentu hanya untuk Super Admin (role "admin").
 *
 * Dipakai untuk Manajemen Akun. Pemegang "akses penuh" (mis. Wakil
 * Kepala Kesiswaan) dibuka hampir semua fitur, tapi area akun
 * sengaja tetap di tangan Super Admin.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses dibatasi. Manajemen Akun hanya untuk Super Admin.');
        }

        return $next($request);
    }
}
