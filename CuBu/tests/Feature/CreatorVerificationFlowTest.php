<?php

namespace Tests\Feature;

use App\Models\CreatorVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatorVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_creator_verification(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('creator.apply.store'), [
            'document' => UploadedFile::fake()->create('portfolio.pdf', 100, 'application/pdf'),
            'portfolio_url' => 'https://example.com/portfolio',
            'notes' => 'Saya mengajar kelas memasak rumahan selama dua tahun.',
        ]);

        $verification = CreatorVerification::firstOrFail();

        $response->assertRedirect(route('profile.me'));
        $this->assertSame('pending', $verification->status);
        $this->assertSame('user', $user->fresh()->role);
        Storage::disk('local')->assertExists($verification->document_path);
    }

    public function test_other_user_cannot_download_private_verification_document(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $path = UploadedFile::fake()->create('portfolio.pdf', 10)->store('creator-documents');
        $verification = CreatorVerification::create([
            'user_id' => $owner->id,
            'document_path' => $path,
            'notes' => 'Pengalaman memasak.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->get(route('creator.verifications.document', $verification))
            ->assertForbidden();
    }

    public function test_admin_can_approve_creator_and_user_receives_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'is_verified' => false]);
        $verification = $this->createVerification($user);

        $this->actingAs($admin)
            ->patch(route('admin.creator-verifications.approve', $verification))
            ->assertRedirect(route('admin.creator-verifications.index'));

        $verification->refresh();
        $user->refresh();

        $this->assertSame('approved', $verification->status);
        $this->assertSame($admin->id, $verification->reviewed_by);
        $this->assertSame('creator', $user->role);
        $this->assertTrue($user->is_verified);
        $this->assertTrue($user->canUploadVideos());
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_admin_can_reject_creator_with_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $verification = $this->createVerification($user);

        $this->actingAs($admin)
            ->patch(route('admin.creator-verifications.reject', $verification), [
                'rejection_reason' => 'Dokumen portofolio belum cukup jelas.',
            ])
            ->assertRedirect(route('admin.creator-verifications.index'));

        $verification->refresh();

        $this->assertSame('rejected', $verification->status);
        $this->assertSame('Dokumen portofolio belum cukup jelas.', $verification->rejection_reason);
        $this->assertSame('user', $user->fresh()->role);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertStringContainsString(
            'ajukan ulang',
            $user->fresh()->notifications()->firstOrFail()->data['message'],
        );
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.creator-verifications.index'))
            ->assertRedirect(route('recipes.index'))
            ->assertSessionHas('error', 'Halaman admin hanya dapat diakses oleh akun admin.');
    }

    public function test_admin_verification_list_is_paginated_and_eager_loaded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 18) as $index) {
            $user = User::factory()->create([
                'name' => 'Calon Creator '.$index,
            ]);

            CreatorVerification::create([
                'user_id' => $user->id,
                'document_path' => 'creator-documents/sample-'.$index.'.pdf',
                'notes' => 'Pengalaman memasak '.$index,
                'status' => 'pending',
                'submitted_at' => now()->subMinutes($index),
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($admin)
            ->getJson('/api/admin/creator-verifications?status=pending&page=1')
            ->assertOk()
            ->assertJsonCount(15, 'verifications')
            ->assertJsonPath('pagination.current_page', 1)
            ->assertJsonPath('pagination.last_page', 2)
            ->assertJsonPath('pagination.total', 18)
            ->assertJsonPath('counts.pending', 18)
            ->assertJsonPath('verifications.0.user.name', 'Calon Creator 1');

        $queries = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $query) => str_contains($query, 'creator_verifications')
                || str_contains($query, 'from `users`'));

        $this->assertLessThanOrEqual(5, $queries->count());
        $this->assertNotEmpty($response->json('verifications.0.user.email'));

        $this->actingAs($admin)
            ->getJson('/api/admin/creator-verifications?status=pending&page=2')
            ->assertOk()
            ->assertJsonCount(3, 'verifications')
            ->assertJsonPath('pagination.current_page', 2);
    }

    public function test_regular_user_cannot_access_admin_verification_api(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/api/admin/creator-verifications')
            ->assertForbidden();
    }

    private function createVerification(User $user): CreatorVerification
    {
        return CreatorVerification::create([
            'user_id' => $user->id,
            'document_path' => 'creator-documents/sample.pdf',
            'notes' => 'Pengalaman memasak.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }
}
