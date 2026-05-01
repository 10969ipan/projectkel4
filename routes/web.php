<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes (Core & Shared)
|--------------------------------------------------------------------------
*/

// Midtrans Webhook Callback
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransController::class, 'callback']);

// EMERGENCY DATA FIX (Internal Fix)
Route::get('/fix-roles', function() {
    \App\Models\User::where('store_role', 'customer')->update(['role' => null]);
    return redirect()->route('store.index')->with('success', 'Database Pelanggan berhasil diperbaiki!');
});

// TEMPORARY MIGRATION ROUTE
Route::get('/migrate-db', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migration sukses dijalankan! <br><br> Hasil: <pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Gagal menjalankan migration: " . $e->getMessage();
    }
});

Route::get('/run-migrate', function() {
    return redirect('/migrate-db');
});

// Home Redirection Logic
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('store.index');
    }
    // Default untuk tamu (Guest), langsung ke Store
    return redirect()->route('store.index');
})->name('home');

// General Authentication (Staff & Admin)
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');