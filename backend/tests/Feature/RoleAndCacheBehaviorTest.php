<?php

namespace Tests\Feature;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAndCacheBehaviorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_assigns_user_role_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->hasRole('user'));
    }

    public function test_user_factory_admin_state_assigns_only_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('user'));
    }

    public function test_login_returns_role_from_spatie_roles(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create([
            'email' => 'admin@travelorders.test',
            'password' => 'password',
        ]);
        $admin->syncRoles(['admin']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@travelorders.test',
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonPath('data.user.role', 'admin');
    }

    public function test_cache_version_increments_when_creating_travel_order(): void
    {
        Cache::forever('travel_orders_admin_cache_version', 1);
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/travel-orders', [
            'requester_name' => 'Cache User',
            'destination' => 'Lisbon',
            'departure_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(2)->toDateString(),
        ])->assertCreated();

        $this->assertSame(2, Cache::get('travel_orders_admin_cache_version'));
    }

    public function test_cache_version_increments_when_updating_status(): void
    {
        Cache::forever('travel_orders_admin_cache_version', 10);
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $order = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);
        $baselineVersion = (int) Cache::get('travel_orders_admin_cache_version');

        $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$order->id.'/status', [
            'status' => TravelOrderStatusEnum::Approved->value,
        ])->assertOk();

        $this->assertSame($baselineVersion + 1, Cache::get('travel_orders_admin_cache_version'));
    }
}
