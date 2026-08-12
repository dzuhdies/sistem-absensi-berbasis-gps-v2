<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\JenisIzinController;
use App\Http\Controllers\GuruController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    return redirect(match (Auth::user()->role) {
        'admin' => '/admin/dashboard',
        'pegawai' => '/pegawai/rekap-absensi',
        'siswa' => '/siswa/absen',
        'guru' => '/guru/rekap-absensi',
        default => '/login',
    });
});

// Fallback untuk shared hosting yang tidak dapat membaca symlink public/storage.
Route::get('/storage/absen/{path}', [SiswaController::class, 'showFotoAbsensi'])
    ->where('path', '.*')
    ->name('absensi.photo');

// Kompatibilitas untuk URL lama ketika document root mengarah ke folder proyek.
Route::get('/public/storage/absen/{path}', [SiswaController::class, 'showFotoAbsensi'])
    ->where('path', '.*')
    ->name('absensi.photo.legacy');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::resource('gedungs', GedungController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('admin.gedungs');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::put('/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/gedungs', [AdminController::class, 'storeGedung'])->name('admin.gedungs.store');
    Route::put('/admin/gedungs/{id}', [AdminController::class, 'updateGedung'])->name('admin.gedungs.update');
    Route::delete('/admin/gedungs/{id}', [AdminController::class, 'destroyGedung'])->name('admin.gedungs.destroy');
    Route::get('/admin/log-aktivitas', [LogController::class, 'index'])->name('admin.log.index');
    Route::get('/logs', [LogController::class, 'index'])->name('admin.logs');
    Route::put('/admin/jam-telat', [AdminController::class, 'updateJamTelat'])->name('admin.jam_telat.update');
    Route::resource('jenis-izin', JenisIzinController::class)->names('admin.jenis_izin');
    Route::get('/guru/create', [AdminController::class, 'createGuru'])->name('admin.guru.create');
    Route::post('/guru/store', [AdminController::class, 'storeGuru'])->name('admin.guru.store');
    Route::get('/guru/{id}/edit', [AdminController::class, 'editGuru'])->name('admin.guru.edit');
    Route::put('/guru/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
});

Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/rekap-absensi', [App\Http\Controllers\PegawaiController::class, 'rekapAbsensi'])->name('pegawai.absensi.rekap');
    Route::get('/pegawai/rekap/export', [PegawaiController::class, 'exportRekap'])->name('pegawai.absensi.export');
});

Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/absen', [SiswaController::class, 'absenForm'])->name('siswa.absen.form');
    Route::post('/siswa/absen-masuk', [SiswaController::class, 'absenMasuk'])->name('siswa.absen.masuk');
    Route::post('/siswa/absen-keluar', [SiswaController::class, 'absenKeluar'])->name('siswa.absen.keluar');
    Route::get('/siswa/riwayat', [App\Http\Controllers\SiswaController::class, 'getRiwayat'])->name('siswa.riwayat');
    Route::post('/siswa/absen-izin', [SiswaController::class, 'absenIzin'])->name('siswa.absen.izin');
});

Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/guru/rekap-absensi', [GuruController::class, 'rekapAbsensi'])->name('guru.guru.rekap');
    Route::get('/guru/export-rekap', [GuruController::class, 'exportRekap'])->name('guru.export.rekap');
});
