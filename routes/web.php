<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporPelanggaranController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SiswaPortalController;
use Illuminate\Support\Facades\Route;

// ==========================
// Root
// ==========================
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isSiswa()) {
            return redirect()->route('siswa.portal');
        }

        if (auth()->user()->isWalas() || auth()->user()->isGuru()) {
            return redirect()->route('lapor.index');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

// ==========================
// Auth Routes
// ==========================
Route::middleware('guest')->group(function () {

    // Halaman login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    // Proses login
    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.process');
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==========================
// Portal Siswa (Akun NIS)
// ==========================
// Sengaja sebelum grup aplikasi tenaga pendidik. Gate ensure_not_siswa
// dipasang di grup bawah, jadi portal ini tetap bisa dibuka siswa.
Route::middleware('auth')->group(function () {
    Route::get('/portal/siswa', [SiswaPortalController::class, 'index'])
        ->name('siswa.portal');
});

// ==========================
// App Routes (Perlu Login)
// ==========================
Route::middleware(['auth', 'ensure_not_siswa'])->group(function () {

    // ==========================
    // Lapor Pelanggaran (Portal Walas / Semua Guru)
    // ==========================
    Route::get('/lapor', [LaporPelanggaranController::class, 'index'])
        ->name('lapor.index');
    Route::post('/lapor', [LaporPelanggaranController::class, 'store'])
        ->name('lapor.store');
    Route::get('/lapor/riwayat', [LaporPelanggaranController::class, 'riwayat'])
        ->name('lapor.riwayat');

    // ========================================================
    // Area Manajemen & Verifikasi (Super Admin, BK, PDS)
    // Guru Walas DIBATASI dari rute-rute di bawah ini!
    // ========================================================
    Route::middleware('can_manage')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Siswa
        Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])
            ->name('siswa.template');
        Route::post('/siswa/import', [SiswaController::class, 'import'])
            ->name('siswa.import');
        Route::get('/search-siswa', [SiswaController::class, 'search'])
            ->name('siswa.search');
        Route::resource('siswa', SiswaController::class);

        // Pelanggaran (Approval & Management)
        Route::post('/pelanggaran/{id}/approve', [PelanggaranController::class, 'approve'])
            ->name('pelanggaran.approve');
        Route::post('/pelanggaran/{id}/reject', [PelanggaranController::class, 'reject'])
            ->name('pelanggaran.reject');
        Route::get('/pelanggaran/export/harian', [PelanggaranController::class, 'exportHarian'])
            ->name('pelanggaran.export.harian');
        Route::get('/pelanggaran/export/mingguan', [PelanggaranController::class, 'exportMingguan'])
            ->name('pelanggaran.export.mingguan');
        Route::get('/pelanggaran/pending', [PelanggaranController::class, 'pending'])
            ->name('pelanggaran.pending');
        Route::resource('pelanggaran', PelanggaranController::class);

        // Prestasi
        Route::resource('prestasi', PrestasiController::class);

        // Leaderboard
        Route::get('/leaderboard', [PrestasiController::class, 'leaderboard'])
            ->name('leaderboard');

        // ========================================================
        // Backup (Super Admin + pemegang akses penuh)
        // ========================================================
        Route::get('/backup', [BackupController::class, 'index'])
            ->name('backup.index');
        Route::get('/backup/export', [BackupController::class, 'export'])
            ->name('backup.export');

        // ========================================================
        // Manajemen Akun — KHUSUS Super Admin
        // (pemegang akses penuh sengaja tidak termasuk)
        // ========================================================
        Route::middleware('super_admin')->group(function () {
            // Ganti password akun (semua user di bawah Super Admin).
            Route::get('/account/{account}/password', [AccountController::class, 'editPassword'])
                ->name('account.password.edit');
            Route::put('/account/{account}/password', [AccountController::class, 'updatePassword'])
                ->name('account.password.update');

            Route::resource('account', AccountController::class)
                ->except(['show'])
                ->parameters(['account' => 'account']);
        });
    });
});
