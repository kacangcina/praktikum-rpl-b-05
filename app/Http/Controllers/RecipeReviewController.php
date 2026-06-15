<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeReviewController extends Controller
{
    public function store(Request $request, Recipe $recipe): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $recipe->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated,
        );

        return response()->json(['message' => 'Ulasan berhasil disimpan.']);
    }
}
