<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EngineerController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\LaporanController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role: Kepala Pimpinan
Route::middleware(['auth', 'role:Kepala Pimpinan'])->prefix('kepala')->name('kepala.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kepalaDashboard'])->name('dashboard');
});

// Role: Pimpinan
Route::middleware(['auth', 'role:Pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pimpinanDashboard'])->name('dashboard');
});

// Role: Engineer
Route::middleware(['auth', 'role:Engineer'])->prefix('engineer')->name('engineer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'engineerDashboard'])->name('dashboard');
});

// Master Data (Akses: Kepala Pimpinan & Pimpinan)
Route::middleware(['auth', 'role:Kepala Pimpinan,Pimpinan'])->prefix('master')->name('master.')->group(function () {
    // Customer
    Route::resource('customer', CustomerController::class)->except(['create', 'show', 'edit']);
    
    // Engineer
    Route::resource('engineer', EngineerController::class)->except(['create', 'show', 'edit']);
    
    // Tool
    Route::resource('tool', ToolController::class)->except(['create', 'show', 'edit']);
});

// Rute Kunjungan (Semua role login dapat melihat list & detail)
Route::middleware(['auth'])->prefix('kunjungan')->name('kunjungan.')->group(function () {
    Route::get('/', [KunjunganController::class, 'index'])->name('index');
    Route::get('/{id}', [KunjunganController::class, 'show'])->name('show');

    // Role Pimpinan
    Route::middleware(['role:Pimpinan'])->group(function () {
        Route::post('/store', [KunjunganController::class, 'store'])->name('store');
    });

    // Role Engineer
    Route::middleware(['role:Engineer'])->group(function () {
        Route::post('/{id}/checkin', [KunjunganController::class, 'checkIn'])->name('checkin');
        Route::post('/{id}/dokumentasi', [KunjunganController::class, 'uploadDokumentasi'])->name('dokumentasi');
        Route::post('/{id}/checkout', [KunjunganController::class, 'checkOut'])->name('checkout');
    });

    // Verifikasi Tanda Tangan Customer
    Route::post('/{id}/signature', [KunjunganController::class, 'verifySignature'])->name('signature');
});

// Rute Laporan & Cetak PDF (Akses semua pengguna terautentikasi)
Route::middleware(['auth'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/{id}/pdf', [LaporanController::class, 'downloadPdf'])->name('pdf');
});