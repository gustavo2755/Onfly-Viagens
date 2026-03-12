<?php

namespace Tests\Feature;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\TravelOrderStatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelOrderStatusLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_logs_route_requires_authentication(): void
    {
        $this->getJson('/api/travel-orders/status-logs')->assertStatus(401);
    }

    public function test_status_logs_route_requires_admin_role(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/travel-orders/status-logs')
            ->assertStatus(403);
    }

    public function test_admin_can_list_status_logs_with_expected_format(): void
    {
        $admin = User::factory()->admin()->create();
        $order = TravelOrder::factory()->create(['status' => TravelOrderStatusEnum::Requested->value]);

        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $order->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/travel-orders/status-logs');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'travel_order_id',
                        'admin_user_id',
                        'from_status',
                        'to_status',
                        'created_at',
                        'admin_user' => ['id', 'name', 'email', 'role'],
                    ],
                ],
                'meta',
                'links',
            ]);
    }

    public function test_observer_creates_log_when_admin_updates_status(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => TravelOrderStatusEnum::Approved->value,
        ])->assertOk();

        $this->assertDatabaseHas('travel_order_status_logs', [
            'travel_order_id' => $travelOrder->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
    }
}
