<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StoreAuthController extends Controller
{
    /** Tampilkan halaman login Pharmacare */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('store.index');
        }
        return view('store-login');
    }

    /** Proses login dari form Pharmacare */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect ke halaman yang dituju, default ke /store
            return redirect()->intended(route('store.index'))->with('success', 'Selamat datang di Pharmacare!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    /** Tampilkan halaman registrasi Pharmacare */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('store.index');
        }
        return view('store-register');
    }

    /** Proses registrasi akun baru */
    public function register(\App\Http\Requests\StoreRegisterRequest $request)
    {
        // Buat user baru dengan role customer
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => null,           // Bukan staff internal
            'store_role' => 'customer',     // role untuk toko
            'paylater_limit' => 0,
        ]);

        // Langsung login setelah daftar
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('store.index')->with('success', 'Akun berhasil dibuat! Selamat berbelanja di Pharmacare 🎉');
    }

    /** Logout khusus toko (kembali ke halaman toko, bukan SIMA-APOTEK) */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('store.index')->with('success', 'Anda berhasil keluar dari Pharmacare.');
    }
}
