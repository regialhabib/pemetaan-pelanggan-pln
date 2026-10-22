<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TugasKunjunganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->middleware('throttle:6,1')
        ->name('login.authenticate');

    Route::get('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', function (Request $request) {
        if ($request->user()->isPetugas()) {
            return redirect()->route('tugas_kunjungan.map');
        }

        return view('map');
    })->name('home');

    // ----------------------------------------------------
    // ACCESSIBLE BY ALL AUTHENTICATED USERS (Admin & Petugas)
    // ----------------------------------------------------
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [DashboardController::class, 'profileUpdate'])->name('profile.update');

    Route::get('/tugas-kunjungan/daftar-tugas', [TugasKunjunganController::class, 'show'])->name('tugas_kunjungan.show');
    Route::get('/tugas-kunjungan/detail/{tugasKunjungan}', [TugasKunjunganController::class, 'detail'])->name('tugas_kunjungan.detail');
    Route::get('/tugas-kunjungan/map/{id?}', [TugasKunjunganController::class, 'map'])->name('tugas_kunjungan.map');
    Route::get('/tugas-kunjungan/pelanggans/{id?}', [TugasKunjunganController::class, 'pelanggans'])->name('tugas_kunjungan.pelanggans');
    Route::post('/api/kunjungan/update-status/{detail}', [TugasKunjunganController::class, 'updateStatus'])->name('kunjungan.update_status');

    // ----------------------------------------------------
    // ADMIN ONLY ROUTES
    // ----------------------------------------------------
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Pelanggan Management
        Route::get('/pelanggans', [PelangganController::class, 'pelanggan'])->name('pelanggan.index');
        Route::post('/pelanggan/update', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::post('/pelanggan/store', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::delete('/pelanggan/destroy/{pelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
        Route::post('/pelanggan/import', [PelangganController::class, 'import'])->name('pelanggan.import');
        Route::get('/pelanggan/import/progress/{key}', [PelangganController::class, 'progress'])->name('pelanggan.import.progress');
        Route::get('/api/pelanggans', [PelangganController::class, 'index'])->name('pelanggan.api');

        // Tugas Kunjungan Management
        Route::get('/tugas-kunjungan', [TugasKunjunganController::class, 'index'])->name('tugas_kunjungan.index');
        Route::post('/tugas-kunjungan', [TugasKunjunganController::class, 'store'])->name('tugas_kunjungan.store');
        Route::delete('/tugas-kunjungan/{tugasKunjungan}', [TugasKunjunganController::class, 'destroy'])->name('tugas_kunjungan.destroy');

        // Laporan
        Route::get('/tugas-kunjungan/laporan', [TugasKunjunganController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/data', [TugasKunjunganController::class, 'laporanData'])->name('laporan.data');
        Route::get('/laporan/print', [TugasKunjunganController::class, 'laporanPrint'])->name('laporan.print');
    });
});
