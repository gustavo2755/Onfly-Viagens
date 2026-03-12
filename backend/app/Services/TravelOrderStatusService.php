<?php

namespace App\Services;

use App\Enums\TravelOrderStatusEnum;
use App\Exceptions\BusinessRuleException;
use App\Models\TravelOrder;
use App\Models\User;
use App\Services\Contracts\TravelOrderStatusServiceInterface;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Servico de transicoes de status de pedidos.
 */
class TravelOrderStatusService implements TravelOrderStatusServiceInterface
{
    /**
     * Atualiza status respeitando regras de autorizacao e negocio.
     */
    public function updateStatus(User $adminUser, TravelOrder $travelOrder, string $status): TravelOrder
    {
        if (!$adminUser->isAdmin()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $nextStatus = TravelOrderStatusEnum::from($status);

        if ($travelOrder->status === TravelOrderStatusEnum::Approved && $nextStatus === TravelOrderStatusEnum::Cancelled) {
            throw new BusinessRuleException('Approved travel orders cannot be cancelled.');
        }

        $travelOrder->update(['status' => $nextStatus->value]);

        return $travelOrder->fresh(['user']);
    }
}
