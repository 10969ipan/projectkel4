<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainRoutingMiddleware
{
    /**
     * Pisahkan akses domain Store (pharmacare.web.id) dan Dashboard Admin (simastok.web.id).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $isAdminDomain = in_array($host, ['simastok.web.id', 'www.simastok.web.id'], true);
        $isStoreDomain = in_array($host, ['pharmacare.web.id', 'www.pharmacare.web.id'], true);

        if (!$isAdminDomain && !$isStoreDomain) {
            return $next($request);
        }

        // Hanya redirect navigasi GET/HEAD agar POST/AJAX/Webhook tidak terganggu
        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            return $next($request);
        }

        $storeBase = 'https://www.pharmacare.web.id';
        $adminBase = 'https://simastok.web.id';

        if ($isAdminDomain) {
            // Root (/) pada simastok.web.id langsung mengarah ke Dashboard atau Login Admin
            if ($request->path() === '/') {
                if (auth()->check() && auth()->user()->isInternal()) {
                    return redirect()->route('dashboard');
                }
                return redirect()->route('login');
            }

            // Jika mengakses path storefront di domain admin, arahkan ke domain store
            if ($request->is(
                'store', 'store/*',
                'cart', 'cart/*',
                'checkout', 'checkout/*',
                'telemedicine', 'telemedicine/*',
                'account', 'account/*',
                'auth/google', 'auth/google/*',
                'cek-ongkir', 'search-destinations'
            )) {
                return redirect()->away($storeBase . $request->getRequestUri());
            }
        }

        if ($isStoreDomain) {
            // Jika mengakses path admin/dashboard di domain store, arahkan ke simastok.web.id
            if ($request->is(
                'dashboard',
                'login',
                'profile',
                'users', 'users/*',
                'categories', 'categories/*',
                'units', 'units/*',
                'items', 'items/*',
                'transactions', 'transactions/*',
                'reports', 'reports/*',
                'pharmacare', 'pharmacare/*',
                'item-requests', 'item-requests/*'
            )) {
                return redirect()->away($adminBase . $request->getRequestUri());
            }
        }

        return $next($request);
    }
}
