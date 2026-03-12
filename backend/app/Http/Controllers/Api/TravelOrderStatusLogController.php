<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelOrderStatusLogCollection;
use App\Models\TravelOrderStatusLog;
use Illuminate\Http\Request;

/**
 * Controlador de logs de alteracao de status de pedidos.
 */
class TravelOrderStatusLogController extends Controller
{
    /**
     * Lista logs de mudanca de status (apenas admin).
     */
    public function index(Request $request): TravelOrderStatusLogCollection
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'This action is unauthorized.');
        }

        $logs = TravelOrderStatusLog::query()
            ->with('adminUser')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return (new TravelOrderStatusLogCollection($logs))->additional([
            'message' => 'Travel order status logs retrieved successfully.',
        ]);
    }
}
