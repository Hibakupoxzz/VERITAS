<?php

use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PelanggaranController;

Route::resource('siswa', SiswaController::class);
Route::resource('pelanggaran', PelanggaranController::class);

Route::get('/search-siswa', [SiswaController::class, 'search']);

