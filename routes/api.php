<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\FoodController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::get('/batches/{id}', [BatchController::class, 'show']);

    // example protected route
    Route::prefix('foods')->group(function () {
        Route::post('/upload', [FoodController::class, 'uploadExcel']);
        Route::post('/', [FoodController::class, 'store']);
        Route::get('/', [FoodController::class, 'index']);

        Route::get('{food}', [FoodController::class, 'show']);
        Route::put('{food}', [FoodController::class, 'update']);
        Route::delete('{food}', [FoodController::class, 'destroy']);



        // khusus nutrisi
        Route::put(
            '{food}/servings/{serving}/nutrition',
            [FoodController::class, 'updateNutrition']
        );
    });
});
