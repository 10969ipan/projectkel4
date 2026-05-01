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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Validasi kredensial TANPA login dulu
        if (Auth::validate($credentials)) {
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            // 2. Cek Role: Hanya Admin dan Staff yang boleh masuk Dashboard
            if ($user->role !== 'admin' && $user->role !== 'staff') {
                return back()->withErrors([
                    'email' => 'Akses ditolak. Akun Pelanggan tidak dapat mengakses Dashboard Admin.',
                ])->withInput($request->only('email'));
            }

            // 3. Jika oke, baru login
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // PULL FULL CART FROM DATABASE TO SESSION
            $fullCart = \App\Models\CartItem::where('user_id', $user->id)->get();
            $newCart = [];
            foreach ($fullCart as $item) {
                $newCart[$item->item_id] = [
                    'id' => $item->item_id,
                    'qty' => $item->qty,
                    'type' => $item->type,
                    'interval' => $item->interval_days
                ];
            }
            session()->put('cart', $newCart);

            if ($user->role === 'admin' || $user->role === 'staff') {
                return redirect()->route('dashboard')->with('success', 'Berhasil masuk ke Dashboard!');
            }

            return redirect()->intended('/')->with('success', 'Berhasil masuk!');
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

        // LANGKAH 4: Redirect ke halaman login admin
        // Arahkan user ke halaman login dashboard setelah logout
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}