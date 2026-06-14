<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BackOfficeMiddleware
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
            // Allow all internal/management roles (Admin, Staff, Custom Roles)
            if ($user->isInternal()) {
                return $next($request);
            }
        }

        // Redirect customers back to the store or show 403
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized back-office access.'], 403);
        }

        return redirect()->route('store.index')->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin/Staff Internal.');
    }
}
