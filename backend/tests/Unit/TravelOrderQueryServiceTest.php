<?php

namespace Tests\Unit;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use App\Services\Contracts\TravelOrderQueryServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TravelOrderQueryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_are_applied_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        TravelOrder::factory()->create([
            'destination' => 'Paris',
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);
        TravelOrder::factory()->create([
            'destination' => 'Tokyo',
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        $service = app(TravelOrderQueryServiceInterface::class);
        $paginator = $service->list($admin, [
            'status' => TravelOrderStatusEnum::Requested->value,
            'destination' => 'Par',
            'per_page' => 15,
            'page' => 1,
        ]);

        $this->assertCount(1, $paginator->items());
        $this->assertSame('Paris', $paginator->items()[0]->destination);
    }

    public function test_regular_user_list_only_returns_own_orders(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        TravelOrder::factory()->count(2)->for($user)->create();
        TravelOrder::factory()->count(3)->for($other)->create();

        $service = app(TravelOrderQueryServiceInterface::class);
        $paginator = $service->list($user, ['per_page' => 15, 'page' => 1]);

        $this->assertCount(2, $paginator->items());
    }

    public function test_admin_dashboard_cache_is_created_with_versioned_key(): void
    {
        Cache::forever('travel_orders_admin_cache_version', 3);
        $admin = User::factory()->admin()->create();
        TravelOrder::factory()->count(2)->create();
        $version = (int) Cache::get('travel_orders_admin_cache_version');

        $service = app(TravelOrderQueryServiceInterface::class);
        $service->dashboard($admin);

        $this->assertTrue(Cache::has('travel_orders_admin_dashboard:v'.$version));
    }

    public function test_find_visible_by_id_respects_ownership_for_non_admin(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ownOrder = TravelOrder::factory()->for($user)->create();
        $otherOrder = TravelOrder::factory()->for($other)->create();

        $service = app(TravelOrderQueryServiceInterface::class);

        $this->assertNotNull($service->findVisibleById($user, $ownOrder->id));
        $this->assertNull($service->findVisibleById($user, $otherOrder->id));
    }
}
