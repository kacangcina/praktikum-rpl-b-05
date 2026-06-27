<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\User;
use App\Notifications\AdminActionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RecipeController extends Controller
{
    public function store(Request $request)
    {
        abort_unless($request->user()?->canPublishRecipes(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'difficulty' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
            'estimated_time' => ['required', 'integer', 'min:1', 'max:1440'],
            'thumbnail' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
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
            'ingredient_names.*.required' => 'Bahan wajib diisi.',
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
        $videoPath = $request->file('video')?->store('cooking-videos', 'local');

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

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Resep berhasil dipublikasikan.', 'recipe_id' => $recipe->id], 201);
        }

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('status', 'Resep berhasil dipublikasikan.');
    }

    public function update(Request $request, Recipe $recipe)
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $requiresReview = in_array($recipe->moderation_status, ['unpublished', 'pending_review'], true);
        $validated = $this->validateRecipe($request);
        $newThumbnail = $request->file('thumbnail')?->store('recipe-thumbnails', 'public');
        $newVideoPath = $request->file('video')?->store('cooking-videos', 'local');
        $oldThumbnail = $recipe->getRawOriginal('thumbnail');
        $oldVideoPath = $recipe->video?->getRawOriginal('file_path');

        DB::transaction(function () use ($request, $recipe, $validated, $newThumbnail, $newVideoPath): void {
            $recipe->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'difficulty' => $validated['difficulty'],
                'estimated_time' => $validated['estimated_time'],
                'thumbnail' => $newThumbnail ?: $recipe->getRawOriginal('thumbnail'),
            ]);

            $recipe->tools()->delete();
            $recipe->ingredients()->delete();
            $recipe->steps()->delete();

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

            if ($newVideoPath) {
                $recipe->video()->updateOrCreate(
                    ['recipe_id' => $recipe->id],
                    [
                        'user_id' => $request->user()->id,
                        'title' => $validated['title'],
                        'description' => $validated['description'],
                        'difficulty' => $validated['difficulty'],
                        'file_path' => $newVideoPath,
                    ],
                );
            } elseif ($recipe->video) {
                $recipe->video->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'difficulty' => $validated['difficulty'],
                ]);
            }
        });

        if ($newThumbnail && $oldThumbnail && $oldThumbnail !== $newThumbnail) {
            Storage::disk('public')->delete($oldThumbnail);
        }

        if ($newVideoPath && $oldVideoPath && $oldVideoPath !== $newVideoPath) {
            Storage::disk('local')->delete($oldVideoPath);
            Storage::disk('public')->delete($oldVideoPath);
        }

        if ($requiresReview) {
            $recipe->update([
                'moderation_status' => 'pending_review',
                'moderated_at' => now(),
            ]);

            User::query()
                ->where('role', 'admin')
                ->whereNull('closed_at')
                ->each(fn (User $admin) => $admin->notify(new AdminActionNotification([
                    'type' => 'recipe_revision_submitted',
                    'level' => 'info',
                    'title' => 'Resep telah diperbaiki',
                    'message' => "{$request->user()->username} telah memperbaiki resep \"{$recipe->title}\" dan meminta peninjauan ulang.",
                    'action_url' => route('admin.dashboard', ['path' => 'recipes']).'?status=pending_review',
                    'action_label' => 'Tinjau resep',
                    'subject' => ['type' => 'recipe', 'id' => $recipe->id, 'title' => $recipe->title],
                ])));
        }

        return response()->json([
            'message' => $requiresReview
                ? 'Resep berhasil diperbaiki dan dikirim untuk ditinjau admin.'
                : 'Resep berhasil diperbarui.',
            'recipe_id' => $recipe->id,
        ]);
    }

    private function validateRecipe(Request $request): array
    {
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

        return $validated;
    }

    public function destroy(Request $request, Recipe $recipe)
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $recipe->load('video');
        $thumbnail = $recipe->getRawOriginal('thumbnail');
        $videoPath = $recipe->video?->getRawOriginal('file_path');

        $recipe->delete();

        if ($thumbnail) {
            Storage::disk('public')->delete($thumbnail);
        }

        if ($videoPath) {
            Storage::disk('local')->delete($videoPath);
            Storage::disk('public')->delete($videoPath);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Resep berhasil dihapus.']);
        }

        return redirect()
            ->route('profile.show', $request->user())
            ->with('status', 'Resep berhasil dihapus.');
    }

    public function index() {
    $recipes = Recipe::all();
    return response()->json([
        'status' => 'success',
        'data' => $recipes
    ]);
}
}
