<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTravelOrderStatusLogRequest;
use App\Http\Resources\TravelOrderStatusLogCollection;
use App\Services\Contracts\TravelOrderStatusLogQueryServiceInterface;

/**
 * Controlador de logs de alteracao de status de pedidos.
 */
class TravelOrderStatusLogController extends Controller
{
    public function __construct(
        private readonly TravelOrderStatusLogQueryServiceInterface $travelOrderStatusLogQueryService
    ) {
    }

    /**
     * Lista logs de mudanca de status (apenas admin).
     */
    public function index(IndexTravelOrderStatusLogRequest $request): TravelOrderStatusLogCollection
    {
        $filters = $request->validated();
        $logs = $this->travelOrderStatusLogQueryService->list($filters, $request->integer('per_page', 15));

        return (new TravelOrderStatusLogCollection($logs))->additional([
            'message' => 'Travel order status logs retrieved successfully.',
        ]);
    }
}
