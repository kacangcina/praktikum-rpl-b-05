<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_recipe_detail(): void
    {
        $recipe = $this->createRecipe();

        $this->getJson('/api/recipes/'.$recipe->id)
            ->assertOk()
            ->assertJsonFragment(['title' => 'Soto Ayam'])
            ->assertJsonFragment(['name' => 'Ayam kampung'])
            ->assertJsonFragment(['title' => 'Rebus ayam']);
    }

    public function test_guest_cannot_watch_recipe_video(): void
    {
        $recipe = $this->createRecipe();
        $recipe->video()->create([
            'user_id' => $recipe->user_id,
            'title' => 'Video Soto',
            'difficulty' => 'sedang',
            'file_path' => 'cooking-videos/soto.mp4',
        ]);

        $this->getJson('/api/recipes/'.$recipe->id)
            ->assertOk()
            ->assertJsonPath('recipe.video.title', 'Video Soto');
    }

    public function test_verified_creator_can_upload_photo_and_video_together(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);

        $this->actingAs($creator)->post(route('recipes.store'), [
            'title' => 'Resep Dua Media',
            'description' => 'Foto menjadi poster dan video tetap dapat diputar.',
            'difficulty' => 'mudah',
            'estimated_time' => 20,
            'thumbnail' => UploadedFile::fake()->createWithContent(
                'foto.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
            ),
            'video' => UploadedFile::fake()->create('video.mp4', 100, 'video/mp4'),
            'tools' => ['Wajan'],
            'ingredient_names' => ['Nasi'],
            'ingredient_quantities' => ['1 piring'],
            'step_titles' => ['Masak'],
            'steps' => ['Masak hingga matang.'],
        ])->assertRedirect();

        $recipe = Recipe::where('title', 'Resep Dua Media')->firstOrFail();
        $this->assertNotNull($recipe->thumbnail);
        $this->assertNotNull($recipe->video);
        Storage::disk('public')->assertExists($recipe->thumbnail);
        Storage::disk('local')->assertExists($recipe->video->file_path);
    }

    public function test_latest_recipe_is_displayed_as_highlighted_recipe(): void
    {
        $this->createRecipe();

        $this->getJson('/api/recipes')
            ->assertOk()
            ->assertJsonPath('featured.title', 'Soto Ayam');
    }

    public function test_recipes_can_be_searched_by_ingredient(): void
    {
        $recipe = $this->createRecipe();
        $recipe->ingredients()->create([
            'ingredient_name' => 'Serai',
            'quantity' => '2 batang',
        ]);

        $this->getJson('/api/recipes?q=Serai')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Soto Ayam']);
    }

    public function test_recipes_can_be_sorted_by_collection_popularity(): void
    {
        $olderPopularRecipe = $this->createRecipe();
        $olderPopularRecipe->update([
            'title' => 'Resep Populer',
            'published_at' => now()->subDay(),
        ]);

        $newerRecipe = $this->createRecipe();
        $newerRecipe->update([
            'title' => 'Resep Terbaru',
            'published_at' => now(),
        ]);

        User::factory()->create()
            ->collections()
            ->create(['name' => 'Favorit'])
            ->recipes()
            ->attach($olderPopularRecipe, ['saved_at' => now()]);

        $this->getJson('/api/recipes?sort=popular')
            ->assertOk()
            ->assertJsonPath('sort', 'popular')
            ->assertJsonPath('recipes.0.title', 'Resep Populer');
    }

    public function test_regular_user_can_publish_a_complete_photo_recipe(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('recipes.store'), [
            'title' => 'Tempe Bacem',
            'description' => 'Tempe manis gurih untuk lauk keluarga.',
            'difficulty' => 'mudah',
            'estimated_time' => 45,
            'thumbnail' => UploadedFile::fake()->createWithContent(
                'tempe.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
            ),
            'tools' => ['Wajan', 'Spatula'],
            'ingredient_names' => ['Tempe', 'Gula merah'],
            'ingredient_quantities' => ['1 papan', '50 gram'],
            'step_titles' => ['Potong tempe', 'Masak bumbu'],
            'steps' => ['Potong tempe.', 'Masak bersama bumbu hingga meresap.'],
        ]);

        $recipe = Recipe::where('title', 'Tempe Bacem')->firstOrFail();

        $response->assertRedirect(route('recipes.show', $recipe));
        $this->assertCount(2, $recipe->tools);
        $this->assertCount(2, $recipe->ingredients);
        $this->assertCount(2, $recipe->steps);
        Storage::disk('public')->assertExists($recipe->thumbnail);
    }

    public function test_regular_user_can_open_recipe_form_without_creator_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('recipes.create'))
            ->assertOk()
            ->assertSee('id="root"', false);
    }

    public function test_verified_creator_can_upload_video_while_creating_recipe(): void
    {
        Storage::fake('local');
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);

        $this->actingAs($creator)
            ->get(route('recipes.create'))
            ->assertOk()
            ->assertSee('id="root"', false);

        $response = $this->actingAs($creator)->post(route('recipes.store'), [
            'title' => 'Nasi Goreng Creator',
            'description' => 'Nasi goreng dengan panduan video lengkap.',
            'difficulty' => 'mudah',
            'estimated_time' => 20,
            'video' => UploadedFile::fake()->create('nasi-goreng.mp4', 100, 'video/mp4'),
            'tools' => ['Wajan'],
            'ingredient_names' => ['Nasi'],
            'ingredient_quantities' => ['1 piring'],
            'step_titles' => ['Goreng nasi'],
            'steps' => ['Goreng semua bahan hingga matang.'],
        ]);

        $recipe = Recipe::where('title', 'Nasi Goreng Creator')->firstOrFail();
        $video = Video::where('recipe_id', $recipe->id)->firstOrFail();

        $response->assertRedirect(route('recipes.show', $recipe));
        $this->assertSame($creator->id, $video->user_id);
        Storage::disk('local')->assertExists($video->file_path);
    }

    public function test_regular_user_cannot_force_video_upload_when_creating_recipe(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('recipes.store'), [
                'title' => 'Resep Video Paksa',
                'description' => 'Video tidak boleh diterima untuk user biasa.',
                'difficulty' => 'mudah',
                'estimated_time' => 20,
                'video' => UploadedFile::fake()->create('paksa.mp4', 100, 'video/mp4'),
                'tools' => ['Wajan'],
                'ingredient_names' => ['Nasi'],
                'ingredient_quantities' => ['1 piring'],
                'step_titles' => ['Masak nasi'],
                'steps' => ['Masak hingga matang.'],
            ])
            ->assertSessionHasErrors('video');

        $this->assertDatabaseMissing('recipes', ['title' => 'Resep Video Paksa']);
    }

    public function test_unverified_creator_cannot_publish_recipe(): void
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => false,
        ]);

        $this->actingAs($creator)
            ->get(route('recipes.create'))
            ->assertForbidden();
    }

    public function test_only_verified_creator_can_upload_videos(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $unverifiedCreator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => false,
        ]);
        $verifiedCreator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);

        $this->assertFalse($user->canUploadVideos());
        $this->assertFalse($unverifiedCreator->canUploadVideos());
        $this->assertTrue($verifiedCreator->canUploadVideos());
    }

    public function test_owner_can_delete_recipe_and_associated_video(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $recipe = $this->createRecipe($owner);
        $recipe->update(['thumbnail' => 'recipe-thumbnails/resep.jpg']);
        Storage::disk('public')->put($recipe->thumbnail, 'foto');

        $video = $recipe->video()->create([
            'user_id' => $owner->id,
            'title' => 'Video Soto',
            'description' => 'Panduan memasak.',
            'difficulty' => 'sedang',
            'file_path' => 'cooking-videos/soto.mp4',
        ]);
        Storage::disk('public')->put($video->file_path, 'video');

        $this->actingAs($owner)
            ->delete(route('recipes.destroy', $recipe))
            ->assertRedirect(route('profile.show', $owner))
            ->assertSessionHas('status', 'Resep berhasil dihapus.');

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_user_cannot_delete_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = $this->createRecipe($owner);

        $this->actingAs($otherUser)
            ->delete(route('recipes.destroy', $recipe))
            ->assertForbidden();

        $this->assertDatabaseHas('recipes', ['id' => $recipe->id]);
    }

    public function test_owner_can_edit_recipe_without_losing_existing_media(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $owner = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        $recipe = $this->createRecipe($owner);
        $recipe->update(['thumbnail' => 'recipe-thumbnails/soto-lama.jpg']);
        Storage::disk('public')->put($recipe->thumbnail, 'foto');
        $video = $recipe->video()->create([
            'user_id' => $owner->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'difficulty' => $recipe->difficulty,
            'file_path' => 'cooking-videos/soto-lama.mp4',
        ]);
        Storage::disk('local')->put($video->file_path, 'video');

        $this->actingAs($owner)
            ->putJson('/api/recipes/'.$recipe->id, [
                'title' => 'Soto Ayam Spesial',
                'description' => 'Kuah kuning yang sudah diperbarui.',
                'difficulty' => 'mudah',
                'estimated_time' => 45,
                'tools' => ['Panci besar', 'Sendok sayur'],
                'ingredient_names' => ['Ayam', 'Kunyit'],
                'ingredient_quantities' => ['500 gram', '2 ruas'],
                'step_titles' => ['Rebus ayam', 'Masukkan bumbu'],
                'steps' => ['Rebus sampai empuk.', 'Masak sampai bumbu meresap.'],
            ])
            ->assertOk()
            ->assertJsonPath('recipe_id', $recipe->id);

        $recipe->refresh();
        $video->refresh();

        $this->assertSame('Soto Ayam Spesial', $recipe->title);
        $this->assertSame('recipe-thumbnails/soto-lama.jpg', $recipe->thumbnail);
        $this->assertSame('cooking-videos/soto-lama.mp4', $video->file_path);
        $this->assertSame('Soto Ayam Spesial', $video->title);
        $this->assertCount(2, $recipe->tools);
        $this->assertCount(2, $recipe->ingredients);
        $this->assertCount(2, $recipe->steps);
        Storage::disk('public')->assertExists($recipe->thumbnail);
        Storage::disk('local')->assertExists($video->file_path);
    }

    public function test_user_cannot_edit_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = $this->createRecipe($owner);

        $this->actingAs($otherUser)
            ->putJson('/api/recipes/'.$recipe->id, [
                'title' => 'Resep Diambil Alih',
            ])
            ->assertForbidden();

        $this->assertSame('Soto Ayam', $recipe->fresh()->title);
    }

    public function test_authenticated_user_can_create_and_update_recipe_review(): void
    {
        $recipe = $this->createRecipe();
        $reviewer = User::factory()->create();

        $this->actingAs($reviewer)
            ->postJson('/api/recipes/'.$recipe->id.'/reviews', [
                'rating' => 4,
                'comment' => 'Enak dan mudah diikuti.',
            ])
            ->assertOk();

        $this->actingAs($reviewer)
            ->postJson('/api/recipes/'.$recipe->id.'/reviews', [
                'rating' => 5,
                'comment' => 'Setelah dicoba lagi hasilnya lebih enak.',
            ])
            ->assertOk();

        $this->assertDatabaseCount('recipe_reviews', 1);
        $this->assertDatabaseHas('recipe_reviews', [
            'recipe_id' => $recipe->id,
            'user_id' => $reviewer->id,
            'rating' => 5,
        ]);
    }

    public function test_guest_cannot_create_recipe_review(): void
    {
        $recipe = $this->createRecipe();

        $this->postJson('/api/recipes/'.$recipe->id.'/reviews', [
            'rating' => 5,
            'comment' => 'Tidak boleh tersimpan.',
        ])->assertUnauthorized();
    }

    private function createRecipe(?User $user = null): Recipe
    {
        $user ??= User::factory()->create();
        $recipe = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Soto Ayam',
            'description' => 'Kuah kuning gurih dan hangat.',
            'difficulty' => 'sedang',
            'estimated_time' => 60,
            'published_at' => now(),
        ]);

        $recipe->tools()->create(['tool_name' => 'Panci']);
        $recipe->ingredients()->create([
            'ingredient_name' => 'Ayam kampung',
            'quantity' => '500 gram',
        ]);
        $recipe->steps()->create([
            'step_number' => 1,
            'title' => 'Rebus ayam',
            'description' => 'Rebus ayam hingga empuk.',
        ]);

        return $recipe;
    }
}
