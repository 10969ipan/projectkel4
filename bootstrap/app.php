<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
        ]);

        // Redireksi dinamis untuk tamu (Guest)
        $middleware->redirectTo(function (\Illuminate\Http\Request $request) {
            // Jika URL mengandung kata kunci toko/ecommerce, lari ke login toko
            if ($request->is('store*') || 
                $request->is('cart*') || 
                $request->is('checkout*') || 
                $request->is('telemedicine*') || 
                $request->is('account*')) {
                return route('store.login');
            }

            // Selain itu, lari ke login default (Admin/Staff)
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
