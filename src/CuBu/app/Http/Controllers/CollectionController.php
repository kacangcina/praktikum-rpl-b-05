<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collection = $request->user()->collections()->firstOrCreate([
            'name' => 'Koleksi Saya',
        ]);

        $recipes = $collection->recipes()
            ->with(['creator', 'ingredients', 'video'])
            ->orderByPivot('saved_at', 'desc')
            ->paginate(12);

        return view('collections.index', compact('collection', 'recipes'));
    }

    public function store(Request $request, Recipe $recipe)
    {
        $collection = $request->user()->collections()->firstOrCreate([
            'name' => 'Koleksi Saya',
        ]);

        if ($collection->recipes()->whereKey($recipe->id)->exists()) {
            return back()->with('status', 'Resep sudah ada di koleksi ini.');
        }

        $collection->recipes()->attach($recipe->id, ['saved_at' => now()]);

        return back()->with('status', 'Resep berhasil disimpan ke Koleksi Saya.');
    }

    public function destroy(Request $request, Recipe $recipe)
    {
        $collection = $request->user()->collections()->where('name', 'Koleksi Saya')->first();
        $collection?->recipes()->detach($recipe->id);

        return back()->with('status', 'Resep dihapus dari koleksi.');
    }
}
