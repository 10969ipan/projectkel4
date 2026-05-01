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

// FORCE CONVERT IMAGES TO WEBP
Route::get('/force-webp', function() {
    try {
        $articlesCount = 0;
        $itemsCount = 0;

        // Update wellness_articles (HealthArticle)
        \Illuminate\Support\Facades\DB::table('health_articles')->get()->each(function ($article) use (&$articlesCount) {
            $newPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $article->image_path);
            if ($newPath !== $article->image_path) {
                \Illuminate\Support\Facades\DB::table('health_articles')
                    ->where('id', $article->id)
                    ->update(['image_path' => $newPath]);
                $articlesCount++;
            }
        });

        // Update items
        \Illuminate\Support\Facades\DB::table('items')->get()->each(function ($item) use (&$itemsCount) {
            $newPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $item->image_path);
            if ($newPath !== $item->image_path) {
                \Illuminate\Support\Facades\DB::table('items')
                    ->where('id', $item->id)
                    ->update(['image_path' => $newPath]);
                $itemsCount++;
            }
        });

        return "Konversi Berhasil! <br> - Artikel Wellness: $articlesCount file diupdate <br> - Produk: $itemsCount file diupdate";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
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