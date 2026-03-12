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

    public function test_admin_can_filter_status_logs_by_travel_order_id(): void
    {
        $admin = User::factory()->admin()->create();
        $orderA = TravelOrder::factory()->create();
        $orderB = TravelOrder::factory()->create();

        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderA->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderB->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Cancelled->value,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/travel-orders/status-logs?travel_order_id='.$orderA->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.travel_order_id', $orderA->id);
    }

    public function test_admin_can_filter_status_logs_by_admin_user_id(): void
    {
        $adminA = User::factory()->admin()->create();
        $adminB = User::factory()->admin()->create();
        $order = TravelOrder::factory()->create();

        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $order->id,
            'admin_user_id' => $adminA->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $order->id,
            'admin_user_id' => $adminB->id,
            'from_status' => TravelOrderStatusEnum::Approved->value,
            'to_status' => TravelOrderStatusEnum::Cancelled->value,
        ]);

        $response = $this->actingAs($adminA, 'sanctum')
            ->getJson('/api/travel-orders/status-logs?admin_user_id='.$adminA->id);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.admin_user_id', $adminA->id);
    }

    public function test_admin_can_filter_status_logs_by_to_status(): void
    {
        $admin = User::factory()->admin()->create();
        $orderA = TravelOrder::factory()->create();
        $orderB = TravelOrder::factory()->create();

        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderA->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
        TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderB->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Cancelled->value,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/travel-orders/status-logs?to_status=approved');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.to_status', TravelOrderStatusEnum::Approved->value);
    }

    public function test_admin_can_filter_status_logs_by_created_at_range(): void
    {
        $admin = User::factory()->admin()->create();
        $orderA = TravelOrder::factory()->create();
        $orderB = TravelOrder::factory()->create();

        $olderLog = TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderA->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
        $newerLog = TravelOrderStatusLog::query()->create([
            'travel_order_id' => $orderB->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Cancelled->value,
        ]);
        $olderLog->forceFill([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ])->save();
        $newerLog->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        $from = now()->subDays(2)->toDateString();
        $to = now()->toDateString();
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/travel-orders/status-logs?created_from={$from}&created_to={$to}");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.to_status', TravelOrderStatusEnum::Cancelled->value);
    }

    public function test_status_logs_filters_validate_input(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/travel-orders/status-logs?created_from=2026-03-20&created_to=2026-03-01');

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['created_to']]);
    }
}
