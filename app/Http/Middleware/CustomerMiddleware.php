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
        // Jika tidak login, biarkan sebagai TAMU (Guest)
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // Daftar rute yang HANYA boleh diakses Pelanggan (Transaksi/Profil)
        $isProtectedRoute = $request->is('cart*', 'checkout*', 'account*', 'reviews*');

        // Jika dia adalah Admin atau Staff
        if ($user->role === 'admin' || $user->role === 'staff') {
            if ($isProtectedRoute) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Admin/Staff tidak dapat melakukan transaksi di Store.'], 403);
                }
                // Kembalikan ke halaman utama Store (BUKAN ke dashboard)
                return redirect()->route('store.index')->with('error', 'Fitur transaksi hanya untuk akun Pelanggan.');
            }
            
            // Biarkan browsing, UI akan menganggap dia Guest (karena pengecekan isCustomer di blade)
            return $next($request);
        }

        // Jika dia login sebagai customer, biarkan lanjut
        if ($user->isCustomer()) {
            return $next($request);
        }

        return $next($request);
    }
}
