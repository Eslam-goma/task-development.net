<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/verify', [VerificationController::class, 'verify']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('tags', TagController::class);

    Route::prefix('posts')->group(function () {
        Route::get('deleted', [PostController::class, 'trashed']);
        Route::post('{id}/restore', [PostController::class, 'restore']);
        Route::apiResource('/', PostController::class);
    });

    Route::get('stats', [StatsController::class, 'index']);
});
