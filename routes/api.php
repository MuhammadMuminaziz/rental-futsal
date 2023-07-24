<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\RefreshTokenController;
use App\Http\Controllers\API\Auth\RegistrationController;
use App\Http\Controllers\API\FutGalController;
use App\Http\Controllers\API\FutsalController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [RegistrationController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('/refresh/token', [AuthController::class, 'refresh']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('futsal', FutsalController::class);

    Route::apiResource('futsal-gallery', FutGalController::class);

    Route::apiResource('users', UserController::class);
});
