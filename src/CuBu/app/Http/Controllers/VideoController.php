<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function create(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipeVideo($request, $recipe);
        $recipe->load('video');

        return view('videos.create', compact('recipe'));
    }

    public function store(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipeVideo($request, $recipe);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
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
            'video.mimes' => 'Video harus berformat MP4.',
            'video.max' => 'Ukuran file melebihi batas 500MB. Kompres video sebelum mengunggah.',
        ]);

        $videoFile = $request->file('video');
        $video = $recipe->video;

        if ($video && $videoFile) {
            Storage::disk('public')->delete($video->file_path);
        }

        $attributes = [
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'difficulty' => $validated['difficulty'],
        ];

        if ($videoFile) {
            $attributes['file_path'] = $videoFile->store('cooking-videos', 'public');
        }

        if ($video) {
            $video->update($attributes);
        } else {
            $recipe->video()->create($attributes);
        }

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('status', 'Video resep berhasil dipublikasikan.');
    }

    private function authorizeRecipeVideo(Request $request, Recipe $recipe): void
    {
        abort_unless(
            $request->user()->canUploadVideos() && $recipe->user_id === $request->user()->id,
            403,
        );
    }
}
