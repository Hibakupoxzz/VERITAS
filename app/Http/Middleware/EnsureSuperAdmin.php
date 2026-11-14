<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi rute tertentu untuk Super Admin dan pemegang "akses
 * penuh" (access_level = full).
 *
 * Dipakai untuk Manajemen Akun. Pemegang akses penuh, misalnya Wakil
 * Kepala Kesiswaan, diperlakukan setara Super Admin.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->isAdmin() && ! $user->hasFullAccess()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses dibatasi. Manajemen Akun hanya untuk Super Admin dan pemegang akses penuh.');
        }

        return $next($request);
    }
}
