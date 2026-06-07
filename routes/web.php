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

// Jalur rahasia untuk eksekusi migrasi di server Vercel
Route::get('/secret-migrate-force', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Sukses migrasi! Log: ' . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return 'Gagal: ' . $e->getMessage();
    }
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