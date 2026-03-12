<?php

namespace App\Providers;

use App\Models\TravelOrder;
use App\Observers\TravelOrderObserver;
use App\Policies\TravelOrderPolicy;
use App\Services\AdminCacheInvalidationService;
use App\Services\AuthService;
use App\Services\Contracts\AdminCacheInvalidationServiceInterface;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\TravelOrderQueryServiceInterface;
use App\Services\Contracts\TravelOrderServiceInterface;
use App\Services\Contracts\TravelOrderStatusServiceInterface;
use App\Services\TravelOrderQueryService;
use App\Services\TravelOrderService;
use App\Services\TravelOrderStatusService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(AdminCacheInvalidationServiceInterface::class, AdminCacheInvalidationService::class);
        $this->app->singleton(TravelOrderServiceInterface::class, TravelOrderService::class);
        $this->app->singleton(TravelOrderStatusServiceInterface::class, TravelOrderStatusService::class);
        $this->app->singleton(TravelOrderQueryServiceInterface::class, TravelOrderQueryService::class);
    }

    public function boot(): void
    {
        Gate::policy(TravelOrder::class, TravelOrderPolicy::class);
        TravelOrder::observe(TravelOrderObserver::class);
    }
}
