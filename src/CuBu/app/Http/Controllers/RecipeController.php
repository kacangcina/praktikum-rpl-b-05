<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $query = Recipe::with(['creator', 'ingredients']);

        if ($search !== '') {
            $like = '%'.$search.'%';

            $query->where(function ($query) use ($like): void {
                $query->where('title', 'like', $like)
                    ->orWhereHas('ingredients', fn ($query) => $query->where('ingredient_name', 'like', $like));
            })->orderByRaw(
                'CASE WHEN title LIKE ? THEN 0 ELSE 1 END',
                [$like],
            );
        }

        $recipes = $query
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $suggestions = $search !== '' && $recipes->isEmpty()
            ? Recipe::with('creator')->latest('published_at')->limit(3)->get()
            : collect();

        $featuredRecipe = $search === ''
            ? Recipe::with(['creator', 'ingredients', 'video'])
                ->latest('published_at')
                ->latest()
                ->first()
            : null;

        return view('recipes.index', compact('recipes', 'search', 'suggestions', 'featuredRecipe'));
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['creator', 'tools', 'ingredients', 'steps', 'video']);

        $isSaved = auth()->check()
            && auth()->user()->collections()
                ->whereHas('recipes', fn ($query) => $query->whereKey($recipe->id))
                ->exists();

        return view('recipes.show', compact('recipe', 'isSaved'));
    }

    public function create()
    {
        abort_unless(Auth::user()?->canPublishRecipes(), 403);

        return view('recipes.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()?->canPublishRecipes(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'difficulty' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
            'estimated_time' => ['required', 'integer', 'min:1', 'max:1440'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'video' => [
                Rule::prohibitedIf(fn () => ! $request->user()->canUploadVideos()),
                'nullable',
                'file',
                'mimetypes:video/mp4',
                'mimes:mp4',
                'max:512000',
            ],
            'tools' => ['required', 'array', 'min:1'],
            'tools.*' => ['required', 'string', 'max:100'],
            'ingredient_names' => ['required', 'array', 'min:1'],
            'ingredient_names.*' => ['required', 'string', 'max:150'],
            'ingredient_quantities' => ['required', 'array', 'min:1'],
            'ingredient_quantities.*' => ['required', 'string', 'max:100'],
            'step_titles' => ['required', 'array', 'min:1'],
            'step_titles.*' => ['required', 'string', 'max:150'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*' => ['required', 'string', 'max:3000'],
        ], [
            'tools.required' => 'Alat masak wajib diisi.',
            'ingredient_names.required' => 'Bahan wajib diisi.',
            'ingredient_names.*.required' => 'Nama bahan wajib diisi.',
            'ingredient_quantities.*.required' => 'Takaran bahan wajib diisi.',
            'step_titles.required' => 'Judul langkah masak wajib diisi.',
            'step_titles.*.required' => 'Judul langkah masak wajib diisi.',
            'steps.required' => 'Langkah masak wajib diisi.',
            'steps.*.required' => 'Langkah masak wajib diisi.',
            'video.prohibited' => 'Hanya creator terverifikasi yang dapat mengunggah video.',
            'video.mimes' => 'Video harus berformat MP4.',
            'video.max' => 'Ukuran video maksimal 500 MB.',
        ]);

        if (count($validated['ingredient_names']) !== count($validated['ingredient_quantities'])) {
            throw ValidationException::withMessages([
                'ingredient_names' => 'Setiap bahan harus memiliki nama dan takaran.',
            ]);
        }

        if (count($validated['step_titles']) !== count($validated['steps'])) {
            throw ValidationException::withMessages([
                'step_titles' => 'Setiap langkah harus memiliki judul dan deskripsi.',
            ]);
        }

        $thumbnail = $request->file('thumbnail')?->store('recipe-thumbnails', 'public');
        $videoPath = $request->file('video')?->store('cooking-videos', 'public');

        $recipe = DB::transaction(function () use ($request, $validated, $thumbnail, $videoPath): Recipe {
            $recipe = Recipe::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'difficulty' => $validated['difficulty'],
                'estimated_time' => $validated['estimated_time'],
                'thumbnail' => $thumbnail,
                'published_at' => now(),
            ]);

            foreach ($validated['tools'] as $tool) {
                $recipe->tools()->create(['tool_name' => trim($tool)]);
            }

            foreach ($validated['ingredient_names'] as $index => $name) {
                $recipe->ingredients()->create([
                    'ingredient_name' => trim($name),
                    'quantity' => trim($validated['ingredient_quantities'][$index]),
                ]);
            }

            foreach ($validated['steps'] as $index => $step) {
                $recipe->steps()->create([
                    'step_number' => $index + 1,
                    'title' => trim($validated['step_titles'][$index]),
                    'description' => trim($step),
                ]);
            }

            if ($videoPath) {
                $recipe->video()->create([
                    'user_id' => $request->user()->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'difficulty' => $validated['difficulty'],
                    'file_path' => $videoPath,
                ]);
            }

            return $recipe;
        });

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('status', 'Resep berhasil dipublikasikan.');
    }

    public function destroy(Request $request, Recipe $recipe)
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $recipe->load('video');
        $thumbnail = $recipe->getRawOriginal('thumbnail');
        $videoPath = $recipe->video?->getRawOriginal('file_path');

        $recipe->delete();

        $disk = Storage::disk('public');

        foreach (array_filter([$thumbnail, $videoPath]) as $path) {
            if (! $disk->delete($path) && $disk->exists($path)) {
                @unlink($disk->path($path));
            }
        }

        return redirect()
            ->route('profile.show', $request->user())
            ->with('status', 'Resep berhasil dihapus.');
    }
}
