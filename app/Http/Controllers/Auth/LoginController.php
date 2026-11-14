<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        /*
         * Login menerima NIG atau email dalam satu kolom.
         *
         * NIG diterima apa adanya (tanpa casting ke angka) karena
         * sebagian besar diawali nol — "0170194016" tidak boleh
         * berubah jadi "174194016".
         *
         * Email dipakai oleh akun yang belum punya NIG, misalnya
         * Super Admin dan akun walas per-kelas.
         */
        $request->validate([
            /*
             * Batas 255 (bukan 20) karena field ini menerima NIG
             * sekaligus email sebagai cadangan. Email "nama@sekolah.sch.id"
             * bisa melebihi 20 karakter, dan pesan error "nig field
             * must not be greater than 20 characters" sangat membingungkan
             * untuk pengguna yang memang sengaja mengetik email.
             *
             * NIG sendiri tetap dibatasi 20 karakter oleh kolom DB.
             */
            'nig' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        $nig = trim($request->input('nig'));

        $user = Auth::attempt(
            ['nig' => $nig, 'password' => $request->input('password')],
            $request->boolean('remember')
        );

        // Jika bukan NIG yang cocok, coba cocokkan sebagai email.
        if (! $user && filter_var($nig, FILTER_VALIDATE_EMAIL)) {
            $user = Auth::attempt(
                ['email' => $nig, 'password' => $request->input('password')],
                $request->boolean('remember')
            );
        }

        if ($user) {
            $request->session()->regenerate();

            if (Auth::user()->isWalas() || Auth::user()->isGuru()) {
                return redirect()->route('lapor.index');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'nig' => 'NIG/email atau password salah.',
        ])->onlyInput('nig');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
