<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) {
            if (config('app.debug')) {
                return null;
            }

            $status = match(true) {
                $e instanceof ValidationException => 422,
                $e instanceof ModelNotFoundException => 404,
                $e instanceof NotFoundHttpException => 404,
                $e instanceof \RuntimeException => 400,
                default => 500
            };

            $message = match(true) {
                $e instanceof ValidationException => $e->errors(),
                $status === 404 => 'Not found',
                $status === 500 => 'Server Error',
                default => $e->getMessage()
            };

            return response()->json([
                'status' => 'fail',
                'data' => $message
            ], $status);
        });
    })->create();
