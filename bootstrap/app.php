<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

// Bind explícito del exception handler:
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\Handler as AppExceptionHandler;

// Middlewares que SÍ existen en el framework
use App\Http\Middleware\Authenticate; // (lo creamos en el paso 2 si falta)
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aliases usados por tus rutas
        $middleware->alias([
            'auth'     => Authenticate::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);

        // Grupo "web" correcto (sin clases faltantes)
        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,       // usamos el del framework
            SubstituteBindings::class,
        ]);

        // Grupo "api" mínimo
        $middleware->group('api', [
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// 👉 Aquí se bindea el ExceptionHandler a tu App\Exceptions\Handler
$app->singleton(ExceptionHandler::class, AppExceptionHandler::class);

return $app;
