<?php

namespace App\Services;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use App\Services\Contracts\TravelOrderServiceInterface;

/**
 * Servico responsavel pela criacao de pedidos.
 */
class TravelOrderService implements TravelOrderServiceInterface
{
    /**
     * Cria pedido associado ao usuario autenticado.
     */
    public function create(User $user, array $payload): TravelOrder
    {
        return $user->travelOrders()->create([
            'requester_name' => $payload['requester_name'],
            'destination' => $payload['destination'],
            'departure_date' => $payload['departure_date'],
            'return_date' => $payload['return_date'],
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);
    }
}
