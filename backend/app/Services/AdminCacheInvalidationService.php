<?php

namespace App\Services;

use App\Services\Contracts\AdminCacheInvalidationServiceInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Servico central de invalidacao de cache administrativo.
 */
class AdminCacheInvalidationService implements AdminCacheInvalidationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function bumpTravelOrdersAdminVersion(): void
    {
        $version = (int) Cache::get('travel_orders_admin_cache_version', 1);
        Cache::forever('travel_orders_admin_cache_version', $version + 1);
    }
}
