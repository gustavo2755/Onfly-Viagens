<?php

namespace Tests\Unit;

use App\Enums\TravelOrderStatusEnum;
use App\Exceptions\BusinessRuleException;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\TravelOrderStatusChangedNotification;
use App\Services\Contracts\TravelOrderStatusServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelOrderStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_order_and_notification_is_sent(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        $service = app(TravelOrderStatusServiceInterface::class);
        $updated = $service->updateStatus($admin, $travelOrder, TravelOrderStatusEnum::Approved->value);

        $this->assertSame(TravelOrderStatusEnum::Approved, $updated->status);
        Notification::assertSentTo($owner, TravelOrderStatusChangedNotification::class);
    }

    public function test_cannot_cancel_after_approval(): void
    {
        $this->expectException(BusinessRuleException::class);

        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $service = app(TravelOrderStatusServiceInterface::class);
        $service->updateStatus($admin, $travelOrder, TravelOrderStatusEnum::Cancelled->value);
    }

    public function test_admin_can_cancel_requested_order(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        $service = app(TravelOrderStatusServiceInterface::class);
        $updated = $service->updateStatus($admin, $travelOrder, TravelOrderStatusEnum::Cancelled->value);

        $this->assertSame(TravelOrderStatusEnum::Cancelled, $updated->status);
        Notification::assertSentTo($owner, TravelOrderStatusChangedNotification::class);
    }

    public function test_non_admin_cannot_update_status(): void
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $owner = User::factory()->create();
        $travelOrder = TravelOrder::factory()->for($owner)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        $service = app(TravelOrderStatusServiceInterface::class);
        $service->updateStatus($user, $travelOrder, TravelOrderStatusEnum::Approved->value);
    }
}
