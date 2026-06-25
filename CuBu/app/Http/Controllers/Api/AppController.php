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
            'user' => $request->user()
                ? $this->user($request->user(), true)
                : null,
        ]);
    }

    public function recipes(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q'));
        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';
        $savedRecipeIds = $this->savedRecipeIds($request);

        $query = Recipe::with(['creator', 'ingredients', 'video'])
            ->published()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // SEARCH
        if ($search !== '') {
            $like = '%' . $search . '%';
            $startsWith = $search . '%';

            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)
                  ->orWhereHas('ingredients', function ($q) use ($like) {
                      $q->where('ingredient_name', 'like', $like);
                  });
            })->orderByRaw(
                'CASE
                    WHEN LOWER(title) = LOWER(?) THEN 1
                    WHEN LOWER(title) LIKE LOWER(?) THEN 2
                    WHEN LOWER(title) LIKE LOWER(?) THEN 3
                    WHEN EXISTS (
                        SELECT 1 FROM recipe_ingredients
                        WHERE recipe_ingredients.recipe_id = recipes.id
                        AND LOWER(recipe_ingredients.ingredient_name) = LOWER(?)
                    ) THEN 4
                    ELSE 5
                END',
                [$search, $startsWith, $like, $search],
            );
        }

        // SORT
        if ($sort === 'popular') {
            $query->withCount('collections')
                  ->orderByDesc('collections_count')
                  ->latest('published_at');
        } else {
            $query->latest('published_at');
        }

        $recipes = $query->paginate(12);
        $recommendations = collect();

        if ($search !== '' && $recipes->total() === 0) {
            $recommendations = Recipe::with(['creator', 'ingredients', 'video'])
                ->published()
                ->whereHas('reviews')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        // FEATURED (FIX UTAMA)
        $featuredModel = null;

        if ($search === '') {
            $featuredModel = Recipe::with(['creator', 'ingredients', 'video'])
                ->published()
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->latest('published_at')
                ->first();
        }

        return response()->json([
            'recipes' => collect($recipes->items())
                ->map(fn (Recipe $recipe) => $this->recipeCard($recipe, $savedRecipeIds))
                ->values(),

            'featured' => $featuredModel
                ? $this->recipeCard($featuredModel, $savedRecipeIds)
                : null,
            'recommendations' => $recommendations
                ->map(fn (Recipe $recipe) => $this->recipeCard($recipe, $savedRecipeIds))
                ->values(),

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
        abort_unless(
            $recipe->moderation_status === 'published'
                || $request->user()?->id === $recipe->user_id
                || $request->user()?->isAdmin(),
            404,
        );

        $recipe->load(['creator', 'tools', 'ingredients', 'steps', 'video', 'reviews.user']);
        $recipe->loadAvg('reviews', 'rating');
        $recipe->loadCount('reviews');

        $saved = $request->user()
            ? $request->user()
                ->collections()
                ->whereHas('recipes', fn ($q) => $q->whereKey($recipe->id))
                ->exists()
            : false;

        return response()->json([
            'recipe' => [
                ...$this->recipeCard($recipe),

                'description' => $recipe->description,

                'tools' => $recipe->tools->map(fn ($tool) => [
                    'id' => $tool->id,
                    'name' => $tool->tool_name,
                ]),

                'ingredients' => $recipe->ingredients->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->ingredient_name,
                    'quantity' => $i->quantity,
                ]),

                'steps' => $recipe->steps->map(fn ($step) => [
                    'id' => $step->id,
                    'number' => $step->step_number,
                    'title' => $step->title ?: 'Langkah ' . $step->step_number,
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
                    && $request->user()?->id === $recipe->user_id,

                'can_delete' => $request->user()?->id === $recipe->user_id,

                'reviews' => $recipe->reviews->map(fn ($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'user' => $this->user($review->user),
                ]),

                'my_review' => $request->user()
                    ? optional(
                        $recipe->reviews->firstWhere('user_id', $request->user()->id),
                        fn ($r) => [
                            'rating' => $r->rating,
                            'comment' => $r->comment,
                        ]
                    )
                    : null,
            ],
        ]);
    }

    public function profile(Request $request, User $user): JsonResponse
    {
        $user->load('latestCreatorVerification');
        $savedRecipeIds = $this->savedRecipeIds($request);

        $recipes = $user->recipes()
            ->when(
                $request->user()?->id !== $user->id && ! $request->user()?->isAdmin(),
                fn ($query) => $query->published(),
            )
            ->with(['creator', 'ingredients', 'video'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest('published_at')
            ->get();

        return response()->json([
            'profile' => $this->user(
                $user,
                $request->user()?->id === $user->id || $request->user()?->isAdmin()
            ),

            'recipes' => $recipes->map(fn (Recipe $recipe) => $this->recipeCard($recipe, $savedRecipeIds)),

            'is_owner' => $request->user()?->id === $user->id,

            'latest_verification' => $user->latestCreatorVerification
                ? $this->verification($user->latestCreatorVerification)
                : null,

            'notifications' => $request->user()?->id === $user->id
                ? $user->notifications()->latest()->limit(10)->get()->map(fn ($n) => [
                    'id' => $n->id,
                    'type' => $n->data['type'] ?? 'account_update',
                    'level' => $n->data['level'] ?? 'info',
                    'title' => $n->data['title'] ?? 'Pembaruan akun CuBu',
                    'message' => $n->data['message'] ?? 'Ada pembaruan pada akun kamu.',
                    'reason' => $n->data['reason'] ?? null,
                    'action_url' => $n->data['action_url'] ?? null,
                    'action_label' => $n->data['action_label'] ?? null,
                    'subject' => $n->data['subject'] ?? null,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at,
                ])
                : [],
            'unread_notifications_count' => $request->user()?->id === $user->id
                ? $user->unreadNotifications()->count()
                : 0,
        ]);
    }

    public function collection(Request $request): JsonResponse
    {
        $collection = $request->user()
            ->collections()
            ->firstOrCreate(['name' => 'Koleksi Saya']);

        $recipes = $collection->recipes()
            ->where('moderation_status', 'published')
            ->whereNotNull('published_at')
            ->with(['creator', 'ingredients', 'video'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByPivot('saved_at', 'desc')
            ->get();

        return response()->json([
            'collection' => [
                'id' => $collection->id,
                'name' => $collection->name,
            ],
            'recipes' => $recipes->map(fn (Recipe $recipe) => $this->recipeCard($recipe, [$recipe->id])),
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

        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,approved,rejected'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $status = $validated['status'] ?? 'pending';
        $perPage = $validated['per_page'] ?? 15;

        $verifications = CreatorVerification::query()
            ->select(['id', 'user_id', 'status', 'submitted_at', 'reviewed_at'])
            ->with('user:id,name,username,email')
            ->where('status', $status)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $counts = CreatorVerification::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'status' => $status,
            'counts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'approved' => (int) ($counts['approved'] ?? 0),
                'rejected' => (int) ($counts['rejected'] ?? 0),
            ],
            'verifications' => $verifications->getCollection()
                ->map(fn (CreatorVerification $verification) => $this->verificationListItem($verification))
                ->values(),
            'pagination' => [
                'current_page' => $verifications->currentPage(),
                'last_page' => $verifications->lastPage(),
                'per_page' => $verifications->perPage(),
                'total' => $verifications->total(),
            ],
        ]);
    }

    public function adminVerification(
        Request $request,
        CreatorVerification $verification,
    ): JsonResponse {
        abort_unless($request->user()?->isAdmin(), 403);

        $verification->load([
            'user:id,name,username,email,role,is_verified',
            'reviewer:id,name,username,email',
        ]);

        return response()->json([
            'verification' => $this->verification($verification),
        ]);
    }

    private function recipeCard(Recipe $recipe, array $savedRecipeIds = []): array
    {
        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'difficulty' => $recipe->difficulty,
            'estimated_time' => $recipe->estimated_time,
            'thumbnail_url' => $recipe->thumbnail_url,

            'ingredients' => $recipe->ingredients->map(fn ($i) => [
                'id' => $i->id,
                'name' => $i->ingredient_name,
            ])->values(),

            'ingredient_count' => $recipe->ingredients->count(),
            'has_video' => (bool) $recipe->video,
            'average_rating' => $recipe->reviews_avg_rating !== null
                ? round((float) $recipe->reviews_avg_rating, 1)
                : null,
            'reviews_count' => (int) ($recipe->reviews_count ?? 0),
            'is_saved' => in_array($recipe->id, $savedRecipeIds, true),
            'creator' => $recipe->creator ? $this->user($recipe->creator) : null,
            'published_at' => $recipe->published_at,
        ];
    }

    private function savedRecipeIds(Request $request): array
    {
        $collection = $request->user()
            ?->collections()
            ->where('name', 'Koleksi Saya')
            ->first();

        return $collection
            ? $collection->recipes()->pluck('recipes.id')->all()
            : [];
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
            'unread_notifications_count' => $includeEmail
                ? $user->unreadNotifications()->count()
                : 0,
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
                ? $this->verificationUser($verification->user)
                : null,
            'reviewer' => $verification->relationLoaded('reviewer') && $verification->reviewer
                ? $this->verificationUser($verification->reviewer)
                : null,
        ];
    }

    private function verificationListItem(CreatorVerification $verification): array
    {
        return [
            'id' => $verification->id,
            'status' => $verification->status,
            'submitted_at' => $verification->submitted_at,
            'reviewed_at' => $verification->reviewed_at,
            'user' => $verification->user
                ? $this->verificationUser($verification->user)
                : null,
        ];
    }

    private function verificationUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
        ];
    }
}
