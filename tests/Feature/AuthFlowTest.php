<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_home(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_user_can_login_and_open_home(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertOk();

        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertOk()->assertSee('id="root"', false);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertGuest();
    }

    public function test_removed_features_are_not_accessible(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/recipes')->assertNotFound();
        $this->get('/profile/1')->assertNotFound();
        $this->get('/collections')->assertNotFound();
    }
}
