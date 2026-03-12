<?php

namespace App\Policies;

use App\Models\TravelOrder;
use App\Models\User;

/**
 * Policy de autorizacao para pedidos de viagem.
 */
class TravelOrderPolicy
{
    /**
     * Permite listagem para usuarios autenticados.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Permite visualizacao para admin ou dono do pedido.
     */
    public function view(User $user, TravelOrder $travelOrder): bool
    {
        return $user->isAdmin() || $travelOrder->user_id === $user->id;
    }

    /**
     * Permite atualizar status apenas para administradores.
     */
    public function updateStatus(User $user, TravelOrder $travelOrder): bool
    {
        return $user->isAdmin();
    }
}
