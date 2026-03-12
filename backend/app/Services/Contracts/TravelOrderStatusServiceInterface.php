<?php

namespace App\Services\Contracts;

use App\Models\TravelOrder;
use App\Models\User;

/**
 * Contrato para transicoes de status de pedidos.
 */
interface TravelOrderStatusServiceInterface
{
    /**
     * Atualiza status de um pedido conforme regras de negocio.
     */
    public function updateStatus(User $adminUser, TravelOrder $travelOrder, string $status): TravelOrder;
}
