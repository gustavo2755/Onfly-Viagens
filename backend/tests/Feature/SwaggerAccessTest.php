<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SwaggerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_swagger_ui_requires_authentication(): void
    {
        $response = $this->getJson('/api/documentation');

        $response->assertStatus(401);
    }

    public function test_user_with_token_cannot_access_swagger_ui(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->get('/api/documentation?token='.$token);

        $response->assertStatus(403);
    }

    public function test_user_with_bearer_header_cannot_access_swagger_ui(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->get('/api/documentation');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_swagger_ui_with_token_in_query(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->get('/api/documentation?token='.$token);

        $response->assertStatus(200);
    }

    public function test_admin_can_access_swagger_ui_with_bearer_header(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->get('/api/documentation');

        $response->assertStatus(200);
    }

    public function test_docs_requires_authentication(): void
    {
        $response = $this->getJson('/docs');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_docs(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->get('/docs?token='.$token);

        $response->assertStatus(403);
    }

    public function test_admin_can_access_docs(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->get('/docs?token='.$token);

        $response->assertStatus(200);
    }

    public function test_asset_requires_authentication(): void
    {
        $response = $this->getJson('/docs/asset/swagger-ui.css');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_asset(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->get('/docs/asset/swagger-ui.css?token='.$token);

        $response->assertStatus(403);
    }

    public function test_admin_can_access_asset(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->get('/docs/asset/swagger-ui.css?token='.$token);

        $response->assertStatus(200);
    }
}
