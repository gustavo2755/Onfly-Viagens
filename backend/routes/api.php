<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelOrderController;
use App\Http\Controllers\Api\TravelOrderStatusLogController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

Route::middleware('auth:sanctum')->prefix('travel-orders')->group(function () {
    Route::get('/', [TravelOrderController::class, 'index']);
    Route::post('/', [TravelOrderController::class, 'store']);
    Route::get('/dashboard', [TravelOrderController::class, 'dashboard']);
    Route::get('/status-logs', [TravelOrderStatusLogController::class, 'index']);
    Route::get('/{id}', [TravelOrderController::class, 'show']);
    Route::patch('/{travelOrder}/status', [TravelOrderController::class, 'updateStatus']);
});
