<?php

use App\Http\Controllers\Admin\CreatorVerificationController as AdminCreatorVerificationController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CreatorVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileFollowController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeReviewController;
use App\Http\Controllers\VideoController;
use App\Models\Recipe;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function (): void {
    Route::get('/session', [AppController::class, 'session']);
    Route::get('/recipes', [AppController::class, 'recipes']);
    Route::get('/recipes/{recipe}', [AppController::class, 'recipe']);
    Route::get('/profiles/{user}', [AppController::class, 'profile']);

    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

    Route::middleware('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/recipes', [RecipeController::class, 'store']);
        Route::put('/recipes/{recipe}', [RecipeController::class, 'update']);
        Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);
        Route::get('/collection', [AppController::class, 'collection']);
        Route::post('/collection/{recipe}', [CollectionController::class, 'store']);
        Route::delete('/collection/{recipe}', [CollectionController::class, 'destroy']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/creator-verification', [AppController::class, 'creatorVerification']);
        Route::post('/creator-verification', [CreatorVerificationController::class, 'store']);
        Route::post('/notifications/read', [NotificationController::class, 'readAll']);
        Route::post('/recipes/{recipe}/video', [VideoController::class, 'store']);
        Route::post('/recipes/{recipe}/reviews', [RecipeReviewController::class, 'store']);
        Route::post('/profiles/{user}/follow', [ProfileFollowController::class, 'store']);
        Route::delete('/profiles/{user}/follow', [ProfileFollowController::class, 'destroy']);

        Route::prefix('admin')->group(function (): void {
            Route::get('/creator-verifications', [AppController::class, 'adminVerifications']);
            Route::get('/creator-verifications/{verification}', [AppController::class, 'adminVerification']);
            Route::patch('/creator-verifications/{verification}/approve', [AdminCreatorVerificationController::class, 'approve']);
            Route::patch('/creator-verifications/{verification}/reject', [AdminCreatorVerificationController::class, 'reject']);
        });
    });
});

Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/recipes', [RecipeController::class, 'store'])->middleware('auth')->name('recipes.store');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

Route::middleware('auth')->group(function (): void {
    Route::post('/creator/apply', [CreatorVerificationController::class, 'store'])->name('creator.apply.store');
    Route::get('/creator/verifications/{verification}/document', [CreatorVerificationController::class, 'download'])
        ->name('creator.verifications.document');
    Route::post('/collections/recipes/{recipe}', [CollectionController::class, 'store'])->name('collections.store');
    Route::delete('/collections/recipes/{recipe}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::get('/recipes/{recipe}/video/watch', [VideoController::class, 'watch'])->name('recipes.video.watch');
    Route::post('/recipes/{recipe}/video', [VideoController::class, 'store'])->name('recipes.video.store');
    Route::post('/notifications/read', [NotificationController::class, 'readAll'])->name('notifications.read');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::patch('/creator-verifications/{verification}/approve', [AdminCreatorVerificationController::class, 'approve'])
            ->name('creator-verifications.approve');
        Route::patch('/creator-verifications/{verification}/reject', [AdminCreatorVerificationController::class, 'reject'])
            ->name('creator-verifications.reject');
    });
});

$react = fn () => view('react');

Route::get('/', $react)->name('home');
Route::get('/recipes', $react)->name('recipes.index');
Route::get('/recipes/create', function () {
    abort_unless(auth()->user()?->canPublishRecipes(), 403);

    return view('react');
})->middleware('auth')->name('recipes.create');
Route::get('/recipes/{recipe}/edit', function (Recipe $recipe) {
    abort_unless($recipe->user_id === auth()->id(), 403);

    return view('react');
})->middleware('auth')->name('recipes.edit');
Route::get('/recipes/{recipe}', $react)->name('recipes.show');
Route::get('/recipes/{recipe}/video', function (Recipe $recipe) {
    abort_unless(auth()->user()?->canUploadVideos() && $recipe->user_id === auth()->id(), 403);

    return view('react');
})->middleware('auth')->name('recipes.video.create');
Route::get('/register', $react)->middleware('guest')->name('register');
Route::get('/login', $react)->middleware('guest')->name('login');
Route::get('/profile', fn () => redirect()->route('profile.show', auth()->user()))
    ->middleware('auth')
    ->name('profile.me');
Route::get('/profile/edit', $react)->middleware('auth')->name('profile.edit');
Route::get('/profile/{user}', $react)->name('profile.show');
Route::get('/collections', $react)->middleware('auth')->name('collections.index');
Route::get('/creator/apply', $react)->middleware('auth')->name('creator.apply');
Route::get('/admin/creator-verifications', function () {
    abort_unless(auth()->user()?->isAdmin(), 403);

    return view('react');
})->middleware('auth')->name('admin.creator-verifications.index');
Route::get('/admin/creator-verifications/{verification}', function () {
    abort_unless(auth()->user()?->isAdmin(), 403);

    return view('react');
})->middleware('auth')->name('admin.creator-verifications.show');
