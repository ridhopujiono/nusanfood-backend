<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // example protected route

    Route::apiResource('foods', App\Http\Controllers\Api\FoodController::class);
    Route::post('/foods/{id}/verify', [App\Http\Controllers\Api\FoodController::class, 'verify']);
});
