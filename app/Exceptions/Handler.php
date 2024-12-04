<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    protected $dontReport = [
        //
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($e instanceof \InvalidArgumentException) {
                Log::error('Invalid Argument Exception:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    protected function handleApiException(Throwable $e, Request $request)
    {
        if ($e instanceof ValidationException) {
            return $this->fail($e->errors(), 422);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $this->fail('Resource not found', 404);
        }

        if ($e instanceof \InvalidArgumentException) {
            return $this->fail($e->getMessage(), 400);
        }

        // Log unexpected errors
        if (!config('app.debug')) {
            Log::error($e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->fail('Server Error', 500);
        }

        return $this->fail($e->getMessage(), 500);
    }
}
