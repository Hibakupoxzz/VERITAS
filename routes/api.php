<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiswaApiController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/siswa', [SiswaApiController::class, 'index']);
Route::post('/siswa', [SiswaApiController::class, 'store']);
Route::post('/siswa/import', [SiswaApiController::class, 'bulkStore']);
