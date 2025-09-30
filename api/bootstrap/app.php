<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'auth' => App\Http\Controllers\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 인증 실패 시 JSON 응답
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api*')) {
                return \App\Support\ApiResponse::error(
                    'UNAUTHORIZED',
                    '인증이 필요합니다. Bearer 토큰을 확인하세요.',
                    401,
                    ['path' => $request->path(), 'method' => $request->method()]
                );
            }
        });
    })
    ->create();
