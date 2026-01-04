<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CONTROLLER AUTENTIKASI
 * 
 * Menangani proses autentikasi pengguna seperti login dan logout
 */
class AuthController extends Controller
{
    /**
     * FUNGSI LOGIN: Menangani proses login pengguna
     * 
     * Proses yang dilakukan:
     * 1. Validasi input email dan password
     * 2. Coba autentikasi dengan kredensial yang diberikan
     * 3. Jika berhasil: regenerate session dan redirect ke dashboard
     * 4. Jika gagal: kembali ke form login dengan pesan error
     * 
     * @param Request $request - Request yang berisi email dan password
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // VALIDASI INPUT: Pastikan email dan password diisi dengan benar
        $credentials = $request->validate([
            'email' => 'required|email',      // Email wajib diisi dan harus format email yang valid
            'password' => 'required',          // Password wajib diisi
        ]);

        // PROSES AUTENTIKASI: Coba login dengan kredensial yang diberikan
        // Auth::attempt() akan mengecek apakah email dan password cocok dengan database
        if (Auth::attempt($credentials)) {

            // LOGIN BERHASIL

            // KEAMANAN: Regenerate session ID untuk mencegah session fixation attack
            // Session fixation adalah serangan di mana attacker mencuri session ID
            $request->session()->regenerate();

            // REDIRECT: Arahkan user ke halaman yang dituju (intended)
            // Jika tidak ada halaman intended, arahkan ke /dashboard
            return redirect()->intended('/dashboard')->with('success', 'Berhasil masuk!');
        }

        // LOGIN GAGAL

        // Kembali ke halaman login dengan pesan error
        // Error akan ditampilkan di form login
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * FUNGSI LOGOUT: Menangani proses logout pengguna
     * 
     * Proses yang dilakukan:
     * 1. Logout user dari sistem
     * 2. Hapus semua data session
     * 3. Regenerate CSRF token untuk keamanan
     * 4. Redirect ke halaman home
     * 
     * @param Request $request - Request HTTP
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // LANGKAH 1: Logout user dari sistem
        // Menghapus autentikasi user saat ini
        Auth::logout();

        // LANGKAH 2: Invalidate session
        // Menghapus semua data session yang tersimpan
        // Ini penting untuk keamanan agar session lama tidak bisa digunakan lagi
        $request->session()->invalidate();

        // LANGKAH 3: Regenerate CSRF token
        // Membuat CSRF token baru untuk mencegah CSRF attack
        // CSRF (Cross-Site Request Forgery) adalah serangan yang memanfaatkan session aktif
        $request->session()->regenerateToken();

        // LANGKAH 4: Redirect ke halaman home
        // Arahkan user ke halaman utama setelah logout
        return redirect('/');
    }
}