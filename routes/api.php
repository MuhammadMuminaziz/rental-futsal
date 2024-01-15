<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\RefreshTokenController;
use App\Http\Controllers\API\Auth\RegistrationController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\FutGalController;
use App\Http\Controllers\API\FutsalController;
use App\Http\Controllers\API\InviteController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\DocRevController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ReviewController;
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

Route::prefix('email')->group(function () {
    Route::get('verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('resend', [EmailVerificationController::class, 'resend'])->name('verification.send')->middleware('auth:sanctum');
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [RegistrationController::class, 'register']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('/refresh/token', [AuthController::class, 'refresh']);
    });
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('futsal/trashed', [FutsalController::class, 'futsalTrashed']);
    Route::post('futsal/restore', [FutsalController::class, 'restoreAll']);
    Route::post('futsal/restore/{id}', [FutsalController::class, 'restore']);
    Route::apiResource('futsal', FutsalController::class);

    Route::apiResource('futsal-gallery', FutGalController::class);

    Route::get('users/trashed', [UserController::class, 'userTrashed']);
    Route::post('users/restore', [UserController::class, 'restoreAll']);
    Route::post('users/restore/{id}', [UserController::class, 'restore']);
    Route::apiResource('users', UserController::class);

    Route::apiResource('facilities', FacilityController::class);

    Route::apiResource('reviews', ReviewController::class);

    Route::apiResource('doc-review', DocRevController::class);

    Route::apiResource('team', TeamController::class);

    Route::apiResource('invite', InviteController::class)->except('show');
    // Route::get('invite', [InviteController::class, 'index']);
    // Route::post('invite', [InviteController::class, 'store']);
    // Route::put('accepted/{id}', [InviteController::class, 'accepted']);
    // Route::delete('invite/{id}', [InviteController::class, 'destroy']);
});
