<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/users')->assertStatus(401);
    }

    public function test_regular_user_cannot_list_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/users')
            ->assertStatus(403);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(2)->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/users');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'email'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }
}
