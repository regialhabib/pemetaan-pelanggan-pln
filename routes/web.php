<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TugasKunjunganController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        if(Auth::user()->role == 'petugas') {
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
    Route::get('/tugas-kunjungan/detail/{id}', [TugasKunjunganController::class, 'detail'])->name('tugas_kunjungan.detail');
    Route::get('/tugas-kunjungan/map/{id?}', [TugasKunjunganController::class, 'map'])->name('tugas_kunjungan.map');
    Route::get('/tugas-kunjungan/pelanggans/{id?}', [TugasKunjunganController::class, 'pelanggans']);
    Route::post('/api/kunjungan/update-status/{id}', [TugasKunjunganController::class, 'updateStatus']);


    // ----------------------------------------------------
    // ADMIN ONLY ROUTES
    // ----------------------------------------------------
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Pelanggan Management
        Route::get('/pelanggans', [PelangganController::class, 'pelanggan'])->name('pelanggan.index');
        Route::post('/pelanggan/update', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::post('/pelanggan/store', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::delete('/pelanggan/destroy/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');
        Route::post('/pelanggan/import', [PelangganController::class, 'import']);
        Route::get('/pelanggan/import/progress/{key}', [PelangganController::class, 'progress']);
        Route::get('/api/pelanggans', [PelangganController::class, 'index']);

        // Tugas Kunjungan Management
        Route::get('/tugas-kunjungan', [TugasKunjunganController::class, 'index'])->name('tugas_kunjungan.index');
        Route::post('/tugas-kunjungan', [TugasKunjunganController::class, 'store'])->name('tugas_kunjungan.store');
        Route::delete('/tugas-kunjungan/{id}', [TugasKunjunganController::class, 'destroy'])->name('tugas_kunjungan.destroy');
        
        // Laporan
        Route::get('/tugas-kunjungan/laporan', [TugasKunjunganController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/data', [TugasKunjunganController::class, 'laporanData'])->name('laporan.data');
        Route::get('/laporan/print', [TugasKunjunganController::class, 'laporanPrint'])->name('laporan.print');
    });
});



Route::get('/login', [LoginController::class, 'login'])
    ->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])
    ->name('login.authenticate');

Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('/register', [RegisterController::class, 'register'])
    ->name('register');

Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');
