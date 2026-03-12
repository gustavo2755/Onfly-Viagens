<?php

namespace App\Services\Contracts;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Contrato para consultas de pedidos e dashboard.
 */
interface TravelOrderQueryServiceInterface
{
    /**
     * Lista pedidos com filtros e paginacao.
     */
    public function list(User $user, array $filters): LengthAwarePaginator;

    /**
     * Busca pedido visivel pelo usuario.
     */
    public function findVisibleById(User $user, int $travelOrderId): ?TravelOrder;

    /**
     * Retorna agregados de dashboard.
     */
    public function dashboard(User $user): array;
}
