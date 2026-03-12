<?php

namespace Tests\Feature;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_with_valid_payload_returns_201(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/travel-orders', [
            'requester_name' => 'John Doe',
            'destination' => 'Lisbon',
            'departure_date' => now()->addDays(3)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'requester_name',
                    'destination',
                    'departure_date',
                    'return_date',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'user_id' => $user->id,
            'destination' => 'Lisbon',
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);
    }

    public function test_create_order_with_invalid_dates_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/travel-orders', [
            'requester_name' => 'John Doe',
            'destination' => 'Lisbon',
            'departure_date' => now()->addDays(7)->toDateString(),
            'return_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors']);
    }

    public function test_create_order_requires_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/travel-orders', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['requester_name', 'destination', 'departure_date', 'return_date'],
            ]);
    }

    public function test_show_existing_order_returns_expected_response_format(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/travel-orders/'.$travelOrder->id);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'requester_name',
                    'destination',
                    'departure_date',
                    'return_date',
                    'status',
                    'created_at',
                    'updated_at',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);
    }

    public function test_show_nonexistent_order_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/travel-orders/999999')->assertStatus(404);
    }

    public function test_non_owner_cannot_see_other_user_order(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($other)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/travel-orders/'.$travelOrder->id);

        $response->assertStatus(404);
    }

    public function test_regular_user_cannot_update_status(): void
    {
        $user = User::factory()->create();
        $travelOrder = TravelOrder::factory()->create(['status' => TravelOrderStatusEnum::Requested->value]);

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_approve_order(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create(['status' => TravelOrderStatusEnum::Requested->value]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $response->assertOk()->assertJsonPath('data.status', TravelOrderStatusEnum::Approved->value);
        $this->assertDatabaseHas('travel_orders', ['id' => $travelOrder->id, 'status' => TravelOrderStatusEnum::Approved->value]);
        $this->assertDatabaseHas('travel_order_status_logs', [
            'travel_order_id' => $travelOrder->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Approved->value,
        ]);
    }

    public function test_admin_cannot_cancel_approved_order(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create(['status' => TravelOrderStatusEnum::Approved->value]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => TravelOrderStatusEnum::Cancelled->value,
        ]);

        $response->assertStatus(422)->assertJsonPath('message', 'Approved travel orders cannot be cancelled.');
    }

    public function test_admin_can_cancel_requested_order(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create(['status' => TravelOrderStatusEnum::Requested->value]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => TravelOrderStatusEnum::Cancelled->value,
        ]);

        $response->assertOk()->assertJsonPath('data.status', TravelOrderStatusEnum::Cancelled->value);
        $this->assertDatabaseHas('travel_orders', ['id' => $travelOrder->id, 'status' => TravelOrderStatusEnum::Cancelled->value]);
        $this->assertDatabaseHas('travel_order_status_logs', [
            'travel_order_id' => $travelOrder->id,
            'admin_user_id' => $admin->id,
            'from_status' => TravelOrderStatusEnum::Requested->value,
            'to_status' => TravelOrderStatusEnum::Cancelled->value,
        ]);
    }

    public function test_status_update_rejects_invalid_status_by_form_request(): void
    {
        $admin = User::factory()->admin()->create();
        $travelOrder = TravelOrder::factory()->create(['status' => TravelOrderStatusEnum::Requested->value]);

        $response = $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$travelOrder->id.'/status', [
            'status' => 'requested',
        ]);

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors' => ['status']]);
    }

    public function test_filters_by_status_and_destination(): void
    {
        $admin = User::factory()->admin()->create();
        TravelOrder::factory()->create([
            'destination' => 'Sao Paulo',
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);
        TravelOrder::factory()->create([
            'destination' => 'Rio de Janeiro',
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/travel-orders?status=requested&destination=Sao');

        $response->assertOk()
            ->assertJsonStructure(['message', 'data', 'meta', 'links'])
            ->assertJsonCount(1, 'data');
    }

    public function test_index_rejects_invalid_filter_ranges_by_form_request(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/travel-orders?created_from=2026-03-10&created_to=2026-03-01');

        $response->assertStatus(422)->assertJsonStructure(['message', 'errors' => ['created_to']]);
    }

    public function test_regular_user_lists_only_own_orders(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        TravelOrder::factory()->count(2)->for($user)->create();
        TravelOrder::factory()->count(3)->for($other)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/travel-orders');

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_admin_lists_all_orders(): void
    {
        $admin = User::factory()->admin()->create();
        TravelOrder::factory()->count(5)->create();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/travel-orders');

        $response->assertOk()->assertJsonCount(5, 'data');
    }

    public function test_dashboard_returns_expected_response_shape(): void
    {
        $admin = User::factory()->admin()->create();
        TravelOrder::factory()->count(2)->create(['status' => TravelOrderStatusEnum::Requested->value]);
        TravelOrder::factory()->count(1)->create(['status' => TravelOrderStatusEnum::Approved->value]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/travel-orders/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => ['total', 'requested', 'approved', 'cancelled'],
            ])
            ->assertJsonPath('data.total', 3)
            ->assertJsonPath('data.requested', 2)
            ->assertJsonPath('data.approved', 1);
    }

    public function test_unauthenticated_requests_are_blocked(): void
    {
        $this->getJson('/api/travel-orders')->assertStatus(401);
    }
}
