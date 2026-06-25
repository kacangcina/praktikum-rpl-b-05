<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecipeController;

// Contoh mendaftarkan route untuk mengambil daftar resep
Route::get('/recipes', [RecipeController::class, 'index']);