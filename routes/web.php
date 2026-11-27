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




// Halaman utama (/) langsung ditangani oleh routes/store.php (store.index)

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

// Vercel Storage Symlink Fallback Route
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');