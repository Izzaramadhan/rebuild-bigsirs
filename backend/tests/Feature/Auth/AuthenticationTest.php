<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function validCaptchaSession(string $code = 'ABCDE'): array
    {
        return [
            CaptchaService::SESSION_KEY => Hash::make($code),
            CaptchaService::EXPIRY_KEY => now()->addMinutes(5)->timestamp,
        ];
    }

    public function test_csrf_endpoint_is_available(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response->assertStatus(204);
    }

    public function test_captcha_endpoint_generates_captcha(): void
    {
        $response = $this->get('/captcha');
        $response->assertOk();
        $response->assertJsonStructure(['success', 'data' => ['image', 'expires_in']]);

        $this->assertTrue(session()->has(CaptchaService::SESSION_KEY));
        $this->assertTrue(session()->has(CaptchaService::EXPIRY_KEY));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'password',
                'captcha' => 'ABCDE',
            ]);

        $this->assertAuthenticated();
        $response->assertOk();
        $response->assertJson(['message' => 'Authenticated.']);
    }

    public function test_users_cannot_authenticate_with_email(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'email' => $user->email,
                'password' => 'password',
                'captcha' => 'ABCDE',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username']);
        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'wrong-password',
                'captcha' => 'ABCDE',
            ]);

        $this->assertGuest();
        $response->assertJsonValidationErrors(['username']);
    }

    public function test_login_rejected_if_captcha_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['captcha']);
        $this->assertGuest();
    }

    public function test_login_rejected_if_captcha_is_incorrect(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'password',
                'captcha' => 'WRONG',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['captcha']);
        $this->assertGuest();
    }

    public function test_login_rejected_if_captcha_is_expired(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession([
            CaptchaService::SESSION_KEY => Hash::make('ABCDE'),
            CaptchaService::EXPIRY_KEY => now()->subMinutes(1)->timestamp,
        ])->postJson('/login', [
            'username' => $user->username,
            'password' => 'password',
            'captcha' => 'ABCDE',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['captcha']);
        $this->assertGuest();
    }

    public function test_captcha_is_consumed_after_attempt(): void
    {
        $user = User::factory()->create();

        // First attempt (wrong password, but valid captcha)
        $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'wrong-password',
                'captcha' => 'ABCDE',
            ]);

        $this->assertGuest();

        // Second attempt with same captcha should fail because it's consumed
        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password',
            'captcha' => 'ABCDE',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['captcha']);
        $this->assertGuest();
    }

    public function test_login_rate_limiting_works_with_username_and_ip(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->withSession($this->validCaptchaSession('ABCDE'))
                ->postJson('/login', [
                    'username' => $user->username,
                    'password' => 'wrong-password',
                    'captcha' => 'ABCDE',
                ]);
        }

        $response = $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'wrong-password',
                'captcha' => 'ABCDE',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username']);
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
        $response->assertJsonPath('username', $user->username);
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

        $this->withSession($this->validCaptchaSession('ABCDE'))
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'password',
                'captcha' => 'ABCDE',
            ]);

        $response = $this->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonMissing(['password']);
        $response->assertJsonMissing(['remember_token']);
    }
}
