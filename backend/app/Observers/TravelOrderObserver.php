<?php

namespace App\Observers;

use App\Models\TravelOrder;
use App\Models\TravelOrderStatusLog;
use App\Notifications\TravelOrderStatusChangedNotification;
use App\Services\Contracts\AdminCacheInvalidationServiceInterface;
use App\Support\Concerns\NormalizesTravelOrderStatus;
use Illuminate\Support\Facades\Auth;

/**
 * Observer para invalidação de cache administrativo e notificações de status.
 */
class TravelOrderObserver
{
    use NormalizesTravelOrderStatus;

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
            $fromStatus = $this->normalizeStatus($travelOrder->getOriginal('status'));
            $toStatus = $this->normalizeStatus($travelOrder->status);
            $adminUserId = Auth::id();

            if ($adminUserId) {
                TravelOrderStatusLog::create([
                    'travel_order_id' => $travelOrder->id,
                    'admin_user_id' => $adminUserId,
                    'from_status' => $fromStatus,
                    'to_status' => $toStatus,
                ]);
            }

            $travelOrder->user?->notify(new TravelOrderStatusChangedNotification(
                $travelOrder,
                $fromStatus,
                $toStatus
            ));
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
}
