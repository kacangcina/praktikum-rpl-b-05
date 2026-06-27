<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        // Mengambil semua resep yang sudah terbit beserta seluruh relasi barunya
        $recipes = Recipe::published()
            ->with(['creator', 'tools', 'ingredients', 'steps', 'video', 'comments.user', 'ratings'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recipes
        ], 200);
    }

    public function show($id)
    {
        // Mengambil detail satu resep berdasarkan ID beserta relasi barunya
        $recipe = Recipe::published()
            ->with(['creator', 'tools', 'ingredients', 'steps', 'video', 'comments.user', 'ratings'])
            ->find($id);

        if (!$recipe) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resep tidak ditemukan atau belum dipublikasikan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $recipe
        ], 200);
    }
}