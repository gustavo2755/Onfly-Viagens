<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        $user = User::factory()->create([
            'email' => 'user@travelorders.test',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonStructure([
            'message',
            'data' => [
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ],
        ]);
    }

    public function test_invalid_login_returns_401(): void
    {
        User::factory()->create([
            'email' => 'user@travelorders.test',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'user@travelorders.test',
            'password' => 'wrong-password',
        ])->assertStatus(401);
    }

    public function test_login_requires_valid_payload_by_form_request(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['email', 'password']]);
    }

    public function test_me_returns_authenticated_user_payload_format(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $response->assertOk()->assertJsonPath('message', 'Logout successful.');
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
