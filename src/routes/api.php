<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CookingConsultationController;
use App\Http\Controllers\CreatorVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function (): void {
    Route::post('/login', [MobileAuthController::class, 'login']);
    Route::post('/register', [MobileAuthController::class, 'register']);

    Route::middleware('optional.sanctum')->group(function (): void {
        Route::get('/recipes', [AppController::class, 'recipes']);
        Route::get('/recipes/{recipe}', [AppController::class, 'recipe']);
        Route::get('/profiles/{user}', [AppController::class, 'profile']);
    });

    Route::middleware(['auth:sanctum', 'active'])->group(function (): void {
        Route::get('/session', [AppController::class, 'session']);
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/collection', [AppController::class, 'collection']);
        Route::post('/collection/{recipe}', [CollectionController::class, 'store']);
        Route::delete('/collection/{recipe}', [CollectionController::class, 'destroy']);
        Route::post('/profile/update', [ProfileController::class, 'update']);
        Route::post('/verify-creator', [CreatorVerificationController::class, 'store']);
        Route::post('/notifications/read', [NotificationController::class, 'readAll']);
        Route::post('/recipes', [RecipeController::class, 'store']);
        Route::post('/recipes/{recipe}/update', [RecipeController::class, 'update']);
        Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);
        Route::post('/recipes/{recipe}/reviews', [RecipeReviewController::class, 'store']);
        Route::post('/cooking-consultation', [CookingConsultationController::class, 'store'])
            ->middleware('throttle:10,1');
    });
});
