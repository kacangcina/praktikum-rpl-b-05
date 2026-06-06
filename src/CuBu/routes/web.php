<?php

use App\Http\Controllers\Admin\CreatorVerificationController as AdminCreatorVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CreatorVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class, 'index'])->name('home');

Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/create', [RecipeController::class, 'create'])->middleware('auth')->name('recipes.create');
Route::post('/recipes', [RecipeController::class, 'store'])->middleware('auth')->name('recipes.store');
Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/profile', [ProfileController::class, 'me'])->middleware('auth')->name('profile.me');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::middleware('auth')->group(function () {
    Route::get('/creator/apply', [CreatorVerificationController::class, 'create'])->name('creator.apply');
    Route::post('/creator/apply', [CreatorVerificationController::class, 'store'])->name('creator.apply.store');
    Route::get('/creator/verifications/{verification}/document', [CreatorVerificationController::class, 'download'])
        ->name('creator.verifications.document');

    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections/recipes/{recipe}', [CollectionController::class, 'store'])->name('collections.store');
    Route::delete('/collections/recipes/{recipe}', [CollectionController::class, 'destroy'])->name('collections.destroy');

    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::get('/recipes/{recipe}/video', [VideoController::class, 'create'])->name('recipes.video.create');
    Route::post('/recipes/{recipe}/video', [VideoController::class, 'store'])->name('recipes.video.store');

    Route::post('/notifications/read', [NotificationController::class, 'readAll'])->name('notifications.read');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/creator-verifications', [AdminCreatorVerificationController::class, 'index'])
            ->name('creator-verifications.index');
        Route::get('/creator-verifications/{verification}', [AdminCreatorVerificationController::class, 'show'])
            ->name('creator-verifications.show');
        Route::patch('/creator-verifications/{verification}/approve', [AdminCreatorVerificationController::class, 'approve'])
            ->name('creator-verifications.approve');
        Route::patch('/creator-verifications/{verification}/reject', [AdminCreatorVerificationController::class, 'reject'])
            ->name('creator-verifications.reject');
    });
});
