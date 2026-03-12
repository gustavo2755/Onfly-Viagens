<?php

namespace App\Observers;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\TravelOrderStatusLog;
use App\Services\Contracts\AdminCacheInvalidationServiceInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Observer para invalidação de cache administrativo.
 */
class TravelOrderObserver
{
    public function __construct(private readonly AdminCacheInvalidationServiceInterface $adminCacheInvalidationService)
    {
    }

    /**
     * Invalida cache de dashboard/listagem em criacao.
     */
    public function created(TravelOrder $travelOrder): void
    {
        $this->adminCacheInvalidationService->bumpTravelOrdersAdminVersion();
    }

    /**
     * Invalida cache de dashboard/listagem em atualizacao.
     */
    public function updated(TravelOrder $travelOrder): void
    {
        if ($travelOrder->wasChanged('status')) {
            $adminUserId = Auth::id();

            if ($adminUserId) {
                TravelOrderStatusLog::create([
                    'travel_order_id' => $travelOrder->id,
                    'admin_user_id' => $adminUserId,
                    'from_status' => $this->normalizeStatus($travelOrder->getOriginal('status')),
                    'to_status' => $this->normalizeStatus($travelOrder->status),
                ]);
            }
        }

        $this->adminCacheInvalidationService->bumpTravelOrdersAdminVersion();
    }

    /**
     * Invalida cache de dashboard/listagem em exclusao.
     */
    public function deleted(TravelOrder $travelOrder): void
    {
        $this->adminCacheInvalidationService->bumpTravelOrdersAdminVersion();
    }

    private function normalizeStatus(mixed $status): string
    {
        if ($status instanceof TravelOrderStatusEnum) {
            return $status->value;
        }

        return (string) $status;
    }
}
