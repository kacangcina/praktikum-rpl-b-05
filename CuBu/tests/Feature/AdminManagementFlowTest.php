<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CookingConsultationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_dynamic_ai_prompt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prompt = 'Kamu adalah chef CuBu. Jawab pertanyaan memasak secara aman dan ringkas.';

        $this->actingAs($admin)
            ->putJson('/api/admin/ai-settings', ['prompt' => $prompt])
            ->assertOk()
            ->assertJsonPath('prompt', $prompt);

        $this->assertDatabaseHas('system_settings', [
            'key' => CookingConsultationService::PROMPT_KEY,
            'value' => $prompt,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson('/api/admin/ai-settings')
            ->assertForbidden();
    }

    public function test_regular_user_is_redirected_from_admin_page_with_clear_message(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/users')
            ->assertRedirect(route('recipes.index'))
            ->assertSessionHas('error', 'Halaman admin hanya dapat diakses oleh akun admin.');
    }

    public function test_admin_can_change_role_and_suspend_user_which_blocks_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'email' => 'blocked@example.com',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$user->id}", ['role' => 'creator'])
            ->assertOk()
            ->assertJsonPath('user.role', 'creator');

        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$user->id}/suspension", [
                'suspended' => true,
                'reason' => 'Pelanggaran ketentuan komunitas.',
            ])
            ->assertOk()
            ->assertJsonPath('user.is_suspended', true);

        $suspensionNotification = $user->fresh()->notifications
            ->first(fn ($notification) => $notification->data['type'] === 'account_suspended')
            ?->data;
        $this->assertNotNull($suspensionNotification);
        $this->assertSame('account_suspended', $suspensionNotification['type']);
        $this->assertSame('Pelanggaran ketentuan komunitas.', $suspensionNotification['reason']);

        auth()->logout();

        $this->postJson('/api/login', [
            'email' => 'blocked@example.com',
            'password' => 'password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('errors.email.0', 'Akun kamu sedang diblokir: Pelanggaran ketentuan komunitas.');
    }

    public function test_closed_user_sees_the_admin_reason_when_trying_to_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'email' => 'closed@example.com',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/admin/users/{$user->id}", [
                'reason' => 'Akun digunakan untuk spam berulang.',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Akun pengguna berhasil ditutup dan diarsipkan.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'closure_reason' => 'Akun digunakan untuk spam berulang.',
        ]);
        $this->assertSame(
            'account_closed',
            $user->fresh()->notifications()->firstOrFail()->data['type'],
        );

        auth()->logout();

        $this->postJson('/api/login', [
            'email' => 'closed@example.com',
            'password' => 'password123',
        ])
            ->assertForbidden()
            ->assertJsonPath('errors.email.0', 'Akun kamu telah ditutup oleh admin: Akun digunakan untuk spam berulang.');
    }

    public function test_admin_cannot_suspend_or_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$admin->id}/suspension", [
                'suspended' => true,
                'reason' => 'Tidak relevan.',
            ])
            ->assertUnprocessable();

        $this->actingAs($admin)
            ->deleteJson("/api/admin/users/{$admin->id}")
            ->assertUnprocessable();
    }

    public function test_unpublished_recipe_disappears_from_public_api_and_can_be_restored(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recipe = $this->createRecipe();

        $this->actingAs($admin)
            ->patchJson("/api/admin/recipes/{$recipe->id}", [
                'status' => 'unpublished',
                'reason' => 'Konten melanggar pedoman.',
            ])
            ->assertOk()
            ->assertJsonPath('recipe.status', 'unpublished');

        $notification = $recipe->creator->notifications()->firstOrFail()->data;
        $this->assertSame('recipe_unpublished', $notification['type']);
        $this->assertSame('Konten melanggar pedoman.', $notification['reason']);
        $this->assertStringContainsString("/recipes/{$recipe->id}/edit", $notification['action_url']);

        auth()->logout();

        $this->getJson('/api/recipes')
            ->assertOk()
            ->assertJsonMissing(['id' => $recipe->id]);

        $this->getJson("/api/recipes/{$recipe->id}")->assertNotFound();

        $this->actingAs($admin)
            ->patchJson("/api/admin/recipes/{$recipe->id}", ['status' => 'published'])
            ->assertOk()
            ->assertJsonPath('recipe.status', 'published');

        auth()->logout();

        $this->getJson("/api/recipes/{$recipe->id}")->assertOk();
    }

    public function test_recipe_owner_is_notified_before_admin_deletes_recipe(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recipe = $this->createRecipe();
        $owner = $recipe->creator;

        $this->actingAs($admin)
            ->deleteJson("/api/admin/recipes/{$recipe->id}", [
                'reason' => 'Resep berisi konten berbahaya.',
            ])
            ->assertOk();

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $notification = $owner->notifications()->firstOrFail()->data;
        $this->assertSame('recipe_deleted', $notification['type']);
        $this->assertSame('Resep berisi konten berbahaya.', $notification['reason']);
        $this->assertNull($notification['action_url']);
    }

    public function test_admin_is_notified_when_owner_repairs_an_unpublished_recipe(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $secondAdmin = User::factory()->create(['role' => 'admin']);
        $recipe = $this->createRecipe();
        $owner = $recipe->creator;

        $recipe->update([
            'moderation_status' => 'unpublished',
            'moderation_reason' => 'Langkah memasak belum aman.',
        ]);

        $this->actingAs($owner)
            ->putJson("/api/recipes/{$recipe->id}", [
                'title' => 'Resep Moderasi Diperbaiki',
                'description' => 'Resep sudah dilengkapi petunjuk keamanan.',
                'difficulty' => 'mudah',
                'estimated_time' => 25,
                'tools' => ['Panci'],
                'ingredient_names' => ['Air'],
                'ingredient_quantities' => ['1 liter'],
                'step_titles' => ['Masak aman'],
                'steps' => ['Gunakan api kecil dan matikan kompor setelah selesai.'],
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Resep berhasil diperbaiki dan dikirim untuk ditinjau admin.');

        $this->assertSame('pending_review', $recipe->fresh()->moderation_status);

        foreach ([$admin, $secondAdmin] as $recipient) {
            $notification = $recipient->fresh()->notifications()->firstOrFail()->data;
            $this->assertSame('recipe_revision_submitted', $notification['type']);
            $this->assertStringContainsString('status=pending_review', $notification['action_url']);
            $this->assertSame($recipe->id, $notification['subject']['id']);
        }
    }

    private function createRecipe(): Recipe
    {
        return Recipe::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Resep Moderasi',
            'description' => 'Resep untuk pengujian moderasi.',
            'difficulty' => 'mudah',
            'estimated_time' => 20,
            'published_at' => now(),
        ]);
    }
}
