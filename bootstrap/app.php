<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\NoStoreResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Cloudflare Tunnel / reverse proxy mengirim X-Forwarded-Proto, jadi URL
        // yang dibuat Laravel tetap https (penting untuk fetch POST pembayaran).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => CheckRole::class,
            'no-store' => NoStoreResponse::class,
        ]);

        // Notifikasi server-to-server Midtrans tidak membawa token CSRF
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
