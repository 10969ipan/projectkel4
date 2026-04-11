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
        if (auth()->check()) {
            $user = auth()->user();
            // Only allow users with store_role 'customer'
            // and NOT internal users accidentally accessing store features with admin account
            if ($user->store_role === 'customer' && is_null($user->role)) {
                return $next($request);
            }
        }

        // Redirect Admin/Staff back to dashboard or show 403
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Admin/Staff cannot use store e-commerce features.'], 403);
        }

        return redirect()->route('dashboard')->with('warning', 'Informasi: Akun Admin/Staff tidak dapat melakukan transaksi di toko. Silakan gunakan akun Pelanggan terpisah.');
    }
}
