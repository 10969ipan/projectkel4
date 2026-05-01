<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika tidak login, biarkan sebagai TAMU (Guest) melihat-lihat toko dulu
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // JIKA sudah login, pastikan dia adalah akun Pelanggan
        // Jika dia adalah Admin atau Staff, blokir akses ke halaman toko
        if ($user->role === 'admin' || $user->role === 'staff') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Internal (Admin/Staff) tidak diperbolehkan mengakses fitur toko.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Informasi: Anda saat ini login sebagai Admin/Staff. Untuk mengakses fitur toko, silakan logout terlebih dahulu dan gunakan akun Pelanggan terpisah.');
        }

        // Jika dia login sebagai customer, biarkan lanjut
        if ($user->store_role === 'customer') {
            return $next($request);
        }

        return $next($request);
    }
}
