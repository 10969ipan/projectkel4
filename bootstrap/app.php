<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/store.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
            'backoffice' => \App\Http\Middleware\BackOfficeMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
        ]);

        // Exclude AI Reply from CSRF to prevent 419 on stale sessions
        $middleware->validateCsrfTokens(except: [
            'consultation/ai-reply',
            'chatbot/*',
            'auth/google/callback',
            'midtrans/callback',
        ]);

        // Redireksi dinamis untuk tamu (Guest)
        $middleware->redirectTo(function (\Illuminate\Http\Request $request) {
            // Bypass redirect for AI Reply & Google OAuth callback
            if ($request->is('consultation/ai-reply') || $request->is('chatbot/*') || $request->is('auth/google*')) {
                return null;
            }

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
