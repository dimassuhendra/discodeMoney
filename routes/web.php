<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuickTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\InvestasiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SumberDanaController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;

// Redirect Halaman Utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/quick-transaction', [QuickTransactionController::class, 'store'])->name('quick-transaction.store');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('pengeluaran', PengeluaranController::class)->except(['create', 'edit', 'show']);

    Route::resource('pemasukan', PemasukanController::class)->except(['create', 'edit', 'show']);

    Route::get('/investasi/history-by-nama', [InvestasiController::class, 'getHistoryByNama'])->name('investasi.history-by-nama');
    Route::post('/investasi/{id}/jual', [InvestasiController::class, 'jual'])->name('investasi.jual');
    Route::resource('investasi', InvestasiController::class)->except(['create', 'edit', 'show']);

    Route::get('/barang/history-by-nama', [BarangController::class, 'getHistoryByNama'])->name('barang.history-by-nama');
    Route::resource('barang', BarangController::class)->except(['create', 'edit', 'show']);

    Route::resource('sumber-dana', SumberDanaController::class)->except(['create', 'edit', 'show']);

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.update-profile');
    Route::put('/pengaturan/kode-akses', [PengaturanController::class, 'updateKodeAkses'])->name('pengaturan.update-kode-akses');
    Route::get('/pengaturan/export-data', [PengaturanController::class, 'exportData'])->name('pengaturan.export-data');
    Route::post('/pengaturan/reset-data', [PengaturanController::class, 'resetData'])->name('pengaturan.reset-data');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
