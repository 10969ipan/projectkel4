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

        // Jika dia adalah Admin, Staff, atau Peran Kustom (Internal)
        if ($user->isInternal()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun admin/staff tidak diizinkan mengakses toko.'], 403);
            }
            // Arahkan ke dashboard admin
            return redirect()->route('dashboard')->with('error', 'Akun admin/staff tidak diizinkan mengakses toko.');
        }

        return $next($request);
    }
}
