<?php

namespace App\Services\Contracts;

use App\Models\TravelOrder;
use App\Models\User;

/**
 * Contrato de criacao de pedidos.
 */
interface TravelOrderServiceInterface
{
    /**
     * Cria um novo pedido associado ao usuario.
     */
    public function create(User $user, array $payload): TravelOrder;
}
