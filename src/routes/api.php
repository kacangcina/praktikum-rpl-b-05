<?php

use App\Http\Controllers\Api\AppController;
use Illuminate\Support\Facades\Route;

Route::get('/mobile/recipes', [AppController::class, 'recipes']);
Route::get('/mobile/recipes/{recipe}', [AppController::class, 'recipe']);
