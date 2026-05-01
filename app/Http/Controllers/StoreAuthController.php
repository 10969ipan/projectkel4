<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class StoreAuthController extends Controller
{
    /** Tampilkan halaman login Pharmacare */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Jika Admin/Staff nyasar ke login store, kembalikan ke dashboard mereka
            if ($user->role === 'admin' || $user->role === 'staff') {
                return redirect()->route('dashboard')->with('info', 'Anda saat ini sudah login sebagai Admin/Staff.');
            }
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

        // 1. Validasi kredensial TANPA login dulu
        if (Auth::validate($credentials)) {
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            // 2. Cek Role: Admin dan Staff dilarang login di Store
            if ($user->role === 'admin' || $user->role === 'staff') {
                return back()->withErrors([
                    'email' => 'Akses ditolak. Akun Admin/Staff hanya dapat digunakan di Dashboard Admin.',
                ])->withInput($request->only('email'));
            }

            // 3. Jika oke, baru login
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // Pindahkan cart dari session ke database jika ada
            $sessionCart = session()->get('cart', []);
            foreach ($sessionCart as $id => $details) {
                // Logika pemindahan cart bisa ditambahkan di sini jika diperlukan
            }

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
        $user = Auth::user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Jika yang logout adalah Admin/Staff (walau dari sisi Toko), arahkan ke login Admin
        if ($user && ($user->role === 'admin' || $user->role === 'staff')) {
            return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sistem.');
        }

        return redirect()->route('store.login')->with('success', 'Anda berhasil keluar dari Pharmacare.');
    }

    // =========================================================================
    // GOOGLE OAUTH
    // =========================================================================

    /** Redirect ke halaman login Google */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /** Handle callback dari Google setelah user login */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('store.login')
                ->with('error', 'Login Google gagal: ' . $e->getMessage());
        }

        // Cari user berdasarkan google_id terlebih dahulu
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Coba cari berdasarkan email (user mungkin sudah daftar manual)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link akun manual ke Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                // Buat akun baru sebagai customer
                $user = User::create([
                    'name'       => $googleUser->getName(),
                    'email'      => $googleUser->getEmail(),
                    'google_id'  => $googleUser->getId(),
                    'avatar'     => $googleUser->getAvatar(),
                    'password'   => Hash::make(Str::random(32)),
                    'role'       => null,
                    'store_role' => 'customer',
                    'paylater_limit' => 0,
                ]);
            }
        } else {
            // Selalu update avatar & nama agar sinkron dengan Google
                // Selalu update avatar & nama agar sinkron dengan Google
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
                    'name'   => $googleUser->getName(),
                ]);
            }

            // ROLE CHECK: Admin dan Staff dilarang login di Store via Google
            if ($user->role === 'admin' || $user->role === 'staff') {
                return redirect()->route('store.login')
                    ->with('error', 'Akses ditolak. Akun Admin/Staff tidak diperbolehkan login di Store.');
            }

            // Login user
            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended(route('store.index'))->with('success', 'Berhasil masuk dengan Google!');
        } catch (\Exception $e) {
            return redirect()->route('store.login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}
