<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Pasang security headers ke SEMUA response web & api.
        $middleware->append(SecurityHeaders::class);

        // Webhook Pakasir datang dari luar (tidak ada sesi), jadi CSRF harus di-skip
        // khusus untuk path ini — validasi dilakukan via Transaction Detail API.
        $middleware->validateCsrfTokens(except: [
            'webhooks/pakasir',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
