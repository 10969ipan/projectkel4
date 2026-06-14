<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Peta izin rute ke nama menu navigasi
     */
    protected static $permissionsMap = [
        'dashboard' => ['dashboard'],
        'items' => [
            'items.index', 'items.show', 'items.create', 'items.store', 'items.edit', 'items.update', 'items.destroy'
        ],
        'transactions' => [
            'transactions.index', 'transactions.create', 'transactions.store'
        ],
        'item_requests' => [
            'item-requests.index', 'item-requests.show', 'item-requests.create', 'item-requests.store', 'item-requests.approve', 'item-requests.reject'
        ],
        'store_transactions' => [
            'admin.pharmacare.transactions', 'admin.pharmacare.transactions.update'
        ],
        'store_logs' => [
            'admin.pharmacare.transaction-logs'
        ],
        'store_customers' => [
            'admin.pharmacare.customers', 'admin.pharmacare.customers.update', 'admin.pharmacare.paylater', 'admin.pharmacare.approve', 'admin.pharmacare.invoice', 'admin.pharmacare.index'
        ],
        'users' => [
            'users.index', 'users.create', 'users.store', 'users.edit', 'users.update', 'users.destroy'
        ],
        'categories' => [
            'categories.index', 'categories.create', 'categories.store', 'categories.edit', 'categories.update', 'categories.destroy'
        ],
        'units' => [
            'units.index', 'units.create', 'units.store', 'units.edit', 'units.update', 'units.destroy'
        ],
        'reports' => [
            'reports.stock', 'reports.stock.download',
            'reports.transactions', 'reports.transactions.download',
            'reports.requests', 'reports.requests.download'
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $route = $request->route();
        
        if (!$route) {
            return $next($request);
        }

        $routeName = $route->getName();

        // Profil & logout selalu diizinkan untuk semua backoffice
        if (in_array($routeName, ['profile', 'profile.update', 'logout'])) {
            return $next($request);
        }

        // Cari menu mana yang mengontrol rute ini
        $requiredMenu = null;
        foreach (self::$permissionsMap as $menu => $routes) {
            if (in_array($routeName, $routes)) {
                $requiredMenu = $menu;
                break;
            }
        }

        // Jika rute dikaitkan dengan menu dan user tidak memiliki akses, lempar 403
        if ($requiredMenu && !$user->hasPermissionToMenu($requiredMenu)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Anda tidak memiliki izin untuk menu ini.'], 403);
            }
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses menu ini.');
        }

        return $next($request);
    }
}
