<?php

use App\Http\Controllers\Api\SiswaApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/siswa', [SiswaApiController::class, 'index']);
Route::post('/siswa', [SiswaApiController::class, 'store']);
Route::post('/siswa/import', [SiswaApiController::class, 'bulkStore']);
