<?php

namespace Tests\Unit;

use App\Services\Contracts\AdminCacheInvalidationServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminCacheInvalidationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_bump_travel_orders_admin_version_increments_cache_version(): void
    {
        Cache::forever('travel_orders_admin_cache_version', 5);

        $service = app(AdminCacheInvalidationServiceInterface::class);
        $service->bumpTravelOrdersAdminVersion();

        $this->assertSame(6, Cache::get('travel_orders_admin_cache_version'));
    }
}
