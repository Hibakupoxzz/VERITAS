<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);

Route::resource('siswa', SiswaController::class);
Route::resource('pelanggaran', PelanggaranController::class);

//
Route::get('/search-siswa', [SiswaController::class, 'search']);

//Export Excel
Route::get(
    '/pelanggaran/export/harian',
    [PelanggaranController::class, 'exportHarian']
)->name('pelanggaran.export.harian');

Route::get(
    '/pelanggaran/export/mingguan',
    [PelanggaranController::class, 'exportMingguan']
)->name('pelanggaran.export.mingguan');
