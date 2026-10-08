<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuickTransactionController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/quick-transaction', [QuickTransactionController::class, 'store'])->name('quick-transaction.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
