<?php

use App\Exceptions\BusinessRuleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: ['api/*']);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'add.token.from.query' => \App\Http\Middleware\AddTokenFromQuery::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (BusinessRuleException $exception, Request $request) {
            return response()->json(['message' => $exception->getMessage()], 422);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(config('app.frontend_url', 'http://localhost:5173') . '/login');
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            return response()->json(['message' => $exception->getMessage() ?: 'HTTP error.'], $exception->getStatusCode());
        });
    })->create();
