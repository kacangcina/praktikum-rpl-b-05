<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreatorVerification;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function session(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user() ? $this->user($request->user(), true) : null,
        ]);
    }

    public function recipes(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q'));
        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';
        $query = Recipe::with(['creator', 'ingredients', 'video']);

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(fn ($query) => $query
                ->where('title', 'like', $like)
                ->orWhereHas('ingredients', fn ($query) => $query->where('ingredient_name', 'like', $like)));
        }

        if ($sort === 'popular') {
            $query->withCount('collections')
                ->orderByDesc('collections_count')
                ->latest('published_at');
        } else {
            $query->latest('published_at');
        }

        $recipes = $query->latest()->paginate(12);

        return response()->json([
            'recipes' => collect($recipes->items())->map(fn (Recipe $recipe) => $this->recipeCard($recipe)),
            'featured' => $search === ''
                ? ($recipes->items()[0] ?? null) ? $this->recipeCard($recipes->items()[0]) : null
                : null,
            'search' => $search,
            'sort' => $sort,
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'total' => $recipes->total(),
            ],
        ]);
    }

    public function recipe(Request $request, Recipe $recipe): JsonResponse
    {
        $recipe->load(['creator', 'tools', 'ingredients', 'steps', 'video', 'reviews.user']);
        $saved = $request->user()
            ? $request->user()->collections()->whereHas('recipes', fn ($query) => $query->whereKey($recipe->id))->exists()
            : false;

        return response()->json([
            'recipe' => [
                ...$this->recipeCard($recipe),
                'description' => $recipe->description,
                'tools' => $recipe->tools->map(fn ($tool) => ['id' => $tool->id, 'name' => $tool->tool_name]),
                'ingredients' => $recipe->ingredients->map(fn ($ingredient) => [
                    'id' => $ingredient->id,
                    'name' => $ingredient->ingredient_name,
                    'quantity' => $ingredient->quantity,
                ]),
                'steps' => $recipe->steps->map(fn ($step) => [
                    'id' => $step->id,
                    'number' => $step->step_number,
                    'title' => $step->title ?: 'Langkah '.$step->step_number,
                    'description' => $step->description,
                ]),
                'video' => $recipe->video ? [
                    'title' => $recipe->video->title,
                    'description' => $recipe->video->description,
                    'difficulty' => $recipe->video->difficulty,
                    'url' => route('recipes.video.watch', $recipe),
                ] : null,
                'is_saved' => $saved,
                'can_edit_recipe' => $request->user()?->id === $recipe->user_id,
                'can_edit_video' => $request->user()?->canUploadVideos()
                    && $request->user()->id === $recipe->user_id,
                'can_delete' => $request->user()?->id === $recipe->user_id,
                'reviews' => $recipe->reviews->map(fn ($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'user' => $this->user($review->user),
                ]),
                'my_review' => $request->user()
                    ? optional($recipe->reviews->firstWhere('user_id', $request->user()->id), fn ($review) => [
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                    ])
                    : null,
            ],
        ]);
    }

    public function profile(Request $request, User $user): JsonResponse
    {
        $user->load('latestCreatorVerification');
        $recipes = $user->recipes()->with(['creator', 'ingredients', 'video'])->latest('published_at')->get();

        return response()->json([
            'profile' => $this->user(
                $user,
                $request->user()?->id === $user->id || $request->user()?->isAdmin(),
            ),
            'recipes' => $recipes->map(fn (Recipe $recipe) => $this->recipeCard($recipe)),
            'is_owner' => $request->user()?->id === $user->id,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'is_following' => $request->user()
                ? $request->user()->following()->whereKey($user->id)->exists()
                : false,
            'can_follow' => $request->user() && $request->user()->id !== $user->id && ! $user->isAdmin(),
            'latest_verification' => $user->latestCreatorVerification ? $this->verification($user->latestCreatorVerification) : null,
            'notifications' => $request->user()?->id === $user->id
                ? $user->notifications()->latest()->limit(10)->get()->map(fn ($notification) => [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ])
                : [],
        ]);
    }

    public function collection(Request $request): JsonResponse
    {
        $collection = $request->user()->collections()->firstOrCreate(['name' => 'Koleksi Saya']);
        $recipes = $collection->recipes()->with(['creator', 'ingredients', 'video'])->orderByPivot('saved_at', 'desc')->get();

        return response()->json([
            'collection' => ['id' => $collection->id, 'name' => $collection->name],
            'recipes' => $recipes->map(fn (Recipe $recipe) => $this->recipeCard($recipe)),
        ]);
    }

    public function creatorVerification(Request $request): JsonResponse
    {
        return response()->json([
            'latest_verification' => $request->user()->latestCreatorVerification
                ? $this->verification($request->user()->latestCreatorVerification)
                : null,
        ]);
    }

    public function adminVerifications(Request $request): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'], true)
            ? $request->query('status')
            : 'pending';

        $items = CreatorVerification::with(['user', 'reviewer'])
            ->where('status', $status)
            ->latest('submitted_at')
            ->get();
        $counts = CreatorVerification::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return response()->json([
            'status' => $status,
            'counts' => $counts,
            'verifications' => $items->map(fn (CreatorVerification $verification) => $this->verification($verification)),
        ]);
    }

    public function adminVerification(Request $request, CreatorVerification $verification): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $verification->load(['user', 'reviewer']);

        return response()->json(['verification' => $this->verification($verification)]);
    }

    private function recipeCard(Recipe $recipe): array
    {
        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'difficulty' => $recipe->difficulty,
            'estimated_time' => $recipe->estimated_time,
            'thumbnail_url' => $recipe->thumbnail_url,
            'ingredient_count' => $recipe->ingredients->count(),
            'has_video' => (bool) $recipe->video,
            'creator' => $recipe->creator ? $this->user($recipe->creator) : null,
            'published_at' => $recipe->published_at,
        ];
    }

    private function user(User $user, bool $includeEmail = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'bio' => $user->bio,
            'avatar_url' => $user->avatar_url,
            'initials' => $user->initials,
            'role' => $user->role,
            'role_label' => $user->role_label,
            'is_verified' => $user->is_verified,
            'can_publish_recipes' => $user->canPublishRecipes(),
            'can_upload_videos' => $user->canUploadVideos(),
            'is_admin' => $user->isAdmin(),
        ];

        if ($includeEmail) {
            $data['email'] = $user->email;
        }

        return $data;
    }

    private function verification(CreatorVerification $verification): array
    {
        return [
            'id' => $verification->id,
            'status' => $verification->status,
            'portfolio_url' => $verification->portfolio_url,
            'notes' => $verification->notes,
            'rejection_reason' => $verification->rejection_reason,
            'submitted_at' => $verification->submitted_at,
            'reviewed_at' => $verification->reviewed_at,
            'document_url' => route('creator.verifications.document', $verification),
            'user' => $verification->relationLoaded('user') && $verification->user
                ? $this->user($verification->user, true)
                : null,
            'reviewer' => $verification->relationLoaded('reviewer') && $verification->reviewer
                ? $this->user($verification->reviewer, true)
                : null,
        ];
    }
}
