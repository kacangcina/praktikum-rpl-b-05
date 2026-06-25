<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $collection = $request->user()->collections()->firstOrCreate([
            'name' => 'Koleksi Saya',
        ]);

        if ($collection->recipes()->whereKey($recipe->id)->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resep sudah ada di koleksi ini.']);
            }

            return back()->with('status', 'Resep sudah ada di koleksi ini.');
        }

        $collection->recipes()->attach($recipe->id, ['saved_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Resep berhasil disimpan ke Koleksi Saya.'], 201);
        }

        return back()->with('status', 'Resep berhasil disimpan ke Koleksi Saya.');
    }

    public function destroy(Request $request, Recipe $recipe)
    {
        $collection = $request->user()->collections()->where('name', 'Koleksi Saya')->first();
        $collection?->recipes()->detach($recipe->id);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Resep dihapus dari koleksi.']);
        }

        return back()->with('status', 'Resep dihapus dari koleksi.');
    }
}
