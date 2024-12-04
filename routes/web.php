<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('/products/image/{filename}', [ProductController::class, 'getImage']);
});

// routes/web.php
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
