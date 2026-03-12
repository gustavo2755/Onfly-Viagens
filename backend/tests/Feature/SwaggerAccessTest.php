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

    public function test_non_admin_cannot_access_swagger_ui(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/api/documentation');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_swagger_ui_via_session(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')->get('/api/documentation');

        $response->assertStatus(200);
    }

    public function test_docs_requires_authentication(): void
    {
        $response = $this->getJson('/docs');

        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_access_docs(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/docs');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_docs_via_session(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')->get('/docs');

        $response->assertStatus(200);
    }

    public function test_asset_requires_authentication(): void
    {
        $response = $this->getJson('/docs/asset/swagger-ui.css');

        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_access_asset(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/docs/asset/swagger-ui.css');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_asset_via_session(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')->get('/docs/asset/swagger-ui.css');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_browser_redirects_to_frontend_login(): void
    {
        $response = $this->get('/api/documentation');

        $response->assertRedirect(config('app.frontend_url', 'http://localhost:5173') . '/login');
    }
}
