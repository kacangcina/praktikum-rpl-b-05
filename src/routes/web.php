<?php

use App\Http\Controllers\Admin\CreatorVerificationController as AdminCreatorVerificationController;
use App\Http\Controllers\Admin\AiSettingsController;
use App\Http\Controllers\Admin\RecipeModerationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CookingConsultationController;
use App\Http\Controllers\CreatorVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeReviewController;
use App\Http\Controllers\VideoController;
use App\Models\Recipe;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/media/{path}', function (string $path) {
    abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('media.public');

Route::prefix('api')->group(function (): void {
    Route::get('/session', [AppController::class, 'session']);
    Route::get('/recipes', [AppController::class, 'recipes']);
    Route::get('/recipes/{recipe}', [AppController::class, 'recipe']);
    Route::get('/profiles/{user}', [AppController::class, 'profile']);

    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

    Route::middleware(['auth', 'active'])->group(function (): void {
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
        Route::post('/cooking-consultation', [CookingConsultationController::class, 'store'])
            ->middleware('throttle:10,1');

        Route::prefix('admin')->middleware('admin')->group(function (): void {
            Route::get('/ai-settings', [AiSettingsController::class, 'show']);
            Route::put('/ai-settings', [AiSettingsController::class, 'update']);
            Route::get('/users', [UserManagementController::class, 'index']);
            Route::patch('/users/{user}', [UserManagementController::class, 'update']);
            Route::patch('/users/{user}/suspension', [UserManagementController::class, 'suspension']);
            Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
            Route::get('/recipes', [RecipeModerationController::class, 'index']);
            Route::patch('/recipes/{recipe}', [RecipeModerationController::class, 'update']);
            Route::delete('/recipes/{recipe}', [RecipeModerationController::class, 'destroy']);
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
Route::post('/recipes', [RecipeController::class, 'store'])->middleware(['auth', 'active'])->name('recipes.store');
Route::put('/profile', [ProfileController::class, 'update'])->middleware(['auth', 'active'])->name('profile.update');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/creator/apply', [CreatorVerificationController::class, 'store'])->name('creator.apply.store');
    Route::get('/creator/verifications/{verification}/document', [CreatorVerificationController::class, 'download'])
        ->name('creator.verifications.document');
    Route::post('/collections/recipes/{recipe}', [CollectionController::class, 'store'])->name('collections.store');
    Route::delete('/collections/recipes/{recipe}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::get('/recipes/{recipe}/video/watch', [VideoController::class, 'watch'])->name('recipes.video.watch');
    Route::post('/recipes/{recipe}/video', [VideoController::class, 'store'])->name('recipes.video.store');
    Route::post('/notifications/read', [NotificationController::class, 'readAll'])->name('notifications.read');

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function (): void {
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
})->middleware(['auth', 'active'])->name('recipes.create');
Route::get('/recipes/{recipe}/edit', function (Recipe $recipe) {
    abort_unless($recipe->user_id === auth()->id(), 403);

    return view('react');
})->middleware(['auth', 'active'])->name('recipes.edit');
Route::get('/recipes/{recipe}', $react)->name('recipes.show');
Route::get('/recipes/{recipe}/video', function (Recipe $recipe) {
    abort_unless(auth()->user()?->canUploadVideos() && $recipe->user_id === auth()->id(), 403);

    return view('react');
})->middleware(['auth', 'active'])->name('recipes.video.create');
Route::get('/register', $react)->middleware('guest')->name('register');
Route::get('/login', $react)->middleware('guest')->name('login');
Route::get('/profile', fn () => redirect()->route('profile.show', auth()->user()))
    ->middleware(['auth', 'active'])
    ->name('profile.me');
Route::get('/profile/edit', $react)->middleware(['auth', 'active'])->name('profile.edit');
Route::get('/profile/{user}', $react)->name('profile.show');
Route::get('/collections', $react)->middleware(['auth', 'active'])->name('collections.index');
Route::get('/consultation', $react)->middleware(['auth', 'active'])->name('consultation.index');
Route::get('/creator/apply', $react)->middleware(['auth', 'active'])->name('creator.apply');
Route::get('/admin/creator-verifications', function () {
    return view('react');
})->middleware(['auth', 'active', 'admin'])->name('admin.creator-verifications.index');
Route::get('/admin/creator-verifications/{verification}', function () {
    return view('react');
})->middleware(['auth', 'active', 'admin'])->name('admin.creator-verifications.show');

Route::get('/admin/{path?}', function () {
    return view('react');
})->where('path', 'ai-settings|users|recipes')
    ->middleware(['auth', 'active', 'admin'])
    ->name('admin.dashboard');
