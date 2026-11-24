<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Public, read-only, throttled chat endpoint: a stale page must never break it with a 419.
        $middleware->validateCsrfTokens(except: ['ai-chat']);
        // The language cookie is a plain "en" / "bn", so it is read without decrypting.
        $middleware->encryptCookies(except: ['lang']);
        $middleware->web(append: [\App\Http\Middleware\Localize::class]);
        $middleware->alias(['admin' => \App\Http\Middleware\EnsureAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
