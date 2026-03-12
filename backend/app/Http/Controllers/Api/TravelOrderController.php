<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTravelOrderRequest;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderStatusRequest;
use App\Http\Resources\TravelOrderCollection;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Services\Contracts\TravelOrderQueryServiceInterface;
use App\Services\Contracts\TravelOrderServiceInterface;
use App\Services\Contracts\TravelOrderStatusServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Controlador de pedidos de viagem.
 */
class TravelOrderController extends Controller
{
    /**
     * @param TravelOrderServiceInterface $travelOrderService Regras de criacao.
     * @param TravelOrderStatusServiceInterface $travelOrderStatusService Regras de transicao de status.
     * @param TravelOrderQueryServiceInterface $travelOrderQueryService Consultas e filtros.
     */
    public function __construct(
        private readonly TravelOrderServiceInterface $travelOrderService,
        private readonly TravelOrderStatusServiceInterface $travelOrderStatusService,
        private readonly TravelOrderQueryServiceInterface $travelOrderQueryService
    ) {
    }

    /**
     * Lista pedidos com filtros e paginacao.
     */
    public function index(IndexTravelOrderRequest $request): TravelOrderCollection
    {
        $this->authorize('viewAny', TravelOrder::class);

        $paginator = $this->travelOrderQueryService->list($request->user(), $request->validated());

        return (new TravelOrderCollection($paginator))->additional([
            'message' => 'Travel orders retrieved successfully.',
        ]);
    }

    /**
     * Cria um pedido com status inicial requested.
     */
    public function store(StoreTravelOrderRequest $request): JsonResponse
    {
        $travelOrder = $this->travelOrderService->create($request->user(), $request->validated());

        return (new TravelOrderResource($travelOrder->load('user')))
            ->additional(['message' => 'Travel order created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Exibe um pedido visivel ao usuario atual.
     */
    public function show(Request $request, int $id): TravelOrderResource
    {
        $travelOrder = $this->travelOrderQueryService->findVisibleById($request->user(), $id);

        if (!$travelOrder) {
            abort(404);
        }

        $this->authorize('view', $travelOrder);

        return (new TravelOrderResource($travelOrder))->additional([
            'message' => 'Travel order retrieved successfully.',
        ]);
    }

    /**
     * Atualiza status de um pedido (somente admin).
     */
    public function updateStatus(UpdateTravelOrderStatusRequest $request, TravelOrder $travelOrder): TravelOrderResource
    {
        $this->authorize('updateStatus', $travelOrder);

        $updated = $this->travelOrderStatusService->updateStatus(
            $request->user(),
            $travelOrder,
            $request->validated('status')
        );

        return (new TravelOrderResource($updated))->additional([
            'message' => 'Travel order status updated successfully.',
        ]);
    }

    /**
     * Retorna metadados do dashboard.
     */
    public function dashboard(Request $request): JsonResource
    {
        $this->authorize('viewAny', TravelOrder::class);

        return JsonResource::make(
            $this->travelOrderQueryService->dashboard($request->user())
        )->additional([
            'message' => 'Travel order dashboard retrieved successfully.',
        ]);
    }
}
