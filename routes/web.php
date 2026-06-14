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




// Home Redirection Logic
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isInternal()) {
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

// Temporary Migration Route
Route::get('/run-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migrasi database berhasil dijalankan!<br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
});