<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index']);
    Route::post('/users', [AdminController::class, 'storeUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

    Route::apiResource('gedungs', GedungController::class);
    Route::get('/logs', [LogController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:pegawai'])->group(function () {
    Route::get('/rekap-absensi', [PegawaiController::class, 'rekapAbsensi']);
    Route::get('/rekap-absensi/export', [PegawaiController::class, 'exportAbsensi']);
});

Route::middleware(['auth:sanctum', 'role:siswa'])->group(function () {
    Route::post('/absen/masuk', [SiswaController::class, 'absenMasuk']);
    Route::post('/absen/keluar', [SiswaController::class, 'absenKeluar']);
    Route::get('/riwayat', [SiswaController::class, 'getRiwayat']);
});
