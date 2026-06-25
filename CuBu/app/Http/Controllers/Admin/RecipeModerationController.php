<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Notifications\AdminActionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RecipeModerationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,published,unpublished,pending_review'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $search = trim($validated['q'] ?? '');
        $status = $validated['status'] ?? 'all';

        $recipes = Recipe::query()
            ->select([
                'id', 'user_id', 'title', 'thumbnail', 'published_at',
                'moderation_status', 'moderation_reason', 'moderated_at',
            ])
            ->with('creator:id,name,username')
            ->withCount(['reviews', 'collections'])
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($status !== 'all', fn ($query) => $query->where('moderation_status', $status))
            ->latest('id')
            ->paginate($validated['per_page'] ?? 15);

        return response()->json([
            'recipes' => $recipes->getCollection()->map(fn (Recipe $recipe) => $this->recipeData($recipe)),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'total' => $recipes->total(),
            ],
        ]);
    }

    public function update(Request $request, Recipe $recipe): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['published', 'unpublished'])],
            'reason' => [
                Rule::requiredIf($request->input('status') === 'unpublished'),
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $recipe->update([
            'moderation_status' => $validated['status'],
            'moderation_reason' => $validated['status'] === 'unpublished'
                ? trim($validated['reason'])
                : null,
            'moderated_at' => now(),
            'moderated_by' => $request->user()->id,
        ]);

        $published = $validated['status'] === 'published';
        $recipe->creator?->notify(new AdminActionNotification([
            'type' => $published ? 'recipe_republished' : 'recipe_unpublished',
            'level' => $published ? 'success' : 'warning',
            'title' => $published ? 'Resep diterbitkan kembali' : 'Resep diturunkan oleh admin',
            'message' => $published
                ? "Resep \"{$recipe->title}\" sudah dapat dilihat publik kembali."
                : "Resep \"{$recipe->title}\" tidak lagi tampil untuk publik.",
            'reason' => $published ? null : trim($validated['reason']),
            'action_url' => $published
                ? route('recipes.show', $recipe)
                : route('recipes.edit', $recipe),
            'action_label' => $published ? 'Lihat resep' : 'Perbaiki resep',
            'subject' => ['type' => 'recipe', 'id' => $recipe->id, 'title' => $recipe->title],
        ]));

        return response()->json([
            'message' => $validated['status'] === 'published'
                ? 'Resep berhasil diterbitkan kembali.'
                : 'Resep berhasil diturunkan dari publikasi.',
            'recipe' => $this->recipeData($recipe->fresh()->load('creator')->loadCount(['reviews', 'collections'])),
        ]);
    }

    public function destroy(Request $request, Recipe $recipe): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $title = $recipe->title;
        $recipe->creator?->notify(new AdminActionNotification([
            'type' => 'recipe_deleted',
            'level' => 'danger',
            'title' => 'Resep dihapus oleh admin',
            'message' => "Resep \"{$title}\" telah dihapus permanen dan tidak dapat dibuka lagi.",
            'reason' => trim($validated['reason']),
            'subject' => ['type' => 'recipe', 'id' => $recipe->id, 'title' => $title],
        ]));

        if ($recipe->thumbnail && ! str_starts_with($recipe->thumbnail, 'http')) {
            Storage::disk('public')->delete($recipe->thumbnail);
        }

        $recipe->delete();

        return response()->json(['message' => 'Resep berhasil dihapus dan pemilik telah diberi notifikasi.']);
    }

    private function recipeData(Recipe $recipe): array
    {
        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'thumbnail_url' => $recipe->thumbnail_url,
            'status' => $recipe->moderation_status,
            'moderation_reason' => $recipe->moderation_reason,
            'moderated_at' => $recipe->moderated_at,
            'published_at' => $recipe->published_at,
            'reviews_count' => (int) ($recipe->reviews_count ?? 0),
            'collections_count' => (int) ($recipe->collections_count ?? 0),
            'creator' => $recipe->creator ? [
                'id' => $recipe->creator->id,
                'name' => $recipe->creator->name,
                'username' => $recipe->creator->username,
            ] : null,
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
