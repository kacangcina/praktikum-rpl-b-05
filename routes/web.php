<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function (): void {
    Route::get('/session', [AppController::class, 'session']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
});

$react = fn () => view('react');

Route::get('/login', $react)->middleware('guest')->name('login');
Route::get('/', $react)->middleware('auth')->name('home');
