<?php

namespace Tests\Feature;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('user', 'web');
    }

    public function test_list_notifications_returns_paginated(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory()->create();
        $user->notify(new TravelOrderStatusChangedNotification($order, 'requested', 'approved'));

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [['id', 'type', 'data', 'read_at', 'created_at']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_list_notifications_respects_unread_only_param(): void
    {
        $user = User::factory()->create();
        $order1 = TravelOrder::factory()->create();
        $order2 = TravelOrder::factory()->create();
        $user->notify(new TravelOrderStatusChangedNotification($order1, 'requested', 'approved'));
        $user->notify(new TravelOrderStatusChangedNotification($order2, 'approved', 'cancelled'));
        $user->notifications()->latest()->first()->markAsRead();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications?unread_only=1');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertNull($response->json('data.0.read_at'));
    }

    public function test_unread_count_returns_correct_number(): void
    {
        $user = User::factory()->create();
        $order1 = TravelOrder::factory()->create();
        $order2 = TravelOrder::factory()->create();
        $user->notify(new TravelOrderStatusChangedNotification($order1, 'requested', 'approved'));
        $user->notify(new TravelOrderStatusChangedNotification($order2, 'requested', 'cancelled'));

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications/unread-count');

        $response->assertOk()
            ->assertJsonPath('data.count', 2);
    }

    public function test_mark_as_read_succeeds_and_updates_read_at(): void
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory()->create();
        $user->notify(new TravelOrderStatusChangedNotification($order, 'requested', 'approved'));
        $id = $user->notifications()->latest()->first()->id;

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/notifications/'.$id.'/read');

        $response->assertNoContent();
        $this->assertNotNull($user->notifications()->find($id)->read_at);
    }

    public function test_mark_as_read_404_for_other_user_notification(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = TravelOrder::factory()->create();
        $owner->notify(new TravelOrderStatusChangedNotification($order, 'requested', 'approved'));
        $id = $owner->notifications()->latest()->first()->id;

        $response = $this->actingAs($other, 'sanctum')
            ->postJson('/api/notifications/'.$id.'/read');

        $response->assertNotFound();
    }

    public function test_status_change_creates_notification_for_travel_order_owner(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $order = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        $this->actingAs($admin, 'sanctum')->patchJson('/api/travel-orders/'.$order->id.'/status', [
            'status' => TravelOrderStatusEnum::Approved->value,
        ])->assertOk();

        $this->assertDatabaseCount('notifications', 1);
        $notification = $owner->notifications()->first();
        $this->assertEquals('requested', $notification->data['from_status']);
        $this->assertEquals('approved', $notification->data['to_status']);
        $this->assertEquals($order->id, $notification->data['travel_order_id']);
    }

    public function test_notifications_require_authentication(): void
    {
        $this->getJson('/api/notifications')->assertUnauthorized();
        $this->getJson('/api/notifications/unread-count')->assertUnauthorized();
        $this->postJson('/api/notifications/'.uuid_create().'/read')->assertUnauthorized();
    }
}
