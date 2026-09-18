<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_endpoint_is_available(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response->assertStatus(204);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertOk();
        $response->assertJson(['message' => 'Authenticated.']);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_rejected_if_validation_fails(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_session_id_is_regenerated_after_login(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/sanctum/csrf-cookie');
        $initialSessionId = session()->getId();

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertNotEquals($initialSessionId, session()->getId());
    }

    public function test_guest_cannot_access_user_data(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_user_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonPath('email', $user->email);
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->postJson('/logout');

        $response->assertStatus(401);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertOk();
        $response->assertJson(['message' => 'Logged out.']);
    }

    public function test_response_does_not_leak_password_or_remember_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonMissing(['password']);
        $response->assertJsonMissing(['remember_token']);
    }

    public function test_login_rate_limiting_works(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
