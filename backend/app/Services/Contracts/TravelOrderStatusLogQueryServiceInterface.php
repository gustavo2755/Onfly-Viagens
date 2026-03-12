<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TravelOrderStatusLogQueryServiceInterface
{
    /**
     * Lista logs de mudanca de status com filtros e paginacao.
     */
    public function list(array $filters, int $perPage): LengthAwarePaginator;
}
