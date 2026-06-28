<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\ManagerAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Http\Middleware\CheckToken;
use Laravel\Passport\Http\Middleware\CheckTokenForAnyScope;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: ['api/stripe/webhook']);

        // $middleware->api(append: [
        //     CreateFreshApiToken::class,
        // ]);

        $middleware->web(append: [
            CreateFreshApiToken::class,
        ]);
        $middleware->alias([
            'admin-auth' => AdminAuth::class,
            'manager-auth' => ManagerAuth::class,
            'scopes' => CheckToken::class,
            'scope' => CheckTokenForAnyScope::class,
        ]);
    })
    // ->withMiddleware(function (Middleware $middleware): void {
    //     $middleware->alias(['admin-auth' => AdminAuth::class]);
    // })
    // ->withMiddleware(function (Middleware $middleware): void {
    //     $middleware->alias(['manager-auth' => ManagerAuth::class]);
    // })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
