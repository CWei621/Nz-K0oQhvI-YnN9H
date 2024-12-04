<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], $code);
    }

    protected function fail($message = 'Error', $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'data' => $message
        ], $code);
    }
}
