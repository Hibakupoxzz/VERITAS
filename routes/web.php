<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PrestasiController;

// ==========================
// Root
// ==========================
Route::get('/', function () {
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
// App Routes
// Perlu login
// ==========================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ==========================
    // Siswa
    // ==========================
    Route::resource('siswa', SiswaController::class);

    // Search siswa
    Route::get('/search-siswa', [SiswaController::class, 'search'])
        ->name('siswa.search');

    // ==========================
    // Pelanggaran
    // ==========================
    Route::resource('pelanggaran', PelanggaranController::class);

    // Export pelanggaran
    Route::get(
        '/pelanggaran/export/harian',
        [PelanggaranController::class, 'exportHarian']
    )->name('pelanggaran.export.harian');

    Route::get(
        '/pelanggaran/export/mingguan',
        [PelanggaranController::class, 'exportMingguan']
    )->name('pelanggaran.export.mingguan');

    // ==========================
    // Prestasi
    // ==========================
    Route::resource('prestasi', PrestasiController::class);

    // ==========================
    // Leaderboard
    // ==========================
    Route::get(
        '/leaderboard',
        [PrestasiController::class, 'leaderboard']
    )->name('leaderboard');
});
