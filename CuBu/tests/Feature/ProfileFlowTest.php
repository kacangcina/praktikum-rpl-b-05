<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_media_route_serves_uploaded_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('recipe-thumbnails/resep.png', 'image-content');

        $this->get(route('media.public', ['path' => 'recipe-thumbnails/resep.png']))
            ->assertOk()
            ->assertHeader('content-disposition', 'inline; filename=resep.png');
    }

    public function test_profile_is_public_and_shows_users_recipes(): void
    {
        $user = User::factory()->create(['bio' => 'Suka masakan rumahan.']);
        Recipe::create([
            'user_id' => $user->id,
            'title' => 'Sayur Asem',
            'description' => 'Segar untuk makan siang.',
            'difficulty' => 'mudah',
            'estimated_time' => 40,
            'published_at' => now(),
        ]);

        $this->getJson('/api/profiles/'.$user->id)
            ->assertOk()
            ->assertJsonFragment(['username' => $user->username])
            ->assertJsonFragment(['bio' => 'Suka masakan rumahan.'])
            ->assertJsonFragment(['title' => 'Sayur Asem']);
    }

    public function test_user_can_update_profile_and_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $avatar = UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        );

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Naufal Koki',
            'username' => 'naufal_koki',
            'bio' => 'Belajar memasak makanan Nusantara.',
            'avatar' => $avatar,
        ]);

        $user->refresh();

        $response->assertRedirect(route('profile.show', $user));
        $this->assertSame('Naufal Koki', $user->name);
        $this->assertSame('naufal_koki', $user->username);
        $this->assertSame('Belajar memasak makanan Nusantara.', $user->bio);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_guest_cannot_edit_profile(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_profile_short_url_redirects_to_authenticated_users_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertRedirect(route('profile.show', $user));
    }

    public function test_regular_user_sees_creator_verification_badge_on_own_profile(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->getJson('/api/profiles/'.$user->id)
            ->assertOk()
            ->assertJsonPath('profile.role', 'user')
            ->assertJsonPath('profile.can_upload_videos', false);
    }

    public function test_admin_profile_shows_admin_status_without_recipe_section(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin CuBu',
        ]);

        $this->actingAs($admin)
            ->getJson('/api/profiles/'.$admin->id)
            ->assertOk()
            ->assertJsonPath('profile.role_label', 'Admin')
            ->assertJsonPath('profile.is_admin', true);
    }

    public function test_verified_creator_profile_shows_video_controls_without_apply_button(): void
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        Recipe::create([
            'user_id' => $creator->id,
            'title' => 'Ayam Bakar Creator',
            'description' => 'Resep milik creator.',
            'difficulty' => 'sedang',
            'estimated_time' => 50,
            'published_at' => now(),
        ]);

        $this->actingAs($creator)
            ->getJson('/api/profiles/'.$creator->id)
            ->assertOk()
            ->assertJsonPath('profile.role_label', 'Creator')
            ->assertJsonPath('profile.can_upload_videos', true);
    }

    public function test_photo_recipe_does_not_offer_video_upload(): void
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'is_verified' => true,
        ]);
        Recipe::create([
            'user_id' => $creator->id,
            'title' => 'Ayam Foto',
            'description' => 'Resep dengan media foto.',
            'difficulty' => 'mudah',
            'estimated_time' => 30,
            'thumbnail' => 'recipe-thumbnails/ayam.jpg',
            'published_at' => now(),
        ]);

        $this->actingAs($creator)
            ->getJson('/api/profiles/'.$creator->id)
            ->assertOk()
            ->assertJsonPath('recipes.0.thumbnail_url', route('media.public', ['path' => 'recipe-thumbnails/ayam.jpg']));
    }

}
