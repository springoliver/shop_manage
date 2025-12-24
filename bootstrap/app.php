<?php

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
        // Exclude webservice API routes from CSRF protection (for Android app)
        $middleware->validateCsrfTokens(except: [
            'ws/webservice/*',
        ]);
        
        $middleware->alias([
            'auth.storeowner' => \App\Http\Middleware\RedirectIfNotStoreOwner::class,
            'auth.admin' => \App\Http\Middleware\RedirectIfNotAdmin::class,
            'auth.employee' => \App\Http\Middleware\RedirectIfNotEmployee::class,
            'guest.storeowner' => \App\Http\Middleware\RedirectIfAuthenticated::class . ':storeowner',
            'guest.admin' => \App\Http\Middleware\RedirectIfAuthenticated::class . ':admin',
            'guest.employee' => \App\Http\Middleware\RedirectIfAuthenticated::class . ':employee',
            'set.group' => \App\Http\Middleware\SetCurrentGroup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
