<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_as_a_regular_user(): void
    {
        $response = $this->post('/register', [
            'username' => 'dapur_nusa',
            'email' => 'dapur@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $user = User::where('email', 'dapur@example.com')->firstOrFail();

        $response->assertRedirect(route('profile.me'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role);
        $this->assertFalse($user->is_verified);
    }

    public function test_duplicate_email_uses_the_documented_error_message(): void
    {
        User::factory()->create(['email' => 'sama@example.com']);

        $response = $this->from('/register')->post('/register', [
            'username' => 'pengguna_baru',
            'email' => 'sama@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'Email sudah digunakan. Silakan gunakan email lain atau masuk ke akun kamu.',
        ]);
    }

    public function test_failed_api_login_returns_validation_error_without_authenticating(): void
    {
        $this->postJson('/api/login', [
            'email' => 'tidakada@example.com',
            'password' => 'password-salah',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'Email atau kata sandi tidak sesuai.');

        $this->assertGuest();
    }
}
