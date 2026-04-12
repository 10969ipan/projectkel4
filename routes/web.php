<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes (Core & Shared)
|--------------------------------------------------------------------------
*/

// EMERGENCY DATA FIX (Internal Fix)
Route::get('/fix-roles', function() {
    \App\Models\User::where('store_role', 'customer')->update(['role' => null]);
    return redirect()->route('store.index')->with('success', 'Database Pelanggan berhasil diperbaiki!');
});

// Home Redirection Logic
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('store.index');
    }
    return view('auth.login');
})->name('home');

// General Authentication (Staff & Admin)
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');