<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipeVideo($request, $recipe);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'difficulty' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
            'video' => [
                Rule::requiredIf(fn () => ! $recipe->video),
                'nullable',
                'file',
                'mimetypes:video/mp4',
                'mimes:mp4',
                'max:512000',
            ],
        ], [
            'video.required' => 'File video wajib diunggah.',
            'description.required' => 'Deskripsi video wajib diisi.',
            'video.mimes' => 'Video harus berformat MP4.',
            'video.max' => 'Ukuran file melebihi batas 500MB. Kompres video sebelum mengunggah.',
        ]);

        $videoFile = $request->file('video');
        $video = $recipe->video;

        if ($video && $videoFile) {
            Storage::disk('local')->delete($video->file_path);
            Storage::disk('public')->delete($video->file_path);
        }

        $attributes = [
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'difficulty' => $validated['difficulty'],
        ];

        if ($videoFile) {
            $attributes['file_path'] = $videoFile->store('cooking-videos', 'local');
        }

        if ($video) {
            $video->update($attributes);
        } else {
            $recipe->video()->create($attributes);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Video resep berhasil dipublikasikan.']);
        }

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('status', 'Video resep berhasil dipublikasikan.');
    }

    public function watch(Recipe $recipe)
    {
        $video = $recipe->video()->firstOrFail();

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($video->file_path)) {
                return Storage::disk($disk)->response($video->file_path);
            }
        }

        abort(404);
    }

    private function authorizeRecipeVideo(Request $request, Recipe $recipe): void
    {
        abort_unless(
            $request->user()->canUploadVideos() && $recipe->user_id === $request->user()->id,
            403,
        );
    }
}
