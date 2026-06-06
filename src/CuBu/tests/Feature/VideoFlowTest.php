<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_open_video_upload_form(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $recipe = $this->createRecipe($user);

        $this->actingAs($user)
            ->get(route('recipes.video.create', $recipe))
            ->assertForbidden();
    }

    public function test_verified_creator_can_upload_mp4_video(): void
    {
        Storage::fake('public');
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        $recipe = $this->createRecipe($creator);

        $response = $this->actingAs($creator)->post(route('recipes.video.store', $recipe), [
            'title' => 'Teknik Memotong Bawang',
            'description' => 'Teknik dasar menggunakan pisau dengan aman.',
            'difficulty' => 'mudah',
            'video' => UploadedFile::fake()->create('kelas.mp4', 100, 'video/mp4'),
        ]);

        $video = Video::firstOrFail();

        $response->assertRedirect(route('recipes.show', $recipe));
        $this->assertSame($creator->id, $video->user_id);
        $this->assertSame($recipe->id, $video->recipe_id);
        Storage::disk('public')->assertExists($video->file_path);
    }

    public function test_creator_cannot_attach_video_to_another_users_recipe(): void
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        $recipe = $this->createRecipe(User::factory()->create());

        $this->actingAs($creator)
            ->get(route('recipes.video.create', $recipe))
            ->assertForbidden();
    }

    public function test_creator_can_edit_video_metadata_without_uploading_file_again(): void
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        $recipe = $this->createRecipe($creator);
        $video = Video::create([
            'user_id' => $creator->id,
            'recipe_id' => $recipe->id,
            'title' => 'Judul Lama',
            'difficulty' => 'mudah',
            'file_path' => 'cooking-videos/lama.mp4',
        ]);

        $this->actingAs($creator)
            ->post(route('recipes.video.store', $recipe), [
                'title' => 'Judul Baru',
                'description' => 'Deskripsi diperbarui.',
                'difficulty' => 'sedang',
            ])
            ->assertRedirect(route('recipes.show', $recipe));

        $video->refresh();
        $this->assertSame('Judul Baru', $video->title);
        $this->assertSame('cooking-videos/lama.mp4', $video->file_path);
    }

    private function createRecipe(User $user): Recipe
    {
        return Recipe::create([
            'user_id' => $user->id,
            'title' => 'Resep Video',
            'description' => 'Resep untuk kelas video.',
            'difficulty' => 'mudah',
            'estimated_time' => 20,
            'published_at' => now(),
        ]);
    }
}
